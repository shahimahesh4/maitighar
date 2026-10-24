<?php

namespace App\Livewire;

use App\Models\Slider as SliderModel;
use Livewire\Component;

class Slider extends Component
{
    public ?string $location = null;
    public ?string $code = null;
    public ?int $sliderId = null;
    public bool $isPreview = false;

    public function mount(?string $location = null, ?string $code = null, ?SliderModel $slider = null, bool $isPreview = false): void
    {
        $this->location = $location ?? $code;
        $this->code = $this->location;
        $this->sliderId = $slider?->id;
        $this->isPreview = $isPreview;
    }

    public function render()
    {
        $query = SliderModel::query()
            ->when(!$this->isPreview, fn ($q) => $q->where('is_active', true))
            ->with(['items' => function ($query) {
                $query->when(!$this->isPreview, fn ($q) => $q->where('is_active', true))
                      ->orderBy('sort_order', 'asc');
            }]);

        $slider = null;
        if ($this->sliderId) {
            $slider = $query->find($this->sliderId);
        } elseif ($this->location) {
            $slider = $query->where('code', $this->location)->first();
        }

        $items = $slider ? $slider->items : collect();

        return view('livewire.slider', [
            'slider' => $slider,
            'items' => $items,
        ]);
    }
}
