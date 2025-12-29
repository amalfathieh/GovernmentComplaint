<?php

namespace App\Jobs;

use App\Services\GoogleDriveBackupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UploadToGoogleDrive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $fileName;

    public function __construct($filePath, $fileName)
    {
        $this->filePath = $filePath;
        $this->fileName = $fileName;
    }

    public function handle()
    {
        $service = new GoogleDriveBackupService();
        $response = $service->uploadFile($this->filePath, $this->fileName);

        if ($response->successful()) {
            Log::info("Backup Success: $this->fileName uploaded to Google Drive.");

            // حذف الملف المحلي بعد التأكد من نجاح الرفع
            if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }
        } else {
            Log::error("Backup Upload Failed for $this->fileName. Error: " . $response->body());
        }
    }

}
