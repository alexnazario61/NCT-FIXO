<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/connection.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        $stmt = $pdo->query("
            SELECT 
                u.*, 
                COUNT(p.id) as phone_count 
            FROM users u 
            LEFT JOIN phone_numbers p ON u.id = p.user_id 
            GROUP BY u.id
            ORDER BY u.name
        ");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'get':
        $id = $_GET['id'] ?? 0;
        if ($id) {
            $stmt = $pdo->prepare("
                SELECT u.*, p.number 
                FROM users u 
                LEFT JOIN phone_numbers p ON u.id = p.user_id 
                WHERE u.id = ?
            ");
            $stmt->execute([$id]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    case 'create':
        $name = $_POST['name'] ?? '';
        if ($name) {
            $stmt = $pdo->prepare("INSERT INTO users (name) VALUES (?)");
            $stmt->execute([$name]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        }
        break;
}
