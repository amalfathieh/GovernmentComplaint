<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleDriveBackupService
{
    protected function getAccessToken()
    {
        $response = Http::post('https://oauth2.googleapis.com/token', [
            'client_id'     => env('GOOGLE_DRIVE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
            'refresh_token' => env('GOOGLE_DRIVE_REFRESH_TOKEN'),
            'grant_type'    => 'refresh_token',
        ]);

        $data = $response->json();

        if (!isset($data['access_token'])) {
            throw new \Exception("fail to get Access Token: " . json_encode($data));
        }

        return $data['access_token'];
    }

    public function uploadFile($filePath, $fileName)
    {
        $accessToken = $this->getAccessToken();
        $folderId = env('GOOGLE_DRIVE_FOLDER_ID');

        // فتح الملف كـ Stream
        // بدلاً من file_get_contents
        $fileStream = fopen($filePath, 'r');

        return Http::withToken($accessToken)
            ->timeout(3600) // مهلة ساعة كاملة للرفع في الخلفية
            ->attach('metadata', json_encode(['name' => $fileName, 'parents' => [$folderId]]), 'metadata.json', ['Content-Type' => 'application/json'])
            ->attach('file', $fileStream, $fileName) // هنا نمرر الـ Stream
            ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');
    }

}
