<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', 1);

$pdo = require '../config/database.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

try {

    switch ($action) {

        case 'load_users':


            $page = max(1, (int) ($_GET['page'] ?? 1));
            $limit = 10;
            $offset = ($page - 1) * $limit;


            $search = trim($_GET['search'] ?? '');
            $barangay_id = ($_GET['barangay_id'] ?? '') !== ''
                ? (int) $_GET['barangay_id']
                : null;

           
            $whereParts = ["u.role != 'admin'"];
            $params = [];

            if ($barangay_id !== null) {
                $whereParts[] = "u.barangay_id = :barangay_id";
                $params[':barangay_id'] = $barangay_id;
            }

            if ($search !== '') {
                $whereParts[] = "(u.name LIKE :search_name OR u.email LIKE :search_email)";
                $params[':search_name'] = "%{$search}%";
                $params[':search_email'] = "%{$search}%";
            }

            $where = implode(' AND ', $whereParts);

            $countSql = "SELECT COUNT(*) FROM users u WHERE $where";
            $countStmt = $pdo->prepare($countSql);

            foreach ($params as $key => $value) {
                $countStmt->bindValue(
                    $key,
                    $value,
                    is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
                );
            }

            $countStmt->execute();
            $total = (int) $countStmt->fetchColumn();

     
            $sql = "
                SELECT u.*, b.name AS barangay_name
                FROM users u
                LEFT JOIN barangays b ON b.id = u.barangay_id
                WHERE $where
                ORDER BY u.created_at DESC
                LIMIT :limit OFFSET :offset
            ";

            $stmt = $pdo->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue(
                    $key,
                    $value,
                    is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
                );
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
                    'pages' => (int) ceil($total / $limit)
                ]
            ]);

            break;

        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action.'
            ]);
            break;
    }

} catch (Throwable $e) {

    // 🔥 Show real error while debugging
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
