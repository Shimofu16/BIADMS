<?php

declare(strict_types=1);

// ADD THESE LINES FOR DEBUGGING
error_reporting(E_ALL);
ini_set('display_errors', 1);

$pdo = require '../config/database.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

// ADD LOGGING
error_log("Action received: " . $action);

try {
    switch ($action) {
        case 'load_barangays':
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;

            // Get total count
            $countStmt = $pdo->query("SELECT COUNT(*) FROM barangays");
            $totalCount = (int) $countStmt->fetchColumn();

            // Get paginated data
            $stmt = $pdo->prepare("
                SELECT 
                    b.*, 
                    (
                        SELECT COUNT(*) 
                        FROM residents r 
                        WHERE r.barangay_id = b.id
                    ) AS total_residents,
                    (
                        SELECT COUNT(*) 
                        FROM family_members fm
                        INNER JOIN residents r ON fm.resident_id = r.id
                        WHERE r.barangay_id = b.id
                    ) AS total_family_members
                FROM barangays b
                LIMIT :limit OFFSET :offset
            ");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $barangays = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'data' => $barangays,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $totalCount,
                    'pages' => ceil($totalCount / $limit)
                ]
            ]);
            break;

        case 'store_barangay':

            if (empty($_POST['name'])) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => 'Barangay name is required'
                ]);
                exit;
            }

            $name = trim($_POST['name']);
            $logoPath = null;

            // Handle logo upload
            if (!empty($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {

                $uploadDir = __DIR__ . '../public/uploads/barangay_logos/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                $fileName = uniqid('barangay_', true) . '.' . $ext;
                $targetPath = $uploadDir . $fileName;

                if (!move_uploaded_file($_FILES['logo']['tmp_name'], $targetPath)) {
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Logo upload failed'
                    ]);
                    exit;
                }

                $logoPath = 'uploads/barangay_logos/' . $fileName;
            }

            $stmt = $pdo->prepare(
                "INSERT INTO barangays (name, logo) VALUES (:name, :logo)"
            );
            $stmt->execute([
                ':name' => $name,
                ':logo' => $logoPath
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Barangay created successfully',
                'id' => $pdo->lastInsertId()
            ]);
            break;

        case 'update_barangay':
            if (empty($_POST['name'])) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => 'Barangay name is required'
                ]);
                exit;
            }
            $id = (int) ($_POST['id'] ?? 0);
            $name = trim($_POST['name']);
            $logoPath = null;

            // Handle logo upload
            if (!empty($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {

                // Get the current logo path from the database
                $stmtLogo = $pdo->prepare("SELECT logo FROM barangays WHERE id = :id");
                $stmtLogo->execute([':id' => $id]);
                $currentLogo = $stmtLogo->fetchColumn();

                // Delete the old logo file if it exists and is not null/empty
                if ($currentLogo && file_exists(__DIR__ . '/../public/' . $currentLogo)) {
                    unlink(__DIR__ . '/../public/' . $currentLogo);
                }

                $uploadDir = __DIR__ . '/../public/uploads/barangay_logos/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                $fileName = uniqid('barangay_', true) . '.' . $ext;
                $targetPath = $uploadDir . $fileName;

                if (!move_uploaded_file($_FILES['logo']['tmp_name'], $targetPath)) {
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Logo upload failed'
                    ]);
                    exit;
                }

                $logoPath = 'uploads/barangay_logos/' . $fileName;
            }

            // Build update query
            $updateFields = "name = :name";
            if ($logoPath !== null) {
                $updateFields .= ", logo = :logo";
            }
            $stmt = $pdo->prepare(
                "UPDATE barangays SET $updateFields WHERE id = :id"
            );
            $params = [
                ':name' => $name,
                ':id' => $id
            ];
            if ($logoPath !== null) {
                $params[':logo'] = $logoPath;
            }
            $stmt->execute($params);
            echo json_encode([
                'success' => true,
                'message' => 'Barangay updated successfully'
            ]);



            break;
        default:
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
            break;
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error',
        'error' => $e->getMessage()
    ]);
}