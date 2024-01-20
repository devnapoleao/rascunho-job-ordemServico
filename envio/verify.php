<?php
session_start();

require 'back/config.php';

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['codigo'])) {
    $codigo = $_POST['codigo'];

    if (!isset($_SESSION['email'])) {
        exit('A sessão expirou ou o e-mail não foi fornecido. Por favor, tente reenviar o código.');
    }

    $email = $_SESSION['email'];

    try {
        $stmt = $pdo->prepare("SELECT id, codigo_acesso, codigo_acesso_timestamp FROM clientes WHERE email = ? LIMIT 1");
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
            // Verificar se já existe ordem de serviço para este cliente
            $stmt = $pdo->prepare("SELECT id FROM ordens_servico WHERE cliente_id = ?");
            $stmt->execute([$user['id']]);
            $ordemServico = $stmt->fetch();

            if ($ordemServico) {
                // Existe ordem de serviço, redirecionar para ordemServico.php
                header("Location: ordemServico.php");
                exit;
            } else {
                // Não existe ordem de serviço, redirecionar para outra página conforme necessário
                header("Location: dados.php");
                exit;
            }
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
