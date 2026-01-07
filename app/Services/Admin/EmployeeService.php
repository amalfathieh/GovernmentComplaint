<?php


namespace App\Services\Admin;


use App\Models\User;
use App\Notifications\NewEmployeeNotification;
use App\Support\ComplaintCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Traits\AuditLog;

class EmployeeService
{

    use AuditLog;

    public function store($data)
    {
        // generate safe password
        $password = Str::random(8);

        // store employee in database
        $employee = User::create([
            'first_name'      => $data->first_name,
            'last_name'       => $data->last_name,
            'email'           => $data->email,
            'phone'           => $data->phone,
            'role'            => 'employee',
            'organization_id' => $data->organization_id,
            'password'        => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        // Send password by email
        $employee->notify(new NewEmployeeNotification($password));
        $this->auditLog('new_employee', 'User', $employee->id);

        // إبطال كاش الموظفين والنسخة العالمية
        ComplaintCache::bump();
        Cache::forget("employees_list");

        return $employee;
    }

    public function get($request)
    {
        $version = ComplaintCache::version();
        $params = $request->only(['organization_id', 'name', 'email']);
        ksort($params);
        $key = "employees_list_v{$version}_" . md5(json_encode($params));

        return Cache::remember($key, now()->addMinutes(10), function () use ($request) {
            return User::with('organization')
                ->where('role', 'employee') // تأكدي من وجود Scope أو استخدام وير مباشرة
                ->filterByOrganization($request->organization_id)
                ->filterByName($request->name)
                ->filterByEmail($request->email)
                ->latest()
                ->paginate(20);
        });
    }
}
