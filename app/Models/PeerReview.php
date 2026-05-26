<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeerReview extends Model
{
    use HasFactory;

    protected $table = 'peer_review';

    protected $fillable = [
        'judul_kegiatan',
        'file_name',
        'file_path',
        'user_nim',
        'status',
        'catatan',
        'group_id',
        'reviewer_id',
        'reviewee_id',
        'kontribusi_kegiatan',
        'tanggung_jawab',
        'kerjasama_tim',
        'inisiatif_motivasi',
        'final_score',
        'submitted_at',
    ];

    protected $casts = [
        'kontribusi_kegiatan' => 'decimal:2',
        'tanggung_jawab' => 'decimal:2',
        'kerjasama_tim' => 'decimal:2',
        'inisiatif_motivasi' => 'decimal:2',
        'final_score' => 'decimal:2',
        'submitted_at' => 'datetime',
    ];

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewee()
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }
}

