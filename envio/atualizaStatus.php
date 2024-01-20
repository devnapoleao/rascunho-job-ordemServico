<?php
session_start();
require 'back/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['os_id'], $_POST['status'])) {
    $os_id = $_POST['os_id'];
    $status = $_POST['status'];

    try {
        $stmt = $pdo->prepare("UPDATE ordens_servico SET status = ? WHERE id = ?");
        $stmt->execute([$status, $os_id]);
    } catch (PDOException $e) {
        die("Erro ao atualizar status: " . $e->getMessage());
    }
}

header("Location: dash.php");
exit;
?>
