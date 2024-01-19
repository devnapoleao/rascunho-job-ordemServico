<?php
require 'back/config.php';

// Consulta para obter as ordens de serviço e informações do cliente
$sql = "SELECT c.nome, c.email, c.telefone, c.endereco, c.cidade, os.id AS ordem_servico_id, os.status 
        FROM clientes c 
        JOIN ordens_servico os ON c.id = os.cliente_id";

try {
    $stmt = $pdo->query($sql);
    $ordens = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro na consulta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Administração de Ordens de Serviço</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Dashboard - Administração de Ordens de Serviço</h2>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Nome do Cliente</th>
                    <th scope="col">E-mail</th>
                    <th scope="col">Telefone</th>
                    <th scope="col">Endereço</th>
                    <th scope="col">Cidade</th>
                    <th scope="col">Documentos</th>
                    <th scope="col">Status</th>
                    <th scope="col">Anexar Documento de Retorno</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ordens as $ordem): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ordem['nome']); ?></td>
                        <td><?php echo htmlspecialchars($ordem['email']); ?></td>
                        <td><?php echo htmlspecialchars($ordem['telefone']); ?></td>
                        <td><?php echo htmlspecialchars($ordem['endereco']); ?></td>
                        <td><?php echo htmlspecialchars($ordem['cidade']); ?></td>
                        <td>
                            <a href="documentos/<?php echo $ordem['ordem_servico_id']; ?>/">Baixar Documentos</a>
                        </td>
                        <td>
                            <form action="alterarStatus.php" method="post">
                                <input type="hidden" name="ordem_servico_id" value="<?php echo $ordem['ordem_servico_id']; ?>">
                                <select name="status" onchange="this.form.submit()">
                                    <option value="Enviado" <?php echo $ordem['status'] == 'Enviado' ? 'selected' : ''; ?>>Enviado</option>
                                    <option value="Recebido" <?php echo $ordem['status'] == 'Recebido' ? 'selected' : ''; ?>>Recebido</option>
                                    <option value="Trabalhando" <?php echo $ordem['status'] == 'Trabalhando' ? 'selected' : ''; ?>>Trabalhando</option>
                                    <option value="Concluído" <?php echo $ordem['status'] == 'Concluído' ? 'selected' : ''; ?>>Concluído</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <form action="anexarRetorno.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="ordem_servico_id" value="<?php echo $ordem['ordem_servico_id']; ?>">
                                <input type="file" name="documento_retorno">
                                <button type="submit">Anexar</button>
                            </form>
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
