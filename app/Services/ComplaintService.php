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

    public function update(Complaint $complaint, string $status = null, string $note = null)
    {
        $userId = Auth::id();
        // ensure lock held by this user (or expired)
        if ($complaint->lockedByAnotherUser(Auth::id())) {
            throw new \RuntimeException('Complaint is locked by another user.');
        }

        return DB::transaction(function () use ($complaint, $status, $note) {
            $old = $complaint->toArray();
            $complaint->fill([
                'status' => $status ?? $complaint->status,
                'note' => $note ?? $complaint->note
                ]);

            $complaint->save();

            return $complaint->fresh();
        });
    }

    // حجز شكوى للمعالجة
    public function lock(Complaint $complaint)
    {

        if ($complaint->lockedByAnotherUser(Auth::id())) {
            throw new \RuntimeException('.الشكوى قيد المعالجة من قبل مستخم اخر');
        }

        $complaint->update([
            'locked_by' => Auth::id(),
            'locked_until' => now()->addMinutes($this->lockMinutes),
        ]);
        return true;
    }

    public function unlock(Complaint $complaint): bool
    {
        // allow unlock if same user or expired
        if ($complaint->lockedByAnotherUser(Auth::id())) {
            throw new \RuntimeException('Cannot unlock, locked by another user.');
        }

        $complaint->update([
            'locked_by' => null,
            'locked_until' => null,
        ]);

        return true;
    }


    public function allComplaint($request){
        $user = Auth::user();
        $query = Complaint::with(['attachments','histories.user','organization'])
            ->filterStatus($request->query());

        if ($user->role == 'admin'){
            $query->filterOrganization($request->query());
        }

        if ($user->role == 'employee') {
            $query->where('organization_id', $user->organization_id);
            }
        return $query->get();
    }

    public function showDetails(Complaint $complaint){

        if ($complaint->lockedByAnotherUser(Auth::id())) {
            throw new \RuntimeException('Complaint is locked by another user.');
        }

        $this->lock($complaint);

        return $complaint->load(['attachments','histories.user','organization']);

    }

}
