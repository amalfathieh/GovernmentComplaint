<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Responses\Response;
use App\Services\Admin\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    private EmployeeService $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }


    public function store(CreateEmployeeRequest $request)
    {
        try {
            $employee = $this->employeeService->store($request);
            return Response::Success($employee, 'Employee created successfully and email sent.', 201);

        }catch (\Exception $ex){
            Log::error('Employee create failed', ['exception' => $ex]);
            return Response::Error($ex->getMessage());
        }
    }

/*
 * GET /employees?organization_id=3
 * GET /employees?name=ah
 * GET /employees?organization_id=3&name=mohammad
 */
    public function getAll(Request $request)
    {
        try {
            $employees = $this->employeeService->get($request);
            return Response::Success($employees);
        } catch (\Exception $e) {
            Log::error('Employee get failed', ['exception' => $e]);
            return Response::Error($e->getMessage());
        }
    }

}
