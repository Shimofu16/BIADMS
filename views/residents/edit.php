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
    <title>Residents - Create</title>
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
                <button onclick="location.href='index.php'"
                    class="px-4 py-2 bg-gray-300 text-black rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Back to Residents
                </button>
            </div>

            <div class="relative overflow-x-auto shadow-xs rounded-md border border-default">
                <div class="p-6 bg-white">
                    <form action="../../modules/residents.php?action=store_resident" method="POST" id="residentForm" class="space-y-8">

                        <div class="border rounded-lg p-6 bg-white">
                            <h2 class="text-lg font-semibold mb-4">
                                Resident Information (Head of the Family)
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <div>
                                    <label class="block text-sm font-medium mb-1">Household No</label>
                                    <input type="text" name="household_no" class="w-full p-2 text-sm border rounded-md">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Barangay</label>
                                    <select name="barangay_id" class="w-full p-2 text-sm border rounded-md">
                                        <option value="">Select Barangay</option>
                                        <?php foreach ($barangays as $barangay): ?>
                                            <option value="<?= $barangay['id'] ?>">
                                                <?= htmlspecialchars($barangay['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">First Name</label>
                                    <input type="text" name="first_name" class="w-full p-2 text-sm border rounded-md">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Middle Name</label>
                                    <input type="text" name="middle_name" class="w-full p-2 text-sm border rounded-md">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Last Name</label>
                                    <input type="text" name="last_name" class="w-full p-2 text-sm border rounded-md">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Gender</label>
                                    <select name="gender" class="w-full p-2 text-sm border rounded-md">
                                        <option value="">Select</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Birth Date</label>
                                    <input type="date" name="birth_date" class="w-full p-2 text-sm border rounded-md">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Civil Status</label>
                                    <select name="civil_status" class="w-full p-2 text-sm border rounded-md">
                                        <option value="">Select</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Widowed">Widowed</option>
                                        <option value="Separated">Separated</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Contact Number</label>
                                    <input type="text" name="contact_number"
                                        class="w-full p-2 text-sm border rounded-md">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Address</label>
                                    <textarea name="address" rows="2"
                                        class="w-full p-2 text-sm border rounded-md"></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Occupation</label>
                                    <input type="text" name="occupation" class="w-full p-2 text-sm border rounded-md">
                                </div>
                            </div>
                        </div>

                        <div class="border rounded-lg p-6 bg-white">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-semibold">Family Members</h2>
                                <button type="button" onclick="addFamilyMember()"
                                    class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                    + Add Member
                                </button>
                            </div>

                            <div id="familyContainer" class="space-y-3"></div>
                        </div>


                        <div id="formErrors" class="hidden text-sm text-red-600"></div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="px-6 py-2 text-white bg-green-600 rounded-md hover:bg-green-700">
                                Save Resident
                            </button>
                        </div>

                    </form>

                </div>

            </div>

        </main>
    </div>

    <?php include '../../public/assets/js/js.php'; ?>

    <script>
        let familyIndex = 0;

        function addFamilyMember() {
            const container = document.getElementById('familyContainer');

            const div = document.createElement('div');
            div.className = 'grid grid-cols-1 md:grid-cols-7 gap-2 border p-3 rounded-md';

            div.innerHTML = `
        <input type="text" name="family[${familyIndex}][first_name]" placeholder="First Name"
            class="p-2 text-sm border rounded-md">

        <input type="text" name="family[${familyIndex}][middle_name]" placeholder="Middle Name"
            class="p-2 text-sm border rounded-md">

        <input type="text" name="family[${familyIndex}][last_name]" placeholder="Last Name"
            class="p-2 text-sm border rounded-md">

        <select name="family[${familyIndex}][gender]"
            class="p-2 text-sm border rounded-md">
            <option value="">Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>

        <input type="date" name="family[${familyIndex}][birth_date]"
            class="p-2 text-sm border rounded-md">

        <input type="text" name="family[${familyIndex}][relationship]" placeholder="Relationship"
            class="p-2 text-sm border rounded-md">

        <button type="button"
            onclick="this.parentElement.remove()"
            class="px-3 py-2 text-sm text-white bg-red-600 rounded-md hover:bg-red-700">
            Remove
        </button>
    `;

            container.appendChild(div);
            familyIndex++;
        }
    </script>

    <script>
        document.getElementById('residentForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const form = this;
            const errorsBox = document.getElementById('formErrors');
            errorsBox.classList.add('hidden');
            errorsBox.innerHTML = '';

            const formData = new FormData(form);

            fetch('store.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
                .then(res => res.json())
                .then(data => {

                    if (!data.success) {
                        errorsBox.classList.remove('hidden');
                        errorsBox.innerHTML = data.errors.join('<br>');
                        return;
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: 'Resident has been created successfully',
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        window.location.href = 'index.php';
                    });

                })
                .catch(() => {
                    Swal.fire('Error', 'Something went wrong', 'error');
                });
        });
    </script>

</body>

</html>