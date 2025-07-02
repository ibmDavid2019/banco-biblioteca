<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}
require_once '../config/db.php';

$erros = [];
$sucesso = "";
$generos = ['Drama', 'Ação', 'Ficção', 'Romance', 'Suspense', 'Comédia'];

// Buscar autores
$autores = $pdo->query("SELECT * FROM autores ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $id_autor = $_POST['id_autor'] ?? '';
    $genero = $_POST['genero'] ?? '';

    if (empty($titulo)) $erros[] = "O título é obrigatório.";
    if (!in_array($genero, $generos)) $erros[] = "Gênero inválido.";

    if (empty($erros)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO livros (titulo, id_autor, genero) VALUES (?, ?, ?)");
            $stmt->execute([$titulo, $id_autor, $genero]);
             // Redirecionar após sucesso
            header("Location: listar.php?sucesso=1");
        exit;
        } catch (Exception $e) {
            $erros[] = "Erro ao salvar: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Adicionar Livro</title>
</head>
<body>
    <h1>Adicionar Livro</h1>
    <a href="listar.php">← Voltar</a><br><br>

    <?php if ($sucesso): ?>
        <p style="color:green;"><?= $sucesso ?></p>
    <?php endif; ?>
    <?php if ($erros): ?>
        <ul style="color:red;">
            <?php foreach ($erros as $erro): ?>
                <li><?= htmlspecialchars($erro) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST">
        <label>Título:</label><br>
        <input type="text" name="titulo" value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>" required><br><br>

        <label>Gênero:</label><br>
        <select name="genero" required>
            <option value="">-- Selecione --</option>
            <?php foreach ($generos as $g): ?>
                <option value="<?= $g ?>" <?= ($_POST['genero'] ?? '') === $g ? 'selected' : '' ?>>
                    <?= $g ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>
        <label>Autor:</label><br>

        <select name="id_autor" required>    
        <option value="">-- Selecione --</option>
        <?php foreach ($autores as $autor): ?>
        <option value="<?= $autor['id'] ?>" <?= ($_POST['id_autor'] ?? '') == $autor['id'] ? 'selected' : '' ?>>
        <?= htmlspecialchars($autor['nome']) ?>
        </option>
        <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Salvar</button>
    </form>

    <?php if (!empty($livro_adicionado)): ?>
    <h3>Livro Adicionado:</h3>
    <ul>
        <li><strong>Título:</strong> <?= htmlspecialchars($livro_adicionado['titulo']) ?></li>
        <li><strong>Gênero:</strong> <?= htmlspecialchars($livro_adicionado['genero']) ?></li>
        <li><strong>Autor:</strong> <?= htmlspecialchars($livro_adicionado['autor']) ?></li>
    </ul>
<?php endif; ?>

</body>
</html>
