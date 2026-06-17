<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HierarchyLevel extends Model
{
    use HasFactory;
     protected $fillable = ['department_id', 'role_id', 'level', 'name'];

    public function department(): BelongsTo {
        return $this->belongsTo(Department::class);
    }

    public function role(): BelongsTo {
        return $this->belongsTo(Role::class);
    }
}


