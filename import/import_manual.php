<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/connection.php';

$data = <<<EOD
11 5197 2777	Camila Pereira de Mariz
11 5197 2785	
11 5197 2786	Odair Jose dos Santos
11 5197 2788	Maico Renan Gonçalves de Araújo da Cunha
11 5197 2789	Maria Julieta dos Santos
11 5197 2790	
11 5197 2791	Vanessa Maria da Rosa
11 5197 2791	Vagner de oliveira silva (LYONFIBER)
11 5197 2793	Pizzaria e Esfiharia Martins LTDA
11 5197 2794	Guilherme Gomes Zani
11 5197 2795	Ana Paula Alves de Souza
11 5197 2796	
11 5197 2798	Maiza Aparecida Prado dos Santos (LIVE CONECT TELECOMUNICACOES)
11 5197 2799	eduardo massayoshi
11 5197 2801	
11 5197 2802	André Bernardo de Oliveira
11 5197 2803	Ramony Kally Venâncio de Souza
11 5197 2804	Renato Nunes De Souza
11 5197 2805	Edgar Baptista Ferreira
11 5197 2806	leandro ayres
11 5197 2807	
11 5197 2808	Ludrut Tech Ltda
11 5197 2809	Claudiana Soares de Souza (LIVE CONECT TELECOMUNICACOES)
11 5197 2810	Claudia Kazumi Kobayashi 
11 5197 2811	Maria Rafaela Silva Santos
11 5197 2812	Maria Marluce lemos do nascimento (IDEAL FIBER)
11 5197 2813	José Antônio do Nascimento (FALCONET)
11 5197 2814	Aurelio de Oliveria Zuliano
11 5197 2815	
11 5197 2816	Cíntia Noêmia de Queiroz de Paula
11 5197 2817	Nayara Alves de Padua Ramos
11 5197 2818	Claudiney Alves da Silva (FALCONET)
11 5197 2819	Edilson de Souza
11 5197 2820	Geilson da Costa
11 5197 2821	
11 5197 2822	ingrid aparecida duarte guimarães (IDEAL FIBER)
11 5197 2823	Instituto Rogerio Banks de Reabilitação 
11 5197 2824	Instituto Rogerio Banks de Reabilitação 
11 5197 2825	adalberto yamundo
11 5197 2826	ricardo Secario
11 5197 2828	
11 5197 2830	Marcos Norberto
11 5197 2831	Marcelo Aparecido Andrade Miranda
11 5197 2832	Jefferson Ferreira da Silva
11 5197 2833	Patricia Turato de Oliveira
11 4744 9039	Maria Passos dos Reis
11 5197 2834	Leandro Feitoza de Carvalho (IDEAL FIBER)
11 5197 2835	Israel de Almeida
11 5197 2836	Jurandi Govea da Silva 
11 5197 2837	Willians Araújo Barbosa (IDEAL FIBER)
11 5197 2838	Sebastiana da silva aquino (LYONFIBER)
11 5197 2839	ELIUDE FERREIRA GOMES (LIVE CONECT TELECOMUNICACOES)
11 5197 2840	Meire da Silva Aquino (LYONFIBER)
11 5197 2841	Cáritas Regional de Suzano
11 5197 2842	FABRICIA BRANDAO MARQUES (LIVE CONECT TELECOMUNICACOES)
11 5197 2843	Sonia Aparecida da Silva Guimarães (IDEAL FIBER)
11 5197 2844	Fábio Antônio de Moraes
11 5197 2845	Fábio Antônio de Moraes
11 5197 2846	Albimaer Zuza Batista
11 5197 2847	Cáritas Regional de Suzano unidade 2 Buenos Aires
11 5197 2850	timetope olumide abidakun
11 5197 2851	EDIMILSON SALES (LIVE CONECT TELECOMUNICACOES)
11 5197 2852	Wagner Casarini (FALCONET)
11 5197 2853	Glaucia Brunetto Dantas (FALCONET)
11 5197 2854	ELIZEU MARCELINO DOS SANTOS (FALCONET)
11 5197 2855	Winnie Souza Costa Agostinho
11 5197 2856	
11 5197 2857	Jessica Oliveira Santos (FALCONET)
11 4746 8597 	
11 5197 2858	Priscila de Oliveira Aquino (FALCONET)
11 5197 2859	
11 5197 2860	Leonardo jesus de oliveira (LIVE CONECT TELECOMUNICACOES)
11 5197 2861	Bruna dos Santos Conceição
11 5197 2862	Ana Paula Ribeiro
11 5197 2863	Maria das Graças dos Santos Alfres (LIVE CONECT TELECOMUNICACOES)
11 5197 2864	Regiane Abreu Faria
11 5197 2865	Maria Eduarda Ferreira da Silva (FALCONET)
11 5197 2866	NELSON GONÇALVES DE SOUZA (FALCONET)
11 5197 2867	Valdinei de Jesus Santos (FALCONET)
11 5197 2868	Matheus Lyon da Silva Goularte Maia Garcia 
11 5197 2869	MAURICIO SEVERO DE SOUZA (FALCONET)
11 5197 2870	SEBASTIÃO SANTANA DO ESPIRITO SANTO (FALCONET)
11 5197 2871	LIVE CONECT TELECOMUNICACOES (LIVE CONECT TELECOMUNICACOES)
11 5197 2872	LIVE CONECT TELECOMUNICACOES (LIVE CONECT TELECOMUNICACOES)
11 5197 2873	
11 5197 2874	
11 5197 2875	Fabiana Carmo Lira (LIVE CONECT TELECOMUNICACOES)
11 5197 2876	Maria Auxiliadora da Silva Araújo
11 5197 2878	Fortunato Alves Feitoza da Costa
11 5197 2879	beatriz dos santos veiga (FALCONET)
11 5197 2881	Silvera de Souza Santos
11 5197 2882	Guilherme Carlos Alves de Miranda
11 5197 2883	bianca aparecida correa (LYON FIBER)
11 5197 2884	Aguinaldo Alves Cordeiro (LIVE CONECT TELECOMUNICACOES)
11 5197 2885	mauricio severo  (FALCONET)
11 5197 2886	
11 5197 2887	Maria das Dores Pereira da Silva (provissoria) 
11 5197 2888	Erika Suellen Goes (LYON FIBER)
11 5197 2889	JESSICA RODRIGUES QUINTILIANO (LIVE CONECT TELECOMUNICACOES)
11 5197 2890	Gilmar
11 5197 2891	
11 5197 2892	telma Maria Ribeiro 2 (IDEAL FIBER)
11 4746 8696	Alexandre Aguiar de Mattos
11 4746 8113	CLEUSA APARECIDA ARAUJO DA SILVA (FALCONET)
11 4746 8169	Ivomar Andrade (LYON FIBER)
11 4751 5864	Humberto Yago de Almeida -  (Ceara Baterias Suzano Ltda )
11 4746 8611	
11 4746 8561	iara dias vicente (LYON FIBER)
11 4751 4791	Karina stephanie da silva braz diniz (IDEAL FIBER)
11 4746 8562	iraci chaves da silva (FALCONET)
11 4742 6725	NATHALIA SILVA (IDEAL FIBER)
11 4746 8542	joelita ferreira nunes (IDEAL FIBER)
11 4746 8615	Maria Fama de Oliverira (LYON FIBER)
11 4746 8609	Ednaldo Ferreira (LYON FIBER)
11 4746 8581	IDEL FIBER (Shirley Pimentel dos Santos)
11 4745 7303	Maria Geane santos da Silva (FALCONET)
11 4743 9359	João Luiz de Camargo
11 4746 8610	
11 4746 8521	
11 4746 8597	
11 4746 8608	Angelina Ferreira Santos Vargas (FALCONET)
11 4743 9169	João Vitor Silva
11 4748 0340	Neide Maria Pereira de Souza
11 4748 0172	J.M Holandas Drogaria Eireli - Graciane Pereira Gonzaga
11 4748 0049	Rogério da Conceição
11 4748 0128	Francisco Pereira dos Santos
11 2581 4258	Daniel espinosa da costa (IDEAL FIBER)
11 3090 3662	Jefferson Costa da Silva Viana
11 2562 3455	Eliane Gomes Alves Diniz (IDEAL FIBER)
11 4751 1011	Jhonatan Guilherme Carvalho da Silva 
11 4746 8193	Luiz Antonio  Pereira da Conceção  (FALCONET)
11 5199 1456	Izabel Josefa da Silva (LIVE CONECT TELECOMUNICACOES)
11 5199 3808	Tainá Alves do Nascimento da Silva (FALCONET)
11 5199 3844	Telma Maria Ribeiro (IDEAL FIBER)
11 5199 3845	
11 5199 4249	
11 2071 5061	Jessica Cristina Honorato dos Santos (LYON FIBER)
11 5199 4335	Francisca olinda da silva (FALCONET)
11 2295 3175	Vilarino de Oliveira Pereira
11 5199 4341	Flavio Makoto Aya
11 4743 9154	José Bezerra Alves Neto
11 4742 6425	José Galdino da Costa
11 2052 8187	Manoel Vitor de Oliveira (LYON FIBER)
11 4751 4699	Emerson Pinheiro Martins 
11 4748 0504	Pizzaria e Esfiharia Martins LTDA
11 4746 8197	
11 3589 4200	
11 5199 4342	Diego Renan Arnez Colque (FALCONET)
11 5199 4348	Graziely Salatine de Santana (IDEAL FIBER)
11 5199 4472	Gilmar (CASA 146)
11 4172 6722	
11 5199 4498	Wallece Clemente Silva (IDEAL FIBER)
11 5199 6433	Larissa Carolina Salmão Inácio (FALCONET)
11 5199 6457	Número não consta no contrato coma TIP
11 5199 7638	Eloiza da Silva Martins (IDEAL FIBER)
11 5199 7827	
11 5199 7831	Edival José de Oliveira (LIVE CONECT TELECOMUNICACOES)
11 2893 3318	Alan Caires (LIVE CONECT TELECOMUNICACOES)
11 5199 7835	Aline Cristina da Silva (FALCONET)
11 5199 7855	Paulo augusto feliciano (IDEAL FIBER)
11 5199 7858	Rafael Henne
11 5199 7861	José Humberto dos Santos (IDEAL FIBER)
11 5199 8054	DeboraMaria Martins da Silva dos Santos (FALCONET)
11 5199 8385	Leonildo Alves de Farias
11 5199 8407	Edinei Cristian Berlanino Carias dos Santos (LYONFIBER)
11 5199 8452	Rafael Rodrigues Gomes (FALCONET)
11 5199 8493	Washington Robson de Carvalho (FALCONET)
11 5199 8578	Marcelo Dias Cordeiro  (ERICK TELECOM)
11 5199 8615	Rogerio de França Pinto  (ERICK TELECOM)
11 5199 8636	Luciana Pongilio Bassani Zagnollo (ERICK TELECOM)
11 5199 8653	Mariana Andrade Fernandes Francisco (ERICK TELECOM)
11 5199 8659	Leonice Bosio (ERICK TELECOM)
11 5199 8709	Carlos Eduardo Benzius Gibin (ERICK TELECOM)
11 5199 8725	Geni Gomes Ribeiro de Lima (ERICK TELECOM)
11 5199 8726	Jaqueline Vilerá De Oliveira (ERICK TELECOM)
11 5199 8734	Fernando Maita Ferreira (ERICK TELECOM)
11 5199 8739	David Alves de Godoy (ERICK TELECOM)
11 5199 8765	Fabiana Palazzi Larangeira L Tavolari (ERICK TELECOM)
11 5199 8766	Caroline Cavalcante dos Santos (ERICK TELECOM)
11 5199 8906	Danilo Jose Octaviani (ERICK TELECOM)
11 5199 8961	Katia Braga dos Santos Sandim (ERICK TELECOM)
11 5199 8992	(ERICK TELECOM)
11 5199 9103	(ERICK TELECOM)
11 5199 9107	(ERICK TELECOM)
11 5199 9108	(ERICK TELECOM)
11 5199 9113	(ERICK TELECOM)
11 5199 9116	(ERICK TELECOM)
11 5199 9117	(ERICK TELECOM)
11 5199 9126	(ERICK TELECOM)
11 5199 9127	(ERICK TELECOM)
11 5199 9181	(ERICK TELECOM)
11 5199 9282	(ERICK TELECOM)
11 5199 9326	(ERICK TELECOM)
11 5199 9358	(ERICK TELECOM)
11 5199 9448	(ERICK TELECOM)
11 5199 9450	(ERICK TELECOM)
11 5199 9453	(ERICK TELECOM)
11 5199 9455	Edson Favero 
11 5199 9458	Laudicea Maria Borges da Silva 
11 5199 9459	Allan Morgado de Nolla (FALCONET)
11 5199 9466	Carlos Eduardo Rodrigues Ferreira Cizzoto
11 5199 9475	
11 5199 9479	
11 5199 9545	
11 5199 9547	
11 5199 9590	
11 5199 9593	
11 5199 9596	
11 5199 9664	
11 5199 9665	
11 5199 9728	
11 5199 9751	
11 5199 9755	
11 5199 9812	
11 5199 9856	
11 5199 9857	
11 5199 9862	
11 5199 9866	
11 5199 9867	
11 5199 9870	
11 5199 9872	
11 5199 4421	
11 5199 4410	
11 5199 4407	
11 5199 4405	
11 5199 4399	
11 5199 3967	
11 5199 7942	
11 5197 1351	
11 5197 1363	
11 5197 1386	
11 5197 1980	
11 5197 2454	
11 5197 2480	
11 5197 2497	
11 5197 2526	
11 5197 2545	
11 5197 2552	
11 5197 7424	
11 5197 8898	
11 5197 9229	
11 5197 9438	
11 4751 5800	
11 4794 3811	

EOD;

try {
    // Inicia a transação
    $conn->begin_transaction();
    
    // Array para controle de números únicos e log
    $processedNumbers = [];
    $duplicateNumbers = [];
    $invalidNumbers = [];
    $totalNumbers = 0;
    
    // Limpa as tabelas
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");
    $conn->query("TRUNCATE TABLE phone_numbers");
    $conn->query("TRUNCATE TABLE users");
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");
    
    // Converte os dados em array
    $lines = explode("\n", $data);
    
    // Arrays para controle
    $companies = [];
    $users = [];
    $numbers = [];
    $count = 0;
    
    // Primeiro passo: identificar todas as empresas
    foreach ($lines as $line) {
        list($number, $originalName) = array_pad(explode("\t", trim($line)), 2, '');
        $originalName = trim($originalName);
        
        if (preg_match('/\((.*?)\)/', $originalName, $matches)) {
            $companyName = trim($matches[1]);
            if (!isset($companies[$companyName]) && 
                in_array($companyName, ['LYONFIBER', 'FALCONET', 'LIVE CONECT TELECOMUNICACOES', 'IDEAL FIBER', 'ERICK TELECOM'])) {
                $stmt = $conn->prepare("INSERT INTO users (name, is_company) VALUES (?, 1)");
                $stmt->bind_param("s", $companyName);
                $stmt->execute();
                $companies[$companyName] = $conn->insert_id;
            }
        }
    }
    
    // Segundo passo: processar cada linha
    foreach ($lines as $line) {
        list($number, $originalName) = array_pad(explode("\t", trim($line)), 2, '');
        $number = preg_replace('/[^0-9]/', '', $number);
        $originalName = trim($originalName);
        
        $totalNumbers++;
        
        // Validação do número
        if (empty($number)) {
            $invalidNumbers[] = $line;
            continue;
        }

        // Verificar duplicatas
        if (isset($processedNumbers[$number])) {
            $duplicateNumbers[] = [
                'number' => $number,
                'first_name' => $processedNumbers[$number],
                'second_name' => $originalName
            ];
            continue;
        }

        // Registra o número processado
        $processedNumbers[$number] = $originalName;

        // Processamento do número
        if (!empty($originalName)) {
            if (preg_match('/^(.*?)\s*\((.*?)\)\s*$/', $originalName, $matches)) {
                $clientName = trim($matches[1]);
                $companyName = trim($matches[2]);
                
                if (isset($companies[$companyName])) {
                    // Criar subcliente
                    $stmt = $conn->prepare("INSERT INTO sub_clients (name, company_id) VALUES (?, ?)");
                    $stmt->bind_param("si", $clientName, $companies[$companyName]);
                    $stmt->execute();
                    $subClientId = $conn->insert_id;
                    
                    // Inserir número vinculado ao subcliente
                    $stmt = $conn->prepare("INSERT INTO phone_numbers (number, user_id, sub_client_id) VALUES (?, ?, ?)");
                    $stmt->bind_param("sii", $number, $companies[$companyName], $subClientId);
                    $stmt->execute();
                }
            } else {
                // Cliente individual ou empresa
                $isCompany = 0;
                if (stripos($originalName, 'LTDA') !== false ||
                    stripos($originalName, 'Instituto') !== false ||
                    stripos($originalName, 'Drogaria') !== false) {
                    $isCompany = 1;
                }
                
                if (isset($users[$originalName])) {
                    $user_id = $users[$originalName];
                } else {
                    $stmt = $conn->prepare("INSERT INTO users (name, is_company) VALUES (?, ?)");
                    $stmt->bind_param("si", $originalName, $isCompany);
                    $stmt->execute();
                    $user_id = $conn->insert_id;
                    $users[$originalName] = $user_id;
                }
                
                $stmt = $conn->prepare("INSERT INTO phone_numbers (number, user_id) VALUES (?, ?)");
                $stmt->bind_param("si", $number, $user_id);
                $stmt->execute();
            }
        } else {
            // Adiciona número sem vínculo com cliente
            $stmt = $conn->prepare("INSERT INTO phone_numbers (number) VALUES (?)");
            $stmt->bind_param("s", $number);
            $stmt->execute();
        }
        
        $numbers[$number] = true;
        $count++;
    }
    
    // Commit da transação
    $conn->commit();
    
    // Gerar relatório
    echo "Relatório de Importação:\n";
    echo "Total de números no arquivo: " . $totalNumbers . "\n";
    echo "Números únicos processados: " . count($processedNumbers) . "\n";
    echo "Números inválidos encontrados: " . count($invalidNumbers) . "\n";
    echo "Números duplicados encontrados: " . count($duplicateNumbers) . "\n\n";
    
    if (!empty($invalidNumbers)) {
        echo "Números inválidos:\n";
        foreach ($invalidNumbers as $line) {
            echo "- " . $line . "\n";
        }
        echo "\n";
    }
    
    if (!empty($duplicateNumbers)) {
        echo "Números duplicados:\n";
        foreach ($duplicateNumbers as $dup) {
            echo "- Número: {$dup['number']}\n";
            echo "  Primeira ocorrência: {$dup['first_name']}\n";
            echo "  Segunda ocorrência: {$dup['second_name']}\n";
        }
    }
    
} catch (Exception $e) {
    if ($conn->in_transaction) {
        $conn->rollback();
    }
    echo "Erro durante a importação: " . $e->getMessage() . "\n";
    error_log($e->getTraceAsString());
}
