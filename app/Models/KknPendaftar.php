<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KknPendaftar extends Model
{
    use HasFactory;

    protected $table = 'kkn_pendaftar';
    
    protected $fillable = [
        'judul_kegiatan',
        'mitra',
        'lokasi_mitra',
        'file_path',
        'file_name',
        'status',
        'catatan_verifikasi',
        'tanggal_verifikasi',
        'user_nim'
    ];

    protected $casts = [
        'tanggal_verifikasi' => 'datetime',
    ];

    public function anggota()
    {
        return $this->hasMany(KknAnggota::class);
    }

    public function getJumlahAnggotaAttribute()
    {
        return $this->anggota()->count();
    }

    public function getKetuaAttribute()
    {
        return $this->anggota()->where('peran', 'Ketua')->first();
    }
} 
