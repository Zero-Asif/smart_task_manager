@props(['completed', 'pending', 'inProgress' => 0])

<div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg">
    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">Tasks Overview</h3>
    <div class="h-80">
        <canvas 
            id="taskOverviewChart"
            data-completed="{{ $completed ?? 0 }}"
            data-pending="{{ $pending ?? 0 }}"
            data-in-progress="{{ $inProgress ?? 0 }}"
        ></canvas>
    </div>
</div>

@push('scripts')
{{-- Chart.js একবার লোড করাই যথেষ্ট, তাই এখানে @once ব্যবহার করা হলো --}}
@once
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endonce

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const chartCanvas = document.getElementById('taskOverviewChart');
        if (!chartCanvas) return;

        const completedTasks = parseInt(chartCanvas.dataset.completed, 10);
        const pendingTasks = parseInt(chartCanvas.dataset.pending, 10);
        const inProgressTasks = parseInt(chartCanvas.dataset.inProgress, 10);

        let taskOverviewChart = null;

        const renderOverviewChart = () => {
            const isDarkMode = document.documentElement.classList.contains('dark');
            const colors = {
                textColor: isDarkMode ? '#e5e7eb' : '#374151',
                completedColor: isDarkMode ? 'rgba(34, 197, 94, 0.7)' : '#22c55e',
                inProgressColor: isDarkMode ? 'rgba(59, 130, 246, 0.7)' : '#3b82f6',
                pendingColor: isDarkMode ? 'rgba(234, 179, 8, 0.7)' : '#eab308',
                borderColor: isDarkMode ? '#1f2937' : '#ffffff',
            };

            const data = {
                labels: ['Completed', 'In Progress', 'Pending'],
                datasets: [{
                    label: 'Tasks',
                    data: [completedTasks, inProgressTasks, pendingTasks],
                    backgroundColor: [colors.completedColor, colors.inProgressColor, colors.pendingColor],
                    borderColor: colors.borderColor,
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            };

            const config = {
                type: 'doughnut',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: colors.textColor }
                        }
                    }
                }
            };
            
            if (taskOverviewChart) taskOverviewChart.destroy();
            // const ctx = document.getElementById('taskOverviewChart');
            // if (ctx) taskOverviewChart = new Chart(ctx.getContext('2d'), config);
            taskOverviewChart = new Chart(chartCanvas.getContext('2d'), config);
        };

        renderOverviewChart();
        window.addEventListener('themeChanged', renderOverviewChart);
    });
</script>
@endpush