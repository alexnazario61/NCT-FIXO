<?php
require_once '../config/connection.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'all':
        // Busca empresas (mantendo os IDs originais)
        $result = $conn->query("
            SELECT 
                u.id,
                u.name, 
                'company' as type,
                COUNT(DISTINCT sc.id) as sub_clients_count,
                COUNT(DISTINCT pn.id) as numbers_count
            FROM users u
            LEFT JOIN sub_clients sc ON u.id = sc.company_id
            LEFT JOIN phone_numbers pn ON u.id = pn.user_id OR sc.id = pn.sub_client_id
            WHERE u.is_company = 1
            GROUP BY u.id
        ");
        
        $companies = [];
        while ($row = $result->fetch_assoc()) {
            $companies[] = $row;
        }

        // Busca clientes individuais (mantendo os IDs originais)
        $result = $conn->query("
            SELECT 
                u.id,
                u.name, 
                'individual' as type,
                0 as sub_clients_count,
                COUNT(pn.id) as numbers_count
            FROM users u
            LEFT JOIN phone_numbers pn ON u.id = pn.user_id
            WHERE (u.is_company = 0 OR u.is_company IS NULL)
              AND u.id NOT IN (SELECT DISTINCT company_id FROM sub_clients)
            GROUP BY u.id
            HAVING COUNT(pn.id) > 0
        ");
        
        $individuals = [];
        while ($row = $result->fetch_assoc()) {
            $individuals[] = $row;
        }

        echo json_encode(array_merge($companies, $individuals));
        break;

    case 'companies':
        $result = $conn->query("
            SELECT
                u.id,
                u.name, 
                COUNT(DISTINCT sc.id) as sub_clients_count,
                (
                    SELECT COUNT(*)
                    FROM phone_numbers pn
                    LEFT JOIN sub_clients sc2 ON sc2.id = pn.sub_client_id
                    WHERE pn.user_id = u.id OR sc2.company_id = u.id
                ) as numbers_count
            FROM users u
            LEFT JOIN sub_clients sc ON u.id = sc.company_id
            WHERE u.is_company = 1
            GROUP BY u.id
            ORDER BY u.name
        ");
        
        $companies = [];
        while ($row = $result->fetch_assoc()) {
            $companies[] = $row;
        }
        
        echo json_encode($companies);
        break;

    case 'sub_clients':
        $company_id = $_GET['company_id'] ?? 0;
        if ($company_id) {
            $stmt = $conn->prepare("
                SELECT 
                    sc.*,
                    COUNT(pn.id) as numbers_count
                FROM sub_clients sc
                LEFT JOIN phone_numbers pn ON sc.id = pn.sub_client_id
                WHERE sc.company_id = ?
                GROUP BY sc.id
                ORDER BY sc.name
            ");
            $stmt->bind_param("i", $company_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $subClients = [];
            while ($row = $result->fetch_assoc()) {
                $subClients[] = $row;
            }
            
            echo json_encode($subClients);
        }
        break;

    case 'update':
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        
        if ($id && $name) {
            $stmt = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
            $stmt->bind_param("si", $name, $id);
            $stmt->execute();
            echo json_encode(['success' => true]);
        }
        break;
        
    case 'update_sub_client':
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        
        if ($id && $name) {
            $stmt = $conn->prepare("UPDATE sub_clients SET name = ? WHERE id = ?");
            $stmt->bind_param("si", $name, $id);
            $stmt->execute();
            echo json_encode(['success' => true]);
        }
        break;

    case 'individuals':
        $result = $conn->query("
            SELECT 
                u.id,
                u.name,
                COUNT(pn.id) as numbers_count
            FROM users u
            LEFT JOIN phone_numbers pn ON u.id = pn.user_id
            WHERE (u.is_company = 0 OR u.is_company IS NULL)
            AND NOT EXISTS (
                SELECT 1 FROM sub_clients sc WHERE sc.company_id = u.id
            )
            GROUP BY u.id
            ORDER BY u.name
        ");
        
        $individuals = [];
        while ($row = $result->fetch_assoc()) {
            $individuals[] = $row;
        }
        
        echo json_encode($individuals);
        break;

    case 'get_numbers':
        $id = $_GET['id'] ?? 0;
        if ($id) {
            $stmt = $conn->prepare("
                SELECT number, id 
                FROM phone_numbers 
                WHERE user_id = ? 
                AND sub_client_id IS NULL
                ORDER BY number
            ");
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

    case 'search':
        $query = $_GET['q'] ?? '';
        if ($query) {
            $stmt = $conn->prepare("
                SELECT id, name, is_company 
                FROM users 
                WHERE name LIKE ? 
                ORDER BY name 
                LIMIT 10
            ");
            $likeQuery = "%$query%";
            $stmt->bind_param("s", $likeQuery);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            
            echo json_encode($users);
        }
        break;

    case 'get':
        $id = $_GET['id'] ?? 0;
        if ($id) {
            $stmt = $conn->prepare("
                SELECT u.*, 
                       CASE WHEN u.is_company = 1 THEN 'company' ELSE 'individual' END as type
                FROM users u 
                WHERE u.id = ?
            ");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            echo json_encode($result->fetch_assoc());
        }
        break;

    case 'get_sub_client':
        $id = $_GET['id'] ?? 0;
        if ($id) {
            $stmt = $conn->prepare("
                SELECT sc.*, u.name as company_name, u.id as company_id
                FROM sub_clients sc
                JOIN users u ON sc.company_id = u.id
                WHERE sc.id = ?
            ");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            echo json_encode($result->fetch_assoc());
        }
        break;
        
    case 'create':
        $name = $_POST['name'] ?? '';
        $type = $_POST['type'] ?? 'individual';
        
        if (empty($name)) {
            echo json_encode(['success' => false, 'error' => 'Nome é obrigatório']);
            break;
        }
        
        try {
            $is_company = ($type == 'company') ? 1 : 0;
            $stmt = $conn->prepare("INSERT INTO users (name, is_company) VALUES (?, ?)");
            $stmt->bind_param("si", $name, $is_company);
            $stmt->execute();
            $client_id = $conn->insert_id;
            
            echo json_encode([
                'success' => true, 
                'id' => $client_id,
                'message' => 'Cliente criado com sucesso'
            ]);
        } catch (Exception $e) {
            if ($conn->errno === 1062) { // Duplicate entry error
                echo json_encode(['success' => false, 'error' => 'Cliente com este nome já existe']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Erro ao criar cliente: ' . $e->getMessage()]);
            }
        }
        break;
        
    case 'create_sub_client':
        $name = $_POST['name'] ?? '';
        $company_id = $_POST['company_id'] ?? 0;
        
        if (empty($name) || empty($company_id)) {
            echo json_encode(['success' => false, 'error' => 'Nome e empresa são obrigatórios']);
            break;
        }
        
        try {
            $stmt = $conn->prepare("INSERT INTO sub_clients (name, company_id) VALUES (?, ?)");
            $stmt->bind_param("si", $name, $company_id);
            $stmt->execute();
            $subclient_id = $conn->insert_id;
            
            echo json_encode([
                'success' => true, 
                'id' => $subclient_id,
                'message' => 'Sub-cliente criado com sucesso'
            ]);
        } catch (Exception $e) {
            if ($conn->errno === 1062) {
                echo json_encode(['success' => false, 'error' => 'Sub-cliente com este nome já existe para esta empresa']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Erro ao criar sub-cliente: ' . $e->getMessage()]);
            }
        }
        break;

    case 'delete_client':
        $id = $_POST['id'] ?? 0;
        
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'ID do cliente não fornecido']);
            break;
        }
        
        $conn->begin_transaction();
        try {
            // Verificar se é uma empresa com subclientes
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM sub_clients WHERE company_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            if ($row['count'] > 0) {
                echo json_encode(['success' => false, 'error' => 'Não é possível excluir uma empresa com sub-clientes. Remova os sub-clientes primeiro.']);
                break;
            }
            
            // Liberar os números associados ao cliente
            $stmt = $conn->prepare("UPDATE phone_numbers SET user_id = NULL WHERE user_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            // Excluir o cliente
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Cliente excluído com sucesso']);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'error' => 'Erro ao excluir cliente: ' . $e->getMessage()]);
        }
        break;
        
    case 'delete_sub_client':
        $id = $_POST['id'] ?? 0;
        
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'ID do sub-cliente não fornecido']);
            break;
        }
        
        $conn->begin_transaction();
        try {
            // Liberar os números associados ao sub-cliente
            $stmt = $conn->prepare("UPDATE phone_numbers SET sub_client_id = NULL WHERE sub_client_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            // Excluir o sub-cliente
            $stmt = $conn->prepare("DELETE FROM sub_clients WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Sub-cliente excluído com sucesso']);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'error' => 'Erro ao excluir sub-cliente: ' . $e->getMessage()]);
        }
        break;
        
    case 'unassigned_numbers':
        // Buscar números atribuídos a empresas sem subcliente específico
        $result = $conn->query("
            SELECT 
                pn.id, 
                pn.number, 
                u.id as user_id, 
                u.name as user_name,
                'company' as type
            FROM phone_numbers pn
            JOIN users u ON pn.user_id = u.id
            WHERE u.is_company = 1 
            AND pn.sub_client_id IS NULL
            ORDER BY u.name, pn.number
        ");
        
        $numbers = [];
        while ($row = $result->fetch_assoc()) {
            $numbers[] = $row;
        }
        
        echo json_encode($numbers);
        break;
}
