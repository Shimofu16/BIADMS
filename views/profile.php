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
    <title>Profile - <?php echo $user['name']; ?></title>
    <?php include '../public/assets/css/styles.php'; ?>
</head>

<body>
    <div class="antialiased bg-gray-50 dark:bg-gray-900">

        <?php include 'includes/header.php'; ?>

        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <main class="p-4 md:ml-64 h-auto pt-20">
            <div class="w-full max-w-sm">
                <h1
                    class="text-xl mb-3 font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                    Update your Account Info.
                </h1>
                <form class="space-y-4 md:space-y-6" method="POST" enctype="multipart/form-data"
                    action="../modules/profile.php">
                    <div>
                        <label for="name"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                        <input type="text" name="name" id="name" placeholder="••••••••"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            value="<?php echo htmlspecialchars($user['name']); ?>" required="">
                    </div>
                    <div>
                        <label for="email"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                        <input type="email" name="email" id="email"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            value="<?php echo htmlspecialchars($user['email']); ?>" required="">
                    </div>

                    <div class="space-y-1.5">
                        <label for="file_input" class="block text-sm font-medium text-gray-700">
                            Upload file
                        </label>

                        <input id="file_input" type="file" class="block w-full text-sm text-gray-700
               file:mr-4 file:py-2 file:px-4
               file:rounded-lg file:border-0
               file:bg-primary file:text-white
               file:text-sm file:font-medium
               hover:file:bg-primary/90
               cursor-pointer
               border border-gray-300 rounded-lg
               focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                            aria-describedby="file_input_help">

                        <p id="file_input_help" class="text-xs text-gray-500">
                            SVG, PNG, JPG or GIF
                        </p>
                    </div>


                    <button type="submit" name="update_info" value="1"
                        class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Update
                        Info.</button>

                </form>
            </div>
            <hr class="h-px my-8">
            <div class="w-full max-w-sm">
                <h1
                    class="text-xl mb-3 font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                    Update Password
                </h1>
                <form class="space-y-4 md:space-y-6" action="../modules/profile.php" method="POST">
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">New
                            Password</label>
                        <input type="password" name="password" id="password" placeholder="••••••••"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            required="">
                    </div>
                    <div>
                        <label for="confirm-password"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Confirm
                            password</label>
                        <input type="password" name="confirm-password" id="confirm-password" placeholder="••••••••"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            required="">
                    </div>
                    <ul id="passwordRules" class="mt-2 space-y-1 text-xs">
                        <li data-rule="length" class="text-gray-500">• At least 8 characters</li>
                        <li data-rule="upper" class="text-gray-500">• One uppercase letter</li>
                        <li data-rule="lower" class="text-gray-500">• One lowercase letter</li>
                        <li data-rule="number" class="text-gray-500">• One number</li>
                        <li data-rule="special" class="text-gray-500">• One special character</li>
                        <li data-rule="match" class="text-gray-500">• Passwords match</li>
                    </ul>

                    <p id="passwordSuccess" class="mt-1 text-xs text-green-600 hidden"></p>
                    <button type="submit" id="updatePasswordBtn" name="update_password" value="1"
                        class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                        Update Password
                    </button>

                </form>
            </div>
        </main>
    </div>

    <?php include '../public/assets/js/js.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const password = document.getElementById('password');
            const confirm = document.getElementById('confirm-password');
            const submit = document.getElementById('updatePasswordBtn');

            const rules = {
                length: val => val.length >= 8,
                upper: val => /[A-Z]/.test(val),
                lower: val => /[a-z]/.test(val),
                number: val => /[0-9]/.test(val),
                special: val => /[^A-Za-z0-9]/.test(val),
                match: () => password.value === confirm.value && confirm.value !== ''
            };

            const ruleElements = document.querySelectorAll('#passwordRules li');

            function validate() {
                let valid = true;

                ruleElements.forEach(li => {
                    const rule = li.dataset.rule;
                    const passed = rules[rule](password.value);

                    li.classList.toggle('text-green-600', passed);
                    li.classList.toggle('text-gray-500', !passed);
                    li.classList.toggle('font-medium', passed);

                    if (!passed) valid = false;
                });

                // Input border feedback
                password.classList.toggle('border-red-500', !valid);
                confirm.classList.toggle('border-red-500', !rules.match());

                submit.disabled = !valid;
            }

            password.addEventListener('input', validate);
            confirm.addEventListener('input', validate);

            submit.disabled = true;
        });
    </script>


</body>

</html>