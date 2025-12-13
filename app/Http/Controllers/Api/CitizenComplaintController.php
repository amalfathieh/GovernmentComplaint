<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComplaintRequest;
use App\Http\Requests\UpdateComplaintRequest;
use App\Http\Responses\Response;
use App\Models\Complaint;
use App\Services\CitizenComplaintService;
use App\Services\ComplaintService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CitizenComplaintController extends Controller
{
    private CitizenComplaintService $citizenComplaintService;

    public function __construct(CitizenComplaintService $citizenComplaintService)
    {
        $this->citizenComplaintService = $citizenComplaintService;
    }

    public function store(StoreComplaintRequest $request)
    {
        $complaint = $this->citizenComplaintService->createComplaint($request->validated() + [
                'attachments' => $request->file('attachments') ?: []
            ]);

        return Response::Success($complaint, 'تم انشاء الشكوى بنجاح، ستتم مراجعتها من قبل المسؤولين', 201);

    }

    // my complaints (citizen)
    public function myComplaints()
    {
        $result = $this->citizenComplaintService->myComplaints(Auth::id());
        return Response::Success($result);

    }


    public function update(UpdateComplaintRequest $request, Complaint $complaint){
        try {
            $this->authorize('update', $complaint);

            $this->citizenComplaintService->update($complaint, $request->validated() + [
                    'attachments' => $request->file('attachments') ?: [],
                    'version_number' => $request->input('version_number'),
                ]);
            return Response::Success(null, 'Complaint updated');

        } catch (\RuntimeException $e) {
            $code = $e->getCode() ?: 400;
            return Response::Error($e->getMessage(), $code);
        } catch (\Exception $e) {
            return Response::Error('Unexpected error: ' . $e->getMessage(), 500);
        }
    }
}
