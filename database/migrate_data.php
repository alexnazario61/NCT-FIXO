<?php
require_once '../config/connection.php';

try {
    // Array de empresas conhecidas
    $companies = [
        'FALCONET' => 'FALCO NET',
        'IDEAL FIBER' => 'IDEAL FIBER',
        'LYON FIBER' => 'LYON FIBER',
        'LYONFIBER' => 'LYON FIBER',
        'LIVE CONNECT' => 'LIVE CONNECT',
        'LIVE CONECT' => 'LIVE CONNECT',
        'ERICK TELECOM' => 'ERICK TELECOM'
    ];

    // Primeiro, marcar os usuários existentes como não-empresas
    $pdo->exec("UPDATE users SET is_company = 0");

    // Criar empresas se não existirem
    $company_ids = [];
    foreach ($companies as $short => $full) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE name = ?");
        $stmt->execute([$full]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $company_ids[$short] = $existing['id'];
            $pdo->prepare("UPDATE users SET is_company = 1, company_type = ? WHERE id = ?")
                ->execute([$full, $existing['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (name, is_company, company_type) VALUES (?, 1, ?)");
            $stmt->execute([$full, $full]);
            $company_ids[$short] = $pdo->lastInsertId();
        }
    }

    // Buscar usuários que são subclientes
    $stmt = $pdo->query("SELECT id, name FROM users WHERE is_company = 0");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as $user) {
        $name = $user['name'];
        
        // Procurar por padrões como "(EMPRESA)" ou "EMPRESA (Nome)"
        if (preg_match('/\((.*?)\)/', $name, $matches)) {
            $company_name = trim($matches[1]);
            $client_name = trim(str_replace('('.$matches[1].')', '', $name));
            
            // Encontrar ID da empresa
            $company_id = null;
            foreach ($companies as $short => $full) {
                if (stripos($company_name, $short) !== false) {
                    $company_id = $company_ids[$short];
                    break;
                }
            }
            
            if ($company_id && !empty($client_name)) {
                // Verificar se o subcliente já existe
                $stmt = $pdo->prepare("SELECT id FROM sub_clients WHERE name = ? AND company_id = ?");
                $stmt->execute([$client_name, $company_id]);
                $existing_sub = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$existing_sub) {
                    // Criar subclient
                    $stmt = $pdo->prepare("INSERT INTO sub_clients (name, company_id) VALUES (?, ?)");
                    $stmt->execute([$client_name, $company_id]);
                    $sub_client_id = $pdo->lastInsertId();
                    
                    // Atualizar números
                    $stmt = $pdo->prepare("UPDATE phone_numbers SET sub_client_id = ? WHERE user_id = ?");
                    $stmt->execute([$sub_client_id, $user['id']]);
                }
            }
        }
    }

    echo "Migração concluída com sucesso!";

} catch (Exception $e) {
    echo "Erro durante a migração: " . $e->getMessage();
    error_log($e->getMessage());
    error_log($e->getTraceAsString());
}
