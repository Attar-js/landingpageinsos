<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    use HasFactory;

    protected $table = 'penilaian';
    
    protected $fillable = [
        'mahasiswa_nim',
        'dosen_id',
        'proposal_kegiatan',
        'peer_review',
        'laporan_akhir',
        'presentasi_akhir',
        'nilai_akhir',
        'catatan',
        'tanggal_penilaian'
    ];

    protected $casts = [
        'tanggal_penilaian' => 'datetime',
        'proposal_kegiatan' => 'decimal:2',
        'peer_review' => 'decimal:2',
        'laporan_akhir' => 'decimal:2',
        'presentasi_akhir' => 'decimal:2',
        'nilai_akhir' => 'decimal:2'
    ];

    /**
     * Relasi ke mahasiswa berdasarkan NIM
     */
    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_nim', 'nim');
    }

    /**
     * Relasi ke dosen
     */
    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    /**
     * Scope untuk filter berdasarkan dosen
     */
    public function scopeByDosen($query, $dosen_id)
    {
        return $query->where('dosen_id', $dosen_id);
    }

    /**
     * Scope untuk filter berdasarkan mahasiswa
     */
    public function scopeByMahasiswa($query, $mahasiswa_nim)
    {
        return $query->where('mahasiswa_nim', $mahasiswa_nim);
    }
}

