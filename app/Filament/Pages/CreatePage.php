<?php

namespace App\Filament\Pages;

use App\Filament\Resources\PageResource;
use App\Models\Page as PageModel;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;

class CreatePage extends Page
{
    public static function canAccess(): bool
    {
        return auth()->user()?->hasAdminPermission('content') ?? false;
    }

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';

    protected static string $view = 'filament.pages.create-page';

    protected static ?string $navigationLabel = 'Create Page';

    protected static ?string $title = 'Create Page';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationGroup = 'Pages';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'status' => 'draft',
            'sort_order' => 0,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Page Details')
                    ->description('Create a new static page for the storefront.')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, $set): void {
                                if (blank($state)) {
                                    return;
                                }

                                $set('slug', Str::slug($state));
                            }),

                        TextInput::make('slug')
                            ->maxLength(255)
                            ->helperText('Leave blank to auto-generate from the title.'),

                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                            ])
                            ->default('draft')
                            ->required(),

                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),

                Section::make('Content')
                    ->schema([

                        RichEditor::make('content')
                            ->label('Content')
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ]),
                    ]),

                Section::make('SEO')
                    ->schema([
                        TextInput::make('meta_title')
                            ->maxLength(255),
                        Textarea::make('meta_description')
                            ->rows(3),
                        Textarea::make('meta_keywords')
                            ->rows(2),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label('Create Page')
                ->submit('create'),
        ];
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $page = PageModel::create([
            'title' => $data['title'] ?? null,
            'slug' => $data['slug'] ?? null,
            'content' => $data['content'] ?? null,
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'meta_keywords' => $data['meta_keywords'] ?? null,
            'status' => $data['status'] ?? 'draft',
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);

        Notification::make()
            ->title('Page created successfully')
            ->success()
            ->send();

        $this->redirect(PageResource::getUrl('edit', ['record' => $page]));
    }
}
