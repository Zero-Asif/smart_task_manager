<div x-data="{ title: '', loading: false, successMessage: '' }" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg">
    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">Add a New Task Quickly</h3>
    <form @submit.prevent="
        if (title.trim() === '') return;
        loading = true;
        fetch('{{ route('tasks.quickStore') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ title: title })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                successMessage = data.message;
                title = '';
                setTimeout(() => successMessage = '', 3000);
                setTimeout(() => location.reload(), 1000); 
            } else { alert(data.message || 'Could not add task.'); }
            loading = false;
        })
        .catch(() => { loading = false; alert('Something went wrong.'); });
    ">
        <div class="flex items-center gap-4">
            <input type="text" x-model="title" placeholder="What needs to be done?" class="flex-grow border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 rounded-md shadow-sm" required>
            <button type="submit" :disabled="loading" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase hover:bg-gray-700 dark:hover:bg-white disabled:opacity-50 transition">
                <span x-show="!loading">Add</span>
                <span x-show="loading">Adding...</span>
            </button>
        </div>
        <template x-if="successMessage">
            <p x-text="successMessage" class="text-sm text-green-600 dark:text-green-400 mt-2"></p>
        </template>
    </form>
</div>