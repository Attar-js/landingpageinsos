<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KknAnggota extends Model
{
    use HasFactory;

    protected $table = 'kkn_anggota';
    
    protected $fillable = [
        'kkn_pendaftar_id',
        'nama',
        'nim',
        'program_studi',
        'peran'
    ];

    public function pendaftar()
    {
        return $this->belongsTo(KknPendaftar::class, 'kkn_pendaftar_id');
    }
} 
