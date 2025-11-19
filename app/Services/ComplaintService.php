<?php


namespace App\Services;


use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComplaintService
{
    private int $lockMinutes = 10;
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

    // تحديث حالة الشكوى مع إدارة التزامن
    public function update(Complaint $complaint, string $status = null, string $note = null)
    {
        $userId = Auth::id();
        // ensure lock held by this user (or expired)
        if ($complaint->locked_by && $complaint->locked_by !== $userId && $complaint->isLocked()) {
            throw new \RuntimeException('Complaint is locked by another user.');
        }

        return DB::transaction(function () use ($complaint, $status, $note) {
            $old = $complaint->toArray();
            $complaint->fill([
                'status' => $status ?? $complaint->status,
                'note' => $note ?? $complaint->note
                ]);

            $complaint->save();

            // create full snapshot history
            $complaint->histories()->create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'old_snapshot' => $old,
                'new_snapshot' => $complaint->fresh()->toArray(),
            ]);

            return $complaint->fresh();
        });
    }

    // حجز شكوى للمعالجة
    public function lock(Complaint $complaint)
    {
        $userId = Auth::id();
        if ($complaint->locked_by && $complaint->locked_by !== $userId && $complaint->isLocked()) {
            throw new \RuntimeException('Complaint is locked by another user.');
        }

        $complaint->update([
            'locked_by' => Auth::id(),
            'locked_until' => now()->addMinutes($this->lockMinutes),
        ]);
        return true;
    }

    public function unlock(Complaint $complaint): bool
    {
        $userId = Auth::id();

        // allow unlock if same user or expired
        if ($complaint->locked_by && $complaint->locked_by !== $userId && $complaint->isLocked()) {
            throw new \RuntimeException('Cannot unlock, locked by another user.');
        }

        $complaint->update([
            'locked_by' => null,
            'locked_until' => null,
        ]);

        return true;
    }

    public function myComplaints($userId)
    {
//        dd(Complaint::all());
        return Complaint::where('user_id', $userId)
            ->with(['attachments', 'organization'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function allComplaint(){
        if (Auth::user()->role == 'admin'){
            $complaints = Complaint::all();
            return $complaints;
        }
        $employee = Auth::user();

        return Complaint::where('organization_id', $employee['organization_id'])->get();

    }
    public function showDetails(Complaint $complaint){
        $user = Auth::user();
        if ($user->organization_id != $complaint->organization_id){
            throw new \RuntimeException('You do not have the required authorization.');
        }

        if ($complaint->locked_by && $complaint->locked_by !== $user->id && $complaint->isLocked()) {
            throw new \RuntimeException('Complaint is locked by another user.');
        }

        $this->lock($complaint);

        return $complaint->load(['attachments','histories.user','organization']);

    }

}
