<?php

namespace App\Filament\Resources\SliderResource\Pages;

use App\Filament\Resources\SliderResource;
use App\Models\Slider;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\MaxWidth;

class EditSlider extends EditRecord
{
    protected static string $resource = SliderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview')
                ->label('Live Preview')
                ->icon('heroicon-o-device-phone-mobile')
                ->color('warning')
                ->modalHeading(fn (Slider $record): string => "Live Preview: {$record->name}")
                ->modalWidth(MaxWidth::SevenExtraLarge)
                ->modalContent(fn (Slider $record) => view('filament.slider-preview-modal', ['slider' => $record]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close Preview'),

            Actions\DeleteAction::make(),
        ];
    }
}
