<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

$pdo = require '../../config/database.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$residentId = (int) $_GET['id'];

$residentStmt = $pdo->prepare("SELECT * FROM residents WHERE id = ?");
$residentStmt->execute([$residentId]);
$resident = $residentStmt->fetch();

if (!$resident) {
    header('Location: index.php');
    exit();
}

$familyStmt = $pdo->prepare("SELECT * FROM family_members WHERE resident_id = ?");
$familyStmt->execute([$residentId]);
$familyMembers = $familyStmt->fetchAll();

$barangays = $pdo->query("SELECT * FROM barangays ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Resident</title>
    <?php include '../../public/assets/css/styles.php'; ?>
</head>

<body>
    <div class="antialiased">
        <?php include '../includes/header.php'; ?>
        <?php include '../includes/sidebar.php'; ?>

        <main class="p-4 md:ml-64 h-auto pt-20">

            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-semibold text-heading">Residents</h1>
                <button onclick="location.href='index.php'" class="px-4 py-2 bg-gray-300 rounded-md">
                    Back to Residents
                </button>
            </div>

            <div class="relative overflow-x-auto shadow-xs rounded-md border border-default">
                <div class="p-6 bg-white">

                    <form id="residentForm" class="space-y-8">
                        <input type="hidden" name="id" value="<?= $resident['id'] ?>">

                        <!-- RESIDENT INFO -->
                        <div class="border rounded-lg p-6 bg-white">
                            <h2 class="text-lg font-semibold mb-4">Resident Information (Head of the Family)</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium mb-1">Household No.</label>
                                    <input type="text" name="household_no"
                                        value="<?= htmlspecialchars($resident['household_no']) ?>"
                                        class="w-full p-2 border rounded-md">
                                </div>

                                <div>
                                    <label class="block text-xs font-medium mb-1">Barangay</label>
                                    <select name="barangay_id" class="w-full p-2 border rounded-md">
                                        <option value="">Select Barangay</option>
                                        <?php foreach ($barangays as $b): ?>
                                            <option value="<?= $b['id'] ?>" <?= $resident['barangay_id'] == $b['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($b['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>


                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                <div>
                                    <label class="block text-xs font-medium mb-1">First Name</label>
                                    <input type="text" name="first_name"
                                        value="<?= htmlspecialchars($resident['first_name']) ?>"
                                        class="w-full p-2 border rounded-md">
                                </div>

                                <div>
                                    <label class="block text-xs font-medium mb-1">Middle Name</label>
                                    <input type="text" name="middle_name"
                                        value="<?= htmlspecialchars($resident['middle_name']) ?>"
                                        class="w-full p-2 border rounded-md">
                                </div>

                                <div>
                                    <label class="block text-xs font-medium mb-1">Last Name</label>
                                    <input type="text" name="last_name"
                                        value="<?= htmlspecialchars($resident['last_name']) ?>"
                                        class="w-full p-2 border rounded-md">
                                </div>
                            </div>


                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-xs font-medium mb-1">Gender</label>
                                    <select name="gender" class="w-full p-2 border rounded-md">
                                        <option value="">Select</option>
                                        <option value="Male" <?= $resident['gender'] == 'Male' ? 'selected' : '' ?>>Male
                                        </option>
                                        <option value="Female" <?= $resident['gender'] == 'Female' ? 'selected' : '' ?>>
                                            Female</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium mb-1">Birth Date</label>
                                    <input type="date" name="birth_date" value="<?= $resident['birth_date'] ?>"
                                        class="w-full p-2 border rounded-md">
                                </div>
                            </div>


                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-xs font-medium mb-1">Civil Status</label>
                                    <select name="civil_status" class="w-full p-2 border rounded-md">
                                        <option value="">Select</option>
                                        <?php foreach (['Single', 'Married', 'Widowed', 'Separated'] as $s): ?>
                                            <option value="<?= $s ?>" <?= $resident['civil_status'] == $s ? 'selected' : '' ?>>
                                                <?= $s ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium mb-1">Contact Number</label>
                                    <input type="text" name="contact_number"
                                        value="<?= htmlspecialchars($resident['contact_number']) ?>"
                                        class="w-full p-2 border rounded-md">
                                </div>
                            </div>


                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                               

                                <div>
                                    <label class="block text-xs font-medium mb-1">Occupation</label>
                                    <input type="text" name="occupation"
                                        value="<?= htmlspecialchars($resident['occupation']) ?>"
                                        class="w-full p-2 border rounded-md">
                                </div>

                                <div>
                                    <label class="block text-xs font-medium mb-1">Special Status</label>
                                    <select name="special_status" class="w-full p-2 border rounded-md">
                                        <option value="">Select Status</option>
                                        <?php foreach (['PWD', 'Senior Citizen', 'Pregnant', 'None'] as $status): ?>
                                            <option value="<?= $status ?>" <?= $resident['special_status'] == $status ? 'selected' : '' ?>>
                                                <?= $status ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                             <div class="mt-4">
                                    <label class="block text-xs font-medium mb-1">Address</label>
                                    <textarea name="address" rows="2"
                                        class="w-full p-2 border rounded-md"><?= htmlspecialchars($resident['address']) ?></textarea>
                                </div>
                        </div>

                        <!-- FAMILY -->
                        <div class="border rounded-lg p-6 bg-white">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-semibold">Family Members</h2>
                                <button type="button" onclick="addFamilyMember()"
                                    class="px-4 py-2 text-white bg-blue-600 rounded-md">
                                    + Add Member
                                </button>
                            </div>

                            <div id="familyContainer" class="space-y-3">
                                <?php foreach ($familyMembers as $i => $f): ?>
                                    <div class="grid family-row" data-index="<?= $i ?>">

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border p-3 rounded-md "
                                            data-index="<?= $i ?>">
    
                                            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div>
                                                    <label class="block text-xs font-medium mb-1">First Name</label>
                                                    <input type="text" name="family[<?= $i ?>][first_name]"
                                                        value="<?= htmlspecialchars($f['first_name']) ?>"
                                                        class="w-full p-2 text-sm border rounded-md" required>
                                                </div>
    
                                                <div>
                                                    <label class="block text-xs font-medium mb-1">Middle Name</label>
                                                    <input type="text" name="family[<?= $i ?>][middle_name]"
                                                        value="<?= htmlspecialchars($f['middle_name']) ?>"
                                                        class="w-full p-2 text-sm border rounded-md">
                                                </div>
    
                                                <div>
                                                    <label class="block text-xs font-medium mb-1">Last Name</label>
                                                    <input type="text" name="family[<?= $i ?>][last_name]"
                                                        value="<?= htmlspecialchars($f['last_name']) ?>"
                                                        class="w-full p-2 text-sm border rounded-md" required>
                                                </div>
                                            </div>
    
                                            <div>
                                                <label class="block text-xs font-medium mb-1">Gender</label>
                                                <select name="family[<?= $i ?>][gender]"
                                                    class="w-full p-2 text-sm border rounded-md">
                                                    <option value="">Select Gender</option>
                                                    <option value="male" <?= $f['gender'] == 'male' ? 'selected' : '' ?>>Male
                                                    </option>
                                                    <option value="female" <?= $f['gender'] == 'female' ? 'selected' : '' ?>>Female
                                                    </option>
                                                </select>
                                            </div>
    
                                            <div>
                                                <label class="block text-xs font-medium mb-1">Birth Date</label>
                                                <input type="date" name="family[<?= $i ?>][birth_date]"
                                                    value="<?= $f['birth_date'] ?>"
                                                    class="w-full p-2 text-sm border rounded-md">
                                            </div>
    
                                            <div>
                                                <label class="block text-xs font-medium mb-1">Relationship</label>
                                                <select name="family[<?= $i ?>][relationship]"
                                                    class="w-full p-2 text-sm border rounded-md">
                                                    <?php foreach (['Spouse', 'Child', 'Father', 'Mother', 'Sibling', 'Grandparent', 'Other'] as $r): ?>
                                                        <option value="<?= $r ?>" <?= $f['relationship'] === $r ? 'selected' : '' ?>>
                                                            <?= $r ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
    
                                            <div>
                                                <label class="block text-xs font-medium mb-1">Civil Status</label>
                                                <select name="family[<?= $i ?>][civil_status]"
                                                    class="w-full p-2 text-sm border rounded-md">
                                                    <?php foreach (['Single', 'Married', 'Widowed', 'Separated'] as $s): ?>
                                                        <option value="<?= $s ?>" <?= $f['civil_status'] === $s ? 'selected' : '' ?>>
                                                            <?= $s ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
    
                                            <div>
                                                <label class="block text-xs font-medium mb-1">Occupation</label>
                                                <input type="text" name="family[<?= $i ?>][occupation]"
                                                    value="<?= htmlspecialchars($f['occupation']) ?>"
                                                    class="w-full p-2 text-sm border rounded-md">
                                          </div>
                                          <div>
                                                <label class="block text-xs font-medium mb-1">Special Status</label>
                                                <select name="family[<?= $i ?>][special_status]"
                                                    class="w-full p-2 text-sm border rounded-md">
                                                    <option value="">Select Status</option>
                                                    <?php foreach (['PWD', 'senior_citizen', '4ps_beneficiary', 'None'] as $status): ?>
                                                        <option value="<?= $status ?>" <?= $f['special_status'] == $status ? 'selected' : '' ?>>
                                                            <?= $status ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                          </div>
                                        </div>
                                        <div class="flex items-end">
                                            <button type="button" onclick="this.closest('.family-row').remove()"
                                                class="px-3 py-2 bg-red-600 text-white rounded-md mt-6">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                    <hr class="my-3">
                                <?php endforeach; ?>

                            </div>
                        </div>

                        <div id="formErrors" class="hidden text-sm text-red-600"></div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md">
                                Update Resident
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </main>
    </div>

    <?php include '../../public/assets/js/js.php'; ?>

    <script>
        let familyIndex = <?= count($familyMembers) ?>;

        function addFamilyMember() {
            const container = document.getElementById('familyContainer');
            const currentIndex = familyIndex;

            const div = document.createElement('div');
            div.className = 'grid flex-wrap';

            div.setAttribute('data-index', currentIndex);

            div.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border p-3 rounded-md ">
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium mb-1">First Name <span class="text-red-500">*</span></label>
                        <input type="text"
                            name="family[${currentIndex}][first_name]"
                            class="w-full p-2 text-sm border rounded-md"
                            required>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1">Middle Name</label>
                        <input type="text"
                            name="family[${currentIndex}][middle_name]"
                            class="w-full p-2 text-sm border rounded-md">
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1">Last Name <span class="text-red-500">*</span></label>
                        <input type="text"
                            name="family[${currentIndex}][last_name]"
                            class="w-full p-2 text-sm border rounded-md"
                            required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium mb-1">Gender</label>
                    <select name="family[${currentIndex}][gender]" class="w-full p-2 text-sm border rounded-md">
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
                    <select name="family[${currentIndex}][relationship]" class="w-full p-2 text-sm border rounded-md">
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
                    <select name="family[${currentIndex}][civil_status]" class="w-full p-2 text-sm border rounded-md">
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
                        class="w-full p-2 text-sm border rounded-md">
                </div>

                <div>
                    <label class="block text-xs font-medium mb-1">Special Status</label>
                    <select name="family[${currentIndex}][special_status]" class="w-full p-2 text-sm border rounded-md">
                        <option value="">Select Status</option>
                        <?php foreach (['PWD', 'senior_citizen', '4ps_beneficiary', 'None'] as $status): ?>
                            <option value="<?= $status ?>">
                                <?= $status ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="flex items-end">
                <button type="button" onclick="this.closest('.family-row').remove()"
                    class="px-3 py-2 bg-red-600 text-white rounded-md mt-6">
                    Remove
                </button>
            </div>
            `;

            container.appendChild(div);
                                                        // add hr aftrer 
            const hr = document.createElement('hr');
            hr.className = 'my-3';
            container.appendChild(hr);

            familyIndex++;
        }

        document.getElementById('residentForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const form = e.target;
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
                const firstName = row.querySelector('[name$="[first_name]"]')?.value.trim();
                const lastName = row.querySelector('[name$="[last_name]"]')?.value.trim();

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
    </script>

</body>

</html>