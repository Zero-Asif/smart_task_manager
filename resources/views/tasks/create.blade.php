<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create a New Task') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">

                    {{-- Alpine.js কম্পোনেন্ট শুরু: AI ফিচার ম্যানেজ করার জন্য --}}
                    <form 
                        action="{{ route('tasks.store') }}" 
                        method="POST"
                        x-data="{
                            title: '{{ old('title', '') }}',
                            description: '{{ old('description', '') }}',
                            subtasks: {{ old('subtasks') ? json_encode(old('subtasks')) : '[]' }},
                            loading: false,
                            error: '',
                            
                            async generateSubtasks() {
                                if (!this.title.trim()) {
                                    this.error = 'Please enter a task title first.';
                                    return;
                                }
                                this.loading = true;
                                this.error = '';
                                
                                try {
                                    const response = await fetch('{{ route('tasks.generateSubtasks') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            title: this.title,
                                            description: this.description
                                        })
                                    });

                                    if (!response.ok) {
                                        const errorData = await response.json();
                                        throw new Error(errorData.error || 'Something went wrong');
                                    }

                                    const data = await response.json();
                                    this.subtasks = data;

                                } catch (err) {
                                    this.error = err.message;
                                } finally {
                                    this.loading = false;
                                }
                            },

                            addSubtask() {
                                this.subtasks.push('');
                                this.$nextTick(() => { this.$refs.subtasksContainer.lastElementChild.querySelector('input').focus(); });
                            },

                            removeSubtask(index) {
                                this.subtasks.splice(index, 1);
                            }
                        }"
                    >
                        @csrf
                        <div class="space-y-6">
                            {{-- Title --}}
                            <div>
                                <label for="title" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Title</label>
                                <input id="title" name="title" type="text" x-model="title" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 rounded-md shadow-sm" required>
                            </div>

                            {{-- Description --}}
                            <div>
                                <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Description (Optional)</label>
                                <textarea id="description" name="description" rows="3" x-model="description" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 rounded-md shadow-sm"></textarea>
                            </div>

                            {{-- Due Date & Priority --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="due_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Due Date</label>
                                    <input id="due_date" name="due_date" type="datetime-local" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 rounded-md shadow-sm" required>
                                </div>
                                <div>
                                    <label for="priority" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Priority</label>
                                    <select id="priority" name="priority" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 rounded-md shadow-sm" required>
                                        <option value="low">Low</option>
                                        <option value="medium" selected>Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                            </div>
                            
                            {{-- AI Assist Section --}}
                            <div class="border-t dark:border-gray-700 pt-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Breakdown into Sub-tasks</h3>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Let AI help you break this large task into smaller, manageable steps.</p>
                                    </div>
                                    <button type="button" @click="generateSubtasks()" :disabled="loading" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        <span x-show="!loading" class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                                            AI Assist ✨
                                        </span>
                                        <span x-show="loading">Generating...</span>
                                    </button>
                                </div>
                                <div x-show="error" x-text="error" class="text-red-500 text-sm mt-2"></div>

                                {{-- Generated Sub-tasks List --}}
                                <div class="mt-4 space-y-2" x-show="subtasks.length > 0" x-ref="subtasksContainer">
                                    <template x-for="(subtask, index) in subtasks" :key="index">
                                        <div class="flex items-center gap-2">
                                            <!-- <input type="text" :name="'subtasks[' + index + ']'" x-model="subtasks[index]" class="flex-grow border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 rounded-md shadow-sm"> -->
                                            <input type="text" :name="'subtasks[]'" x-model="subtasks[index]" class="flex-grow border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" placeholder="Sub-task title">
                                            <button type="button" @click="removeSubtask(index)" class="p-1 text-gray-400 hover:text-red-500">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                                <button type="button" @click="addSubtask()" class="mt-2 text-sm text-indigo-600 dark:text-indigo-400 hover:underline" x-show="subtasks.length > 0">
                                    + Add another sub-task
                                </button>
                            </div>

                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center justify-end mt-8 pt-6 border-t dark:border-gray-700 gap-4">
                            <a href="{{ route('tasks.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Cancel</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Create Task
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
