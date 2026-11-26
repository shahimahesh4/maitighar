<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\OrdersRelationManager;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'User';

    protected static ?string $navigationLabel = 'Users & Roles';

    protected static ?string $modelLabel = 'User';

    protected static ?string $pluralModelLabel = 'Users & Roles';

    /**
     * User accounts and role assignment are reserved for Super Admins.
     * We override Filament's policy fallback as the general admin gate grants
     * normal administrators broad access to operational resources.
     */
    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'super_admin';
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canViewAny()
            && auth()->id() !== $record->getKey()
            // Preserve Super Admin accounts so an administrator cannot lock the
            // business out of the only role able to manage access.
            && $record->role !== 'super_admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),

                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->required(),

                TextInput::make('phone')
                    ->tel()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),

                Forms\Components\Select::make('role')
                    ->options(User::roleOptions())
                    ->default('customer')
                    ->required()
                    ->live()
                    ->disabled(fn (?User $record): bool => auth()->user()?->role !== 'super_admin'
                        || ($record !== null && auth()->id() === $record->getKey())
                    )
                    ->helperText(fn (?User $record): ?string => $record !== null && auth()->id() === $record->getKey()
                            ? 'Your own role is locked to prevent losing admin access.'
                            : null
                    ),

                Forms\Components\FileUpload::make('avatar')
                    ->label('Profile photo')
                    ->image()
                    ->directory('profile-photos')
                    ->imageEditor()
                    ->maxSize(2048),

                Forms\Components\DatePicker::make('date_of_birth')
                    ->label('Date of birth')
                    ->maxDate(now()->subDay()),

                Forms\Components\Select::make('gender')
                    ->options([
                        'female' => 'Female',
                        'male' => 'Male',
                        'other' => 'Other',
                        'prefer_not_to_say' => 'Prefer not to say',
                    ]),

                TextInput::make('address')
                    ->label('Street address')
                    ->maxLength(255)
                    ->columnSpanFull(),

                TextInput::make('city')->maxLength(100),
                TextInput::make('state')->label('Province / State')->maxLength(100),
                TextInput::make('postal_code')->label('Postal code')->maxLength(20),

                Forms\Components\Fieldset::make('Employment details')
                    ->schema([
                        TextInput::make('employee_id')->label('Employee ID')->unique(ignoreRecord: true)->maxLength(50),
                        TextInput::make('job_title')->label('Job title')->maxLength(100),
                        TextInput::make('department')->maxLength(100),
                        Forms\Components\DatePicker::make('joining_date')->label('Joining date')->maxDate(now()),
                        Forms\Components\Select::make('employment_type')->label('Employment type')->options([
                            'full_time' => 'Full time',
                            'part_time' => 'Part time',
                            'contract' => 'Contract',
                            'intern' => 'Intern',
                        ]),
                    ])
                    ->visible(fn (Forms\Get $get): bool => in_array($get('role'), ['super_admin', 'admin', 'trainer', 'staff'], true))
                    ->columns(2)
                    ->columnSpanFull(),

                Forms\Components\CheckboxList::make('admin_permissions')
                    ->label('Admin area access')
                    ->helperText('Select only the backend areas this staff member needs. Unselected areas are hidden and blocked.')
                    ->options(User::adminPermissionOptions())
                    ->columns(2)
                    ->bulkToggleable()
                    ->visible(fn (Forms\Get $get): bool => in_array($get('role'), ['trainer', 'staff'], true))
                    ->disabled(fn (): bool => auth()->user()?->role !== 'super_admin')
                    ->columnSpanFull(),

                Forms\Components\DateTimePicker::make('email_verified_at')
                    ->label('Email Verified at')
                    ->default(now()),

                TextInput::make('password')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn ($livewire) => $livewire instanceof CreateRecord),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => User::roleOptions()[$state] ?? ucfirst($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')->options(User::roleOptions()),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn (User $record): bool => static::canDelete($record))
                        ->requiresConfirmation(),
                ]),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            OrdersRelationManager::class,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
