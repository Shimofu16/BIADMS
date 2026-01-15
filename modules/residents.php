<?php
declare(strict_types=1);

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

        case 'load_residents':

            $limit = (int) ($_GET['limit'] ?? 10);
            $page = max(1, (int) ($_GET['page'] ?? 1));
            $offset = ($page - 1) * $limit;

            $search = trim($_GET['search'] ?? '');
            $barangay_id = $_GET['barangay_id'] ?? '';
            $civil_status = $_GET['civil_status'] ?? '';

            $where = [];
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
            $total = (int) $countStmt->fetchColumn();

            /* Data */
            $stmt = $pdo->prepare("
                SELECT 
                    r.id,
                    r.household_no,
                    r.first_name,
                    r.last_name,
                    r.middle_name,
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


        case 'family_members':

            $stmt = $pdo->prepare("
                SELECT first_name, last_name, relationship
                FROM family_members
                WHERE resident_id = ?
            ");
            $stmt->execute([(int) $_GET['resident_id']]);

            echo json_encode([
                'success' => true,
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ]);
            break;

        case 'store_resident':

            case 'store_resident':
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data || json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'errors' => ['Invalid JSON payload']
        ]);
        exit;
    }

    $errors = [];
    $required = [
        'household_no' => 'Household No',
        'barangay_id' => 'Barangay',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'gender' => 'Gender',
        'birth_date' => 'Birth Date',
        'civil_status' => 'Civil Status'
    ];

    // Validate required fields
    foreach ($required as $field => $label) {
        if (empty(trim($data[$field] ?? ''))) {
            $errors[$field] = "$label is required";
        }
    }

    // Validate barangay_id exists
    if (!empty($data['barangay_id']) && !is_numeric($data['barangay_id'])) {
        $errors['barangay_id'] = 'Invalid barangay selected';
    }

    // Validate birth_date format
    if (!empty($data['birth_date'])) {
        $date = DateTime::createFromFormat('Y-m-d', $data['birth_date']);
        if (!$date || $date->format('Y-m-d') !== $data['birth_date']) {
            $errors['birth_date'] = 'Invalid date format (YYYY-MM-DD required)';
        }
    }

    if (!empty($errors)) {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'errors' => $errors
        ]);
        exit;
    }

    // Start transaction outside of try-catch
    $pdo->beginTransaction();

    try {
        // Sanitize inputs
        $household_no = htmlspecialchars(trim($data['household_no']), ENT_QUOTES, 'UTF-8');
        $barangay_id = (int) $data['barangay_id'];
        $first_name = htmlspecialchars(trim($data['first_name']), ENT_QUOTES, 'UTF-8');
        $middle_name = !empty($data['middle_name']) ? htmlspecialchars(trim($data['middle_name']), ENT_QUOTES, 'UTF-8') : null;
        $last_name = htmlspecialchars(trim($data['last_name']), ENT_QUOTES, 'UTF-8');
        $gender = htmlspecialchars(trim($data['gender']), ENT_QUOTES, 'UTF-8');
        $birth_date = $data['birth_date'];
        $civil_status = htmlspecialchars(trim($data['civil_status']), ENT_QUOTES, 'UTF-8');
        $contact_number = !empty($data['contact_number']) ? preg_replace('/[^0-9]/', '', $data['contact_number']) : null;
        $address = !empty($data['address']) ? htmlspecialchars(trim($data['address']), ENT_QUOTES, 'UTF-8') : null;
        $occupation = !empty($data['occupation']) ? htmlspecialchars(trim($data['occupation']), ENT_QUOTES, 'UTF-8') : null;

        // Check if household number already exists
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM residents WHERE household_no = ?");
        $checkStmt->execute([$household_no]);
        if ($checkStmt->fetchColumn() > 0) {
            throw new Exception("Household number already exists");
        }

        // Insert resident - FIXED: Remove extra spaces and use consistent placeholders
        $stmt = $pdo->prepare("
            INSERT INTO residents (
                household_no, 
                barangay_id, 
                first_name, 
                middle_name, 
                last_name,
                gender, 
                birth_date, 
                civil_status, 
                contact_number, 
                address, 
                occupation
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $household_no,
            $barangay_id,
            $first_name,
            $middle_name,
            $last_name,
            $gender,
            $birth_date,
            $civil_status,
            $contact_number,
            $address,
            $occupation
        ]);

        $residentId = (int) $pdo->lastInsertId();

        // Insert family members if present
        if (!empty($data['family']) && is_array($data['family'])) {
            $familyStmt = $pdo->prepare("
                INSERT INTO family_members (
                    resident_id, 
                    first_name, 
                    middle_name, 
                    last_name,
                    gender, 
                    birth_date, 
                    relationship, 
                    civil_status, 
                    occupation
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($data['family'] as $member) {
                // Only insert if first_name and last_name are provided
                $memberFirstName = trim($member['first_name'] ?? '');
                $memberLastName = trim($member['last_name'] ?? '');
                
                if (empty($memberFirstName) || empty($memberLastName)) {
                    continue;
                }

                $familyStmt->execute([
                    $residentId,
                    htmlspecialchars($memberFirstName, ENT_QUOTES, 'UTF-8'),
                    !empty($member['middle_name']) ? htmlspecialchars(trim($member['middle_name']), ENT_QUOTES, 'UTF-8') : null,
                    htmlspecialchars($memberLastName, ENT_QUOTES, 'UTF-8'),
                    !empty($member['gender']) ? htmlspecialchars(trim($member['gender']), ENT_QUOTES, 'UTF-8') : null,
                    !empty($member['birth_date']) ? $member['birth_date'] : null,
                    !empty($member['relationship']) ? htmlspecialchars(trim($member['relationship']), ENT_QUOTES, 'UTF-8') : null,
                    !empty($member['civil_status']) ? htmlspecialchars(trim($member['civil_status']), ENT_QUOTES, 'UTF-8') : null,
                    !empty($member['occupation']) ? htmlspecialchars(trim($member['occupation']), ENT_QUOTES, 'UTF-8') : null
                ]);
            }
        }

        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Resident created successfully',
            'resident_id' => $residentId
        ]);

    } catch (Exception $e) {
        // Rollback if transaction was started
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create resident',
            'error' => $e->getMessage(),
            'error_details' => 'File: ' . $e->getFile() . ' Line: ' . $e->getLine()
        ]);
    }

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
