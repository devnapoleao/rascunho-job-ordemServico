<?php
session_start();
require 'back/config.php';

if (isset($_GET['cliente_id'])) {
    $cliente_id = $_GET['cliente_id'];
    $diretorio = "documentos/" . $cliente_id . "/";

    if (is_dir($diretorio)) {
        // Cria um arquivo zip temporário
        $zipNome = "Documentos-Cliente-" . $cliente_id . ".zip";
        $zip = new ZipArchive();

        if ($zip->open($zipNome, ZipArchive::CREATE) === TRUE) {
            // Adiciona todos os arquivos do diretório ao arquivo zip
            $arquivos = new DirectoryIterator($diretorio);
            foreach ($arquivos as $arquivo) {
                if (!$arquivo->isDot()) {
                    $zip->addFile($diretorio . $arquivo->getFilename(), $arquivo->getFilename());
                }
            }
            $zip->close();

            // Envia o arquivo zip para o cliente
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($zipNome) . '"');
            header('Content-Length: ' . filesize($zipNome));
            readfile($zipNome);

            // Remove o arquivo zip após o download
            unlink($zipNome);
        } else {
            echo "Não foi possível criar o arquivo zip.";
        }
    } else {
        echo "Nenhum documento encontrado para este cliente.";
    }
} else {
    echo "ID do cliente não fornecido.";
}
?>
