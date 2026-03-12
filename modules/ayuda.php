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
        case 'load_ayuda':

            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;

            $search = trim($_GET['search'] ?? '');
            $barangayId = isset($_GET['barangay_id']) ? (int) $_GET['barangay_id'] : null;

            $where = '';
            $params = [];


            if ($search !== '') {
                $where .= "WHERE a.title LIKE :search_title";
                $params[':search_title'] = "%{$search}%";
            }


            if ($barangayId) {
                $where .= ($where === '' ? 'WHERE' : ' AND') . " a.barangay_id = :barangay_id";
                $params[':barangay_id'] = $barangayId;
            }


            $countSql = "SELECT COUNT(*) FROM ayuda_distributions a $where";
            $countStmt = $pdo->prepare($countSql);

            foreach ($params as $key => $val) {
                if (is_int($val)) {
                    $countStmt->bindValue($key, $val, PDO::PARAM_INT);
                } else {
                    $countStmt->bindValue($key, $val, PDO::PARAM_STR);
                }
            }

            $countStmt->execute();
            $totalCount = (int) $countStmt->fetchColumn();

            $sql = "
        SELECT 
            a.*,
            b.name AS barangay_name
        FROM ayuda_distributions a
        LEFT JOIN barangays b ON a.barangay_id = b.id
        $where
        ORDER BY a.created_at DESC
        LIMIT :limit OFFSET :offset";

            $stmt = $pdo->prepare($sql);

            foreach ($params as $key => $val) {
                if (is_int($val)) {
                    $stmt->bindValue($key, $val, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($key, $val, PDO::PARAM_STR);
                }
            }

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();
            $ayuda_distributions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'data' => $ayuda_distributions,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $totalCount,
                    'pages' => ceil($totalCount / $limit)
                ]
            ]);
            break;

        case 'store_ayuda':
            try {

                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid request method'
                    ]);
                    exit;
                }

                // Collect input
                $title = trim($_POST['title'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $eligibility = $_POST['eligibility'] ?? '';
                $distribution_date = $_POST['distribution_date'] ?? '';
                $start_time = $_POST['start_time'] ?? '';
                $end_time = $_POST['end_time'] ?? '';
                $barangay_id = $_POST['barangay_id'] ?? '';
                $venue = trim($_POST['venue'] ?? '');
                $budget_amount = $_POST['budget_amount'] ?? 0;
                $source_of_funds = trim($_POST['source_of_funds'] ?? '');
                $total_target_beneficiaries = $_POST['total_target_beneficiaries'] ?? 0;
                $total_actual_beneficiaries = $_POST['total_actual_beneficiaries'] ?? 0;
                $status = $_POST['status'] ?? 'planned';

                // Basic validation
                if (
                    empty($title) ||
                    empty($eligibility) ||
                    empty($distribution_date) ||
                    empty($barangay_id)
                ) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Required fields are missing'
                    ]);
                    exit;
                }

                // Insert query
                $stmt = $pdo->prepare("
            INSERT INTO ayuda_distributions (
                title,
                description,
                eligibility,
                distribution_date,
                start_time,
                end_time,
                barangay_id,
                venue,
                budget_amount,
                source_of_funds,
                total_target_beneficiaries,
                total_actual_beneficiaries,
                status,
                created_at,
                updated_at
            ) VALUES (
                :title,
                :description,
                :eligibility,
                :distribution_date,
                :start_time,
                :end_time,
                :barangay_id,
                :venue,
                :budget_amount,
                :source_of_funds,
                :total_target_beneficiaries,
                :total_actual_beneficiaries,
                :status,
                NOW(),
                NOW()
            )
        ");

                $stmt->execute([
                    ':title' => $title,
                    ':description' => $description,
                    ':eligibility' => $eligibility,
                    ':distribution_date' => $distribution_date,
                    ':start_time' => $start_time,
                    ':end_time' => $end_time,
                    ':barangay_id' => $barangay_id,
                    ':venue' => $venue,
                    ':budget_amount' => $budget_amount,
                    ':source_of_funds' => $source_of_funds,
                    ':total_target_beneficiaries' => $total_target_beneficiaries,
                    ':total_actual_beneficiaries' => $total_actual_beneficiaries,
                    ':status' => $status
                ]);

                echo json_encode([
                    'success' => true,
                    'message' => 'Ayuda distribution created successfully'
                ]);

            } catch (PDOException $e) {

                echo json_encode([
                    'success' => false,
                    'message' => 'Database error',
                    'error' => $e->getMessage()
                ]);

            }

            exit;

            break;
        default:
            echo json_encode(['error' => 'Invalid action']);
    }
} catch (Exception $e) {
    // ADD ERROR LOGGING
    error_log("Error: " . $e->getMessage());
    echo json_encode(['error' => 'An error occurred while processing your request.']);
}


?>