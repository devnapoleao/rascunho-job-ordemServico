<?php
session_start(); // Inicia a sessão PHP para armazenar informações entre as páginas

require 'back/config.php'; // Inclui o arquivo de configuração do banco de dados

function enviarEmail($email, $codigo_acesso) {
    $mensagem = "Seu código de acesso é: $codigo_acesso";
    $headers = 'From: webmaster@seusite.com' . "\r\n" .
               'Reply-To: webmaster@seusite.com' . "\r\n" .
               'X-Mailer: PHP/' . phpversion();

    return mail($email, "Código de Acesso", $mensagem, $headers);
}

// Verifique se o e-mail foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Tenta buscar o usuário pelo e-mail fornecido
    try {
        $stmt = $pdo->prepare("SELECT id FROM clientes WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Gera um novo código de acesso
        $codigo_acesso = rand(1000, 9999); // Em produção, considere um método mais seguro
        $codigo_acesso_timestamp = date('Y-m-d H:i:s');

        if ($user) {
            // Usuário existe, atualiza o código de acesso e o timestamp
            $stmt = $pdo->prepare("UPDATE clientes SET codigo_acesso = ?, codigo_acesso_timestamp = ? WHERE email = ?");
            $stmt->execute([$codigo_acesso, $codigo_acesso_timestamp, $email]);
        } else {
            // Usuário não existe, insere novo usuário
            $stmt = $pdo->prepare("INSERT INTO clientes (nome, email, codigo_acesso, codigo_acesso_timestamp) VALUES (?, ?, ?, ?)");
            $stmt->execute(['', $email, $codigo_acesso, $codigo_acesso_timestamp]); // Nome está vazio por enquanto
        }

        // Armazena o e-mail e o código de acesso na sessão
        $_SESSION['email'] = $email;
        $_SESSION['codigo_acesso'] = $codigo_acesso; // Apenas para validação, não seguro para autenticação

        // Enviar e-mail com o código
        if (enviarEmail($email, $codigo_acesso)) {
            // Redireciona para a página de inserção do código
            header("Location: codigo.html");
            exit;
        } else {
            throw new Exception("Falha ao enviar o e-mail.");
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        exit('Ocorreu um erro de banco de dados.');
    } catch (Exception $e) {
        error_log($e->getMessage());
        exit('Ocorreu um erro ao enviar o e-mail.');
    }
} else {
    // Redireciona o usuário de volta para o formulário de e-mail se nenhum e-mail foi postado
    header("Location: index.html");
    exit;
}
?>
