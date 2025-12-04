<?php


namespace App\Services;


use App\Jobs\SendComplaintNotification;
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


    public function update(Complaint $complaint, string $status = null, string $note = null)
    {
        $userId = Auth::id();

        return DB::transaction(function () use ($complaint, $status, $note, $userId) {

            $complaint = Complaint::where('id', $complaint->id)
                ->lockForUpdate()
                ->first();

            // ensure lock held by this user (or expired)
            if ($complaint->lockedByAnotherUser($userId)) {
                throw new \RuntimeException('Complaint is locked by another user.');
            }

            $complaint->update([
                'status'          => $status ?? $complaint->status,
                'note'            => $note ?? $complaint->note,
            ]);

            //send notification to user
            SendComplaintNotification::dispatch($complaint, $complaint->user);

            return $complaint->fresh();
        });
    }

    // حجز شكوى للمعالجة
    public function lock(Complaint $complaint)
    {
        DB::transaction(function () use ($complaint) {

            $complaint = Complaint::where('id', $complaint->id)->lockForUpdate()->first();

            if ($complaint->lockedByAnotherUser(Auth::id())) {
                throw new \RuntimeException('الشكوى قيد المعالجة من قبل مستخدم آخر');
            }

            $complaint->update([
                'locked_by' => Auth::id(),
                'locked_until' => now()->addMinutes($this->lockMinutes),
            ]);
        });
        return true;
    }

    public function unlock(Complaint $complaint): bool
    {
        DB::transaction(function () use ($complaint) {
            $complaint = Complaint::where('id', $complaint->id)->lockForUpdate()->first();

            // allow unlock if same user or expired
            if ($complaint->lockedByAnotherUser(Auth::id())) {
                throw new \RuntimeException('Cannot unlock, locked by another user.');
            }

            $complaint->update([
                'locked_by' => null,
                'locked_until' => null,
            ]);
        });

        return true;
    }


    public function allComplaintForAdmin($request)
    {

        return Complaint::with(['attachments', 'histories.user', 'organization'])
            ->filterStatus($request->query())
            ->filterOrganization($request->query())
            ->latest()
            ->get();
    }

    public function allComplaintForEmployee($request)
    {
        $user = Auth::user();

        return Complaint::with(['attachments'])
            ->filterStatus($request->query())
            ->where('organization_id', $user->organization_id)
            ->latest()
            ->get();
    }

    /*public function allComplaint($request)
    {
        $user = Auth::user();
        $query = Complaint::with(['attachments', 'histories.user', 'organization'])
            ->filterStatus($request->query());

        if ($user->role == 'admin') {
            $query->filterOrganization($request->query());
        }

        if ($user->role == 'employee') {
            $query->where('organization_id', $user->organization_id);
        }
        return $query->get();
    }*/

}
