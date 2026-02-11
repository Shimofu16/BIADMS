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
                    <form action="../../modules/residents.php?action=store_resident" method="POST" id="residentForm"
                        class="space-y-8">

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
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
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
                                        <option value="single">Single</option>
                                        <option value="married">Married</option>
                                        <option value="widowed">Widowed</option>
                                        <option value="separated">Separated</option>
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
                                    <label for="block text-sm font-medium mb-1">
                                        PWD/ Senior Citizen/ 4Ps Beneficiary
                                    </label>
                                    <select name="special_status" class="w-full p-2 text-sm border rounded-md">
                                        <option value="">Select</option>
                                        <?php
                                        foreach (['PWD' => 'PWD', 'Senior Citizen' => 'Senior Citizen', '4Ps Beneficiary' => '4Ps Beneficiary'] as $value => $label): ?>
                                            <option value="<?= $value ?>">
                                                <?= $label ?>
                                            </option>
                                        <?php endforeach; ?>

                                    </select>
                                </div>


                                <div>
                                    <label class="block text-sm font-medium mb-1">Occupation</label>
                                    <input type="text" name="occupation" class="w-full p-2 text-sm border rounded-md">
                                </div>


                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium mb-1">Address</label>
                                <textarea name="address" rows="2"
                                    class="w-full p-2 text-sm border rounded-md"></textarea>
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
            const currentIndex = familyIndex;

            const div = document.createElement('div');
            div.className = 'grid family-row';
            div.setAttribute('data-index', currentIndex);

            div.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border p-3 rounded-md ">
        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium mb-1">First Name <span class="text-red-500">*</span></label>
                <input type="text"
                    name="family[${currentIndex}][first_name]"
                    placeholder="First Name"
                    class="w-full p-2 text-sm border rounded-md family-first-name"
                    required>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Middle Name</label>
                <input type="text"
                    name="family[${currentIndex}][middle_name]"
                    placeholder="Middle Name"
                    class="w-full p-2 text-sm border rounded-md">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Last Name <span class="text-red-500">*</span></label>
                <input type="text"
                    name="family[${currentIndex}][last_name]"
                    placeholder="Last Name"
                    class="w-full p-2 text-sm border rounded-md family-last-name"
                    required>
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Gender</label>
            <select name="family[${currentIndex}][gender]"
                class="w-full p-2 text-sm border rounded-md">
                <option value="">Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Birth Date</label>
            <input type="date"
                name="family[${currentIndex}][birth_date]"
                class="w-full p-2 text-sm border rounded-md">
        </div>
        <div>
    <label class="block text-xs font-medium mb-1">Relationship</label>
    <select name="family[${currentIndex}][relationship]"
        class="w-full p-2 text-sm border rounded-md">
        <option value="">Select Relationship</option>
        <option value="spouse">Spouse</option>
        <option value="child">Child</option>
        <option value="father">Father</option>
        <option value="mother">Mother</option>
        <option value="sibling">Sibling</option>
        <option value="grandparent">Grandparent</option>
        <option value="Other">Other</option>
    </select>
</div>

        <div>
            <label class="block text-xs font-medium mb-1">Civil Status</label>
            <select name="family[${currentIndex}][civil_status]"
                class="w-full p-2 text-sm border rounded-md">
                <option value="">Select Status</option>
                <option value="Single">Single</option>
                <option value="Married">Married</option>
                <option value="Widowed">Widowed</option>
                <option value="Separated">Separated</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Occupation</label>
            <input type="text"
                name="family[${currentIndex}][occupation]"
                placeholder="Occupation"
                class="w-full p-2 text-sm border rounded-md">
        </div>
        <div>
                                            <label for="block text-sm font-medium mb-1">
                                                PWD/ Senior Citizen/ 4Ps Beneficiary
                                            </label>
                                            <select name="family[${currentIndex}][special_status]" class="w-full p-2 text-sm border rounded-md">
                                                <option value="">Select</option>
                                                <?php
                                                foreach (['PWD' => 'PWD', 'Senior Citizen' => 'Senior Citizen', '4Ps Beneficiary' => '4Ps Beneficiary'] as $value => $label): ?>
                                                    <option value="<?= $value ?>">
                                                        <?= $label ?>
                                                    </option>
                                                    <?php endforeach; ?>
                                            </select>


                  </div>
                  </div>
        <div class="flex items-end">
            <button type="button"
                onclick="this.closest('.family-row').remove()"
                class="px-3 py-2 text-sm text-white bg-red-600 rounded-md hover:bg-red-700 mt-6">
                Remove
            </button>
        </div>
    `;

            container.appendChild(div);

            const hr = document.createElement('hr');
            hr.className = 'my-3';
            container.appendChild(hr);
            familyIndex++; // Increment after adding
        }

        // Form submission handler
        document.getElementById('residentForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const form = this;
            const errorsBox = document.getElementById('formErrors');
            errorsBox.classList.add('hidden');
            errorsBox.innerHTML = '';

            // Clear previous error highlights
            document.querySelectorAll('.border-red-500').forEach(el => {
                el.classList.remove('border-red-500');
                el.classList.add('border-gray-300');
            });

            // Validate required fields first
            const requiredFields = form.querySelectorAll('[required]');
            let hasEmptyRequired = false;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.remove('border-gray-300');
                    field.classList.add('border-red-500');
                    hasEmptyRequired = true;
                }
            });

            if (hasEmptyRequired) {
                errorsBox.classList.remove('hidden');
                errorsBox.innerHTML = 'Please fill in all required fields';
                errorsBox.scrollIntoView({ behavior: 'smooth' });
                return;
            }

            // Build payload
            const payload = {
                household_no: form.household_no.value.trim(),
                barangay_id: form.barangay_id.value,
                first_name: form.first_name.value.trim(),
                middle_name: form.middle_name.value.trim() || null,
                last_name: form.last_name.value.trim(),
                gender: form.gender.value,
                birth_date: form.birth_date.value,
                civil_status: form.civil_status.value,
                contact_number: form.contact_number.value.trim() || null,
                address: form.address.value.trim() || null,
                occupation: form.occupation.value.trim() || null,
                special_status: form.special_status.value || null,
                family: []
            };

            // Collect family members
            document.querySelectorAll('.family-row').forEach(row => {
                const firstName = row.querySelector('.family-first-name')?.value.trim();
                const lastName = row.querySelector('.family-last-name')?.value.trim();

                if (!firstName || !lastName) return;

                payload.family.push({
                    first_name: firstName,
                    middle_name: row.querySelector('[name$="[middle_name]"]')?.value.trim() || null,
                    last_name: lastName,
                    gender: row.querySelector('[name$="[gender]"]')?.value || null,
                    birth_date: row.querySelector('[name$="[birth_date]"]')?.value || null,
                    relationship: row.querySelector('[name$="[relationship]"]')?.value.trim() || null,
                    civil_status: row.querySelector('[name$="[civil_status]"]')?.value || null,
                    occupation: row.querySelector('[name$="[occupation]"]')?.value.trim() || null,
                    special_status: row.querySelector('[name$="[special_status]"]')?.value || null
                });
            });

            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Saving...';
            submitBtn.disabled = true;

            try {
                const response = await fetch('../../modules/residents.php?action=store_resident', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!data.success) {
                    errorsBox.classList.remove('hidden');

                    if (typeof data.errors === 'object') {
                        // Show field-specific errors
                        Object.entries(data.errors).forEach(([field, message]) => {
                            const fieldEl = form.querySelector(`[name="${field}"]`);
                            if (fieldEl) {
                                fieldEl.classList.remove('border-gray-300');
                                fieldEl.classList.add('border-red-500');
                                errorsBox.innerHTML += `<p>${message}</p>`;
                            } else {
                                errorsBox.innerHTML += `<p>${message}</p>`;
                            }
                        });
                    } else {
                        errorsBox.innerHTML = data.message || 'Validation failed';
                    }

                    errorsBox.scrollIntoView({ behavior: 'smooth' });
                    return;
                }

                // Success
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: data.message || 'Resident has been created successfully',
                    timer: 2500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = 'index.php';
                });

            } catch (err) {
                console.error('Error:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An unexpected error occurred. Please try again.'
                });
            } finally {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        });

        // Add event listeners to remove error styling when user starts typing
        document.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('input', function () {
                if (this.classList.contains('border-red-500')) {
                    this.classList.remove('border-red-500');
                    this.classList.add('border-gray-300');
                }
            });
        });

        // Add initial empty family member row on page load
        document.addEventListener('DOMContentLoaded', function () {
            addFamilyMember();
        });
    </script>


</body>

</html>