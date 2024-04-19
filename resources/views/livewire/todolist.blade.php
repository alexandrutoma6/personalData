<?php

use Livewire\Volt\Component;
use App\Models\Todo;
use Illuminate\Support\Collection;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

new class extends Component implements HasForms, HasActions {
    use InteractsWithActions;
    use InteractsWithForms;

    public $todos;
    public $task = '';
    public $editedTask = '';
    public $pendingTasks = 0;
    public $user;

    public function mount()
    {
        $this->user = Auth::user();
        $this->fetchTodos();
    }

    private function fetchTodos()
    {
        $this->todos = Todo::byOwner($this->user->id)
            ->get()
            ->reverse()
            ->toArray();
        $this->pendingTasks = array_count_values(array_column($this->todos, 'status'))['pending'] ?? 0;
    }

    public function addTodo()
    {
        $newTask = trim($this->task);

        if ($newTask != '') {
            $todo = new Todo();
            $todo->task = $newTask;
            $this->user->todos()->save($todo);
            // $todo->save();
            $this->task = '';
        }
        $this->fetchTodos();
    }

    public function deleteTodo($todoId)
    {
        Todo::findOrFail($todoId)->delete();
        $this->fetchTodos();
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->color('critical')
            ->link()
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                Todo::findOrFail($arguments['todoId'])->delete();
                $this->fetchTodos();

                Notification::make()->title('Taks deleted successfully')->success()->send();
            });
    }
    public function toBoard()
    {
        return redirect('admin/todos-board');
    }
};
?>

<div class="border border-gray-200 rounded-lg ">

    @livewire('notifications')

    <div class="flex items-center px-5 py-4 border-b border-gray-200">
        <div class="flex space-x-3 flex-1">
            <x-filament::input class="flex-1 border-black rounded-lg w-96" type="text" wire:model="task"
                wire:keydown.enter="addTodo()" placeholder="Add a new task..." />
            <div class="px-3">
                <x-filament::button color="neutral" wire:click="addTodo()" icon="heroicon-m-plus-circle">
                    {{ __('Create new task') }}
                </x-filament::button>
            </div>
        </div>
    </div>

    <div>
        @if (count($todos) > 0)
            @if ($this->pendingTasks > 0)
                <x-filament::badge color="neutral">
                    {{ __('You have') }} <span
                        class="font-bold">{{ $this->pendingTasks }}</span> {{ __('pending tasks') }}
                </x-filament::badge>
            @else
                <x-filament::badge color="neutral">
                    {{ __('No pending tasks') }}
                </x-filament::badge>
            @endif
        @endif

        <div class="justify-center">
            @forelse($todos as $todo)
                <div class="text-center">
                    <div class="flex align-center items-center py-3 px-3">
 
                            <x-filament::button 
                                outlined
                                size="sm"
                                color="{{ $todo['status'] == 'done' ? 'danger' : 'green' }}"
                                tooltip="Click to delete"
                                wire:click="deleteTodo({{ $todo['id'] }})">
                                {{ $todo['task'] }} 
                            </x-filament::button>

                    </div>
                </div>
            @empty
                <x-filament::badge color="neutral">
                    {{ __('You have no tasks added yet.') }}
                </x-filament::badge>
            @endforelse
        </div>


    </div>
    <x-filament-actions::modals />
</div>
