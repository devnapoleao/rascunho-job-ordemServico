<?php
require 'back/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $descricao = $_POST['descricao'];
    // Suponha que você tenha o ID do cliente armazenado em uma sessão ou obtido de outra forma
    $cliente_id = $_SESSION['cliente_id']; // Exemplo: ID da sessão

    // Preparar a declaração SQL
    $sql = "INSERT INTO ordens_servico (cliente_id, descricao, status) VALUES (:cliente_id, :descricao, 'Pendente')";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->execute();

        // Redirecionar de volta para a página de ordens de serviço ou para uma página de sucesso
        header("Location: ordemServico.html");
        exit;
    } catch (PDOException $e) {
        die("Erro ao inserir ordem de serviço: " . $e->getMessage());
    }
} else {
    header("Location: ordemServico.html");
    exit;
}
?>
