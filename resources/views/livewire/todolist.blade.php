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

        if ($newTask != '')
        {
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

        if ($newTask != '')
        {
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

                Notification::make()
                ->title('Taks deleted successfully')
                ->success()
                ->send();
            });
    }

    public function deleteDoneTasks()
    {
        Todo::where('status', 'done')->delete();
        $this->fetchTodos();

        Notification::make()
            ->title('Done tasks deleted successfully')
            ->success()
            ->send();
    }

    public function deleteDoneTasksAction(): Action
    {
        return Action::make('deleteDoneTasks')
            ->color('critical')
            ->icon('heroicon-m-minus-circle')
            ->requiresConfirmation()
            ->action(fn() => $this->deleteDoneTasks());
    }
}; ?>

<x-slot name="componentHeader">
    {{ __('Todos') }}
</x-slot>


<div>
    @livewire('notifications')
    <div >
        <div >
            <span class="font-bold">{{ __('My Tasks') }}:</span>
            <x-filament::input type="text" wire:model="task"
            wire:keydown.enter="addTodo()" placeholder="Add a new task..."></x-filament::input>
            <x-filament::button wire:click="addTodo()" icon="heroicon-m-plus-circle">
                {{ __('Create new task') }}
            </x-filament::button>
        </div>
        <div>
            @if(collect($todos)->pluck('status')->contains('done'))
                {{ $this->deleteDoneTasksAction }}
            @endif
        </div>
    </div>
    <div class="p-5">
        @if(count($todos) > 0)
            @if ($this->pendingTasks > 0)
                <p class="mb-2"> {{ __('You have') }} <span class="font-bold">{{ $this->pendingTasks }}</span> {{ __('pending tasks') }}</p>
            @else
                <p class="mb-2"> {{ __('No pending tasks') }}</p>
            @endif
        @endif

        @forelse($todos as $todo)
            <div >
                <!-- CheckBox for marking the task done / pending -->
                <input type="checkbox" id="toggleStatus-{{ $todo['id'] }}"
                    wire:change="toggleStatus({{ $todo['id'] }})"
                    {{ $todo['status'] === 'done' ? 'checked' : '' }}>

                @if ($editingTodoId === $todo['id'])
                    <div class="pr-3">
                        <!-- Display input field for editing -->
                        <input type="text" wire:model="editedTask" wire:keydown.enter="saveEdit({{ $todo['id'] }})">
                    </div>
                    <div class="space-x-3">
                        <!-- Save and Cancel buttons -->
                        <button
                            wire:click="saveEdit({{ $todo['id'] }})">{{ __('Save') }}</button>
                        <button wire:click="cancelEdit">{{ __('Cancel') }}</button>
                    </div>
                @else
                    <div class="flex-1">
                        <!-- Line on the task's name -->
                        <label for="toggleStatus-{{ $todo['id'] }}"
                            class="ml-2 {{ $todo['status'] == 'done' ? 'line-through text-gray-500' : '' }}">
                            {{ $todo['task'] }}
                        </label>
                    </div>
                    <div >
                        <!-- Edit Button -->
                        <button
                            wire:click="editTodo({{ $todo['id'] }})">{{ __('Edit') }}</button>
                        <!-- Delete Button -->
                        {{ ($this->deleteAction)(['todoId' => $todo['id']]) }}
                    </div>
                @endif
            </div>
        @empty
            <p>{{ __('You have no tasks added yet.') }}</p>
        @endforelse
    </div>
    <x-filament-actions::modals/>
</div>
