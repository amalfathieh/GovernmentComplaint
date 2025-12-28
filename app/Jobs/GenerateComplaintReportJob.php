<?php

namespace App\Jobs;

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
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}
/*

namespace App\Jobs;

use App\Exports\ComplaintsExport;
use App\Models\User;
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

    protected $user;
    protected $filters;
    protected $reportType; // 'xlsx' or 'pdf'

    /**
     * Create a new job instance.
     */
   /* public function __construct(User $user, array $filters, string $reportType = 'xlsx')
    {
        $this->user = $user;
        $this->filters = $filters;
        $this->reportType = $reportType;
    }*/

    /**
     * Execute the job.
     */
    /*public function handle()
    {
        try {
            $fileName = 'reports/complaints_' . now()->format('Y_m_d_H_i_s') . '.' . $this->reportType;

            // 1. توليد الملف وتخزينه مباشرة على الـ Disk الخارجي (S3)
            // ملاحظة: Excel::store تدعم التخزين المباشر مما يقلل استهلاك الرام
            if ($this->reportType === 'xlsx') {
                Excel::store(new ComplaintsExport($this->filters), $fileName, 's3');
            } else {
                // في حالة PDF قد تحتاج لحفظه مؤقتاً ثم رفعه (يعتمد على المكتبة)
                // هنا مثال مبسط للـ PDF
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.complaints_pdf', [
                    'complaints' => \App\Models\Complaint::filter($this->filters)->get()
                ]);
                Storage::disk('s3')->put($fileName, $pdf->output());
            }

            // 2. الحصول على رابط الملف (Temporary URL لزيادة الأمان)
            // الرابط صالح لمدة ساعة واحدة مثلاً
            $url = Storage::disk('s3')->temporaryUrl($fileName, now()->addHour());

            // 3. إرسال الرابط إلى التلغرام
            $this->sendTelegramNotification($url);

        } catch (\Exception $e) {
            // تسجيل الخطأ في حال حدوث مشكلة
            Log::error("Failed to generate report for user {$this->user->id}: " . $e->getMessage());
            // يمكن هنا إرسال إشعار فشل للمستخدم أيضاً
        }
    }

    protected function sendTelegramNotification($fileUrl)
    {
        // تأكد من وضع التوكن والـ Chat ID في ملف .env
        $botToken = env('TELEGRAM_BOT_TOKEN');

        // في هذا السيناريو، نفترض أن الـ chat_id الخاص بالمشرف أو الموظف مخزن في الداتابيز
        // أو يمكنك إرساله لقناة إدارية ثابتة
        $chatId = $this->user->telegram_chat_id ?? env('TELEGRAM_ADMIN_CHANNEL_ID');

        $message = "✅ *تم تجهيز التقرير بنجاح*\n\n";
        $message .= "👤 الطالب: " . $this->user->first_name . "\n";
        $message .= "📄 النوع: " . strtoupper($this->reportType) . "\n";
        $message .= "🔗 [اضغط هنا لتحميل الملف]($fileUrl)";

        Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',
        ]);
    }*/

    /**
     * التعامل مع الفشل (اختياري)
     */
   /* public function failed(\Throwable $exception)
    {
        // إرسال تنبيه للمطورين أن الـ Queue فشلت
        Log::critical("Queue Failed: " . $exception->getMessage());

}*/
