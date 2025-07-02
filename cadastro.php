<?php
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $tipo = $_POST['tipo'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        $erro = "E-mail já cadastrado!";
    } else {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $senhaHash, $tipo]);
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Cadastro</title></head>
<body>
    <h1>Cadastro</h1>
    <?php if (!empty($erro)) echo "<p style='color:red;'>$erro</p>"; ?>
    <form method="POST">
        <label>Nome:</label><br>
        <input type="text" name="nome" required><br><br>
        <label>E-mail:</label><br>
        <input type="email" name="email" required><br><br>
        <label>Senha:</label><br>
        <input type="password" name="senha" required><br><br>
        <label>Tipo:</label><br>
        <select name="tipo">
            <option value="Aluno">Aluno</option>
            <option value="Professor">Professor</option>
            <option value="Visitante">Visitante</option>
        </select><br><br>
        <button type="submit">Cadastrar</button>
    </form>
</body>
</html>
