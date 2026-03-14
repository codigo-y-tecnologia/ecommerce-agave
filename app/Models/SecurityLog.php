<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Usuario;

class SecurityLog extends Model
{

    protected $table = 'tbl_security_logs';

    protected $fillable = [
        'user_id',
        'event_type',
        'severity',
        'category',
        'ip_address',
        'user_agent',
        'description',
        'metadata',
        'country',
        'is_resolved',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'metadata'    => 'array',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    // ── Relaciones ──────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            Usuario::class,
            'user_id',
            'id_usuario'
        );
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(
            Usuario::class,
            'resolved_by',
            'id_usuario'
        );
    }

    // ── Scopes ──────────────────────────────────────────────
    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // ── Helpers de UI ───────────────────────────────────────
    public function severityBadgeClass(): string
    {
        return match ($this->severity) {
            'critical' => 'danger',
            'warning'  => 'warning',
            default    => 'info',
        };
    }

    public function severityIcon(): string
    {
        return match ($this->severity) {
            'critical' => '🔴',
            'warning'  => '🟡',
            default    => '🔵',
        };
    }
}
