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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'User';

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

                Forms\Components\Select::make('trainer_id')
                    ->label('Assigned trainer')
                    ->options(fn () => User::trainers()->orderBy('name')->pluck('name', 'id'))
                    ->placeholder('No trainer assigned')
                    ->helperText('Only users with the Trainer role appear here.')
                    ->visible(fn (Forms\Get $get): bool => $get('role') === 'student')
                    ->disabled(fn (?User $record): bool => auth()->user()?->role !== 'super_admin'
                        || ($record !== null && auth()->id() === $record->getKey())
                    )
                    ->nullable(),

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

                Forms\Components\Fieldset::make('Emergency contact')
                    ->schema([
                        TextInput::make('emergency_contact_name')->label('Name')->maxLength(255),
                        TextInput::make('emergency_contact_relationship')->label('Relationship')->maxLength(60),
                        TextInput::make('emergency_contact_phone')->label('Phone')->tel()->maxLength(30),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Forms\Components\Fieldset::make('Student details')
                    ->schema([
                        TextInput::make('guardian_name')->label('Guardian name')->maxLength(255),
                        TextInput::make('guardian_phone')->label('Guardian phone')->tel()->maxLength(30),
                        TextInput::make('education_level')->label('Education level')->maxLength(100),
                        TextInput::make('institution')->label('School / Institution')->maxLength(255),
                    ])
                    ->visible(fn (Forms\Get $get): bool => $get('role') === 'student')
                    ->columns(2)
                    ->columnSpanFull(),

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
                Tables\Columns\TextColumn::make('trainer.name')
                    ->label('Assigned Trainer')
                    ->placeholder('—')
                    ->toggleable(),
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
                        ->visible(fn (User $record): bool => auth()->user()?->role === 'super_admin'
                            && auth()->id() !== $record->getKey()
                        )
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
