<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Responses\Response;
use App\Models\User;
use App\Notifications\NewEmployeeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function store(CreateEmployeeRequest $request)
    {
        // توليد كلمة مرور عشوائية آمنة
        $password = Str::random(8);

        // إنشاء الموظف
        $employee = User::create([
            'first_name'       => $request->first_name,
            'last_name'        => $request->last_name,
            'email'            => $request->email,
            'phone'            => $request->phone,
            'role'             => 'employee',
            'organization_id'  => $request->organization_id,
            'password'         => Hash::make($password),
        ]);

        // إرسال كلمة المرور عبر البريد
        $employee->notify(new NewEmployeeNotification($password));

        return Response::Success($employee, 'Employee created successfully and email sent.', 201);

    }
}
