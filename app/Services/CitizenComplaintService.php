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
    public function createComplaint(array $data )
    {
        return DB::transaction(function () use ( $data) {
            $complaint = Complaint::create([
                ...$data,
                'user_id' => Auth::id(),
            ]);

            // حفظ المرفقات
            if (!empty($data['attachments'])) {
                foreach ($data['attachments'] as $attachment) {
                    $complaint->attachments()->create([
                        'file_path' => $this->fileService->upload($attachment, 'complaints'),// $attachment->store('complaints'),
                        'file_type' => $attachment->getMimeType(),
                    ]);
                }
            }

            // تسجيل في السجل التاريخي
            $complaint->histories()->create([
                'user_id' => Auth::id(),
                'action' => 'created',
                'old_snapshot' => null,
                'new_snapshot' => $complaint->toArray(),
            ]);

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
