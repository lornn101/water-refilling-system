<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AdminActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'admin_name',
        'action',
        'target_user_id',
        'target_user_name',
        'description',
        'ip_address',
    ];

    // ============================================================
    // RELATIONSHIPS
    // ============================================================
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id')->withTrashed();
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id')->withTrashed();
    }

    // ============================================================
    // STATIC HELPER: LOG AN ACTION
    // ============================================================
    public static function log(string $action, ?User $targetUser, ?string $description = null): self
    {
        $admin = Auth::user();

        return static::create([
            'admin_id' => $admin?->id,
            'admin_name' => $admin?->name ?? 'System',
            'action' => $action,
            'target_user_id' => $targetUser?->id,
            'target_user_name' => $targetUser?->name ?? 'Unknown',
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }

    // ============================================================
    // DISPLAY HELPERS
    // ============================================================
    public function getActionLabel(): string
    {
        return match ($this->action) {
            'approved' => 'Approved Account',
            'rejected' => 'Rejected Account',
            'edited' => 'Edited Account',
            'deleted' => 'Deleted Account',
            'restored' => 'Restored Account',
            'force_deleted' => 'Permanently Deleted',
            'created_rider' => 'Created Rider Account',
            default => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }

    public function getActionIcon(): string
    {
        return match ($this->action) {
            'approved' => '✅',
            'rejected' => '❌',
            'edited' => '✏️',
            'deleted' => '🗑️',
            'restored' => '♻️',
            'force_deleted' => '💀',
            'created_rider' => '🛵',
            default => '📝',
        };
    }

    public function getActionBadgeColor(): string
    {
        return match ($this->action) {
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'edited' => 'bg-blue-100 text-blue-800',
            'deleted' => 'bg-gray-100 text-gray-800',
            'restored' => 'bg-emerald-100 text-emerald-800',
            'force_deleted' => 'bg-red-800 text-white',
            'created_rider' => 'bg-cyan-100 text-cyan-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}