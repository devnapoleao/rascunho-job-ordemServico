<?php
session_start();
require 'back/config.php';

if (isset($_GET['os_id'])) {
    $ordem_servico_id = $_GET['os_id'];
    $diretorio = "documentos/" . $ordem_servico_id . "/";

    if (is_dir($diretorio)) {
        $zipNome = "Documentos-OrdemServico-" . $ordem_servico_id . ".zip";
        $zip = new ZipArchive();

        if ($zip->open($zipNome, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($diretorio, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $file) {
                if ($file->isFile()) {
                    $filePath = $file->getRealPath();
                    // Adiciona o arquivo ao zip com o nome do arquivo apenas, sem o caminho completo
                    $zip->addFile($filePath, basename($filePath));
                }
            }

            $zip->close();

            // Envia o arquivo zip para o cliente como download
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($zipNome) . '"');
            header('Content-Length: ' . filesize($zipNome));
            readfile($zipNome);

            // Apaga o arquivo zip após o download
            unlink($zipNome);
            exit;
        } else {
            echo "Não foi possível criar o arquivo zip.";
        }
    } else {
        echo "Nenhum documento encontrado para esta ordem de serviço.";
    }
} else {
    echo "ID da ordem de serviço não fornecido.";
}
?>
