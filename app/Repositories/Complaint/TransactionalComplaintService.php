<?php


namespace App\Repositories\Complaint;


use App\Models\Complaint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TransactionalComplaintService implements ComplaintServiceInterface
{

    private ComplaintServiceInterface $inner;

    public function __construct(ComplaintServiceInterface $inner)
    {
        $this->inner = $inner;    }

    public function update(Complaint $complaint, $status, $note)
    {
        return DB::transaction(fn() => $this->inner->update($complaint, $status, $note));
    }

    public function lock(Complaint $complaint)
    {
        return DB::transaction(fn() => $this->inner->lock($complaint));

    }

    public function unlock(Complaint $complaint)
    {
        return DB::transaction(fn() => $this->inner->unlock($complaint));
    }

    public function allComplaintForAdmin($request)
    {
        return $this->inner->allComplaintForAdmin($request);
    }

    public function allComplaintForEmployee($request)
    {
        return $this->inner->allComplaintForEmployee($request);
    }

}
