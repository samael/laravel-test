<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Ticket extends Model implements HasMedia
{
    /** @use HasFactory<TicketFactory> */
    use HasFactory, InteractsWithMedia;

    /**
     * Media Usage Example:
     *
     * // Add a file to collection
     * $ticket->addMedia(request()->file('attachment'))
     *     ->toMediaCollection('tickets_files');
     *
     * // Get files
     * $files = $ticket->getMedia('tickets_files');
     *
     * // Get first file URL
     * $fileUrl = $ticket->getFirstMediaUrl('tickets_files');
     *
     * // Delete file
     * $ticket->deleteMedia($ticket->getFirstMedia('tickets_files'));
     *
     * @see https://spatie.be/docs/laravel-medialibrary
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'client_id',
        'topic',
        'body',
        'status',
        'date_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_at' => 'datetime',
    ];

    /**
     * Ticket owner (client).
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'client_id');
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('tickets_files')
            ->acceptsMimeTypes([
                'application/pdf',
                'image/jpeg',
                'image/png',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/plain',
                'text/csv',
            ]);
    }

    /**
     * Get allowed media mime types for tickets.
     *
     * @return array<string>
     */
    public static function allowedMediaTypes(): array
    {
        return [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/csv',
        ];
    }

    /**
     * Get maximum file size in bytes.
     */
    public static function maxFileSize(): int
    {
        return 1024 * 1024 * 10; // 10MB
    }

    /**
     * Filter tickets by statistics period.
     */
    public function scopeForStatisticsPeriod(Builder $query, string $period): Builder
    {
        [$from, $to] = self::statisticsPeriodBounds($period);

        return $query->whereBetween('date_at', [$from, $to]);
    }

    /**
     * Get period boundaries used in statistics.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    public static function statisticsPeriodBounds(string $period): array
    {
        $now = Carbon::now();

        return match ($period) {
            'day' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            default => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
        };
    }
}
