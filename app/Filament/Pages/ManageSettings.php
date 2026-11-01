<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\Ai\GeminiService;
use App\Services\ImageOptimizationService;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ManageSettings extends Page
{
    public static function canAccess(): bool
    {
        return auth()->user()?->hasAdminPermission('settings') ?? false;
    }

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.manage-settings';

    protected static ?string $navigationLabel = 'Store Settings';

    protected static ?string $title = 'Store Settings';

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationGroup = 'Settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'store_name' => Setting::get('store_name', 'Maitighar'),
            'store_email' => Setting::get('store_email', 'maitigharaustralia@gmail.com'),
            'store_phone' => Setting::get('store_phone', '+61 432 685 546'),
            'store_address' => Setting::get('store_address', '2/75 Dartbrook Road, Auburn NSW, Sydney, NSW 2144'),
            'store_logo' => Setting::get('store_logo', ''),
            'store_favicon' => Setting::get('store_favicon', ''),
            'store_og_image' => Setting::get('store_og_image', ''),
            'seo_meta_title' => Setting::get('seo_meta_title', 'Maitighar - Nepalese Heritage with Love'),
            'seo_meta_description' => Setting::get('seo_meta_description', 'Maitighar - Nepalese Heritage with Love. Authentic Nepalese heritage, culture, and craftsmanship.'),
            'seo_meta_keywords' => Setting::get('seo_meta_keywords', 'Nepalese heritage, Nepalese crafts, Maitighar, Australia'),
            'default_currency' => Setting::get('default_currency', 'AUD'),
            'currency_symbol' => Setting::get('currency_symbol', '$'),
            'sku_prefix' => Setting::get('sku_prefix', 'MG-AUS-'),
            'shipping_cost' => Setting::get('shipping_cost', 0.00),
            'facebook_url' => Setting::get('facebook_url', 'https://www.facebook.com/maitighar'),
            'instagram_url' => Setting::get('instagram_url', 'https://instagram.com/maitighar'),
            'tiktok_url' => Setting::get('tiktok_url', 'https://www.tiktok.com'),
            'youtube_url' => Setting::get('youtube_url', 'https://www.youtube.com'),
            'twitter_url' => Setting::get('twitter_url', 'https://twitter.com/maitighar'),
            'maintenance_mode' => (bool) Setting::get('maintenance_mode', false),
            'maintenance_title' => Setting::get('maintenance_title', 'Maitighar update is underway'),
            'maintenance_message' => Setting::get('maintenance_message', 'We are polishing the website to bring you a better experience. Please check back soon.'),
            'coming_soon_topline_left' => Setting::get('coming_soon_topline_left', 'From Nepal, with Love'),
            'coming_soon_topline_right' => Setting::get('coming_soon_topline_right', 'Australia'),
            'coming_soon_logo' => Setting::get('coming_soon_logo', ''),
            'coming_soon_eyebrow' => Setting::get('coming_soon_eyebrow', 'Nepalese Heritage with Love'),
            'coming_soon_heading' => Setting::get('coming_soon_heading', 'Coming Soon'),
            'coming_soon_subheading' => Setting::get('coming_soon_subheading', 'A beautiful new chapter is coming.'),
            'coming_soon_description' => Setting::get('coming_soon_description', 'Nepalese heritage, culture, and craftsmanship — soon at home in Australia.'),
            'coming_soon_facebook_label' => Setting::get('coming_soon_facebook_label', 'Follow us on Facebook'),
            'coming_soon_values' => Setting::get('coming_soon_values', ['Nepalese Heritage', 'Culture', 'Craftsmanship']),
            'coming_soon_footer_text' => Setting::get('coming_soon_footer_text', '© 2026 Maiti Ghar · Made with heritage and heart'),
            'coming_soon_footer_credit' => Setting::get('coming_soon_footer_credit', 'Crafted by Siddhi Tech Nepal'),
            'admin_notification_email' => Setting::get('admin_notification_email', 'maitigharaustralia@gmail.com'),
            'email_from_name' => Setting::get('email_from_name', 'Maitighar'),
            'email_footer' => Setting::get('email_footer', 'Maitighar - Nepalese Heritage with Love.'),
            'send_customer_receipts' => (bool) Setting::get('send_customer_receipts', true),
            'email_verification_enabled' => filter_var(
                Setting::get('email_verification_enabled', false),
                FILTER_VALIDATE_BOOL,
            ),
            'phone_verification_enabled' => (bool) Setting::get('phone_verification_enabled', false),
            'sparrow_sms_enabled' => (bool) Setting::get('sparrow_sms_enabled', false),
            'sparrow_sms_token' => Setting::get('sparrow_sms_token', ''),
            'sparrow_sms_identity' => Setting::get('sparrow_sms_identity', ''),
            'sparrow_sms_endpoint' => Setting::get('sparrow_sms_endpoint', 'https://api.sparrowsms.com/v2/sms/'),
            'phone_otp_expiry_minutes' => Setting::get('phone_otp_expiry_minutes', 10),
            'phone_otp_resend_seconds' => Setting::get('phone_otp_resend_seconds', 60),
            'gemini_api_key' => Setting::get('gemini_api_key', ''),
            'gemini_active_model' => Setting::get('gemini_active_model', 'gemini-1.5-flash'),
            'google_recaptcha_enabled' => (bool) Setting::get('google_recaptcha_enabled', false),
            'google_recaptcha_site_key' => Setting::get('google_recaptcha_site_key', ''),
            'google_recaptcha_secret_key' => Setting::get('google_recaptcha_secret_key', ''),
            'google_analytics_id' => Setting::get('google_analytics_id', ''),
            'photo_gallery_banner_image' => Setting::get('photo_gallery_banner_image', ''),
            'video_gallery_banner_image' => Setting::get('video_gallery_banner_image', ''),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Store settings')
                    ->tabs([
                        Tabs\Tab::make('Store')
                            ->icon('heroicon-o-building-storefront')
                            ->schema([
                                Section::make('General Configuration')
                                    ->description('Primary contact details for the e-commerce store.')
                                    ->schema([
                                        TextInput::make('store_name')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('store_email')
                                            ->email()
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('store_phone')
                                            ->maxLength(255),
                                        Textarea::make('store_address')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ])->columns(2),

                                Section::make('Brand Assets')
                                    ->description('These uploads are used across the storefront, browser tabs, search previews, and social sharing cards.')
                                    ->schema([
                                        FileUpload::make('store_logo')
                                            ->label('Store logo')
                                            ->image()
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->maxSize(2048)
                                            ->disk('public')
                                            ->directory('settings/brand')
                                            ->visibility('public')
                                            ->imageResizeMode('contain')
                                            ->imageResizeTargetWidth('800')
                                            ->imageResizeTargetHeight('800')
                                            ->imageResizeUpscale(false)
                                            ->saveUploadedFileUsing(static fn (TemporaryUploadedFile $file): ?string => app(ImageOptimizationService::class)
                                                ->storeAsWebp($file->getRealPath(), 'public', 'settings/brand', 800, 800))
                                            ->deleteUploadedFileUsing(static fn (string $file): bool => Storage::disk('public')->delete($file))
                                            ->helperText('JPG, PNG, or WebP up to 2 MB. Used for the storefront and admin brand mark.'),
                                        FileUpload::make('store_favicon')
                                            ->label('Favicon')
                                            ->acceptedFileTypes(['image/png', 'image/x-icon', 'image/vnd.microsoft.icon', 'image/svg+xml', 'image/webp'])
                                            ->maxSize(512)
                                            ->disk('public')
                                            ->directory('settings/brand')
                                            ->visibility('public')
                                            ->deleteUploadedFileUsing(static fn (string $file): bool => Storage::disk('public')->delete($file))
                                            ->helperText('PNG, ICO, SVG, or WebP up to 512 KB. A square 512 × 512 PNG is recommended.'),
                                        FileUpload::make('store_og_image')
                                            ->label('Open Graph image')
                                            ->image()
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->maxSize(3072)
                                            ->disk('public')
                                            ->directory('settings/brand')
                                            ->visibility('public')
                                            ->imageResizeMode('cover')
                                            ->imageResizeTargetWidth('1200')
                                            ->imageResizeTargetHeight('630')
                                            ->imageResizeUpscale(false)
                                            ->saveUploadedFileUsing(static fn (TemporaryUploadedFile $file): ?string => app(ImageOptimizationService::class)
                                                ->storeAsWebp($file->getRealPath(), 'public', 'settings/brand', 1200, 630))
                                            ->deleteUploadedFileUsing(static fn (string $file): bool => Storage::disk('public')->delete($file))
                                            ->helperText('JPG, PNG, or WebP up to 3 MB. Recommended size: 1200 × 630px.'),
                                    ])->columns(3),

                                Section::make('Search Engine Optimisation')
                                    ->description('Default SEO values used when a page or product does not provide its own metadata.')
                                    ->schema([
                                        TextInput::make('seo_meta_title')
                                            ->label('Default SEO title')
                                            ->maxLength(60)
                                            ->helperText('Recommended maximum: 60 characters.'),
                                        Textarea::make('seo_meta_description')
                                            ->label('Default SEO description')
                                            ->rows(3)
                                            ->maxLength(160)
                                            ->helperText('Recommended maximum: 160 characters.'),
                                        Textarea::make('seo_meta_keywords')
                                            ->label('Default SEO keywords')
                                            ->rows(3)
                                            ->helperText('Optional comma-separated keywords.')
                                            ->columnSpanFull(),
                                    ])->columns(2),

                                Section::make('Currency & Shipping')
                                    ->description('Default store currency and shipping configurations.')
                                    ->schema([
                                        Select::make('default_currency')
                                            ->label('Default Store Currency')
                                            ->options([
                                                'AUD' => 'Australian Dollar (AUD - $)',
                                                'USD' => 'US Dollar (USD - $)',
                                                'EUR' => 'Euro (EUR - €)',
                                                'GBP' => 'British Pound (GBP - £)',
                                                'NPR' => 'Nepalese Rupee (NPR - Rs.)',
                                            ])
                                            ->default('AUD')
                                            ->required(),
                                        TextInput::make('currency_symbol')
                                            ->label('Currency Symbol')
                                            ->default('$')
                                            ->required()
                                            ->maxLength(10),
                                        TextInput::make('shipping_cost')
                                            ->label('Default Shipping Cost (AUD)')
                                            ->numeric()
                                            ->prefix('$')
                                            ->required(),
                                        TextInput::make('sku_prefix')
                                            ->label('Default Product SKU Prefix')
                                            ->default('MG-AUS-')
                                            ->helperText('Prefix used for sequential product SKU auto-generation (e.g. MG-AUS-001).')
                                            ->required()
                                            ->maxLength(30),
                                    ])->columns(2),

                                Section::make('Social Media Accounts')
                                    ->description('Configure profile links for store social networks.')
                                    ->schema([
                                        TextInput::make('facebook_url')
                                            ->url()
                                            ->placeholder('https://facebook.com/username'),
                                        TextInput::make('instagram_url')
                                            ->url()
                                            ->placeholder('https://instagram.com/username'),
                                        TextInput::make('tiktok_url')
                                            ->label('TikTok URL')
                                            ->url()
                                            ->placeholder('https://tiktok.com/@username'),
                                        TextInput::make('youtube_url')
                                            ->label('YouTube URL')
                                            ->url()
                                            ->placeholder('https://youtube.com/@channel'),
                                        TextInput::make('twitter_url')
                                            ->label('Twitter / X URL')
                                            ->url()
                                            ->placeholder('https://twitter.com/username'),
                                    ])->columns(3),
                            ]),

                        Tabs\Tab::make('Gallery Banners')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make('Photo & Video Gallery Headers')
                                    ->description('Upload a separate full-width banner for each gallery page. The same image-overlay header style is used on About Us and Contact Us.')
                                    ->schema([
                                        FileUpload::make('photo_gallery_banner_image')
                                            ->label('Photo Gallery banner image')
                                            ->image()
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->maxSize(2048)
                                            ->disk('public')
                                            ->directory('settings/gallery-banners')
                                            ->visibility('public')
                                            ->imageResizeMode('contain')
                                            ->imageResizeTargetWidth('1920')
                                            ->imageResizeTargetHeight('900')
                                            ->imageResizeUpscale(false)
                                            ->saveUploadedFileUsing(static fn (TemporaryUploadedFile $file): ?string => app(ImageOptimizationService::class)
                                                ->storeAsWebp($file->getRealPath(), 'public', 'settings/gallery-banners', 1920, 900))
                                            ->deleteUploadedFileUsing(static fn (string $file): bool => Storage::disk('public')->delete($file))
                                            ->helperText('JPG, PNG, or WebP up to 2 MB. Optimized to WebP at a maximum of 1920 × 900px.'),
                                        FileUpload::make('video_gallery_banner_image')
                                            ->label('Video Gallery banner image')
                                            ->image()
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->maxSize(2048)
                                            ->disk('public')
                                            ->directory('settings/gallery-banners')
                                            ->visibility('public')
                                            ->imageResizeMode('contain')
                                            ->imageResizeTargetWidth('1920')
                                            ->imageResizeTargetHeight('900')
                                            ->imageResizeUpscale(false)
                                            ->saveUploadedFileUsing(static fn (TemporaryUploadedFile $file): ?string => app(ImageOptimizationService::class)
                                                ->storeAsWebp($file->getRealPath(), 'public', 'settings/gallery-banners', 1920, 900))
                                            ->deleteUploadedFileUsing(static fn (string $file): bool => Storage::disk('public')->delete($file))
                                            ->helperText('JPG, PNG, or WebP up to 2 MB. Optimized to WebP at a maximum of 1920 × 900px.'),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Communications')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->schema([
                                Section::make('Email, Receipts & Verification')
                                    ->description('Control branded customer emails, administrator notifications, and account verification requirements.')
                                    ->schema([
                                        TextInput::make('admin_notification_email')->label('Admin notification email')->email()->required(),
                                        TextInput::make('email_from_name')->label('Email sender name')->required(),
                                        Textarea::make('email_footer')->label('Email footer message')->rows(2)->columnSpanFull(),
                                        Toggle::make('send_customer_receipts')
                                            ->label('Send customer confirmation emails')
                                            ->helperText('Contact and registration receipt emails.'),
                                        Toggle::make('email_verification_enabled')
                                            ->label('Require email OTP verification')
                                            ->default(false)
                                            ->helperText('Send new customers a six-digit email code before allowing access to verified-only features.'),
                                        Toggle::make('phone_verification_enabled')
                                            ->label('Require phone verification')
                                            ->default(false)
                                            ->disabled(fn ($get) => ! $get('sparrow_sms_enabled'))
                                            ->helperText('New customers must verify their Nepal mobile number. Enable Sparrow SMS first.'),
                                    ])->columns(2),

                                Section::make('Sparrow SMS')
                                    ->description('Configure OTP delivery using the official Sparrow SMS API for Nepal.')
                                    ->schema([
                                        Toggle::make('sparrow_sms_enabled')
                                            ->label('Enable Sparrow SMS')
                                            ->helperText('Turn this on after entering live credentials.'),
                                        TextInput::make('sparrow_sms_identity')
                                            ->label('Sender identity')
                                            ->placeholder('Approved Sparrow sender name')
                                            ->required(fn ($get) => (bool) $get('sparrow_sms_enabled')),
                                        TextInput::make('sparrow_sms_token')
                                            ->label('API token')
                                            ->password()
                                            ->revealable()
                                            ->required(fn ($get) => (bool) $get('sparrow_sms_enabled')),
                                        TextInput::make('sparrow_sms_endpoint')
                                            ->label('API endpoint')
                                            ->url()
                                            ->required(),
                                        TextInput::make('phone_otp_expiry_minutes')
                                            ->label('OTP expiry')
                                            ->numeric()->minValue(2)->maxValue(30)->suffix('minutes')->required(),
                                        TextInput::make('phone_otp_resend_seconds')
                                            ->label('Resend cooldown')
                                            ->numeric()->minValue(30)->maxValue(300)->suffix('seconds')->required(),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('System')
                            ->icon('heroicon-o-adjustments-horizontal')
                            ->schema([
                                Section::make('Advanced & Status Controls')
                                    ->description('Control system behavior.')
                                    ->schema([
                                        Toggle::make('maintenance_mode')
                                            ->label('Enable Maintenance Mode')
                                            ->helperText('Shows a maintenance page on the public website. The admin panel remains available.')
                                            ->live(),
                                        TextInput::make('maintenance_title')
                                            ->label('Maintenance page title')
                                            ->required(fn ($get) => (bool) $get('maintenance_mode'))
                                            ->visible(fn ($get) => (bool) $get('maintenance_mode'))
                                            ->maxLength(255),
                                        Textarea::make('maintenance_message')
                                            ->label('Maintenance page message')
                                            ->required(fn ($get) => (bool) $get('maintenance_mode'))
                                            ->visible(fn ($get) => (bool) $get('maintenance_mode'))
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ]),
                                Section::make('Coming Soon Page')
                                    ->description('Content shown to public visitors when Maintenance Mode is enabled. Store contact details and Facebook URL are reused from the Store tab.')
                                    ->schema([
                                        TextInput::make('coming_soon_topline_left')->label('Top line — left')->required()->maxLength(80),
                                        TextInput::make('coming_soon_topline_right')->label('Top line — right')->required()->maxLength(80),
                                        FileUpload::make('coming_soon_logo')
                                            ->label('Coming soon logo')
                                            ->image()
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->maxSize(2048)
                                            ->disk('public')
                                            ->directory('settings/coming-soon')
                                            ->visibility('public')
                                            ->imageResizeMode('contain')
                                            ->imageResizeTargetWidth('600')
                                            ->imageResizeTargetHeight('600')
                                            ->imageResizeUpscale(false)
                                            ->saveUploadedFileUsing(static fn (TemporaryUploadedFile $file): ?string => app(ImageOptimizationService::class)
                                                ->storeAsWebp($file->getRealPath(), 'public', 'settings/coming-soon', 600, 600))
                                            ->deleteUploadedFileUsing(static fn (string $file): bool => Storage::disk('public')->delete($file))
                                            ->helperText('Optional. Leave blank to use the standard Maitighar logo.'),
                                        TextInput::make('coming_soon_eyebrow')->label('Red eyebrow text')->required()->maxLength(120),
                                        TextInput::make('coming_soon_heading')->label('Main heading')->required()->maxLength(120),
                                        TextInput::make('coming_soon_subheading')->label('Supporting heading')->required()->maxLength(180)->columnSpanFull(),
                                        Textarea::make('coming_soon_description')->label('Description')->required()->rows(2)->columnSpanFull(),
                                        TextInput::make('coming_soon_facebook_label')->label('Facebook button label')->required()->maxLength(80),
                                        Repeater::make('coming_soon_values')
                                            ->label('Heritage highlights')
                                            ->simple(TextInput::make('value')->required()->maxLength(50))
                                            ->defaultItems(3)
                                            ->minItems(1)
                                            ->maxItems(5)
                                            ->reorderable(),
                                        TextInput::make('coming_soon_footer_text')->label('Footer text')->required()->maxLength(180),
                                        TextInput::make('coming_soon_footer_credit')->label('Footer credit')->required()->maxLength(120),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Integrations')
                            ->icon('heroicon-o-puzzle-piece')
                            ->schema([
                                Section::make('Google Gemini AI Integration')
                                    ->description('Configure Google Gemini LLM settings to enable automated catalog enhancements.')
                                    ->schema([
                                        TextInput::make('gemini_api_key')
                                            ->label('Gemini API Key')
                                            ->password()
                                            ->helperText('Get your API key from Google AI Studio.')
                                            ->suffixAction(
                                                \Filament\Forms\Components\Actions\Action::make('verifyGeminiKey')
                                                    ->label('Verify & Fetch Models')
                                                    ->icon('heroicon-o-check-badge')
                                                    ->color('success')
                                                    ->action(function ($state, Set $set) {
                                                        if (empty($state)) {
                                                            Notification::make()
                                                                ->danger()
                                                                ->title('API Key Required')
                                                                ->body('Please type your API key before verifying.')
                                                                ->send();

                                                            return;
                                                        }

                                                        // Store temporary to check connection
                                                        Setting::set('gemini_api_key', $state);
                                                        $service = app(GeminiService::class);
                                                        $models = $service->listModels();

                                                        if (! empty($models)) {
                                                            Notification::make()
                                                                ->success()
                                                                ->title('Gemini API Connection Success!')
                                                                ->body('Successfully connected and retrieved '.count($models).' available models.')
                                                                ->send();
                                                        } else {
                                                            Notification::make()
                                                                ->danger()
                                                                ->title('Gemini API Connection Failed')
                                                                ->body('Invalid key or network timeout. Please verify credentials.')
                                                                ->send();
                                                        }
                                                    })
                                            ),
                                        Select::make('gemini_active_model')
                                            ->label('Active AI Model')
                                            ->options(function () {
                                                $service = app(GeminiService::class);
                                                $models = $service->listModels();

                                                if (empty($models)) {
                                                    return [
                                                        'gemini-1.5-flash' => 'Gemini 1.5 Flash (Default)',
                                                        'gemini-1.5-pro' => 'Gemini 1.5 Pro',
                                                        'gemini-2.5-flash' => 'Gemini 2.5 Flash',
                                                    ];
                                                }

                                                return collect($models)->pluck('displayName', 'name')->toArray();
                                            })
                                            ->helperText('Select the AI model for automated content writing across the admin portal.')
                                            ->required(),
                                    ])->columns(2),
                                Section::make('Google reCAPTCHA')
                                    ->description('Enable Google reCAPTCHA on website forms to reduce spam. Leave keys blank to disable.')
                                    ->schema([
                                        Toggle::make('google_recaptcha_enabled')
                                            ->label('Enable Google reCAPTCHA')
                                            ->helperText('Requires a site key and secret key to validate form submissions.'),
                                        TextInput::make('google_recaptcha_site_key')
                                            ->label('reCAPTCHA Site Key')
                                            ->required(fn ($get) => (bool) $get('google_recaptcha_enabled'))
                                            ->helperText('Enter the Google reCAPTCHA site key.'),
                                        TextInput::make('google_recaptcha_secret_key')
                                            ->label('reCAPTCHA Secret Key')
                                            ->password()
                                            ->required(fn ($get) => (bool) $get('google_recaptcha_enabled'))
                                            ->helperText('Enter the Google reCAPTCHA secret key.'),
                                    ])
                                    ->columns(2),
                                Section::make('Google Analytics')
                                    ->description('Add your GA4 Measurement ID to measure website visits and e-commerce engagement.')
                                    ->schema([
                                        TextInput::make('google_analytics_id')
                                            ->label('GA4 Measurement ID')
                                            ->placeholder('G-XXXXXXXXXX')
                                            ->maxLength(20)
                                            ->rules(['nullable', 'regex:/^G-[A-Z0-9]+$/'])
                                            ->dehydrateStateUsing(fn (?string $state): string => strtoupper(trim((string) $state)))
                                            ->helperText('Create a GA4 web data stream and paste its Measurement ID. Leave blank to disable analytics.'),
                                    ]),
                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            Setting::set($key, $value);
        }

        Notification::make()
            ->title('Store settings saved successfully!')
            ->success()
            ->send();
    }
}
