<?php
require 'back/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Suponha que $cliente_id seja o ID do cliente que você obteve do banco de dados.
    // Você precisará ajustar isso para recuperar o ID correto, talvez da sessão ou do banco de dados.
    $cliente_id = $_SESSION['cliente_id']; // Exemplo: ID da sessão

    $diretorio = "documentos/$cliente_id/";
    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0777, true); // Cria a pasta se não existir
    }

    foreach ($_FILES['documentos']['name'] as $key => $name) {
        if ($_FILES['documentos']['error'][$key] == 0) {
            $nome_temporario = $_FILES['documentos']['tmp_name'][$key];
            $novo_nome = $diretorio . basename($name);
            if (move_uploaded_file($nome_temporario, $novo_nome)) {
                // Arquivo foi carregado e salvo com sucesso
                echo "O arquivo $name foi enviado com sucesso.<br>";
            } else {
                // Tratar erro de upload
                echo "Houve um erro ao enviar o arquivo $name.<br>";
            }
        }
    }
    
    // Após o processamento, redireciona para uma nova página
    header("Location: sucesso.html");
    exit;
}
?>
