<?php


namespace App\Services;


use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Traits\Transactional;

class CitizenComplaintService
{
    use Transactional;

    public $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    // إنشاء شكوى جديدة مع Versioning
    public function createComplaint(array $data)
    {
        return $this->runInTransaction(function () use ($data) {
            $complaint = Complaint::create([
                ...$data,
                'user_id' => Auth::id(),
            ]);

            // حفظ المرفقات
            $this->saveAttachments($data['attachments'], $complaint);

            Cache::forget("my_complaints_user_{$complaint->user_id}");
            Cache::forget("employee_{$complaint->locked_by}_complaints");
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

        return $this->runInTransaction(function () use ($data, $complaint) {
            $complaint->update([
                'description' => $data['description'] ?? $complaint->description,
                'location' => $data['location'] ?? $complaint->location,
            ]);

            // حفظ المرفقات الجديدة
            $this->saveAttachments($data['attachments'], $complaint);

            Cache::forget("my_complaints_user_{$complaint->user_id}");
            Cache::forget("employee_{$complaint->locked_by}_complaints");

            return $complaint;
        });

    }

    public function myComplaints($userId)
    {
        return Cache::remember(
            "my_complaints_user_{$userId}",
            now()->addMinutes(3),
            function () use ($userId) {
                return Complaint::where('user_id', $userId)
                    ->with(['attachments', 'organization'])
                    ->latest()
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
            }
        );
    }

    protected function saveAttachments($attachments, $complaint)
    {
        // حفظ المرفقات
        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                $complaint->attachments()->create([
                    'file_path' => $this->fileService->upload($attachment, 'complaints'), // $attachment->store('complaints'),
                    'file_type' => $attachment->getMimeType(),
                ]);
            }
        }
    }
}
