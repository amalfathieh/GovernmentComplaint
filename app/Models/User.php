<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\AuditLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, AuditLog;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'user',
        'organization_id',
        'role',
        'email_verified_at',
        'fcm_token',
        'locked_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'locked_until' => 'datetime',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function routeNotificationForFcm()
    {
        return $this->fcm_token;
    }

    public function scopeFilter(Builder $builder, $filter)
    {

        $builder->when($filter['organization'] ?? false, function ($builder, $value) {
            $builder->where('organization', 'Like', "%{$value}%");
        });
        $builder->when($filter['status'] ?? false, function ($builder, $value) {
            $builder->where('status', $value);
        });
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function scopeEmployees($query)
    {
        return $query->where('role', 'employee');
    }

    public function scopeFilterByOrganization($query, $organizationId)
    {
        if ($organizationId) {
            return $query->where('organization_id', $organizationId);
        }

        return $query;
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function scopeFilterByName($query, $name)
    {
        if ($name) {
            return $query->where(function ($q) use ($name) {
                $q->where('first_name', 'like', "%{$name}%")
                    ->orWhere('last_name', 'like', "%{$name}%");
            });
        }

        return $query;
    }

    public function scopeFilterByEmail($query, $email)
    {
        if ($email) {
            return $query->where('email', 'like', "%{$email}%");
        }

        return $query;
    }


    /*public function scopeFilterUsers($query, array $filters)
    {
        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }
        if (!empty($filters['organization_id'])) {
            $query->where('organization_id', $filters['organization_id']);
        }
        if (!empty($filters['registered_after'])) {
            $query->whereDate('created_at', '>=', $filters['registered_after']);
        }
        if (!empty($filters['registered_before'])) {
            $query->whereDate('created_at', '<=', $filters['registered_before']);
        }
        return $query->with('organization');
    }*/
}
