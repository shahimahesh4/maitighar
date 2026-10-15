<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn (): bool => auth()->user()?->role === 'super_admin'
                    && auth()->id() !== $this->record->getKey()),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $currentUser = auth()->user();

        // Keep authorization-critical fields immutable for the current account,
        // even if a crafted request bypasses the disabled form controls.
        if ($currentUser?->getKey() === $this->record->getKey()) {
            $data['role'] = $this->record->role;
            $data['trainer_id'] = $this->record->trainer_id;
        } elseif ($currentUser?->role !== 'super_admin') {
            unset($data['role'], $data['trainer_id'], $data['admin_permissions']);
        }

        return $data;
    }
}
