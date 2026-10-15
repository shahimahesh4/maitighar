# AI Agent Guide for bhairahawa-beauty

## Purpose
This repository is a Laravel 12 e-commerce application with:
- Livewire 3 frontend pages
- Filament 3 admin panel at `stnapanel`
- Tailwind CSS / Vite frontend asset pipeline
- Driver-based payment gateway system with fallback routing
- Google Gemini AI integration for product copy and SEO

## Key files and locations
- `routes/web.php` - main public routes, auth routes, payment webhook/callback routes, and CMS page catch-all.
- `app/Livewire/` - frontend page components and auth pages.
- `resources/views/` - Blade views for frontend pages and Livewire components.
- `app/Filament/` - Filament admin pages, resources, widgets, and settings UI.
- `app/Providers/Filament/AdminPanelProvider.php` - Filament panel config and custom admin path `stnapanel`.
- `app/Services/Payment/PaymentGatewayManager.php` - payment driver resolution, fallback routing, health sync.
- `app/Services/Ai/GeminiService.php` - Google Gemini content generation.
- `app/Models/PaymentGateway.php` - DB-backed payment gateway configuration.
- `app/Models/Setting.php` - dynamic store settings used by Filament and services.

## Project conventions
- Admin panel is mounted at `http://127.0.0.1:8000/stnapanel`.
- Payment drivers are configured in the DB and resolved through `PaymentGatewayManager`.
- AI settings are stored in Filament Store Settings and used by `ProductResource` for description generation.
- The public route catch-all at the bottom of `routes/web.php` serves pages by slug and excludes reserved prefixes such as `stnapanel`, `livewire`, `api`, `storage`, `_ignition`, `_debugbar`.
- Filament resources and pages are auto-discovered from `app/Filament/Resources` and `app/Filament/Pages`.

## Local setup and commands
Use these commands to install and run the application locally:

1. `composer install`
2. `npm install`
3. copy `.env.example` to `.env`
4. `php artisan key:generate`
5. `php artisan migrate:fresh --seed`
6. `php artisan db:seed --class=PaymentGatewaySeeder`
7. `npm run dev`
8. `php artisan serve`

Useful commands:
- `php artisan test`
- `npm run build`
- `npm run dev`

## When editing features
- Confirm whether a change belongs in the frontend Livewire layer or the Filament admin layer.
- For payment gateway changes, update `app/Services/Payment/*` and review `app/Filament/Resources/PaymentGatewayResource.php`.
- For AI copy generation, inspect `app/Services/Ai/GeminiService.php`, `app/Filament/Pages/ManageSettings.php`, and `app/Filament/Resources/ProductResource.php`.
- Preserve the existing fallback payment routing behavior and the `stnapanel` Filament path.

## Useful documentation
- [`README.md`](README.md) - install instructions, feature overview, and AI/Payment architecture notes.

> Note: No existing `.github/copilot-instructions.md`, `AGENTS.md`, or agent customization file was present when this guide was created.
