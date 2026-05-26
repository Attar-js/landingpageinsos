<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('group_document_reviews', function (Blueprint $table) {
            $table->string('laporan_status', 20)->default('pending')->after('status');
            $table->string('artikel_status', 20)->default('pending')->after('laporan_status');
            $table->string('video_status', 20)->default('pending')->after('artikel_status');
            $table->text('laporan_note')->nullable()->after('note');
            $table->text('artikel_note')->nullable()->after('laporan_note');
            $table->text('video_note')->nullable()->after('artikel_note');
        });

        DB::table('group_document_reviews')->orderBy('id')->each(function ($row) {
            $status = $row->status ?? 'pending';
            $note = $row->note;
            $payload = [
                'laporan_status' => $status,
                'artikel_status' => $status,
                'video_status' => $status,
            ];
            if ($status === 'rejected' && $note) {
                $payload['laporan_note'] = $note;
                $payload['artikel_note'] = $note;
                $payload['video_note'] = $note;
            }
            DB::table('group_document_reviews')->where('id', $row->id)->update($payload);
        });
    }

    public function down(): void
    {
        Schema::table('group_document_reviews', function (Blueprint $table) {
            $table->dropColumn([
                'laporan_status',
                'artikel_status',
                'video_status',
                'laporan_note',
                'artikel_note',
                'video_note',
            ]);
        });
    }
};
