<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComplaintRequest;
use App\Http\Requests\UpdateComplaintRequest;
use App\Http\Responses\Response;
use App\Models\Complaint;
use App\Services\CitizenComplaintService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CitizenComplaintController extends Controller
{
    private CitizenComplaintService $citizenComplaintService;

    public function __construct(CitizenComplaintService $citizenComplaintService)
    {
        $this->citizenComplaintService = $citizenComplaintService;
    }

    public function store(StoreComplaintRequest $request)
    {
        try {
            $complaint = $this->citizenComplaintService->createComplaint($request->validated() + [
                    'attachments' => $request->file('attachments') ?: []
                ]);

            return Response::Success($complaint, 'تم انشاء الشكوى بنجاح، ستتم مراجعتها من قبل المسؤولين', 201);

        } catch (\Exception $exception) {
            Log::error('Complaint create failed', ['exception' => $exception]);
            return Response::Error('Unexpected error: ' . $exception->getMessage(), 500);

        }
    }

    // my complaints (citizen)
    public function myComplaints()
    {
        try {
            $result = $this->citizenComplaintService->myComplaints(Auth::id());
            return Response::Success($result);
        }catch (\Exception $exception) {
            Log::error('Complaint get failed', ['exception' => $exception]);
            return Response::Error('Unexpected error: ' . $exception->getMessage(), 500);

        }
    }


    public function update(UpdateComplaintRequest $request, Complaint $complaint)
    {
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
            Log::error('Complaint update failed', [
                'exception' => $e,
                'complaint_id' => $complaint->id,
            ]);
            return Response::Error('Unexpected error: ' . $e->getMessage(), 500);
        }
    }
}
