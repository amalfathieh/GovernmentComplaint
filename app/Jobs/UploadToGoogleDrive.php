<?php

namespace App\Jobs;

use App\Services\GoogleDriveBackupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        $service->uploadFile($this->filePath, $this->fileName);

        // حذف الملف بعد نجاح الرفع
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }
}
