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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay</title>
    <?php include '../../public/assets/css/styles.php'; ?>
</head>

<body>
    <div class="antialiased bg-gray-50 dark:bg-gray-900">

        <?php include '../includes/header.php'; ?>

        <!-- Sidebar -->
        <?php include '../includes/sidebar.php'; ?>

        <main class="p-4 md:ml-64 h-auto pt-20">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-semibold text-heading">Barangay</h1>
                <button onclick="location.href='create.php'"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Add Barangay
                </button>
            </div>

            <div class="relative overflow-x-auto shadow-xs rounded-md border border-default">
                <!-- Filter form -->
                <form id="filterForm" class="p-4 flex flex-wrap gap-3 items-center justify-end">
                    <input type="text" name="search" placeholder="Search barangay" id="search"
                        class="block w-full max-w-96 px-3 py-2 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body">
                </form>

                <!-- TABLE -->
                <table class="w-full text-sm text-left text-body">
                    <thead class="text-sm bg-neutral-secondary-medium border-y border-default-medium">
                        <tr>
                            <!-- <th class="p-4">
                                <input id="selectAll" type="checkbox"
                                    class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                            </th> -->
                            <th class="px-6 py-3 font-medium">Logo</th>
                            <th class="px-6 py-3 font-medium">Barangay Name</th>
                            <th class="px-6 py-3 font-medium">Residents Count</th>
                            <th class="px-6 py-3 font-medium">Action</th>
                        </tr>
                    </thead>

                    <tbody id="barangayTable">
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
            endpoint: '../../modules/barangay.php',
            action: 'load_barangays',
            paginationContnainer: 'pagination',
            filters: [
                'search',
            ],
            renderTable: function (data) {
                const tableBody = document.getElementById('barangayTable');
                tableBody.innerHTML = '';
                data.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
            <td class="px-6 py-4">
                ${row.logo ? `<img src="../../public/${row.logo}" alt="Logo" class="w-10 h-10 object-cover rounded-full">` : '<span class="text-gray-500">No Logo</span>'}
            </td>
            <td class="px-6 py-4">${row.name}</td>
            <td class="px-6 py-4">${row.total_residents}</td>
            <td class="px-6 py-4">
                <button onclick="location.href='edit.php?id=${row.id}'" class="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Edit</button>
                <button onclick="deleteBarangay(${row.id})" class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">Delete</button>
            </td>
        `;
                    tableBody.appendChild(tr);
                });
            }
        });
    </script>

</body>

</html>