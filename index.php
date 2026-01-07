<?php



session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: ./views/dashboard.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php include 'public/assets/css/styles.php'; ?>
</head>

<body>

    <section class="bg-gray-50">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 ">
                <!-- <img class="w-8 h-8 mr-2" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/logo.svg"
                    alt="logo">
                    -->
                BIADMS
            </a>
            <div class="w-full bg-white rounded-lg shadow :mt-0 sm:max-w-md xl:p-0 -800 -700">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1 class="text-xl text-center font-bold leading-tight tracking-tight text-gray-900 md:text-2xl ">
                        Sign in to your account
                    </h1>
                    <form class="space-y-4 md:space-y-6" action="./config/auth.php" method="POST">

                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 ">Email</label>
                            <input type="email" name="email" id="email"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 -700 -600 -400  -blue-500 -blue-500"
                                required="">
                        </div>
                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 ">Password</label>
                            <input type="password" name="password" id="password"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 -700 -600 -400  -blue-500 -blue-500"
                                required="">
                        </div>
                        <div class="flex items-center justify-between">
                            <!-- <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="remember" aria-describedby="remember" type="checkbox"
                                        class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 -700 -600 -primary-600 -gray-800"
                                        required="">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="remember" class="text-gray-500 -300">Remember me</label>
                                </div>
                            </div> -->
                            <a href="#" class="text-sm font-medium text-primary-600 hover:underline -500">Forgot
                                password?</a>
                        </div>
                        <button type="submit"
                            name="login"
                            value="1"
                            class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center -600 -primary-700 -primary-800">Sign
                            in</button>
                        <!-- <p class="text-sm font-light text-gray-500 -400">
                            Don’t have an account yet? <a href="#"
                                class="font-medium text-primary-600 hover:underline -500">Sign up</a>
                        </p> -->
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include 'public/assets/js/js.php'; ?>

</body>

</html>