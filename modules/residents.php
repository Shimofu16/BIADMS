<?php
declare(strict_types=1);

$pdo = require '../config/database.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

try {

    switch ($action) {

        /* ===============================
           LOAD RESIDENTS (TABLE DATA)
        =============================== */
        case 'load_residents':

            $limit  = (int)($_GET['limit'] ?? 10);
            $page   = max(1, (int)($_GET['page'] ?? 1));
            $offset = ($page - 1) * $limit;

            $search       = trim($_GET['search'] ?? '');
            $barangay_id  = $_GET['barangay_id'] ?? '';
            $civil_status = $_GET['civil_status'] ?? '';

            $where  = [];
            $params = [];

            if ($search !== '') {
                $where[] = "(r.first_name LIKE :search OR r.last_name LIKE :search)";
                $params['search'] = "%$search%";
            }

            if ($barangay_id !== '') {
                $where[] = "r.barangay_id = :barangay_id";
                $params['barangay_id'] = $barangay_id;
            }

            if ($civil_status !== '') {
                $where[] = "r.civil_status = :civil_status";
                $params['civil_status'] = $civil_status;
            }

            $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

            /* Count */
            $countStmt = $pdo->prepare("
                SELECT COUNT(*)
                FROM residents r
                $whereSQL
            ");
            $countStmt->execute($params);
            $total = (int)$countStmt->fetchColumn();

            /* Data */
            $stmt = $pdo->prepare("
                SELECT 
                    r.id,
                    r.household_no,
                    r.first_name,
                    r.last_name,
                    r.gender,
                    r.civil_status,
                    b.name AS barangay
                FROM residents r
                JOIN barangays b ON b.id = r.barangay_id
                $whereSQL
                ORDER BY r.last_name
                LIMIT :limit OFFSET :offset
            ");

            foreach ($params as $k => $v) {
                $stmt->bindValue(":$k", $v);
            }

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode([
                'success' => true,
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'pages' => ceil($total / $limit)
                ]
            ]);
            break;

        /* ===============================
           FAMILY MEMBERS (MODAL)
        =============================== */
        case 'family_members':

            $stmt = $pdo->prepare("
                SELECT first_name, last_name, relationship
                FROM family_members
                WHERE resident_id = ?
            ");
            $stmt->execute([(int)$_GET['resident_id']]);

            echo json_encode([
                'success' => true,
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ]);
            break;

        default:
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error',
        'error' => $e->getMessage()
    ]);
}
