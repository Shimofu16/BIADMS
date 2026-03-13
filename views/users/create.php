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
                <h1 class="text-2xl font-semibold text-heading">Create User</h1>
                <button onclick="location.href='index.php'"
                    class="px-4 py-2 bg-gray-300 text-black rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Back to User List
                </button>
            </div>

            <div class="relative overflow-x-auto shadow-xs rounded-md border border-default">
                <div class="p-6 bg-white">
                    <form method="POST" id="userForm" enctype="multipart/form-data" class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" name="name" id="name" required
                                    class="mt-1 w-full h-11 px-4 border rounded-md focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" id="email" required
                                    class="mt-1 w-full h-11 px-4 border rounded-md focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="barangay_id"
                                    class="block text-sm font-medium text-gray-700">Barangay</label>
                                <select name="barangay_id" id="barangay_id" required
                                    class="mt-1 w-full h-11 px-4 border rounded-md focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Barangay</option>
                                    <?php foreach ($barangays as $barangay): ?>
                                        <option value="<?= htmlspecialchars($barangay['id']) ?>">
                                            <?= htmlspecialchars($barangay['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>


                        <div id="formErrors" class="hidden text-sm text-red-600"></div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="px-8 h-11 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                                Save User
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
            const form = document.getElementById('userForm');
            if (!form) return;

            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                const formData = new FormData(form);



                const response = await fetch(
                    '../../modules/users.php?action=store_user',
                    {
                        method: 'POST',
                        body: formData,
                        headers: { 'Accept': 'application/json' }
                    }
                );

                const data = await response.json();

                if (!data.success) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'An error occurred while saving the user.'
                    });
                    return;
                }
                // Success
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: data.message || 'User has been created successfully',
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