<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../config/db.php';

// Consulta para buscar os empréstimos com nome do livro e usuário
$sql = "
    SELECT e.id, l.titulo, u.nome AS usuario_nome, e.data_emprestimo, e.data_devolucao
    FROM emprestimos e
    JOIN livros l ON e.id_livro = l.id
    JOIN usuarios u ON e.id_usuario = u.id
    ORDER BY e.data_emprestimo DESC
";

$emprestimos = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Empréstimos</title>
</head>
<body>
    <div style="text-align: right;">
        <span>Olá, <?= htmlspecialchars($_SESSION['usuario']['nome']) ?>!</span>
        <a href="../logout.php" style="margin-left: 10px;">Sair</a>
    </div>

    <h1>Lista de Empréstimos</h1>

    <a href="listar.php">← Voltar para Livros</a><br><br>
    <a href="emprestar.php">+ Novo Empréstimo</a><br><br>

    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>Livro</th>
                <th>Usuário</th>
                <th>Data de Empréstimo</th>
                <th>Data de Devolução</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($emprestimos): ?>
                <?php foreach ($emprestimos as $emp): ?>
                    <tr>
                        <td><?= htmlspecialchars($emp['titulo']) ?></td>
                        <td><?= htmlspecialchars($emp['usuario_nome']) ?></td>
                        <td><?= htmlspecialchars($emp['data_emprestimo']) ?></td>
                        <td><?= $emp['data_devolucao'] ? htmlspecialchars($emp['data_devolucao']) : '—' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">Nenhum empréstimo registrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
