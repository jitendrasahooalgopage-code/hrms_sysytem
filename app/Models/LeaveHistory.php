<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveHistory extends Model
{
    protected $fillable = ['leave_id', 'actor_id', 'action', 'note'];

    public function leave(): BelongsTo {
        return $this->belongsTo(Leave::class);
    }

    public function actor(): BelongsTo {
        return $this->belongsTo(Employee::class, 'actor_id');
    }
}