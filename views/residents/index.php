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

$barangayStmt = $pdo->query("SELECT * FROM barangays ORDER BY name");
$barangays = $barangayStmt->fetchAll();


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Residents</title>
    <?php include '../../public/assets/css/styles.php'; ?>
</head>

<body>
    <div class="antialiased">

        <?php include '../includes/header.php'; ?>

        <!-- Sidebar -->
        <?php include '../includes/sidebar.php'; ?>

        <main class="p-4 md:ml-64 h-auto pt-20">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-semibold text-heading">Residents</h1>
                <button
                    onclick="location.href='create.php'"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Add Resident
                </button>
            </div>

            <div class="relative overflow-x-auto shadow-xs rounded-md border border-default">
                <form id="filterForm" class="p-4 flex flex-wrap gap-3 items-center justify-end">
                    <input type="text" name="search" placeholder="Search resident"
                        id="searchInput"
                        class="block w-full max-w-96 px-3 py-2 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body">

                    <select name="barangay_id" class="px-3 py-2 border rounded-base text-sm"
                        id="barangayFilter"
                    >
                        <option value="">All Barangays</option>
                        <?php foreach ($barangays as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <select name="civil_status" class="px-3 py-2 border rounded-base text-sm"
                        id="civilStatusFilter"
                    >
                        <option value="">All Civil Status</option>
                        <option value="single">Single</option>
                        <option value="married">Married</option>
                        <option value="widowed">Widowed</option>
                        <option value="separated">Separated</option>
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
                            <th class="px-6 py-3 font-medium">Resident Name</th>
                            <th class="px-6 py-3 font-medium">Gender</th>
                            <th class="px-6 py-3 font-medium">Civil Status</th>
                            <th class="px-6 py-3 font-medium">Barangay</th>
                            <th class="px-6 py-3 font-medium">Action</th>
                        </tr>
                    </thead>

                    <tbody id="residentsTable">
                        <!-- AJAX rows injected here -->
                    </tbody>
                </table>

                <!-- Pagination -->
                <div id="pagination" class="p-4 flex gap-2"></div>


                <div id="familyModal" class="fixed inset-0 bg-black bg-opacity-50 hidden">
                    <div
                        class="bg-white rounded-md shadow-lg max-w-md mx-auto mt-20 p-6 relative">
                        <button
                            onclick="closeFamilyModal()"
                            class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 focus:outline-none">
                            &times;
                        </button>
                        <h2 class="text-xl font-semibold mb-4">Family Members</h2>
                        <div id="familyList" class="space-y-2">
                            <!-- Family members loaded here -->
                        </div>
                    </div>
            </div>

        </main>
    </div>

    <?php include '../../public/assets/js/js.php'; ?>

    <script>
        let currentPage = 1;

        function loadResidents(page = 1) {
            currentPage = page;

            const params = new URLSearchParams({
                action: 'load_residents',
                page,
                search: document.getElementById('searchInput').value,
                barangay_id: document.getElementById('barangayFilter').value,
                civil_status: document.getElementById('civilStatusFilter').value
            });

            fetch(`../../modules/residents.php?${params}`)
                .then(res => res.json())
                .then(res => {
                    if (!res.success) return;

                    renderTable(res.data);
                    renderPagination(res.pagination);
                });
        }

        function renderTable(data) {
            const tbody = document.getElementById('residentsTable');
            tbody.innerHTML = '';

            if (!data.length) {
                tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-body">
                    No residents found
                </td>
            </tr>`;
                return;
            }

            data.forEach((r, index) => {
                tbody.innerHTML += `
        <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-md">
            <th scope="row"
                class="px-6 py-4 font-md text-md whitespace-nowrap">
                ${r.last_name}, ${r.first_name} ${r.middle_name ? r.middle_name.charAt(0) + '.' : ''}
            </th>

            <td class="px-6 py-4">${r.gender}</td>

            <td class="px-6 py-4">${r.civil_status}</td>

            <td class="px-6 py-4">${r.barangay}</td>

            <td class="px-6 py-4">
                <button
                    onclick="loadFamily(${r.id})"
                    class="font-md text-fg-brand hover:underline">
                    View Family
                </button>
            </td>
        </tr>`;
            });
        }

        function renderPagination(p) {
            const container = document.getElementById('pagination');
            container.innerHTML = '';

            for (let i = 1; i <= p.pages; i++) {
                container.innerHTML += `
            <button 
                onclick="loadResidents(${i})"
                class="px-3 py-1 rounded ${i === p.page ? 'bg-blue-600 text-white' : 'bg-gray-200'
                    }">
                ${i}
            </button>`;
            }
        }

       
        function loadFamily(residentId) {
            fetch(`../../modules/residents.php?action=family_members&resident_id=${residentId}`)
                .then(res => res.json())
                .then(res => {
                    const list = document.getElementById('familyList');
                    list.innerHTML = '';

                    if (!res.data.length) {
                        list.innerHTML = '<p class="text-gray-500">No family members</p>';
                    } else {
                        res.data.forEach(f => {
                            list.innerHTML += `
                        <p>
                            ${f.first_name} ${f.last_name}
                            <span class="text-sm text-gray-500">(${f.relationship})</span>
                        </p>`;
                        });
                    }

                    document.getElementById('familyModal').classList.remove('hidden');
                });
        }

        function closeFamilyModal() {
            document.getElementById('familyModal').classList.add('hidden');
        }


        document.addEventListener('DOMContentLoaded', () => {
            loadResidents();

            document.getElementById('searchInput').addEventListener('input', () => loadResidents(1));
            document.getElementById('barangayFilter').addEventListener('change', () => loadResidents(1));
            document.getElementById('civilStatusFilter').addEventListener('change', () => loadResidents(1));
        });

    </script>


</body>

</html>