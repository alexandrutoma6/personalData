<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class TodosReport extends ChartWidget
{
    protected static ?string $heading = 'Todos Report';
    protected static ?int $sort = 2;

    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => true,
            ],
        ],
        'animation' => [
            'duration' =>  1400,
            'easing' => 'linear',
            'delay' => 250
        ],
    ];

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Todos',
                    'data' => [$this->countPendingTodos(), $this->countDoneTodos()],
                    'backgroundColor' => ['#BFDBFE', '#4BC0C0'],
                    'borderColor' => ['#BFDBFE', '#4BC0C0'],
                ],
            ],
                'labels' => ['Pending', 'Done'],
        ];
    }

    protected function countPendingTodos(): int
    {
        return \App\Models\Todo::where('status', 'pending')->count();
    }
    protected function countDoneTodos(): int
    {
        return \App\Models\Todo::where('status', 'done')->count();
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
