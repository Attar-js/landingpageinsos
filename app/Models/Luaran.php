<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Luaran extends Model
{
    use HasFactory;

    protected $table = 'luaran';

    protected $fillable = [
        'judul_kegiatan',
        'video_aftermovie',
        'artikel_link',
        'artikel_file_path',
        'artikel_file_name',
        'status',
        'catatan',
        'user_nim'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
} 
