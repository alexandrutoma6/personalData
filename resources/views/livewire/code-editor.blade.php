<?php

use Illuminate\Support\Facades\Http;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Wiebenieuwenhuis\FilamentCodeEditor\Components\CodeEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Group;
use Livewire\Volt\Component;

//https://www.jdoodle.com/subscribe-api the API compiler
new class extends Component implements HasForms, HasActions {
    use InteractsWithActions;
    use InteractsWithForms;

    public ?array $data = [];
    public $output;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->columns(2)
                    ->schema([
                        Select::make('programming_language')
                            ->options([
                                'python3' => 'Python',
                                'java' => 'Java',
                                'cpp' => 'C++',
                                'csharp' => 'C#',
                                'go' => 'GO',
                            ])
                            ->default('python3')
                            ->selectablePlaceholder(false)
                            ->native(false),
                    ]),
                CodeEditor::make('code_editor'),
            ])
            ->statePath('data');
    }

    //action button to trigger the api request
    public function compileAction(): Action
    {
        return Action::make('compile')->color('secondary')->action(fn() => $this->compile());
    }

    //use data from the config file and form the form data
    protected function compile()
    {
        $response = Http::post(config('compiler-api.url'), [
            'clientId' => config('compiler-api.client_id'),
            'clientSecret' => config('compiler-api.client_secret'),
            'script' => $this->data['code_editor'],
            'language' => $this->data['programming_language'],
        ])->json();
        return $this->output = $response['output'];
    }

    public function handleNameError(String $string)
    {
        return substr($string, 9);
    }

}; ?>

<div class="px-2">
    <div class="py-4 ">
        {{ $this->form }}
    </div>

    <div class="pt-2 pb-6 ">
        {{ $this->compileAction() }}
    </div>
    @if (isset($this->output))
        @if (str_contains($this->output, 'NameError'))
             @php
                $position = strpos($this->output, 'NameError');
                $substring = substr($this->output, $position + strlen('NameError:'));
                $substring = ucfirst($substring);
            @endphp

            <div class="italic font-bold border border-gray-200 rounded-lg p-2 bg-gray-100">
                {{ $substring }}
            </div>
        @else
            <div class="font-bold border border-gray-200 rounded-lg p-2 bg-gray-100">
                <p class="p-2 font-bold"> {{ $this->output }} </p>
            </div>
        @endif
    @endif
</div>
