<?php

namespace App\Models;

use App\Events\ComplaintUpdated;
use App\Observers\ComplaintObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'title',
        'description',
        'type',
        'note',
        'location',
        'status',
        'user_id',
        'organization_id',
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

//        $oldSnapshot = $complaint->getOriginal();
//        $newSnapshot = $complaint;
//        event(new ComplaintUpdated($oldSnapshot, $newSnapshot));
//        static::observe(ComplaintObserver::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
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

    public function lockedByAnotherUser($userId): bool
    {
        return $this->locked_by &&
            $this->locked_by !== $userId &&
            $this->isLocked();
    }


    private static function generateReferenceNumber(): string
    {
        do {
            $refNumber = 'CMP-' . random_int(100000, 999999);
        } while (self::where('reference_number', $refNumber)->exists());

        return $refNumber;
    }
}
