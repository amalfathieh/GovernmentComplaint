<?php


namespace App\Repositories\CitizenComplaint;


use App\Events\ComplaintUpdated;
use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Http\Responses\Response;
use App\Repositories\CitizenComplaint\CitizenComplaintServiceInterface;
use App\Services\FileService;

class CitizenComplaintService implements CitizenComplaintServiceInterface
{


    // إنشاء شكوى جديدة مع Versioning
    public function createComplaint(array $data)
    {
        $complaint = Complaint::create([
            ...$data,
            'user_id' => Auth::id(),
        ]);

        // حفظ المرفقات
        $this->saveAttachments($data['attachments'], $complaint);

        Cache::forget("my_complaints_user_{$complaint->user_id}");
        Cache::forget("employee_{$complaint->locked_by}_complaints");

        return Response::Success($complaint, 'تم انشاء الشكوى بنجاح، ستتم مراجعتها من قبل المسؤولين', 201);
    }

    public function update($complaint, array $data)
    {
        // Check status
        if ($complaint->status !== "need_info") {
            return Response::Error('need_info لا يمكن التعديل إلا إذا كانت الحالة');
        }

        // Check version_number (Optimistic Locking)
        if ($complaint->version_number !== (int)$data['version_number']) {
            return Response::Error('Conflict: complaint was updated by another user', 409);
        }

        // 1. تصوير الحالة قبل التعديل
        $oldSnapshot = $complaint->toArray();
        $oldSnapshot['attachments'] = $complaint->attachments->toArray();

        // 2. تحديث البيانات النصية
        $complaint->update([
            'description' => $data['description'] ?? $complaint->description,
            'location'    => $data['location'] ?? $complaint->location,
        ]);

        // 3. معالجة المرفقات
        $attachmentsChanged = false;
        if (!empty($data['attachments'])) {
            $this->saveAttachments($data['attachments'], $complaint);
            $attachmentsChanged = true;
        }

        // 4. إطلاق الـ Event بناءً على ما حدث
        if ($complaint->wasChanged() || $attachmentsChanged) {
            event(new ComplaintUpdated($oldSnapshot, $complaint, Auth::id()));
        }

        Cache::forget("my_complaints_user_{$complaint->user_id}");
        Cache::forget("employee_{$complaint->locked_by}_complaints");
        return Response::Success(null, 'Complaint updated');
    }

    public function myComplaints($userId)
    {
        $result = Cache::remember(
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
        return Response::Success($result);
    }

    protected function saveAttachments($attachments, $complaint)
    {
        // حفظ المرفقات
        if (!empty($attachments)) {
            $fileService = new FileService();
            foreach ($attachments as $attachment) {
                $complaint->attachments()->create([
                    'file_path' => $fileService->upload($attachment, 'complaints'), // $attachment->store('complaints'),
                    'file_type' => $attachment->getMimeType(),
                ]);
            }
        }
    }
}
