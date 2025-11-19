<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComplaintRequest;
use App\Http\Requests\UpdateComplaintByEmpolyeeRequest;
use App\Http\Responses\Response;
use App\Models\Complaint;
use App\Services\ComplaintService;
use Illuminate\Support\Facades\Auth;


class ComplaintController extends Controller
{
    private ComplaintService $complaintService;

    public function __construct(ComplaintService $complaintService)
    {
        $this->complaintService = $complaintService;
    }

    public function store(StoreComplaintRequest $request)
    {
        $complaint = $this->complaintService->createComplaint($request->validated() + [
                'attachments' => $request->file('attachments') ?: []
            ]);

        return Response::Success($complaint, 'تم انشاء الشكوى بنجاح، ستتم مراجعتها من قبل المسؤولين', 201);

    }

    // my complaints (citizen)
    public function myComplaints()
    {
        $result = $this->complaintService->myComplaints(Auth::id());
        return Response::Success($result);

    }

    //complaints for specific organization show by employee or admin
    public function allComplaint()
    {
        $complaints = $this->complaintService->allComplaint();
        return Response::Success($complaints);
    }

    // show detail
    public function show(Complaint $complaint)
    {
        try{
            $complaint = $this->complaintService->showDetails($complaint);

            return Response::Success($complaint);

        } catch (\RuntimeException $e) {
            return Response::Error($e->getMessage(), 403);
        }
    }

    // update (employee)
    public function update(UpdateComplaintByEmpolyeeRequest $request, Complaint $complaint)
    {
        try {
            $complaint = $this->complaintService->update($complaint, $request->status, $request->note);
            return Response::Success($complaint, 'Complaint updated');
        } catch (\RuntimeException $e) {
            return Response::Error($e->getMessage(), 423);
        }
    }


    // lock for processing (employee)
    public function lock(Complaint $complaint)
    {
        try {
            $this->complaintService->lock($complaint);
            return Response::Success( $complaint->fresh(), 'Locked for processing');

        } catch (\RuntimeException $e) {
            return Response::Error($e->getMessage(), 423);

        }
    }

    // unlock
    public function unlock(Complaint $complaint)
    {
        try {
            $this->complaintService->unlock($complaint);
            return Response::Success(null, 'Unlocked');

        } catch (\RuntimeException $e) {
            return Response::Error($e->getMessage(), 403);
        }
    }

}
