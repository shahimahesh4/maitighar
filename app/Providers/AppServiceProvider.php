<?php

namespace App\Providers;

use App\Listeners\SendPaymentNotification;
use App\Models\Brand;
use App\Models\Category;
use App\Models\GalleryAlbum;
use App\Models\HomeSlider;
use App\Models\MediaAsset;
use App\Models\Page;
use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\User;
use App\Models\VideoAlbum;
use App\Observers\OptimizeModelImages;
use App\Services\Payment\PaymentGatewayManager;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentGatewayManager::class, function ($app) {
            return new PaymentGatewayManager;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Livewire navigation reuses the existing stylesheet. Re-preloading that
        // CSS on every navigated response creates Chrome's "preloaded but not
        // used" warning, so retain Vite's module preloads but skip CSS preloads.
        Vite::usePreloadTagAttributes(function (string $src, string $url): array|false {
            return str_ends_with(parse_url($url, PHP_URL_PATH) ?: $url, '.css') ? false : [];
        });

        Gate::before(function ($user, string $ability, array $arguments): ?bool {
            if (! in_array($user->role, ['super_admin', 'admin', 'trainer', 'staff'], true)) {
                return null;
            }

            if (in_array($user->role, ['super_admin', 'admin'], true)) {
                return true;
            }

            $subject = $arguments[0] ?? null;
            $modelClass = is_object($subject) ? $subject::class : $subject;
            $permission = match (class_basename((string) $modelClass)) {
                'Product', 'Category', 'Brand', 'ProductFaq', 'InventoryLog' => 'catalog',
                'Order', 'OrderItem', 'Address' => 'orders',
                'User' => 'users',
                'Page', 'PageFaq', 'MediaAsset', 'MediaFolder', 'GalleryAlbum' => 'content',
                'Coupon', 'Review', 'Wishlist', 'Subscriber' => 'marketing',
                'SupportTicket', 'ContactMessage' => 'support',
                'PaymentGateway', 'PaymentTransaction', 'PaymentRefund', 'PaymentDispute', 'PaymentPayout' => 'payments',
                'Setting', 'ShippingRate' => 'settings',
                default => null,
            };

            return $permission === null ? false : $user->hasAdminPermission($permission);
        });

        Event::subscribe(SendPaymentNotification::class);

        foreach ([
            Product::class, Category::class, Brand::class, Page::class,
            GalleryAlbum::class, HomeSlider::class, VideoAlbum::class, MediaAsset::class,
            User::class, PaymentGateway::class, PaymentTransaction::class,
        ] as $model) {
            $model::observe(OptimizeModelImages::class);
        }

        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            $url = url(route('password.reset', ['token' => $token, 'email' => $notifiable->getEmailForPasswordReset()], false));

            return (new MailMessage)
                ->subject('Reset your Maitighar password')
                ->view('emails.branded', [
                    'subjectLine' => 'Password reset',
                    'heading' => 'Reset your password',
                    'intro' => 'We received a request to reset your account password. This link will expire for your security.',
                    'details' => ['Account' => $notifiable->email],
                    'actionLabel' => 'Reset password',
                    'actionUrl' => $url,
                    'messageText' => 'If you did not request this change, you can safely ignore this email.',
                ]);
        });
    }
}
