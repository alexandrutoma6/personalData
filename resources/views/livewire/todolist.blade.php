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
    public $editingTodoId;
    public $pendingTasks = 0;

    public function mount()
    {
        $this->fetchTodos();
    }

    private function fetchTodos()
    {
        $this->todos = Todo::all()->reverse()->toArray();
        $this->pendingTasks = array_count_values(array_column($this->todos, 'status'))['pending'] ?? 0;
    }

    public function addTodo()
    {
        $newTask = trim($this->task);

        if ($newTask != '') {
            $todo = new Todo();
            $todo->task = $newTask;
            // $this->user->todos()->save($todo);
            $todo->save();
            $this->task = '';
        }
        $this->fetchTodos();
    }

    public function editTodo($todoId)
    {
        $this->editingTodoId = $todoId;
        $this->editedTask = Todo::findOrFail($todoId)->task;
        $this->fetchTodos();
    }

    public function saveEdit($todoId)
    {
        $newTask = trim($this->editedTask);

        if ($newTask != '') {
            $todo = Todo::findOrFail($todoId);
            $todo->task = $newTask;
            $todo->save();
        }
        $this->editingTodoId = null;
        $this->fetchTodos();
    }

    public function cancelEdit()
    {
        $this->editingTodoId = null;
        $this->editedTask = '';
        $this->fetchTodos();
    }

    public function toggleStatus(Todo $todo)
    {
        $todo->status = $todo->status == 'pending' ? 'done' : 'pending';
        $todo->save();
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

<div class="border border-gray-200 rounded-lg">

    @livewire('notifications')

    <div class="flex items-center px-5 py-4 border-b border-gray-200">
        <div class="flex space-x-3 flex-1"> 
            <x-filament::input class="flex-1 border-black rounded-lg w-96" type="text" wire:model="task"
                wire:keydown.enter="addTodo()" placeholder="Add a new task..." />
            <x-filament::button class="gap-2" wire:click="addTodo()" icon="heroicon-m-plus-circle">
                {{ __('Create new task') }}
            </x-filament::button>
            <div class="p-2"></div>
            <x-filament::button class="" wire:click="toBoard" icon="heroicon-o-clipboard-document-check">
                {{ __('To board') }}
            </x-filament::button>
        </div>
    </div>
        
    <div class="ml-5">
        @if (count($todos) > 0)
            @if ($this->pendingTasks > 0)
                <p class="mb-2 text-sm text-orange"> {{ __('You have') }} <span
                        class="font-bold">{{ $this->pendingTasks }}</span> {{ __('pending tasks') }}</p>
            @else
                <p class="ml-2 mb-2 text-sm text-neutral"> {{ __('No pending tasks') }}</p>
            @endif
        @endif

        <div class="justify-center">
            @forelse($todos as $todo)
                <div class="text-center">
                    <div class="flex align-center items-center py-3 px-3">
                        <div>
                            <button title="Click to delete" wire:click="deleteTodo({{ $todo['id'] }})"
                                for="toggleStatus-{{ $todo['id'] }}"
                                class="ml-2 {{ $todo['status'] == 'done' ? 'text-gray-400' : '' }}">
                                {{ $todo['task'] }}
                            </button>

                        </div>
                    </div>
                </div>
            @empty
                <p class="text-neutral text-sm">{{ __('You have no tasks added yet.') }}</p>
            @endforelse
        </div>


    </div>
    <x-filament-actions::modals />
</div>