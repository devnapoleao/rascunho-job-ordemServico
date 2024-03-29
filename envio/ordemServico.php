<?php
session_start();
require 'back/config.php';

// Verifica se o e-mail está na sessão
if (!isset($_SESSION['email'])) {
    die("Por favor, faça login novamente.");
}

$email = $_SESSION['email'];

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['descricao'])) {
    $descricao = $_POST['descricao'];

    try {
        // Obter o cliente_id usando o email da sessão
        $stmt = $pdo->prepare("SELECT id FROM clientes WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            die("Cliente não encontrado.");
        }

        $cliente_id = $user['id'];

        // Inserir a ordem de serviço
        $sql = "INSERT INTO ordens_servico (cliente_id, descricao, status) VALUES (:cliente_id, :descricao, 'Pendente')";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->execute();
    } catch (PDOException $e) {
        die("Erro ao inserir ordem de serviço: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Ordem de Serviço - Yelly</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Criar Ordem de Serviço</h2>
        <form action="ordemServico.php" method="post">
            <div class="form-group">
                <label for="descricao">Digite o que deseja:</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>

        <h2 class="mt-4">Ordens de Serviço</h2>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Cliente ID</th>
                    <th scope="col">Descrição</th>
                    <th scope="col">Status</th>
                    <th scope="col">Criado em</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $stmt = $pdo->query("SELECT * FROM ordens_servico");
                    while ($ordem = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($ordem['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($ordem['cliente_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($ordem['descricao']) . "</td>";
                        echo "<td>" . htmlspecialchars($ordem['status']) . "</td>";
                        echo "<td>" . htmlspecialchars($ordem['criado_em']) . "</td>";
                        echo "</tr>";
                    }
                } catch (PDOException $e) {
                    die("Erro ao buscar ordens de serviço: " . $e->getMessage());
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.1.12/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
