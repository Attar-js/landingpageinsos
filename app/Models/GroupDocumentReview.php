<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupDocumentReview extends Model
{
    use HasFactory;

    public const DOC_LAPORAN = 'laporan_akhir';
    public const DOC_ARTIKEL = 'artikel';
    public const DOC_VIDEO = 'video';

    public const ITEM_STATUSES = ['pending', 'approved', 'rejected'];

    protected $fillable = [
        'group_id',
        'reviewed_by',
        'status',
        'note',
        'laporan_status',
        'artikel_status',
        'video_status',
        'laporan_note',
        'artikel_note',
        'video_note',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function statusFor(string $docKey): string
    {
        return match ($docKey) {
            self::DOC_LAPORAN => $this->laporan_status ?? 'pending',
            self::DOC_ARTIKEL => $this->artikel_status ?? 'pending',
            self::DOC_VIDEO => $this->video_status ?? 'pending',
            default => 'pending',
        };
    }

    public function noteFor(string $docKey): ?string
    {
        return match ($docKey) {
            self::DOC_LAPORAN => $this->laporan_note,
            self::DOC_ARTIKEL => $this->artikel_note,
            self::DOC_VIDEO => $this->video_note,
            default => null,
        };
    }

    public function setItemStatus(string $docKey, string $status, ?string $note = null): void
    {
        $column = match ($docKey) {
            self::DOC_LAPORAN => 'laporan_status',
            self::DOC_ARTIKEL => 'artikel_status',
            self::DOC_VIDEO => 'video_status',
            default => null,
        };
        $noteColumn = match ($docKey) {
            self::DOC_LAPORAN => 'laporan_note',
            self::DOC_ARTIKEL => 'artikel_note',
            self::DOC_VIDEO => 'video_note',
            default => null,
        };

        if ($column) {
            $this->{$column} = $status;
        }
        if ($noteColumn) {
            $this->{$noteColumn} = $note;
        }
    }

    public function syncOverallStatus(): void
    {
        $statuses = [
            $this->laporan_status ?? 'pending',
            $this->artikel_status ?? 'pending',
            $this->video_status ?? 'pending',
        ];

        if (collect($statuses)->every(fn ($s) => $s === 'approved')) {
            $this->status = 'approved';
            $this->note = null;
        } elseif (collect($statuses)->contains('rejected')) {
            $this->status = 'rejected';
        } else {
            $this->status = 'pending';
        }
    }
}
