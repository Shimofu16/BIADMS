<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

$pdo = require '../config/database.php';

/* ===============================
   KPI QUERIES
=================================*/

$totalResidents = $pdo->query("SELECT COUNT(*) FROM residents")->fetchColumn();

$totalFamilies = $pdo->query("
    SELECT COUNT(DISTINCT household_no) 
    FROM residents
")->fetchColumn();

$totalBarangays = $pdo->query("SELECT COUNT(*) FROM barangays")->fetchColumn();

$specialStatusStats = $pdo->query("
    SELECT special_status, COUNT(*) as total
    FROM residents
    WHERE special_status IS NOT NULL
      AND special_status != ''
    GROUP BY special_status
    ORDER BY total DESC
")->fetchAll(PDO::FETCH_ASSOC);

$totalSpecial = array_sum(array_column($specialStatusStats, 'total'));

/* ===============================
   Residents per Barangay
=================================*/

$barangayStats = $pdo->query("
    SELECT b.name, COUNT(r.id) as total
    FROM barangays b
    LEFT JOIN residents r ON r.barangay_id = b.id
    GROUP BY b.id
    ORDER BY total DESC
")->fetchAll(PDO::FETCH_ASSOC);

/* ===============================
   Gender Distribution
=================================*/

$genderStats = $pdo->query("
    SELECT gender, COUNT(*) as total
    FROM residents
    GROUP BY gender
")->fetchAll(PDO::FETCH_ASSOC);

/* ===============================
   Civil Status
=================================*/

$civilStats = $pdo->query("
    SELECT civil_status, COUNT(*) as total
    FROM residents
    GROUP BY civil_status
")->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <?php include '../public/assets/css/styles.php'; ?>
</head>

<body>
    <div class="antialiased bg-gray-50 dark:bg-gray-900">

        <?php include 'includes/header.php'; ?>

        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <main class="p-6 md:ml-64 pt-20">

            <!-- KPI CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

                <?php
                function card($title, $value, $color)
                {
                    echo "
            <div class='bg-white rounded-2xl shadow p-5 border-l-4 $color'>
                <p class='text-gray-500 text-sm'>$title</p>
                <h2 class='text-3xl font-bold mt-2'>$value</h2>
            </div>";
                }

                card('Total Residents', number_format($totalResidents), 'border-blue-500');
                card('Total Families', number_format($totalFamilies), 'border-green-500');
                card('Total Barangays', number_format($totalBarangays), 'border-purple-500');
                card('Special Status Residents', number_format($totalSpecial), 'border-red-500');
                ?>

            </div>


            <!-- CHARTS -->
            <div class="grid lg:grid-cols-2 gap-6 mb-6">

                <div class="bg-white p-6 rounded-2xl shadow w-full min-h-[420px]" id="barangayChartWrapper">
                    <h3 class="font-semibold mb-4">Residents per Barangay</h3>
                    <canvas id="barangayChart"></canvas>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow w-full min-h-[420px]" id="genderChartWrapper">
                    <h3 class="font-semibold mb-4">Gender Distribution</h3>
                    <canvas id="genderChart" style="height: 300px;"></canvas>
                </div>

            </div>

            <div class="grid lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white p-6 rounded-2xl shadow w-full min-h-[420px]" id="specialChartWrapper">
                    <h3 class="font-semibold mb-4">Special Status Distribution</h3>
                    <canvas id="specialChart"></canvas>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow w-full min-h-[420px]" id="civilChartWrapper">
                    <h3 class="font-semibold mb-4">Civil Status Breakdown</h3>
                    <canvas id="civilChart" style="height: 300px;"></canvas>
                </div>
            </div>


        </main>

    </div>

    <?php include '../public/assets/js/js.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>

        // ===============================
        // DATA
        // ===============================

        const barangayLabels = <?= json_encode(array_column($barangayStats, 'name')) ?>;
        const barangayData = <?= json_encode(array_column($barangayStats, 'total')) ?>;

        const genderLabels = <?= json_encode(array_column($genderStats, 'gender')) ?>;
        const genderData = <?= json_encode(array_column($genderStats, 'total')) ?>;

        const civilLabels = <?= json_encode(array_column($civilStats, 'civil_status')) ?>;
        const civilData = <?= json_encode(array_column($civilStats, 'total')) ?>;

        const specialLabels = <?= json_encode(array_column($specialStatusStats, 'special_status')) ?>;
        const specialData = <?= json_encode(array_column($specialStatusStats, 'total')) ?>;



        


        

        // ===============================
        // CHARTS
        // ===============================


        // ⭐ Residents per Barangay (HORIZONTAL — scalable)
        new Chart(document.getElementById('barangayChart'), {
            type: 'bar',
            data: {
                labels: barangayLabels,
                datasets: [{
                    label: 'Residents',
                    data: barangayData
                }]
            },
            options: {
                indexAxis: 'y',
            }
        });


        // Gender
        new Chart(document.getElementById('genderChart'), {
            type: 'doughnut',
            data: {
                labels: genderLabels,
                datasets: [{
                    data: genderData
                }]
            },
        });


        // Civil Status
        new Chart(document.getElementById('civilChart'), {
            type: 'pie',
            data: {
                labels: civilLabels,
                datasets: [{
                    data: civilData
                }]
            },
        });


        // ⭐ Special Status (AUTO HEIGHT + HORIZONTAL)
        new Chart(document.getElementById('specialChart'), {
            type: 'bar',
            data: {
                labels: specialLabels,
                datasets: [{
                    label: 'Residents',
                    data: specialData
                }]
            },
            options: {
                indexAxis: 'y',
            }
        });

    </script>


</body>

</html>