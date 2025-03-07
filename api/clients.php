<?php
require_once '../config/connection.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'all':
        // Busca empresas (mantendo os IDs originais)
        $companies = $pdo->query("
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
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Busca clientes individuais (mantendo os IDs originais)
        $individuals = $pdo->query("
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
        ")->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(array_merge($companies, $individuals));
        break;

    case 'companies':
        $stmt = $pdo->query("
            SELECT 
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
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'sub_clients':
        $company_id = $_GET['company_id'] ?? 0;
        if ($company_id) {
            $stmt = $pdo->prepare("
                SELECT 
                    sc.*,
                    COUNT(pn.id) as numbers_count
                FROM sub_clients sc
                LEFT JOIN phone_numbers pn ON sc.id = pn.sub_client_id
                WHERE sc.company_id = ?
                GROUP BY sc.id
                ORDER BY sc.name
            ");
            $stmt->execute([$company_id]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    case 'update':
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        
        if ($id && $name) {
            $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
            $stmt->execute([$name, $id]);
            echo json_encode(['success' => true]);
        }
        break;
        
    case 'update_sub_client':
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        
        if ($id && $name) {
            $stmt = $pdo->prepare("UPDATE sub_clients SET name = ? WHERE id = ?");
            $stmt->execute([$name, $id]);
            echo json_encode(['success' => true]);
        }
        break;

    case 'individuals':
        $stmt = $pdo->query("
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
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'get_numbers':
        $id = $_GET['id'] ?? 0;
        if ($id) {
            $stmt = $pdo->prepare("
                SELECT number, id 
                FROM phone_numbers 
                WHERE user_id = ? 
                AND sub_client_id IS NULL
                ORDER BY number
            ");
            $stmt->execute([$id]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    case 'search':
        $query = $_GET['q'] ?? '';
        if ($query) {
            $stmt = $pdo->prepare("
                SELECT id, name, is_company 
                FROM users 
                WHERE name LIKE ? 
                ORDER BY name 
                LIMIT 10
            ");
            $stmt->execute(["%$query%"]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    case 'get':
        $id = $_GET['id'] ?? 0;
        if ($id) {
            $stmt = $pdo->prepare("
                SELECT u.*, 
                       CASE WHEN u.is_company = 1 THEN 'company' ELSE 'individual' END as type
                FROM users u 
                WHERE u.id = ?
            ");
            $stmt->execute([$id]);
            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        }
        break;
}
