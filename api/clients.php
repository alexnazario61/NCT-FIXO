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
            SELECT DISTINCT
                u.id,
                u.name, 
                COUNT(DISTINCT sc.id) as sub_clients_count,
                COUNT(DISTINCT pn.id) as numbers_count
            FROM users u
            LEFT JOIN sub_clients sc ON u.id = sc.company_id
            LEFT JOIN phone_numbers pn ON (u.id = pn.user_id OR sc.id = pn.sub_client_id)
            WHERE u.is_company = 1
            GROUP BY u.id, u.name
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
                COUNT(DISTINCT pn.id) as numbers_count
            FROM users u
            LEFT JOIN phone_numbers pn ON u.id = pn.user_id
            WHERE u.is_company = 0 
            AND u.id NOT IN (
                SELECT DISTINCT company_id FROM sub_clients
            )
            AND u.id NOT IN (
                SELECT DISTINCT p.user_id 
                FROM phone_numbers p 
                WHERE p.sub_client_id IS NOT NULL
            )
            GROUP BY u.id, u.name
            HAVING numbers_count > 0
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
}
