<?php
require_once '../config/connection.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'available':
        try {
            $stmt = $pdo->query("
                SELECT * FROM phone_numbers 
                WHERE user_id IS NULL 
                AND sub_client_id IS NULL 
                ORDER BY number
            ");
            $numbers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($numbers);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao buscar números disponíveis']);
        }
        break;

    case 'assign':
        $numbers = $_POST['numbers'] ?? [];
        $user_id = $_POST['user_id'] ?? null;
        
        if ($user_id && !empty($numbers)) {
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare("UPDATE phone_numbers SET user_id = ? WHERE number = ?");
                foreach ($numbers as $number) {
                    $stmt->execute([$user_id, $number]);
                }
                $pdo->commit();
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
        break;
        
    case 'remove':
        $number = $_POST['number'] ?? '';
        if ($number) {
            $stmt = $pdo->prepare("UPDATE phone_numbers SET user_id = NULL WHERE number = ?");
            $stmt->execute([$number]);
            echo json_encode(['success' => true]);
        }
        break;

    case 'get_by_client':
        $id = $_GET['id'] ?? 0;
        $is_sub_client = $_GET['is_sub_client'] ?? false;
        
        if ($id) {
            if ($is_sub_client) {
                $stmt = $pdo->prepare("
                    SELECT * FROM phone_numbers 
                    WHERE sub_client_id = ?
                    ORDER BY number
                ");
            } else {
                $stmt = $pdo->prepare("
                    SELECT * FROM phone_numbers 
                    WHERE user_id = ? AND sub_client_id IS NULL
                    ORDER BY number
                ");
            }
            $stmt->execute([$id]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    case 'create':
        $numbers = $_POST['numbers'] ?? '';
        $numbersList = array_map('trim', explode(',', $numbers));
        
        if (!empty($numbersList)) {
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare("INSERT INTO phone_numbers (number) VALUES (?)");
                foreach ($numbersList as $number) {
                    if (!empty($number)) {
                        $stmt->execute([$number]);
                    }
                }
                $pdo->commit();
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
        break;

    case 'assign_reserved':
        $number = $_POST['number'] ?? '';
        $sub_client_id = $_POST['sub_client_id'] ?? null;
        
        if ($number && $sub_client_id) {
            $stmt = $pdo->prepare("UPDATE phone_numbers SET sub_client_id = ? WHERE number = ?");
            $stmt->execute([$sub_client_id, $number]);
            echo json_encode(['success' => true]);
        }
        break;
}
