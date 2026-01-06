<?php


namespace App\Repositories\CitizenComplaint;

use App\Models\Complaint;
use App\Repositories\CitizenComplaint\CitizenComplaintServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TransactionalCitizenComplaintService implements CitizenComplaintServiceInterface
{

    private CitizenComplaintServiceInterface $inner;

    public function __construct(CitizenComplaintServiceInterface $inner)
    {
        $this->inner = $inner;
    }

    public function createComplaint(array $data)
    {
        return DB::transaction(fn() => $this->inner->createComplaint($data));
    }

    public function update($complaint, array $data)
    {
        return DB::transaction(fn() => $this->inner->update($complaint, $data));

    }

    public function myComplaints($userId)
    {
        return $this->inner->myComplaints($userId);

    }

}
