<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'complaint_id','user_id','action','old_snapshot','new_snapshot','note'
    ];

    protected $casts = [
        'old_snapshot' => 'array'
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
