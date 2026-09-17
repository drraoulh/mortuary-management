<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'color',       // green | blue | yellow | red | purple
        'user_id',
        'loggable_type',
        'loggable_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Polymorphic relation — logs can belong to any model
    public function loggable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Convenience factory methods ────────────────────────────────────
    public static function log(string $description, string $color = 'blue', ?int $userId = null): self
    {
        return self::create([
            'description' => $description,
            'color'       => $color,
            'user_id'     => $userId ?? auth()->id(),
        ]);
    }
}