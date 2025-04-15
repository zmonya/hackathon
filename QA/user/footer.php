    <script>
        // Pass PHP data to JavaScript
        const officeLabels = <?php echo json_encode($office_labels); ?>;
        const officeRatings = <?php echo json_encode($office_ratings); ?>;
        const ratingLabels = <?php echo json_encode($rating_labels); ?>;
        const ratingCounts = <?php echo json_encode($rating_counts); ?>;
        const ratingColors = <?php echo json_encode(array_values($rating_colors)); ?>;

        // Initialize Charts
        document.addEventListener('DOMContentLoaded', () => {
            // Satisfaction Chart
            const satisfactionCtx = document.getElementById('satisfactionChart').getContext('2d');
            new Chart(satisfactionCtx, {
                type: 'bar',
                data: {
                    labels: officeLabels.length ? officeLabels : ['No Data'],
                    datasets: [{
                        label: 'Average Rating',
                        data: officeRatings.length ? officeRatings : [0],
                        backgroundColor: 'rgba(26, 60, 94, 0.7)',
                        borderColor: 'rgba(26, 60, 94, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true, max: 5 }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

            // Rating Breakdown Chart
            const ratingCtx = document.getElementById('ratingChart').getContext('2d');
            new Chart(ratingCtx, {
                type: 'doughnut',
                data: {
                    labels: ratingLabels.length ? ratingLabels : ['No Data'],
                    datasets: [{
                        data: ratingCounts.length ? ratingCounts : [1],
                        backgroundColor: ratingCounts.length ? ratingColors : ['#ccc'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });

            // Set initial section
            showSection('dashboard');
        });

        // Section Navigation
        function showSection(section) {
            const sections = document.querySelectorAll('.dashboard-section, .reports-section, .feedback-section, .user-management-section');
            sections.forEach(el => el.classList.remove('active'));
            
            const targetSection = document.querySelector(`.${section}-section`);
            if (targetSection) {
                targetSection.classList.add('active');
            } else {
                console.error(`Section ${section}-section not found`);
            }

            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => item.classList.remove('active'));
            const targetMenu = document.querySelector(`.menu-item[onclick="showSection('${section}')"]`);
            if (targetMenu) {
                targetMenu.classList.add('active');
            } else {
                console.error(`Menu item for ${section} not found`);
            }
        }

        document.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('click', () => {
                console.log('Menu item clicked:', item.textContent);
            });
        });
    </script>
</body>
</html>