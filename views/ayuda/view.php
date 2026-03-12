<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}
$user = [
    'id' => $_SESSION['user_id'],
    'name' => $_SESSION['name'],
    'email' => $_SESSION['email'],
    'image' => $_SESSION['image'],
];

$pdo = require '../../config/database.php';

$ayuda_id = $_GET['id'] ?? null;
if (!$ayuda_id) {
    header('Location: index.php');
    exit;
}

$ayudaStmt = $pdo->prepare("
    SELECT ad.*, b.name AS barangay_name
    FROM ayuda_distributions ad
    LEFT JOIN barangays b ON ad.barangay_id = b.id
    WHERE ad.id = ?
");
$ayudaStmt->execute([$ayuda_id]);
$ayuda = $ayudaStmt->fetch(PDO::FETCH_ASSOC);

if (!$ayuda) {
    header('Location: index.php');
    exit;
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda - View</title>
    <?php include '../../public/assets/css/styles.php'; ?>
</head>

<body>
    <div class="antialiased">

        <?php include '../includes/header.php'; ?>

        <!-- Sidebar -->
        <?php include '../includes/sidebar.php'; ?>

        <main class="p-4 md:ml-64 h-auto pt-20">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-semibold text-heading">Ayuda</h1>
                <button onclick="location.href='index.php'"
                    class="px-4 py-2 bg-gray-300 text-black rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Back to Ayuda Distributions
                </button>
            </div>

            <div class="relative overflow-x-auto shadow-xs rounded-md border border-default">
                <div class="p-6 bg-white rounded-lg shadow-sm space-y-8">

                    <!-- HEADER -->
                    <div class="border-b pb-4">
                        <h2 class="text-2xl font-semibold text-heading">
                            <?= htmlspecialchars($ayuda['title']) ?>
                        </h2>
                    </div>


                    <!-- BASIC INFORMATION -->
                    <div>
                        <h3 class="text-lg font-semibold text-heading mb-3">
                            Basic Information
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>
                                <p class="text-sm text-gray-500">Eligibility</p>
                                <p class="font-medium">
                                    <?= htmlspecialchars($ayuda['eligibility'] ?? '-') ?>
                                </p>
                            </div>

                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-500">Description</p>
                                <p class="font-medium">
                                    <?= nl2br(htmlspecialchars($ayuda['description'] ?? '-')) ?>
                                </p>
                            </div>

                        </div>
                    </div>


                    <!-- DISTRIBUTION SCHEDULE -->
                    <div>
                        <h3 class="text-lg font-semibold text-heading mb-3">
                            Distribution Schedule
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                            <div>
                                <p class="text-sm text-gray-500">Distribution Date</p>
                                <p class="font-medium">
                                    <?= ($ayuda['distribution_date']
                                        ? (new DateTime($ayuda['distribution_date']))->format('F j, Y')
                                        : '-') ?>
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">Time</p>
                                <p class="font-medium">
                                    <?= ($ayuda['start_time'] && $ayuda['end_time'])
                                        ? (new DateTime($ayuda['start_time']))->format('g:i A') .
                                        ' - ' .
                                        (new DateTime($ayuda['end_time']))->format('g:i A')
                                        : '-' ?>
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">Venue</p>
                                <p class="font-medium">
                                    <?= htmlspecialchars($ayuda['venue'] ?? '-') ?>
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">Barangay</p>
                                <p class="font-medium">
                                    <?php
                                    if ($ayuda['barangay_id']) {
                                        $barangayStmt = $pdo->prepare("SELECT name FROM barangays WHERE id = ?");
                                        $barangayStmt->execute([$ayuda['barangay_id']]);
                                        $barangay = $barangayStmt->fetch(PDO::FETCH_ASSOC);
                                        echo htmlspecialchars($barangay['name']);
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </p>
                            </div>

                        </div>
                    </div>


                    <!-- BENEFICIARIES & BUDGET -->
                    <div>
                        <h3 class="text-lg font-semibold text-heading mb-3">
                            Beneficiaries & Budget
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                            <div>
                                <p class="text-sm text-gray-500">Budget Amount</p>
                                <p class="font-medium">
                                    <?= $ayuda['budget_amount']
                                        ? '₱' . number_format($ayuda['budget_amount'], 2)
                                        : '-' ?>
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">Target Beneficiaries</p>
                                <p class="font-medium">
                                    <?= $ayuda['total_target_beneficiaries'] ?? '-' ?>
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">Actual Beneficiaries</p>
                                <p class="font-medium">
                                    <?= $ayuda['total_actual_beneficiaries'] ?? '-' ?>
                                </p>
                            </div>

                        </div>
                    </div>


                    <!-- SYSTEM INFO -->
                    <div class="border-t pt-4">
                        <p class="text-sm text-gray-500">Created At</p>
                        <p class="font-medium">
                            <?= (new DateTime($ayuda['created_at']))->format('F j, Y, g:i A') ?>
                        </p>
                    </div>

                </div>

                

            </div>

        </main>
    </div>

    <?php include '../../public/assets/js/js.php'; ?>





</body>

</html>