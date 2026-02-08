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

// Fetch barangays for filter dropdown
$barangayStmt = $pdo->query("SELECT id, name FROM barangays ORDER BY name ASC");
$barangays = $barangayStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <?php include '../../public/assets/css/styles.php'; ?>
</head>

<body>
    <div class="antialiased">

        <?php include '../includes/header.php'; ?>

        <!-- Sidebar -->
        <?php include '../includes/sidebar.php'; ?>

        <main class="p-4 md:ml-64 h-auto pt-20">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-semibold text-heading">Users</h1>
                <button onclick="location.href='create.php'"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Add User
                </button>
            </div>

            <div class="relative overflow-x-auto shadow-xs rounded-md border border-default">
                <form id="filterForm" class="p-4 flex flex-wrap gap-3 items-center justify-end">
                    <input type="text" name="search" placeholder="Search user" id="search"
                        class="block w-full max-w-96 px-3 py-2 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body">
                    <select name="barangay_id" class="px-3 py-2 border rounded-base text-sm" id="barangay_id">
                        <option value="">All Barangays</option>
                        <?php foreach ($barangays as $b): ?>
                            <option value="<?= $b['id'] ?>">
                                <?= htmlspecialchars($b['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <!-- TABLE -->
                <table class="w-full text-sm text-left text-body">
                    <thead class="text-sm bg-neutral-secondary-medium border-y border-default-medium">
                        <tr>
                            <!-- <th class="p-4">
                                <input id="selectAll" type="checkbox"
                                    class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                            </th> -->
                            <th class="px-6 py-3 font-medium">Name</th>
                            <th class="px-6 py-3 font-medium">Email</th>
                            <th class="px-6 py-3 font-medium">Barangay</th>
                            <th class="px-6 py-3 font-medium">Action</th>
                        </tr>
                    </thead>

                    <tbody id="table">
                        <!-- AJAX rows injected here -->
                    </tbody>
                </table>

                <!-- Pagination -->
                <div id="pagination" class="p-4 flex gap-2"></div>

            </div>

        </main>
    </div>

    <?php include '../../public/assets/js/js.php'; ?>

    <script src="../../public/assets/js/table.js"></script>

    <script>
        PaginatedTable.init({
            endpoint: '../../modules/users.php',
            action: 'load_users',
            paginationContnainer: 'pagination',
            filters: [
                'search',
                'barangay_id'
            ],
            renderTable(data) {
                const tableBody = document.getElementById('table');
                tableBody.innerHTML = '';

                if (!data.length) {
                    tableBody.innerHTML = `
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center">
                            No users found
                        </td>
                    </tr>`;
                    return;
                }
                data.forEach((u, index) => {
                    tableBody.innerHTML += `
                    <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-md">
                        <th scope="row"
                            class="px-6 py-4 font-md text-md text-heading flex items-center">
                        <img src="../../public/uploads/users/${u.image}" alt="${u.name}" class="w-10 h-10 rounded-full object-cover inline-block mr-3">
                            ${u.name}
                        </th>

                        <td class="px-6 py-4">${u.email}</td>

                        <td class="px-6 py-4">${u.barangay_name}</td>

                        <td class="px-6 py-4">
                        
                            <button
                                onclick="location.href='edit.php?id=${u.id}'"
                                class="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Edit
                            </button>
                        </td>
                    </tr>`;
                });
            }
        })
    </script>

</body>

</html>