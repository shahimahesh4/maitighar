<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentVoucherController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\VideoGalleryController;
use App\Livewire\Auth\ForgotPasswordPage;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\ResetPasswordPage;
use App\Livewire\Auth\VerifyPhonePage;
use App\Livewire\Auth\VerifyEmailOtpPage;
use App\Livewire\CancelPage;
use App\Livewire\CartPage;
use App\Livewire\CategoriesPage;
use App\Livewire\CheckoutPage;
use App\Livewire\ContactPage;
use App\Livewire\DashboardPage;
use App\Livewire\HomePage;
use App\Livewire\MyOrderDetailPage;
use App\Livewire\MyOrdersPage;
use App\Livewire\ProductDetailPage;
use App\Livewire\ProductsPage;
use App\Livewire\ProfilePage;
use App\Livewire\SuccessPage;
use App\Livewire\SupportTicketsPage;
use App\Livewire\WishlistPage;
use App\Models\Order;
use App\Services\CommunicationService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/', HomePage::class)->name('index');

Route::get('/categories', CategoriesPage::class)->name('product-categories');

Route::get('/products', ProductsPage::class)->name('all-products');

Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('/gallery/{album}', [GalleryController::class, 'show'])->name('gallery.show');
Route::get('/videos', [VideoGalleryController::class, 'index'])->name('videos.index');
Route::get('/videos/{album}', [VideoGalleryController::class, 'show'])->name('videos.show');

Route::get('/product/{slug}', ProductDetailPage::class)->name('product-details');

Route::get('/cart', CartPage::class)->name('cart-products');

Route::get('/checkout', CheckoutPage::class)->middleware('auth')->name('checkout');

Route::get('/wishlist', WishlistPage::class)->middleware('auth')->name('wishlist');

Route::get('/contact', ContactPage::class)->name('contact');

Route::get('/dashboard', DashboardPage::class)->middleware(['auth', 'frontend.user'])->name('dashboard');
Route::get('/profile/edit', ProfilePage::class)->middleware(['auth', 'frontend.user'])->name('profile.edit');
Route::get('/support-tickets', SupportTicketsPage::class)->middleware(['auth', 'frontend.user'])->name('support-tickets');

Route::get('/my-orders', MyOrdersPage::class)->middleware('auth')->name('my-orders');

Route::get('/my-orders/{order}', MyOrderDetailPage::class)->middleware('auth')->name('my-order-details');

// Auth routes

Route::get('/login', LoginPage::class)->name('login');

Route::get('/register', RegisterPage::class)->name('register');

Route::get('/forgot-password', ForgotPasswordPage::class)->name('forgot-password');

Route::get('/reset-password/{token}', ResetPasswordPage::class)->name('password.reset');
Route::get('/verify-email', VerifyEmailOtpPage::class)->middleware(['auth', 'throttle:12,1'])->name('verification.notice');
Route::get('/verify-email/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    if (app(CommunicationService::class)->phoneVerificationEnabled() && ! $request->user()->phone_verified_at) {
        return redirect()->route('phone.verification.notice')->with('status', 'Email verified. Now verify your phone number.');
    }

    return redirect('/')->with('status', 'Email verified successfully.');
})->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', function (Request $request) {
    app(\App\Services\EmailOtpService::class)->sendVerificationCode($request->user());

    return back()->with('status', 'A new verification code was sent.');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
Route::get('/verify-phone', VerifyPhonePage::class)->middleware(['auth', 'throttle:12,1'])->name('phone.verification.notice');

Route::get('/success', SuccessPage::class)->middleware('auth')->name('success');

Route::get('/cancelled', CancelPage::class)->name('cancelled');

// Payment Gateway System Routes
Route::post('/payment/webhook/{gateway}', [PaymentController::class, 'webhook'])->name('payment.webhook');
Route::get('/payment/callback/{gateway}', [PaymentController::class, 'callback'])->name('payment.callback');
Route::post('/payment/callback/{gateway}', [PaymentController::class, 'callback']);
Route::get('/payment/cancel/{order_id}', [PaymentController::class, 'cancel'])->name('payment.cancel');

Route::get('/stnapanel/orders/{order}/invoice', function (Order $order) {
    abort_unless(Auth::user()?->hasAdminPermission('orders'), 403, 'Unauthorized action.');

    return view('orders.invoice', compact('order'));
})->middleware('auth')->name('orders.invoice');

Route::get('/stnapanel/payment-vouchers/{transaction}', [PaymentVoucherController::class, 'show'])
    ->middleware('auth')
    ->name('payment-vouchers.show');

Route::get('/stnapanel/sliders/{slider}/preview', function (\App\Models\Slider $slider) {
    return view('filament.slider-iframe-preview', ['slider' => $slider]);
})->middleware('auth')->name('filament.sliders.preview');


Route::get('/currency/{currency}', function (string $currency) {
    $code = strtoupper(trim($currency));
    if (in_array($code, ['AUD', 'USD', 'NPR', 'EUR', 'GBP'], true)) {
        session(['currency' => $code]);
        cookie()->queue('currency', $code, 60 * 24 * 30);
    }

    return redirect()->back();
})->name('currency.switch');

// Public pages by slug. Exclude common reserved prefixes so we don't
// accidentally intercept Filament admin, Livewire, API, or other system routes.
Route::get('/{page:slug}', [PageController::class, 'show'])
    ->where('page', '^(?!stnapanel|livewire|api|storage|_ignition|_debugbar).+$')
    ->name('pages.show');

// Route::get('/', function () {
//     return view('welcome');
// });
