<?php
// back/config.php

$host = 'localhost'; // ou o endereço do seu servidor de banco de dados
$dbname = 'servico';
$dbuser = 'root';
$dbpass = '';
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";

try {
    $pdo = new PDO($dsn, $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>
