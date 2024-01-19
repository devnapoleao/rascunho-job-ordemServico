<?php
// Incluir o arquivo de configuração do banco de dados
require 'back/config.php';

// Verificar se o formulário foi submetido via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar os dados do formulário
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $cidade = $_POST['cidade'];

    // Preparar a declaração SQL
    $sql = "INSERT INTO clientes (nome, telefone, endereco, cidade) VALUES (:nome, :telefone, :endereco, :cidade)";

    try {
        // Preparar a declaração preparada
        $stmt = $pdo->prepare($sql);

        // Vincular os parâmetros aos valores do formulário
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':endereco', $endereco);
        $stmt->bindParam(':cidade', $cidade);

        // Executar a declaração preparada
        $stmt->execute();

        // Redirecionar para uma nova página ou informar sucesso
        header("Location: documentos.html"); // Substitua por sua página de sucesso
        exit;
    } catch (PDOException $e) {
        // Tratar erro
        die("Erro ao inserir dados: " . $e->getMessage());
    }
} else {
    // Redirecionar de volta para o formulário se o método não for POST
    header("Location: index.html"); // Substitua pelo seu arquivo de formulário
    exit;
}
?>
