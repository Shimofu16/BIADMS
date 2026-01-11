<?php
session_start();

$indexPath = '../index.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location:'. $indexPath);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    try {
        $conn = require 'database.php';
        // Sanitize input safely
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        
        // Fetch the user by email only
        $stmt = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            echo 'No user found';
            echo $indexPath;
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => 'Incorrect email or password!'
            ];
            header('Location:' . $indexPath);
            exit;
        }
        
        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['image'] = $row['image'] ?? null;
            
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Welcome back, ' . htmlspecialchars($row['name']) . '!'
            ];
            header('Location: ../views/dashboard.php');
            exit;
        } else {
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => 'Incorrect email or password!'
            ];
            header('Location:' . $indexPath);
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'An error occurred during login. Please try again later.'
        ];
        error_log('Login error: ' . $e->getMessage());
        header('Location:' . $indexPath);
        exit;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    // Clear session variables
    session_unset();
    session_destroy();
    
    session_start();
    $_SESSION['alert'] = [
        'type' => 'success',
        'message' => 'Logged out successfully!'
    ];
    header('Location:' . $indexPath);
    exit;
}


?>
