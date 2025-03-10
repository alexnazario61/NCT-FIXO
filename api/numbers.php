<?php
require_once '../config/connection.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'available':
        try {
            $result = $conn->query("
                SELECT * FROM phone_numbers 
                WHERE user_id IS NULL 
                AND sub_client_id IS NULL 
                ORDER BY number
            ");
            
            $numbers = [];
            while ($row = $result->fetch_assoc()) {
                $numbers[] = $row;
            }
            
            echo json_encode($numbers);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao buscar números disponíveis']);
        }
        break;

    case 'assign':
        $numbers = $_POST['numbers'] ?? [];
        $client_id = $_POST['client_id'] ?? null;
        $sub_client_id = $_POST['sub_client_id'] ?? null;
        
        // Verificar se os números e cliente foram fornecidos
        if (empty($numbers) || (!$client_id && !$sub_client_id)) {
            echo json_encode([
                'success' => false, 
                'error' => 'Números e cliente/sub-cliente são obrigatórios'
            ]);
            break;
        }
        
        if (!is_array($numbers)) {
            $numbers = [$numbers];
        }
        
        $conn->begin_transaction();
        try {
            if ($sub_client_id) {
                // Atualiza para sub-cliente
                $stmt = $conn->prepare("UPDATE phone_numbers SET sub_client_id = ?, user_id = NULL WHERE number = ? AND (user_id IS NULL OR sub_client_id IS NULL)");
                foreach ($numbers as $number) {
                    $stmt->bind_param("is", $sub_client_id, $number);
                    $stmt->execute();
                }
            } else {
                // Atualiza para cliente normal
                $stmt = $conn->prepare("UPDATE phone_numbers SET user_id = ?, sub_client_id = NULL WHERE number = ? AND (user_id IS NULL OR sub_client_id IS NULL)");
                foreach ($numbers as $number) {
                    $stmt->bind_param("is", $client_id, $number);
                    $stmt->execute();
                }
            }
            
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Números atribuídos com sucesso']);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'error' => 'Erro ao atribuir números: ' . $e->getMessage()]);
        }
        break;
        
    case 'remove':
        $number = $_POST['number'] ?? '';
        if ($number) {
            $stmt = $conn->prepare("UPDATE phone_numbers SET user_id = NULL, sub_client_id = NULL WHERE number = ?");
            $stmt->bind_param("s", $number);
            $stmt->execute();
            echo json_encode(['success' => true]);
        }
        break;

    case 'get_by_client':
        $id = $_GET['id'] ?? 0;
        $is_sub_client = $_GET['is_sub_client'] ?? false;
        
        if ($id) {
            if ($is_sub_client) {
                $stmt = $conn->prepare("
                    SELECT * FROM phone_numbers 
                    WHERE sub_client_id = ?
                    ORDER BY number
                ");
            } else {
                $stmt = $conn->prepare("
                    SELECT * FROM phone_numbers 
                    WHERE user_id = ? AND sub_client_id IS NULL
                    ORDER BY number
                ");
            }
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $numbers = [];
            while ($row = $result->fetch_assoc()) {
                $numbers[] = $row;
            }
            echo json_encode($numbers);
        }
        break;

    case 'create':
        $numbers = $_POST['numbers'] ?? '';
        
        if (empty($numbers)) {
            echo json_encode(['success' => false, 'error' => 'Nenhum número fornecido']);
            break;
        }
        
        $numbersList = array_map('trim', explode(',', $numbers));
        $numbersList = array_filter($numbersList, function($item) {
            return !empty($item) && preg_match('/^\d+$/', $item);
        });
        
        if (empty($numbersList)) {
            echo json_encode(['success' => false, 'error' => 'Nenhum número válido fornecido']);
            break;
        }
        
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("INSERT IGNORE INTO phone_numbers (number) VALUES (?)");
            $inserted = 0;
            $duplicates = 0;
            
            foreach ($numbersList as $number) {
                $stmt->bind_param("s", $number);
                $stmt->execute();
                
                if ($conn->affected_rows > 0) {
                    $inserted++;
                } else {
                    $duplicates++;
                }
            }
            
            $conn->commit();
            echo json_encode([
                'success' => true, 
                'inserted' => $inserted,
                'duplicates' => $duplicates,
                'message' => "Números adicionados com sucesso. Inseridos: $inserted, Duplicados: $duplicates"
            ]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'error' => 'Erro ao adicionar números: ' . $e->getMessage()]);
        }
        break;

    case 'assign_reserved':
        $number = $_POST['number'] ?? '';
        $sub_client_id = $_POST['sub_client_id'] ?? null;
        
        if ($number && $sub_client_id) {
            $stmt = $conn->prepare("UPDATE phone_numbers SET sub_client_id = ? WHERE number = ?");
            $stmt->bind_param("is", $sub_client_id, $number);
            $stmt->execute();
            echo json_encode(['success' => true]);
        }
        break;

    case 'unassigned':
        // Números atribuídos a empresas mas sem subcliente específico
        $result = $conn->query("
            SELECT 
                pn.number, 
                pn.id,
                u.id as user_id,
                u.name as company_name
            FROM phone_numbers pn
            JOIN users u ON pn.user_id = u.id
            WHERE pn.sub_client_id IS NULL
            AND u.is_company = 1
            ORDER BY u.name, pn.number
        ");
        
        $unassignedNumbers = [];
        while ($row = $result->fetch_assoc()) {
            $unassignedNumbers[] = $row;
        }
        
        echo json_encode($unassignedNumbers);
        break;
        
    case 'company_numbers':
        // Buscar números reservados para uma empresa específica
        $company_id = $_GET['company_id'] ?? 0;
        if ($company_id) {
            $stmt = $conn->prepare("
                SELECT 
                    pn.id, 
                    pn.number, 
                    u.id as user_id,
                    u.name as company_name
                FROM phone_numbers pn
                JOIN users u ON pn.user_id = u.id
                WHERE pn.sub_client_id IS NULL
                AND u.is_company = 1
                AND u.id = ?
                ORDER BY pn.number
            ");
            $stmt->bind_param("i", $company_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $reservedNumbers = [];
            while ($row = $result->fetch_assoc()) {
                $reservedNumbers[] = $row;
            }
            
            echo json_encode($reservedNumbers);
        } else {
            echo json_encode([]);
        }
        break;
}
