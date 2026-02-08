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

        case 'load_residents':

            $limit = (int) ($_GET['limit'] ?? 10);
            $page = max(1, (int) ($_GET['page'] ?? 1));
            $offset = ($page - 1) * $limit;

            $search = trim($_GET['search'] ?? '');
            $barangay_id = $_GET['barangay_id'] ?? '';
            $civil_status = $_GET['civil_status'] ?? '';

            $where = [];
            $params = [];

            /* ✅ SEARCH FIX — UNIQUE PLACEHOLDERS */
            if ($search !== '') {

                $where[] = "(
            r.first_name LIKE :search_first
            OR r.middle_name LIKE :search_middle
            OR r.last_name LIKE :search_last
            OR r.household_no LIKE :search_household
        )";

                $params[':search_first'] = "%$search%";
                $params[':search_middle'] = "%$search%";
                $params[':search_last'] = "%$search%";
                $params[':search_household'] = "%$search%";
            }

            if ($barangay_id !== '') {
                $where[] = "r.barangay_id = :barangay_id";
                $params[':barangay_id'] = (int) $barangay_id;
            }

            if ($civil_status !== '') {
                $where[] = "r.civil_status = :civil_status";
                $params[':civil_status'] = $civil_status;
            }

            $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

            /* COUNT */
            $countStmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM residents r
        $whereSQL
    ");

            foreach ($params as $key => $val) {
                $countStmt->bindValue(
                    $key,
                    $val,
                    is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR
                );
            }

            $countStmt->execute();
            $total = (int) $countStmt->fetchColumn();

            /* DATA */
            $stmt = $pdo->prepare("
        SELECT 
            r.id,
            r.household_no,
            r.first_name,
            r.middle_name,
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

            foreach ($params as $key => $val) {
                $stmt->bindValue(
                    $key,
                    $val,
                    is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR
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

            foreach ($required as $field => $label) {
                if (empty(trim($data[$field] ?? ''))) {
                    $errors[$field] = "$label is required";
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

            try {
                $pdo->beginTransaction();

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
                    trim($data['household_no']),
                    (int) $data['barangay_id'],
                    trim($data['first_name']),
                    $data['middle_name'] ?? null,
                    trim($data['last_name']),
                    $data['gender'],
                    $data['birth_date'],
                    $data['civil_status'],
                    $data['contact_number'] ?? null,
                    $data['address'] ?? null,
                    $data['occupation'] ?? null
                ]);

                $residentId = (int) $pdo->lastInsertId();

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
                        if (empty($member['first_name']) || empty($member['last_name'])) {
                            continue;
                        }

                        $familyStmt->execute([
                            $residentId,
                            trim($member['first_name']),
                            $member['middle_name'] ?? null,
                            trim($member['last_name']),
                            $member['gender'] ?? null,
                            $member['birth_date'] ?? null,
                            $member['relationship'] ?? null,
                            $member['civil_status'] ?? null,
                            $member['occupation'] ?? null
                        ]);
                    }
                }

                $pdo->commit();

                echo json_encode([
                    'success' => true,
                    'message' => 'Resident created successfully'
                ]);

            } catch (Exception $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }

            break;
        case 'update_resident':
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
            foreach ($required as $field => $label) {
                if (empty(trim($data[$field] ?? ''))) {
                    $errors[$field] = "$label is required";
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
            try {
                $pdo->beginTransaction();

                $stmt = $pdo->prepare("
            UPDATE residents SET
                household_no = ?,
                barangay_id = ?,
                first_name = ?,
                middle_name = ?,
                last_name = ?,
                gender = ?,
                birth_date = ?,
                civil_status = ?,
                contact_number = ?,
                address = ?,
                occupation = ?
            WHERE id = ?
        ");

                $stmt->execute([
                    trim($data['household_no']),
                    (int) $data['barangay_id'],
                    trim($data['first_name']),
                    $data['middle_name'] ?? null,
                    trim($data['last_name']),
                    $data['gender'],
                    $data['birth_date'],
                    $data['civil_status'],
                    $data['contact_number'] ?? null,
                    $data['address'] ?? null,
                    $data['occupation'] ?? null,
                    (int) $data['resident_id']
                ]);

                // Delete existing family members
                $deleteStmt = $pdo->prepare("DELETE FROM family_members WHERE resident_id = ?");
                $deleteStmt->execute([(int) $data['resident_id']]);

                // Insert updated family members
                if (!empty($data['family']) && is_array($data['family'])) {
                    $familyStmt = $pdo->prepare("
                INSERT INTO family_members (
                    resident_id,
                    first_name,
                    middle_name,
                    last_name,
                    birth_date,
                    relationship,
                    civil_status,
                    occupation
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

                    foreach ($data['family'] as $member) {
                        if (empty($member['first_name']) || empty($member['last_name'])) {
                            continue;
                        }
                        $familyStmt->execute([
                            (int) $data['resident_id'],
                            trim($member['first_name']),
                            $member['middle_name'] ?? null,
                            trim($member['last_name']),
                            $member['birth_date'] ?? null,
                            $member['relationship'] ?? null,
                            $member['civil_status'] ?? null,
                            $member['occupation'] ?? null
                        ]);
                    }
                }

                $pdo->commit();

                echo json_encode([
                    'success' => true,
                    'message' => 'Resident updated successfully'
                ]);

            } catch (Exception $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
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
