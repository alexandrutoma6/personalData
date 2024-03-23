<?php

namespace App\Livewire;

use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Components;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Actions;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Services\GoogleCalendar;
use Filament\Widgets\Widget;

class CalendarWidget extends FullCalendarWidget
{
    // Define the model for where you want to make the CRUD
    public Model | string | null $model = Event::class;

    // This function will let you to add events be selecting multiple days and fill in the form the start and end date
    //this function will generate the 'New Event' button
    protected function headerActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mountUsing(
                    function (Forms\Form $form, array $arguments) {
                        $form->fill([
                            'starts_at' => $arguments['start'] ?? null,
                            'ends_at' => $arguments['end'] ?? null
                        ]);
                    }
                )
                ->mutateFormDataUsing(function (array $data): array {
                    $data['owner_user_id'] = auth()->id();
                    return $data;
                })
                ->after(function (array $data, Event $event,  GoogleCalendar $googleService): void {
                    if (auth()->user()->isLoggedInGoogle) {
                        if ($data['synced_google']) {
                            $googleEventID = $googleService->addEventToGoogleCalendar($data);
                            $data['google_calendar_id'] = $googleEventID;
                            $event->update($data);
                        }
                    }
                    //using the ->after() method the fetchEvents() will not be called, so in order to refresh the calendar event, use the refreshRecords()
                    $this->refreshRecords();
                })
        ];
    }

    // This function make the event Editable and opens a popup when you drag and drop an event.
    protected function modalActions(): array
    {
        return [
            Actions\EditAction::make()
                ->mountUsing(
                    function (Event $record, Forms\Form $form, array $arguments) {
                        $form->fill([
                            'title' => $record->title,
                            'description' => $record->description,
                            'location' => $record->location,
                            'link' => $record->link,
                            'is_recurrent' => $record->is_recurrent,
                            'recurrence' => $record->recurrence,
                            'all_day' => $record->all_day,
                            'synced_google' => $record->synced_google,
                            'starts_at' => $arguments['event']['start'] ?? $record->starts_at,
                            'ends_at' => $arguments['event']['end'] ?? $record->ends_at,
                        ]);
                    }
                )
                ->mutateFormDataUsing(function (array $data, Event $event): array {
                    $data['recurrence'] = $data['is_recurrent'] ? $data['recurrence'] : NULL;
                    $data['google_calendar_id'] = $event->google_calendar_id;
                    return $data;
                })
                ->after(function (array $data, Event $event, GoogleCalendar $googleService) {
                    if (auth()->user()->isLoggedInGoogle) {
                        if ($data['synced_google']) {
                            $googleEventID = $googleService->updateEventInGoolgeCalendar($data);
                            //if the event was created but not synced with the google calendar, the google_calendar_id needs to be updated
                            if ($data['google_calendar_id'] === null) {
                                $data['google_calendar_id'] = $googleEventID;
                                //update the event in the database with the new google_calendar_id
                                $event->update($data);
                            }
                        }
                    }

                    $this->refreshRecords();
                })
                ,
            Actions\DeleteAction::make()
                ->after(function (Event $record, GoogleCalendar $googleService) {
                    if (auth()->user()->isLoggedInGoogle) {
                        if ($record->synced_google) {
                            //delete the event from the google calendar
                            $googleService->deleteEventFromGoolgeCalendar($record->google_calendar_id);
                        }
                    }
                    $this->refreshRecords();
                }),
        ];
    }

    // With this function you define the fields for creating new events.
    public function getFormSchema(): array
    {
        return [
            Components\TextInput::make('title')
                ->required(),
            Components\Textarea::make('description'),
            Components\Grid::make()
                ->schema([
                    Components\TextInput::make('location'),
                    Components\TextInput::make('link'),

                ]),
            Components\Grid::make()
                ->schema([
                    Components\DateTimePicker::make('starts_at')
                        ->required(),
                    Components\DateTimePicker::make('ends_at')
                        ->required(),

                ]),
            Components\Checkbox::make('synced_google')
                //disable the option to sync with google if you are not connected to a google account
                ->disabled(fn () => !(auth()->user()->isLoggedInGoogle))
                //hint for the user to connect his google account
                ->hint('Login into Google Calendar to sync events.')
                ->inline()
                ->reactive()
                ->label('Synced with Google Calendar'),
            Components\Checkbox::make('all_day')
                ->inline()
                ->reactive(),
            Components\Checkbox::make('is_recurrent')
                ->inline()
                ->reactive()
                ->label('Recurrent Event'),
            Components\Section::make('Recurrence')
                ->hidden(fn ($get) => $get('is_recurrent') === false)
                ->statePath('recurrence')
                ->schema([
                    Components\Grid::make()
                        ->schema([
                            Components\Radio::make('recurrence')
                                ->options(['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'])
                                ->required(true)
                                ->default(null),
                            Components\CheckboxList::make('days_of_week')
                                ->options([
                                    '0' => 'Monday',
                                    '1' => 'Tuesday',
                                    '2' => 'Wednesday',
                                    '3' => 'Thursday',
                                    '4' => 'Friday',
                                    '5' => 'Saturday',
                                    '6' => 'Sunday',
                                ])
                                ->hidden()
                                ->default(null),
                        ])
                ])
                ->default(null),
        ];
    }

    // Fetch events from your Model and show in the Calendar
    public function fetchEvents(array $fetchInfo): array
    {
        return Event::query()
            ->where(function ($query) use ($fetchInfo) {
                $query->where('starts_at', '>=', $fetchInfo['start'])
                    ->where('ends_at', '>=', $fetchInfo['start'])
                    ->where('owner_user_id', auth()->id());
            })
            ->orWhere(function ($query) use ($fetchInfo) {
                $query->where('is_recurrent', 1); // Check if the event is recurrent
            })
            ->get()
            ->map(
                fn (Event $event) => [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->starts_at,
                    'end' => $event->ends_at,
                    'rrule' =>
                    $event->recurrence
                        ?
                        [
                            'freq' => $event->recurrence['recurrence'],
                            'byweekday' => $event->recurrence['days_of_week'] ? array_map('intval',  $event->recurrence['days_of_week']) : '',
                            'dtstart' => $event->starts_at
                        ]
                        : null,
                    'allDay' => $event->all_day,
                ]
            )
            ->all();
    }
}
