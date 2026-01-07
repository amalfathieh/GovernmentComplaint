<?php


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
            $timestamp = now()->format('Y_m_d_H_i_s');
            $fileName = "complaints_{$timestamp}.{$this->reportType}";
            $relativeStoragePath = "reports/" . $fileName;

            // الحفظ
            if (in_array($this->reportType, ['xlsx', 'csv'])) {
                Excel::store(new ComplaintsExport($this->filters), $relativeStoragePath, 'public');
            } else if ($this->reportType === 'pdf') {
                $reportService = new ComplaintReportService();
                $complaints = $reportService->generateReport($this->filters);
                $pdf = Pdf::loadView('reports.complaints_pdf', compact('complaints'))
                    ->setPaper('A4', 'portrait');
                Storage::disk('public')->put($relativeStoragePath, $pdf->output());
            }

            // --- التغيير الجذري هنا ---
            // نسحب الدومين من الإعدادات ونضيف عليه المسار يدوياً لضمان عدم ضياعه
            $baseUrl = rtrim(config('app.url'), '/');
            $fullUrl = $baseUrl . '/storage/' . $relativeStoragePath;

            $this->sendTelegramNotification($fullUrl);

        } catch (\Exception $e) {
            Log::error("Report Job Error: " . $e->getMessage());
        }
    }

    protected function sendTelegramNotification($fullUrl)
    {
        $botToken = config('services.telegram.token') ?? env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_ADMIN_CHANNEL_ID');

        // بناء الرسالة باستخدام HTML وهو الأكثر استقراراً مع الروابط المعقدة
        $message = "<b>✅ تم تجهيز التقرير بنجاح</b>\n\n";
        $message .= "<b>📄 النوع:</b> " . strtoupper($this->reportType) . "\n";
        $message .= "<b>🔗 الرابط:</b> <a href='{$fullUrl}'>إضغط هنا لتحميل الملف</a>\n\n";
        $message .= "<code>{$fullUrl}</code>"; // وضع الرابط الخام داخل وسم code يمنع تلغرام من العبث برموزه

        Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => false,
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
