<?php

namespace App\Support;

final class OrderPresentation
{
    public static function statusClass(string $status): string
    {
        return match ($status) {
            'new' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            'processing' => 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-400',
            'shipped' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
            'delivered' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
            'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            default => 'bg-slate-100 text-slate-800',
        };
    }

    public static function paymentStatusClass(string $status): string
    {
        return match ($status) {
            'pending' => 'bg-blue-50 text-blue-800 dark:bg-blue-950/40 dark:text-blue-300',
            'paid' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
            'failed' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            default => 'bg-slate-100 text-slate-800',
        };
    }

    public static function trackingSteps(string $currentStatus): array
    {
        $steps = [
            'new' => ['label' => 'Order Placed', 'icon' => 'bi-receipt'],
            'processing' => ['label' => 'Processing', 'icon' => 'bi-gear'],
            'shipped' => ['label' => 'Shipped', 'icon' => 'bi-truck'],
            'delivered' => ['label' => 'Delivered', 'icon' => 'bi-check2-circle'],
        ];
        $statusOrder = array_keys($steps);
        $currentIndex = array_search($currentStatus, $statusOrder, true);
        $currentIndex = $currentIndex === false ? 0 : $currentIndex;

        return collect($steps)
            ->map(function (array $step, string $key) use ($statusOrder, $currentIndex): array {
                $index = array_search($key, $statusOrder, true);
                $isCompleted = $index < $currentIndex;
                $isCurrent = $index === $currentIndex;

                return [
                    ...$step,
                    'completed' => $isCompleted,
                    'circle_class' => $isCompleted
                        ? 'bg-[#0C1E8C] border-[#0C1E8C] text-white'
                        : ($isCurrent
                            ? 'bg-blue-50 border-[#0C1E8C] text-[#0C1E8C] dark:bg-blue-950/40 dark:text-blue-400'
                            : 'bg-white dark:bg-slate-900 border-gray-200 text-gray-400 dark:border-slate-800'),
                    'label_class' => $isCompleted || $isCurrent
                        ? 'text-[#0C1E8C] dark:text-blue-400 font-bold'
                        : 'text-gray-400 dark:text-slate-500 font-medium',
                ];
            })
            ->values()
            ->all();
    }
}
