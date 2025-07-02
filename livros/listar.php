<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}
require_once '../config/db.php';

// Consulta livros com status de empréstimo ativo (sem data_devolucao)
$sql = "
    SELECT l.id, l.titulo, l.genero, a.nome AS autor_nome,
           e.id AS emprestimo_id, u.nome AS usuario_nome
    FROM livros l
    LEFT JOIN autores a ON l.id_autor = a.id
    LEFT JOIN emprestimos e ON l.id = e.id_livro AND e.data_devolucao IS NULL
    LEFT JOIN usuarios u ON e.id_usuario = u.id
    ORDER BY l.titulo
";

$livros = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lista de Livros</title>
</head>
<body>
    <div style="text-align: right;">
        <span>Olá, <?= htmlspecialchars($_SESSION['usuario']['nome']) ?>!</span>
        <a href="../logout.php" style="margin-left: 10px;">Sair</a>
    </div>

    <h1>Lista de Livros</h1>

    <a href="adicionar.php">+ Adicionar Livro</a>
    

    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>Título</th>
                <th>Autor</th>
                <th>Gênero</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($livros): ?>
                <?php foreach ($livros as $livro): ?>
                    <tr>
                        <td><?= htmlspecialchars($livro['titulo']) ?></td>
                        <td><?= htmlspecialchars($livro['autor_nome']) ?></td>
                        <td><?= htmlspecialchars($livro['genero']) ?></td>
                        <td>
                            <?php if ($livro['emprestimo_id']): ?>
                                Emprestado para <?= htmlspecialchars($livro['usuario_nome']) ?>
                            <?php else: ?>
                                Disponível
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($livro['emprestimo_id']): ?>
                                <form method="POST" action="devolver.php" style="display:inline;">
                                    <input type="hidden" name="emprestimo_id" value="<?= $livro['emprestimo_id'] ?>">
                                    <button type="submit">Devolver</button>
                                </form>
                            <?php else: ?>
                                <a href="emprestar.php?livro_id=<?= $livro['id'] ?>">Emprestar</a>
                                
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">Nenhum livro cadastrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
