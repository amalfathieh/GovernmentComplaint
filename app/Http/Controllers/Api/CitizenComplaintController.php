<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComplaintRequest;
use App\Http\Responses\Response;
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
}
