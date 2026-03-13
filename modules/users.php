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

        case 'store_user':

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid request method'
                ]);
                exit;
            }

            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $role = 'barangay';
            $barangay_id = $_POST['barangay_id'];
            $password = '';

            $barangay = $pdo->prepare("SELECT name FROM barangays WHERE id = :id");
            $barangay->bindValue(':id', $barangay_id, PDO::PARAM_INT);
            $barangay->execute();
            if ($barangay->rowCount() === 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Selected barangay does not exist.'
                ]);
                exit;
            }

            $barangay_name = strtolower($barangay->fetchColumn());
            $password = password_hash($barangay_name . '_default_password', PASSWORD_DEFAULT);

            if (empty($name) || empty($email) || empty($barangay_id)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'All fields are required.'
                ]);
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid email format.'
                ]);
                exit;
            }



            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, barangay_id) VALUES (:name, :email, :password, :role, :barangay_id)");
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $password, PDO::PARAM_STR);
            $stmt->bindValue(':role', $role, PDO::PARAM_STR);
            $stmt->bindValue(':barangay_id', $barangay_id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode([
                'success' => true,
                'message' => 'User created successfully.'
            ]);

            break;

        case 'delete_user':

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid request method'
                ]);
                exit;
            }

            $user_id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

            if ($user_id <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid user ID.'
                ]);
                exit;
            }

            try {

                $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
                $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
                $stmt->execute();

                echo json_encode([
                    'success' => true,
                    'message' => 'User deleted successfully.'
                ]);

            } catch (PDOException $e) {

                echo json_encode([
                    'success' => false,
                    'message' => 'Database error',
                    'error' => $e->getMessage()
                ]);

            }
            break;

        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action.'
            ]);
            break;
    }

} catch (Throwable $e) {

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
