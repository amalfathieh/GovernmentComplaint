<?php

namespace App\Console\Commands;

use App\Jobs\UploadToGoogleDrive;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DatabaseBackupCommand extends Command
{
    protected $signature = 'db:backup-to-drive';
    protected $description = 'تفريغ قاعدة البيانات ورفعها إلى Google Drive';
    public function handle()
    {
        $this->info('جاري بدء عملية النسخ الاحتياطي الكامل (قاعدة بيانات + مرفقات)...');

        $timestamp = now()->format('Y-m-d_H-i-s');
        $dbFileName = 'db-backup-' . $timestamp . '.sql';
        $filesFileName = 'attachments-backup-' . $timestamp . '.zip';

        $dbPath = storage_path('app/' . $dbFileName);
        $filesPath = storage_path('app/' . $filesFileName);

        // --- أولاً: نسخ قاعدة البيانات ---
        $dbHost = config('database.connections.mysql.host');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password_archive');
        $dbName = config('database.connections.mysql.database');

        $dbCommand = empty($dbPass)
            ? sprintf('mysqldump --host=%s --user=%s %s > "%s"', $dbHost, $dbUser, $dbName, $dbPath)
            : sprintf('mysqldump --host=%s --user=%s --password=%s %s > "%s"', $dbHost, $dbUser, $dbPass, $dbName, $dbPath);

        exec($dbCommand, $output, $dbReturn);

        // --- ثانياً: ضغط مجلد المرفقات ---
        // نفترض أن المرفقات مخزنة في storage/app/public/attachments
        $attachmentsDir = storage_path('app/public/complaints');

        $zip = new \ZipArchive();
        if ($zip->open($filesPath, \ZipArchive::CREATE) === TRUE) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($attachmentsDir));
            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($attachmentsDir) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
            $zipSuccess = true;
        } else {
            $zipSuccess = false;
        }

        // --- ثالثاً: الرفع إلى Google Drive ---
        try {
            UploadToGoogleDrive::dispatch($dbPath, $dbFileName);
            UploadToGoogleDrive::dispatch($filesPath, $filesFileName);

            $this->info('تمت إضافة مهام الرفع إلى الطابور (Queue). ستتم المعالجة في الخلفية.');

            Log::info("Full Backup Success: DB and Attachments uploaded.");

        } catch (\Exception $e) {
            Log::info('حدث خطأ أثناء رفع نسخة احتياطية من قاعدة البيانات: ' . $e->getMessage());

            $this->error('حدث خطأ أثناء الرفع: ' . $e->getMessage());
        }
    }
}
