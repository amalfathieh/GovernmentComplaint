<?php


namespace App\Services\Admin;


use App\Models\User;
use App\Notifications\NewEmployeeNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeService
{

    public function store($array)
    {
        // generate safe password
        $password = Str::random(8);

        // store employee in database
        $employee = User::create([
            'first_name'       => $array->first_name,
            'last_name'        => $array->last_name,
            'email'            => $array->email,
            'phone'            => $array->phone,
            'role'             => 'employee',
            'organization_id'  => $array->organization_id,
            'password'         => Hash::make("12345678"),
            'email_verified_at' =>now(),
        ]);

        // Send password by email
        $employee->notify(new NewEmployeeNotification($password));

        AuditService::log(
            action: 'add new employee',
            model: 'User',
            modelId: $employee->id
        );

        return $employee;

    }

    public function get($request)
    {
        $employees = User::with('organization')
            ->employees()
            ->filterByOrganization($request->organization_id)
            ->filterByName($request->name)
            ->filterByEmail($request->email)
            ->get();

        return $employees;
    }

}
