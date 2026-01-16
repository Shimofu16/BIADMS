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

    <script>
        function loadBarangays(page) {
            const params = new URLSearchParams({
                action: 'load_barangays',
                page,
            });
            fetch(`../../modules/barangay.php?${params}`)
                .then(response => response.json())
                .then(res => {
                    const barangayTable = document.getElementById('barangayTable');
                    barangayTable.innerHTML = '';
                   
                    
                    res.data.forEach(barangay => {
                        const row = document.createElement('tr');
                        row.classList.add('border-b', 'hover:bg-gray-50', 'dark:hover:bg-gray-600');

                        row.innerHTML = `
                            <td class="px-6 py-4">
                                <img src="${barangay.logo_url}" alt="Logo" class="w-10 h-10 rounded-full object-cover">
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">${barangay.name}</td>
                            <td class="px-6 py-4">${barangay.residents_count}</td>
                            <td class="px-6 py-4">
                                <button onclick="location.href='view.php?barangay_id=${barangay.id}'"
                                    class="px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    View
                                </button>
                                <button onclick="location.href='edit.php?barangay_id=${barangay.id}'"
                                    class="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Edit
                                </button>
                            </td>
                        `;

                        barangayTable.appendChild(row);
                    });

                    // Pagination
                    const pagination = document.getElementById('pagination');
                    pagination.innerHTML = '';
                    for (let i = 1; i <= res.pagination.pages; i++) {
                        const pageBtn = document.createElement('button');
                        pageBtn.textContent = i;
                        pageBtn.classList.add('px-3', 'py-1', 'rounded-md', 'focus:outline-none', 'focus:ring-2', 'focus:ring-blue-500', 'focus:ring-offset-2');
                        if (i === res.pagination.page) {
                            pageBtn.classList.add('bg-blue-600', 'text-white');
                        } else {
                            pageBtn.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                            pageBtn.onclick = () => loadBarangays(i);
                        }
                        pagination.appendChild(pageBtn);
                    }
                })
                .catch(error => console.error('Error fetching barangays:', error));
        }
        document.addEventListener('DOMContentLoaded', function () {
            loadBarangays(1); // Load first page on initial load
        });
    </script>

</body>

</html>