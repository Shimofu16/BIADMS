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

// Fetch barangays for the select dropdown
$stmt = $pdo->query("SELECT id, name FROM barangays ORDER BY name ASC");
$barangays = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda Beneficiaries - Create</title>
    <?php include '../../public/assets/css/styles.php'; ?>
</head>

<body>
    <div class="antialiased">

        <?php include '../includes/header.php'; ?>

        <!-- Sidebar -->
        <?php include '../includes/sidebar.php'; ?>

        <main class="p-4 md:ml-64 h-auto pt-20">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-semibold text-heading">Ayuda Beneficiaries</h1>
                <button onclick="location.href='index.php'"
                    class="px-4 py-2 bg-gray-300 text-black rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Back to Ayuda List
                </button>
            </div>

            <div class="relative overflow-x-auto shadow-xs rounded-md border border-default">
                <div class="p-6 bg-white">
                    <form  method="POST" id="ayudaForm"
                        enctype="multipart/form-data" class="space-y-8">

                        <!-- ================= BASIC INFORMATION ================= -->
                        <fieldset class="border border-gray-200 rounded-lg p-6">
                            <legend class="text-lg font-semibold text-gray-700 px-2">
                                Basic Information
                            </legend>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium mb-1">Title</label>
                                    <input type="text" name="title" required
                                        class="w-full h-11 px-4 border rounded-md focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium mb-1">Description</label>
                                    <textarea name="description" rows="3" required
                                        class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500"></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Eligibility</label>
                                    <select name="eligibility" required
                                        class="w-full h-11 px-4 border rounded-md focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select Eligibility</option>
                                        <option value="households">Households</option>
                                        <option value="special_beneficiaries">Special Beneficiaries</option>
                                    </select>
                                </div>

                            </div>
                        </fieldset>


                        <!-- ================= SCHEDULE & LOCATION ================= -->
                        <fieldset class="border border-gray-200 rounded-lg p-6">
                            <legend class="text-lg font-semibold text-gray-700 px-2">
                                Schedule & Location
                            </legend>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">

                                <div>
                                    <label class="block text-sm font-medium mb-1">Distribution Date</label>
                                    <input type="date" name="distribution_date" required
                                        class="w-full h-11 px-4 border rounded-md focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Start Time</label>
                                    <input type="time" name="start_time" required
                                        class="w-full h-11 px-4 border rounded-md focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">End Time</label>
                                    <input type="time" name="end_time" required
                                        class="w-full h-11 px-4 border rounded-md focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Barangay</label>
                                    <select name="barangay_id" required
                                        class="w-full h-11 px-4 border rounded-md focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select Barangay</option>
                                        <?php foreach ($barangays as $barangay): ?>
                                            <option value="<?= htmlspecialchars($barangay['id']) ?>">
                                                <?= htmlspecialchars($barangay['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium mb-1">Venue</label>
                                    <input type="text" name="venue" required
                                        class="w-full h-11 px-4 border rounded-md focus:ring-2 focus:ring-blue-500">
                                </div>

                            </div>
                        </fieldset>


                        <!-- ================= BUDGET & FUNDING ================= -->
                        <fieldset class="border border-gray-200 rounded-lg p-6">
                            <legend class="text-lg font-semibold text-gray-700 px-2">
                                Budget & Funding
                            </legend>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">

                                <div>
                                    <label class="block text-sm font-medium mb-1">Budget Amount</label>
                                    <input type="number" step="0.01" name="budget_amount" required
                                        class="w-full h-11 px-4 border rounded-md focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Source of Funds</label>
                                    <input type="text" name="source_of_funds" required
                                        class="w-full h-11 px-4 border rounded-md focus:ring-2 focus:ring-blue-500">
                                </div>

                            </div>
                        </fieldset>


                        <!-- ================= BENEFICIARIES & STATUS ================= -->
                        <fieldset class="border border-gray-200 rounded-lg p-6">
                            <legend class="text-lg font-semibold text-gray-700 px-2">
                                Beneficiaries & Status
                            </legend>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">

                                <div>
                                    <label class="block text-sm font-medium mb-1">Target Beneficiaries</label>
                                    <input type="number" name="total_target_beneficiaries" required
                                        class="w-full h-11 px-4 border rounded-md focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Actual Beneficiaries</label>
                                    <input type="number" name="total_actual_beneficiaries" required
                                        class="w-full h-11 px-4 border rounded-md focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Status</label>
                                    <select name="status" required
                                        class="w-full h-11 px-4 border rounded-md focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select Status</option>
                                        <option value="planned">Planned</option>
                                        <option value="ongoing">Ongoing</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>

                            </div>
                        </fieldset>


                        <div id="formErrors" class="hidden text-sm text-red-600"></div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="px-8 h-11 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                                Save Ayuda
                            </button>
                        </div>

                    </form>
                </div>

            </div>

        </main>
    </div>

    <?php include '../../public/assets/js/js.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('ayudaForm');
            if (!form) return;

            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                const formData = new FormData(form);

                

                const response = await fetch(
                    '../../modules/ayuda.php?action=store_ayuda',
                    {
                        method: 'POST',
                        body: formData,
                        headers: { 'Accept': 'application/json' }
                    }
                );

                const data = await response.json();

                if (!data.success) {
                    Swal.fire('Error', data.message, 'error');
                    return;
                }

                // Success
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: data.message || 'Ayuda has been created successfully',
                    timer: 2500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = 'index.php';
                });
            });
        });
    </script>
</body>

</html>