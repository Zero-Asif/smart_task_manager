@props(['labels', 'data'])

<div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg">
    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">Weekly Completion</h3>
    <div class="h-80">
        <canvas id="weeklyTaskChart"></canvas>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const chartLabels = JSON.parse('{!! $labels !!}');
        const chartData = JSON.parse('{!! $data !!}');
        let weeklyChart = null;

        const renderOrUpdateChart = () => {
            const isDarkMode = document.documentElement.classList.contains('dark');
            const colors = {
                barColor: isDarkMode ? 'rgba(79, 70, 229, 0.7)' : 'rgba(99, 102, 241, 0.8)',
                gridColor: isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)',
                textColor: isDarkMode ? '#e5e7eb' : '#374151',
            };

            const config = {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Tasks Completed',
                        data: chartData,
                        backgroundColor: colors.barColor,
                        borderColor: colors.barColor,
                        borderWidth: 1,
                        borderRadius: 5,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { ticks: { color: colors.textColor, precision: 0 }, grid: { color: colors.gridColor } },
                        x: { ticks: { color: colors.textColor }, grid: { display: false } }
                    }
                }
            };

            if (weeklyChart) weeklyChart.destroy();
            const ctx = document.getElementById('weeklyTaskChart');
            if (ctx) weeklyChart = new Chart(ctx.getContext('2d'), config);
        };

        renderOrUpdateChart();
        window.addEventListener('themeChanged', renderOrUpdateChart);
    });
</script>
@endpush