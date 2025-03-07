<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/connection.php';

$csv_file = '../Linhas-NCTfixo.csv';

if (!file_exists($csv_file)) {
    die("Arquivo CSV não encontrado!");
}

$handle = fopen($csv_file, 'r');

if ($handle !== FALSE) {
    try {
        // Inicia a transação
        $pdo->beginTransaction();

        // Limpa as tabelas antes de importar
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        $pdo->exec("TRUNCATE TABLE phone_numbers");
        $pdo->exec("TRUNCATE TABLE users");
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        
        // Pula o cabeçalho
        fgetcsv($handle, 0, ';');
        
        $users = [];
        $numbers = [];
        $count = 0;
        
        while (($data = fgetcsv($handle, 0, ';')) !== FALSE) {
            $number = preg_replace('/[^0-9]/', '', trim($data[0]));
            $originalName = trim($data[1]);
            
            if (!empty($number)) {
                $user_id = null;
                
                // Verifica se o número já foi processado
                if (isset($numbers[$number])) {
                    continue; // Pula números duplicados
                }
                
                if (!empty($originalName)) {
                    // Verifica se é empresa (contém parênteses com "empresa")
                    $isCompany = preg_match('/\(empresa\)/i', $originalName);
                    
                    // Remove a indicação de empresa do nome
                    $name = preg_replace('/\s*\(empresa\)\s*/i', '', $originalName);
                    
                    if (isset($users[$name])) {
                        $user_id = $users[$name];
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO users (name, is_company) VALUES (?, ?)");
                        $stmt->execute([$name, $isCompany ? 1 : 0]);
                        $user_id = $pdo->lastInsertId();
                        $users[$name] = $user_id;
                    }
                }
                
                // Marca o número como processado
                $numbers[$number] = true;
                
                // Insere número
                $stmt = $pdo->prepare("INSERT INTO phone_numbers (number, user_id) VALUES (?, ?)");
                $stmt->execute([$number, $user_id]);
                $count++;
            }
        }
        
        // Commit apenas se chegou até aqui sem erros
        if ($pdo->inTransaction()) {
            $pdo->commit();
            echo "Importação concluída com sucesso! Total de números importados: " . $count;
        }
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo "Erro durante a importação: " . $e->getMessage();
    } finally {
        fclose($handle);
    }
}
