<?php

namespace App\Models;

use App\Notifications\StaffInvitation;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Which panels this user may enter.
     *
     * Filament calls this on every authenticated panel request and again during
     * login, where a false return surfaces as "These credentials do not match
     * our records" — the same message as a wrong password.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return match ($panel->getId()) {
            'admin' => $this->hasAnyRole(['super_admin', 'clinic-manager']),
            'staff' => $this->hasAnyRole(['super_admin', 'clinic-manager', 'staff-manager', 'staff']),
            default => false,
        };
    }

    /**
     * Email this user a link to choose their own password.
     *
     * Used when an admin creates the account and when they resend the invitation.
     * The callback only swaps in the invitation wording; the link itself comes
     * from the guard-scoped `password.reset` route via ResetPassword::createUrlUsing(),
     * so it stays valid no matter which panel the recipient can reach.
     *
     * @return string Password::RESET_LINK_SENT | INVALID_USER | RESET_THROTTLED
     */
    public function sendStaffInvitation(): string
    {
        return Password::broker('users')->sendResetLink(
            ['email' => $this->email],
            function (CanResetPassword $user, string $token): void {
                $this->notify(app(StaffInvitation::class, ['token' => $token]));
            },
        );
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
}
