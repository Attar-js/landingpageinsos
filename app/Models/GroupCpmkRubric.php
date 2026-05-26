<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupCpmkRubric extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'deskripsi_p5',
        'deskripsi_c3',
        'deskripsi_a2',
        'skor_p5',
        'skor_c3',
        'skor_a2',
        'catatan',
        'filled_by',
        'skor_filled_by',
    ];

    protected $casts = [
        'skor_p5' => 'decimal:2',
        'skor_c3' => 'decimal:2',
        'skor_a2' => 'decimal:2',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function filledByUser()
    {
        return $this->belongsTo(User::class, 'filled_by');
    }

    public function skorFilledByUser()
    {
        return $this->belongsTo(User::class, 'skor_filled_by');
    }

    public function hasDeskripsi(): bool
    {
        return filled(trim((string) $this->deskripsi_p5))
            && filled(trim((string) $this->deskripsi_c3))
            && filled(trim((string) $this->deskripsi_a2));
    }

    public function hasSkor(): bool
    {
        return $this->skor_p5 !== null && $this->skor_c3 !== null && $this->skor_a2 !== null;
    }

    public function getRubrikTotalAttribute(): float
    {
        return round(
            ((float) ($this->skor_p5 ?? 0) * 0.35)
            + ((float) ($this->skor_c3 ?? 0) * 0.30)
            + ((float) ($this->skor_a2 ?? 0) * 0.35),
            2
        );
    }
}
