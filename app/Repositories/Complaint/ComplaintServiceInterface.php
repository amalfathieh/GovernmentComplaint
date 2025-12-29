<?php


namespace App\Repositories\Complaint;


use App\Models\Complaint;
use Illuminate\Support\Collection;

interface ComplaintServiceInterface
{

    public function update(Complaint $complaint, $status, $note);

    public function lock(Complaint $complaint);

    public function unlock(Complaint $complaint);

    public function allComplaintForAdmin($request);

    public function allComplaintForEmployee($request);

}
