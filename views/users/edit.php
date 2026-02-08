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


if (!$barangay) {
    header('Location: index.php');
    exit;
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User - Edit</title>
    <?php include '../../public/assets/css/styles.php'; ?>
</head>

<body>
    <div class="antialiased">

        <?php include '../includes/header.php'; ?>

        <!-- Sidebar -->
        <?php include '../includes/sidebar.php'; ?>

        <main class="p-4 md:ml-64 h-auto pt-20">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-semibold text-heading">User</h1>
                <button onclick="location.href='index.php'"
                    class="px-4 py-2 bg-gray-300 text-black rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Back to Users
                </button>
            </div>

            <div class="relative overflow-x-auto shadow-xs rounded-md border border-default">
                <div class="p-6 bg-white">
                    <form id="userForm" class="space-y-8">
                        <input type="hidden" name="id" value="<?= $barangay['id'] ?>">
                        <div>
                            <label for="name" class="block mb-2 font-medium text-gray-900">Barangay Name</label>
                            <input type="text" name="name" id="name" value="<?= htmlspecialchars($barangay['name']) ?>"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">

                        </div>
                        <div>
                            <label for="logo" class="block mb-2 font-medium text-gray-900">Barangay Logo</label>
                            <input type="file" name="logo" id="logo" accept="image/*"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <?php if (!empty($barangay['logo'])): ?>
                            <div class="mb-2">
                                <img src="../../public/<?= $barangay['logo'] ?>" alt="Barangay Logo"
                                    class="h-20 rounded-md border">
                            </div>
                        <?php endif; ?>

                        <div id="formErrors" class="hidden text-sm text-red-600"></div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="px-6 py-2 text-white bg-green-600 rounded-md hover:bg-green-700">
                                Save Barangay
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
                console.log('Intercepted submit');

                const formData = new FormData(form);

                const response = await fetch(
                    '../../modules/barangay.php?action=update_barangay',
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

                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: data.message
                });
            });
        });
    </script>



</body>

</html>