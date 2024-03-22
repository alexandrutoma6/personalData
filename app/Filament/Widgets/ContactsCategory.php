<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Contact;

class ContactsCategory extends ChartWidget
{
    protected static ?string $heading = 'Contacts Categories';
    protected static ?string $maxHeight = '275px';
    protected static ?int $sort = 2;


    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => true,
                'position' => 'top'
            ],
        ],
        'scales' => [
            'y' => [

                'display' => false
            ],
            'x' => [

                'display' => false
            ]
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
                    'label' => 'Contacts Categories',
                    'data' => [$this->countWorkContacts(), $this->countSchoolContacts(), $this->countFamilyContacts(), $this->countFriendsContacts(), $this->countOtherContacts()],
                    'backgroundColor' => ['#FF6384','#36A2EB','#FFCE56','#4BC0C0','#C779D0'],
                ],
            ],
            'labels' => ['Work', 'School', 'Family', 'Friends', 'Other'],
        ];
    }
    protected function countWorkContacts(): int
    {
        return Contact::where('category', 'work')->where('owner_user_id', auth()->id())->count();
    }
    protected function countSchoolContacts(): int
    {
        return Contact::where('category', 'school')->where('owner_user_id', auth()->id())->count();
    }
    protected function countFamilyContacts(): int
    {
        return Contact::where('category', 'family')->where('owner_user_id', auth()->id())->count();
    }
    protected function countFriendsContacts(): int
    {
        return Contact::where('category', 'friends')->where('owner_user_id', auth()->id())->count();
    }
    protected function countOtherContacts(): int
    {
        return Contact::whereNull('category')->where('owner_user_id', auth()->id())->count();
    }


    protected function getType(): string
    {
        return 'pie';
    }
}
