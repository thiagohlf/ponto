<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Approval extends Model
{
    protected $fillable = [
        'approvable_type',
        'approvable_id',
        'status',
        'type',
        'requested_by',
        'requested_at',
        'approved_by',
        'approved_at',
        'request_justification',
        'approval_notes',
        'rejection_reason',
        'metadata',
        'priority',
        'deadline',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'metadata' => 'json',
        'priority' => 'integer',
        'deadline' => 'date',
    ];

    // Relacionamentos
    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('requested_by', $userId);
    }

    public function scopeForApprover($query, $userId)
    {
        return $query->where('approved_by', $userId);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now()->toDateString())
                    ->where('status', 'pending');
    }

    public function scopeExpiringSoon($query, $days = 3)
    {
        return $query->where('deadline', '<=', now()->addDays($days)->toDateString())
                    ->where('status', 'pending');
    }

    // Métodos auxiliares
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function isOverdue()
    {
        return $this->deadline && $this->deadline < now()->toDateString() && $this->isPending();
    }

    public function isExpiringSoon($days = 3)
    {
        return $this->deadline && $this->deadline <= now()->addDays($days)->toDateString() && $this->isPending();
    }

    public function isHighPriority()
    {
        return $this->priority >= 4;
    }

    public function isMediumPriority()
    {
        return $this->priority === 3;
    }

    public function isLowPriority()
    {
        return $this->priority <= 2;
    }

    public function getDaysUntilDeadline()
    {
        if (!$this->deadline) {
            return null;
        }

        return now()->diffInDays($this->deadline, false);
    }

    public function approve($approver, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);
    }

    public function reject($approver, $reason)
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
        ]);
    }
}