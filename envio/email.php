<?php
require 'back/config.php'; // Inclui o arquivo de configuração

// Certifique-se de que o script está sendo chamado com o método POST e o campo email foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = $_POST['email'];

    try {
        // Prepara a declaração SQL para verificar se o e-mail já existe na tabela de clientes
        $stmt = $pdo->prepare("SELECT id FROM clientes WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Gera um novo código de acesso
        $codigo_acesso = rand(1000, 9999); // Geração de um código simples, deve ser mais seguro em produção
        $codigo_acesso_timestamp = date('Y-m-d H:i:s'); // Timestamp da geração do código

        if ($user) {
            // Usuário existe, atualiza o código de acesso e o timestamp
            $update_stmt = $pdo->prepare("UPDATE clientes SET codigo_acesso = ?, codigo_acesso_timestamp = ? WHERE email = ?");
            $update_stmt->execute([$codigo_acesso, $codigo_acesso_timestamp, $email]);

        } else {
            // Usuário não existe, insere novo usuário com código de acesso e timestamp
            $insert_stmt = $pdo->prepare("INSERT INTO clientes (email, codigo_acesso, codigo_acesso_timestamp) VALUES (?, ?, ?)");
            $insert_stmt->execute([$email, $codigo_acesso, $codigo_acesso_timestamp]);
        }
        // Após gerar o código de acesso e antes do redirecionamento para codigo.html
        $_SESSION['email'] = $email;
        $_SESSION['codigo_acesso'] = $codigo_acesso; // Somente para validação futura

        // Enviar e-mail com o código (a função mail() precisa ser configurada corretamente no php.ini)
        $mensagem = "Seu código de acesso é: $codigo_acesso";
        $headers = 'From: webmaster@seusite.com' . "\r\n" .
                   'Reply-To: webmaster@seusite.com' . "\r\n" .
                   'X-Mailer: PHP/' . phpversion();

        if (!mail($email, "Código de Acesso", $mensagem, $headers)) {
            throw new Exception("Falha ao enviar o e-mail.");
        }

        // Redireciona para codigo.html após o envio do e-mail
        header("Location: codigo.html");
        exit;

    } catch (PDOException $e) {
        // Tratamento de erros do PDO
        error_log($e->getMessage());
        exit('Ocorreu um erro de banco de dados.');
    } catch (Exception $e) {
        // Captura falhas no envio de e-mail ou outras exceções
        error_log($e->getMessage());
        exit('Ocorreu um erro no servidor.');
    }

} else {
    // Método incorreto ou campo de e-mail não preenchido
    exit('Método de requisição inválido ou campo de e-mail não preenchido.');
}
?>
