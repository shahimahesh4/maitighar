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
            'services_hero_eyebrow' => Setting::get('services_hero_eyebrow', 'Maitighar Studio'),
            'services_hero_title' => Setting::get('services_hero_title', 'Heritage & Services,'),
            'services_hero_accent' => Setting::get('services_hero_accent', 'made personal.'),
            'services_hero_intro' => Setting::get('services_hero_intro', 'From authentic Nepali heritage creations to personalized services, Maitighar brings you tradition and elegance with heartfelt care.'),
            'services_primary_cta_label' => Setting::get('services_primary_cta_label', 'Book an appointment'),
            'services_catalog_eyebrow' => Setting::get('services_catalog_eyebrow', 'Our offerings'),
            'services_catalog_title' => Setting::get('services_catalog_title', 'Find your perfect service & product'),
            'services_catalog_intro' => Setting::get('services_catalog_intro', 'Choose an option to see details, who it is perfect for, and how to order or book.'),
            'services_promise_eyebrow' => Setting::get('services_promise_eyebrow', 'The Maitighar Experience'),
            'services_promise_title' => Setting::get('services_promise_title', 'Authentic craft. Thoughtful care.'),
            'services_promises' => Setting::get('services_promises', [
                ['title' => 'Personal consultation', 'description' => 'We begin by understanding your requirements, needs, and desired result.'],
                ['title' => 'Trained professionals', 'description' => 'Experienced artisans and artists deliver considered, technique-led service.'],
                ['title' => 'Hygiene & Quality first', 'description' => 'Clean practices, authentic Nepali quality, and a comfortable experience.'],
            ]),
            'services_cta_eyebrow' => Setting::get('services_cta_eyebrow', 'Ready when you are'),
            'services_cta_title' => Setting::get('services_cta_title', 'Experience Nepalese Heritage with Love.'),
            'services_cta_text' => Setting::get('services_cta_text', 'Talk with our team about your questions, orders, and appointments.'),
            'services_cta_label' => Setting::get('services_cta_label', 'Contact Maitighar'),
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

                        Tabs\Tab::make('Services Page')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                Section::make('Hero & Catalog')
                                    ->description('Content shown above the dynamic service cards.')
                                    ->schema([
                                        TextInput::make('services_hero_eyebrow')->label('Hero eyebrow')->required(),
                                        TextInput::make('services_hero_title')->label('Hero title')->required(),
                                        TextInput::make('services_hero_accent')->label('Hero accent text')->required(),
                                        Textarea::make('services_hero_intro')->label('Hero introduction')->rows(3)->required()->columnSpanFull(),
                                        TextInput::make('services_primary_cta_label')->label('Primary button label')->required(),
                                        TextInput::make('services_catalog_eyebrow')->label('Catalog eyebrow')->required(),
                                        TextInput::make('services_catalog_title')->label('Catalog heading')->required(),
                                        Textarea::make('services_catalog_intro')->label('Catalog introduction')->rows(2)->required()->columnSpanFull(),
                                    ])->columns(2),
                                Section::make('Experience Promises')
                                    ->schema([
                                        TextInput::make('services_promise_eyebrow')->label('Section eyebrow')->required(),
                                        TextInput::make('services_promise_title')->label('Section heading')->required(),
                                        Repeater::make('services_promises')
                                            ->label('Promise cards')
                                            ->schema([
                                                TextInput::make('title')->required(),
                                                Textarea::make('description')->rows(2)->required(),
                                            ])
                                            ->minItems(1)
                                            ->maxItems(6)
                                            ->reorderable()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                            ->columnSpanFull(),
                                    ])->columns(2),
                                Section::make('Booking Call to Action')
                                    ->schema([
                                        TextInput::make('services_cta_eyebrow')->label('Eyebrow')->required(),
                                        TextInput::make('services_cta_title')->label('Heading')->required(),
                                        Textarea::make('services_cta_text')->label('Supporting text')->rows(2)->required()->columnSpanFull(),
                                        TextInput::make('services_cta_label')->label('Button label')->required(),
                                    ])->columns(2),
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
