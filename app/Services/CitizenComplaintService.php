<?php


namespace App\Services;


use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CitizenComplaintService
{
    public $fileService;
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    // إنشاء شكوى جديدة مع Versioning
    public function createComplaint(array $data)
    {
        return DB::transaction(function () use ($data) {
            $complaint = Complaint::create([
                ...$data,
                'user_id' => Auth::id(),
            ]);

            // حفظ المرفقات
            if (!empty($data['attachments'])) {
                foreach ($data['attachments'] as $attachment) {
                    $complaint->attachments()->create([
                        'file_path' => $this->fileService->upload($attachment, 'complaints'), // $attachment->store('complaints'),
                        'file_type' => $attachment->getMimeType(),
                    ]);
                }
            }

            return $complaint;
        });
    }

    public function update($complaint, array $data)
    {
        // Check status
        if ($complaint->status !== "need_info") {
            throw new \RuntimeException('need_info لا يمكن التعديل إلا إذا كانت الحالة');
        }


        // Check version_number (Optimistic Locking)
        if ($complaint->version_number !== (int)$data['version_number']) {
            throw new \RuntimeException('Conflict: complaint was updated by another user', 409);
        }

        return DB::transaction(function () use ($data, $complaint) {
            $complaint->update([
                'description' => $data['description'] ?? $complaint->description,
                'location' => $data['location'] ?? $complaint->location,
            ]);

            // حفظ المرفقات الجديدة
            if (!empty($data['attachments'])) {
                foreach ($data['attachments'] as $attachment) {
                    $complaint->attachments()->create([
                        'file_path' => $this->fileService->upload($attachment, 'complaints'),
                        'file_type' => $attachment->getMimeType(),
                    ]);
                }
            }

            return $complaint;
        });
    }


    public function myComplaints($userId)
    {

        return Complaint::where('user_id', $userId)
            ->with(['attachments', 'organization'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
