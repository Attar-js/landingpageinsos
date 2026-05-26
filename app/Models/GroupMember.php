<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'mahasiswa_id',
        'role',
        'status',
        'dropped_at',
        'drop_reason',
    ];

    protected $casts = [
        'dropped_at' => 'datetime',
    ];

    // Relationships
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    // Helper methods
    public function isLeader()
    {
        return $this->role === 'leader';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }
}

