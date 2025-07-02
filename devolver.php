<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['emprestimo_id'])) {
    $emprestimo_id = (int)$_POST['emprestimo_id'];

    $stmt = $pdo->prepare("UPDATE emprestimos SET data_devolucao = NOW() WHERE id = ? AND data_devolucao IS NULL");
    $stmt->execute([$emprestimo_id]);
}

header("Location: listar.php");
exit;
