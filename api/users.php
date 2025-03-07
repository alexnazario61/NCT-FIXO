<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/connection.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        $result = $conn->query("
            SELECT 
                u.*, 
                COUNT(p.id) as phone_count 
            FROM users u 
            LEFT JOIN phone_numbers p ON u.id = p.user_id 
            GROUP BY u.id
            ORDER BY u.name
        ");
        
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        
        echo json_encode($users);
        break;

    case 'get':
        $id = $_GET['id'] ?? 0;
        if ($id) {
            $stmt = $conn->prepare("
                SELECT u.*, p.number 
                FROM users u 
                LEFT JOIN phone_numbers p ON u.id = p.user_id 
                WHERE u.id = ?
            ");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $userData = [];
            while ($row = $result->fetch_assoc()) {
                $userData[] = $row;
            }
            
            echo json_encode($userData);
        }
        break;

    case 'create':
        $name = $_POST['name'] ?? '';
        if ($name) {
            $stmt = $conn->prepare("INSERT INTO users (name) VALUES (?)");
            $stmt->bind_param("s", $name);
            $stmt->execute();
            echo json_encode(['success' => true, 'id' => $conn->insert_id]);
        }
        break;
}
