<?php
// Iniciar a sessão e incluir a configuração do banco de dados
session_start();
require 'back/config.php';

// Se os dados forem postados, processar o formulário
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'], $_SESSION['email'])) {
    // Captura os dados do formulário
    $email = $_SESSION['email']; // Usando o e-mail da sessão para garantir consistência
    $nome = $_POST['nome'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $endereco = $_POST['endereco'] ?? '';
    $cidade = $_POST['cidade'] ?? '';

    try {
        // Iniciar transação para garantir atomicidade
        $pdo->beginTransaction();

        // Verificar se já existe um cliente com esse e-mail
        $stmt = $pdo->prepare("SELECT id FROM clientes WHERE email = ?");
        $stmt->execute([$email]);
        $cliente = $stmt->fetch();

        if ($cliente) {
            // Cliente existe, atualiza os dados
            $stmt = $pdo->prepare("UPDATE clientes SET nome = ?, telefone = ?, endereco = ?, cidade = ? WHERE email = ?");
            $stmt->execute([$nome, $telefone, $endereco, $cidade, $email]);
        } else {
            // Cliente não existe, lança um erro
            throw new Exception("Nenhum cliente existente com esse e-mail foi encontrado.");
        }

        // Concluir a transação
        $pdo->commit();

        // Redirecionar para a página de confirmação ou próxima etapa
        header("Location: documentos.html");
        exit;

    } catch (Exception $e) {
        // Desfazer a transação em caso de erro
        $pdo->rollBack();
        die("Erro: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar Usuário - Yelly</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- ... sidebar e outros elementos ... -->
        <div class="form-container">
            <div class="form-title">Para dar continuidade informe alguns dados básicos</div>
            <p class="form-description">Após esse pré-cadastro não precisará fazer isso novamente!</p>
            <form action="dados.php" method="post">
                <input type="hidden" id="email" name="email" value="<?php echo $_SESSION['email']; ?>">
                <input type="text" id="nome" name="nome" placeholder="Exemplo: Napoleão de Viana Assunção" required>
                <input type="tel" id="telefone" name="telefone" placeholder="(98)900000000">
                <input type="text" id="endereco" name="endereco" placeholder="Rua dos Carpinteiros, n 12, Centro" required>
                <input type="text" id="cidade" name="cidade" placeholder="Teresina" required>
                <button type="submit" class="btn-continue">Continuar</button>
                <button type="button" class="btn-back" onclick="window.history.back();">Voltar</button>
            </form>            
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.1.12/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
