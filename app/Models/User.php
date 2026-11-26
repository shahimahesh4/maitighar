<?php

namespace App\Models;

use App\Notifications\BrandedVerifyEmailNotification;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'email_verified_at',
        'password',
        'role',
        'avatar',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'state',
        'postal_code',
        'employee_id',
        'job_title',
        'department',
        'joining_date',
        'employment_type',
        'admin_permissions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_code',
        'phone_verification_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'email_verification_expires_at' => 'datetime',
            'email_verification_sent_at' => 'datetime',
            'date_of_birth' => 'date',
            'joining_date' => 'date',
            'admin_permissions' => 'array',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $user): void {
            if (! in_array($user->role, ['trainer', 'staff'], true)) {
                $user->admin_permissions = null;
            }
        });
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new BrandedVerifyEmailNotification);
    }

    public static function roleOptions(): array
    {
        return [
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'staff' => 'Staff',
            'customer' => 'Customer',
        ];
    }

    public static function adminPermissionOptions(): array
    {
        return [
            'catalog' => 'Products, categories, brands & inventory',
            'orders' => 'Orders & fulfilment',
            'services' => 'Services & appointments',
            'users' => 'Customers & user records',
            'content' => 'Website pages & media',
            'marketing' => 'Coupons, reviews, wishlists & subscribers',
            'support' => 'Support tickets & contact messages',
            'payments' => 'Payments, refunds, disputes & payouts',
            'settings' => 'Store settings & shipping configuration',
        ];
    }

    public function hasAdminPermission(string $permission): bool
    {
        if (in_array($this->role, ['super_admin', 'admin'], true)) {
            return true;
        }

        return in_array($permission, $this->admin_permissions ?? [], true);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'trainer', 'staff'], true);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
}
