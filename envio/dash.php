<?php
session_start();
require 'back/config.php';

try {
    $stmt = $pdo->query("SELECT c.id, c.nome, c.email, c.telefone, c.endereco, c.cidade, os.id AS os_id, os.status, os.descricao FROM clientes c LEFT JOIN ordens_servico os ON c.id = os.cliente_id");
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar informações: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Administração</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Dashboard - Administração de Clientes e Ordens de Serviço</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Cliente ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Telefone</th>
                    <th>Endereço</th>
                    <th>Cidade</th>
                    <th>Ordem de Serviço ID</th>
                    <th>Descrição</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cliente['id']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['nome']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['telefone']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['endereco']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['cidade']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['os_id']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['descricao']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['status']); ?></td>
                        <td>
                            <form action="atualizaStatus.php" method="post">
                                <input type="hidden" name="os_id" value="<?php echo $cliente['os_id']; ?>">
                                <select name="status">
                                    <option value="Pendente">Pendente</option>
                                    <option value="Em Progresso">Em Progresso</option>
                                    <option value="Concluído">Concluído</option>
                                </select>
                                <button type="submit">Atualizar</button>
                            </form>
                            <a href="downloadDocumentos.php?os_id=<?php echo $cliente['id']; ?>">Download Documentos</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.1.12/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
