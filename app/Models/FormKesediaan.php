<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormKesediaan extends Model
{
    use HasFactory;

    protected $table = 'form_kesediaan';

    protected $fillable = [
        'judul_kegiatan',
        'file_name',
        'file_path',
        'user_nim',
        'status',
        'catatan',
    ];
}

