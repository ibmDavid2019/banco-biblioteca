<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../config/db.php';

// Buscar usuários para o select
$usuarios = $pdo->query("SELECT id, nome FROM usuarios ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// Buscar livros para o select
$livros = $pdo->query("SELECT id, titulo FROM livros ORDER BY titulo")->fetchAll(PDO::FETCH_ASSOC);

$erros = [];
$sucesso = "";

// Capturar o parâmetro livro_id da URL para pré-seleção
$livro_id_selecionado = isset($_GET['livro_id']) ? (int)$_GET['livro_id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_livro = $_POST['id_livro'] ?? '';
    $id_usuario = $_POST['id_usuario'] ?? '';

    if (empty($id_livro)) $erros[] = "Selecione um livro.";
    if (empty($id_usuario)) $erros[] = "Selecione um usuário.";

    if (empty($erros)) {
        try {
            // Verificar se o livro já está emprestado (sem data_devolucao)
            $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM emprestimos WHERE id_livro = ? AND data_devolucao IS NULL");
            $stmtCheck->execute([$id_livro]);
            if ($stmtCheck->fetchColumn() > 0) {
                $erros[] = "Este livro já está emprestado.";
            } else {
                // Inserir empréstimo
                $stmt = $pdo->prepare("INSERT INTO emprestimos (id_livro, id_usuario, data_emprestimo) VALUES (?, ?, NOW())");
                $stmt->execute([$id_livro, $id_usuario]);
                $sucesso = "Empréstimo registrado com sucesso!";
            }
        } catch (Exception $e) {
            $erros[] = "Erro ao registrar empréstimo: " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrar Empréstimo</title>
</head>
<body>
    <h1>Registrar Empréstimo</h1>
    <a href="listar.php">← Voltar para Livros</a><br><br>

    <?php if ($sucesso): ?>
        <p style="color:green;"><?= htmlspecialchars($sucesso) ?></p>
    <?php endif; ?>
    <?php if ($erros): ?>
        <ul style="color:red;">
            <?php foreach ($erros as $erro): ?>
                <li><?= htmlspecialchars($erro) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST">
        <label>Livro:</label><br>
        <select name="id_livro" required>
            <option value="">-- Selecione --</option>
            <?php foreach ($livros as $livro): ?>
                <option value="<?= $livro['id'] ?>"
                    <?php
                    // Pré-selecionar: se formulário foi enviado, manter escolha do usuário, senão usar o parâmetro livro_id
                    if (isset($_POST['id_livro'])) {
                        echo $_POST['id_livro'] == $livro['id'] ? 'selected' : '';
                    } else {
                        echo ($livro_id_selecionado == $livro['id']) ? 'selected' : '';
                    }
                    ?>
                >
                    <?= htmlspecialchars($livro['titulo']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Usuário:</label><br>
        <select name="id_usuario" required>
            <option value="">-- Selecione --</option>
            <?php foreach ($usuarios as $usuario): ?>
                <option value="<?= $usuario['id'] ?>" <?= (isset($_POST['id_usuario']) && $_POST['id_usuario'] == $usuario['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($usuario['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Registrar Empréstimo</button>
    </form>
</body>
</html>
