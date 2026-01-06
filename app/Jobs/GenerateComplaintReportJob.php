<?php

/*namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateComplaintReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
/*public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
/*public function handle(): void
    {
        //
    }
}*/


namespace App\Jobs;

use App\Exports\ComplaintsExport;
use App\Models\User;
use App\Services\Admin\ComplaintReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class GenerateComplaintReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // protected $user;
    protected $filters;
    protected $reportType; // 'xlsx' or 'pdf'
    protected $token;

    /**
     * Create a new job instance.
     *
     */
    public function __construct(array $filters, string $reportType = 'xlsx')
    {
        $this->token = config('services.telegram.token');

        // $this->user = $user;
        $this->filters = $filters;
        $this->reportType = $reportType;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $fileName = 'reports/complaints_' . now()->format('Y_m_d_H_i_s') . '.' . $this->reportType;

            if ($this->reportType === 'xlsx' || $this->reportType === 'csv') {
                Excel::store(new ComplaintsExport($this->filters), $fileName, 'public');
            } else if ($this->reportType === 'pdf') {
                $reportService = new ComplaintReportService();
                $complaints = $reportService->generateReport($this->filters);
                $pdf = Pdf::loadView('reports.complaints_pdf', compact('complaints'))
                    ->setPaper('A4', 'portrait');
                Storage::disk('public')->put($fileName, $pdf->output());
            }

            $url = asset('storage/' . $fileName);
            // 3. Send download Url by Telegram
            $this->sendTelegramNotification($url);
        } catch (\Exception $e) {
            // تسجيل الخطأ في حال حدوث مشكلة
            Log::error("Failed to generate report for user : " . $e->getMessage());
        }
    }

    protected function sendTelegramNotification($fileUrl)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_ADMIN_CHANNEL_ID');

        // استخدام النص المباشر أحياناً أفضل لتجنب أخطاء الـ Markdown مع الروابط
        $message = "✅ *تم تجهيز التقرير بنجاح*\n\n";
        $message .= "📄 النوع: " . strtoupper($this->reportType) . "\n";
        $message .= "🔗 رابط التحميل:\n" . $fileUrl; // إرسال الرابط كنص صريح

        Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',
        ]);
    }

    /**
     * التعامل مع الفشل (اختياري)
     */
    public function failed(\Throwable $exception)
    {
        // إرسال تنبيه للمطورين أن الـ Queue فشلت
        Log::critical("Queue Failed: " . $exception->getMessage());
    }
}
