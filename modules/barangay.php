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