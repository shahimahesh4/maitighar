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
                ->visible(fn (): bool => UserResource::canDelete($this->record)),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $currentUser = auth()->user();

        // Keep authorization-critical fields immutable for the current account,
        // even if a crafted request bypasses the disabled form controls.
        if ($currentUser?->getKey() === $this->record->getKey()) {
            $data['role'] = $this->record->role;
        } elseif ($currentUser?->role !== 'super_admin') {
            unset($data['role'], $data['admin_permissions']);
        }

        return $data;
    }
}
