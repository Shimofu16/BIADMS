<?php
session_start();



// Auth guard
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$userId = $_SESSION['user_id'];
$conn = require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_info'])) {
    $message = 'Profile updated successfully.';
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);

    // Basic validation
    if ($name === '' || $email === '') {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'Name and email are required.'
        ];
        header('Location: ../views/profile.php');
        exit;
    }

    // Handle image upload (optional)
    $imageName = $_SESSION['image'] ?? null;

    if (!empty($_FILES['file_input']['name'])) {

        $file = $_FILES['file_input'];
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'image here.'
        ];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/svg+xml', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($file['type'], $allowedTypes)) {
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => 'Invalid image type.'
            ];
            header('Location: ../views/profile.php');
            exit;
        }

        if ($file['size'] > $maxSize) {
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => 'Image size must not exceed 2MB.'
            ];
            header('Location: ../views/profile.php');
            exit;
        }

        // Generate safe filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $imageName = uniqid('user_', true) . '.' . $extension;

        $uploadPath = '../public/uploads/users/' . $imageName;

        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => 'Failed to upload image.'
            ];
            header('Location: ../views/profile.php');
            exit;
        }

        $message = 'Profile and image updated successfully.';
    }

    // Update user
    $stmt = $conn->prepare("
        UPDATE users 
        SET name = ?, email = ?, image = ?, updated_at = NOW()
        WHERE id = ?
    ");

    $stmt->execute([$name, $email, $imageName, $userId]);

    // Update session
    $_SESSION['name']  = $name;
    $_SESSION['email'] = $email;
    $_SESSION['image'] = $imageName;

    $_SESSION['alert'] = [
        'type' => 'success',
        'message' => $message
    ];

    header('Location: ../views/profile.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {

    $password        = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm-password'] ?? '';

    if ($password === '' || $confirmPassword === '') {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'All password fields are required.'
        ];
        header('Location: ../views/profile.php');
        exit;
    }

    if ($password !== $confirmPassword) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'Passwords do not match.'
        ];
        header('Location: ../views/profile.php');
        exit;
    }

    if (strlen($password) < 8) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'Password must be at least 8 characters.'
        ];
        header('Location: ../views/profile.php');
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        UPDATE users 
        SET password = ?, updated_at = NOW()
        WHERE id = ?
    ");

    $stmt->execute([$hashedPassword, $userId]);

    $_SESSION['alert'] = [
        'type' => 'success',
        'message' => 'Password updated successfully.'
    ];

    header('Location: ../views/profile.php');
    exit;
}

// Fallback
header('Location: ../views/profile.php');
exit;
