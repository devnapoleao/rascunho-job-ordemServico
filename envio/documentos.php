<?php
session_start();
require 'back/config.php';

// Verifica se o cliente está logado pelo e-mail
if (!isset($_SESSION['email'])) {
    die("Você não está logado. Por favor, faça login para continuar.");
}

$email = $_SESSION['email'];

// Buscar o cliente_id usando o email da sessão
$stmt = $pdo->prepare("SELECT id FROM clientes WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    die("Cliente não encontrado.");
}

$cliente_id = $user['id'];
$diretorio = "documentos/" . $cliente_id . "/";

// Cria o diretório se ele não existir
if (!is_dir($diretorio)) {
    if (!mkdir($diretorio, 0777, true)) {
        die("Falha ao criar o diretório de documentos.");
    }
}

// Processar o upload dos arquivos
$uploadSuccess = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_FILES['documentos'])) {
    foreach ($_FILES['documentos']['name'] as $key => $name) {
        if ($_FILES['documentos']['error'][$key] === UPLOAD_ERR_OK) {
            $nome_temporario = $_FILES['documentos']['tmp_name'][$key];
            $novo_nome = $diretorio . basename($name);

            // Mover o arquivo para o diretório do cliente
            if (move_uploaded_file($nome_temporario, $novo_nome)) {
                $uploadSuccess = true; // Marcar sucesso no upload
            } else {
                echo "Falha ao carregar o arquivo: $name<br>";
                $uploadSuccess = false;
                break; // Se um arquivo falhar, não continuar com os outros
            }
        } else {
            echo "Erro ao carregar o arquivo $name: " . $_FILES['documentos']['error'][$key] . "<br>";
            $uploadSuccess = false;
            break; // Se um arquivo falhar, não continuar com os outros
        }
    }
    if ($uploadSuccess) {
        // Todos os arquivos foram carregados com sucesso, redirecionar para ordemServico.php
        header("Location: ordemServico.php");
        exit;
    }
} else {
    echo "Nenhum arquivo foi enviado.";
}
?>
