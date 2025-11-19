<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'title',
        'description',
        'type',
        'location',
        'status',
        'user_id',
        'organization_id',
        'assigned_to',
        'locked_until',
        'locked_by'
    ];

    protected $casts = [
        'locked_until' => 'datetime'
    ];

    protected static function booted()
    {
        static::creating(function (Complaint $complaint){
            $complaint->reference_number = self::generateReferenceNumber();
        });
    }

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function attachments()
    {
        return $this->hasMany(ComplaintAttachment::class);
    }

    public function histories()
    {
        return $this->hasMany(ComplaintHistory::class);
    }

    // التحقق من أن الشكوى محجوزة
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    // حجز الشكوى لمنع التزامن
    public function lockForUser(User $user, int $minutes = 30): bool
    {
        if ($this->isLocked() && $this->assigned_to !== $user->id) {
            return false;
        }

        $this->update([
            'assigned_to' => $user->id,
            'locked_until' => now()->addMinutes($minutes)
        ]);

        return true;
    }

    // تحرير الشكوى
    public function unlock(): void
    {
        $this->update([
            'locked_by' => null,
            'locked_until' => null
        ]);
    }


    private static function generateReferenceNumber(): string
    {
        do {
            $refNumber = 'CMP-' . random_int(100000, 999999);
        } while (self::where('reference_number', $refNumber)->exists());

        return $refNumber;
    }
}
