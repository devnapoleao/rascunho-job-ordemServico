<?php
session_start();

require 'back/config.php';

// Verifica se o e-mail está na sessão e se o formulário foi submetido
if (!isset($_SESSION['email'])) {
    exit('A sessão expirou ou o e-mail não foi fornecido. Por favor, tente reenviar o código.');
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['codigo'])) {
    $codigo = $_POST['codigo'];
    $email = $_SESSION['email'];

    try {
        $stmt = $pdo->prepare("SELECT codigo_acesso, codigo_acesso_timestamp FROM clientes WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            exit('E-mail não encontrado ou código de acesso não foi gerado para este e-mail.');
        }

        // Calcula a diferença de tempo desde a criação do código de acesso
        $current_time = new DateTime();
        $code_time = new DateTime($user['codigo_acesso_timestamp']);
        $interval = $current_time->diff($code_time);
        $minutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;

        if ($user['codigo_acesso'] === $codigo && $minutes < 15) {
            // Código correto e ainda válido
            header("Location: dados.html");
            exit;
        } else {
            exit('O código informado está incorreto ou expirou.');
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        exit('Ocorreu um erro de banco de dados.');
    }
} else {
    exit('Nenhum código foi submetido.');
}
?>
