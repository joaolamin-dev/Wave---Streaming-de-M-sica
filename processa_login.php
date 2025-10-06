<?php
session_start();
include 'conexao.php';

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = isset($_POST['email']) ? strtolower(trim($_POST['email'])) : '';
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';

    if ($email === '' || $senha === '') {
        $mensagem = "Preencha e-mail e senha.";
    } else {
        if ($email === 'admin' && $senha === 'admin') {
            $_SESSION['usuario'] = 'admin';
            header("Location: listagem.php");
            exit;
        }
	if ($email === 'rh' && $senha === 'rh') {
    $_SESSION['usuario'] = 'rh';
    header("Location: listagem_empregos.php");
    exit;
}
	if ($email === 'suporte' && $senha === 'suporte') {
    $_SESSION['usuario'] = 'suporte';
    header("Location: listagem_suporte.php");
    exit;
}


        $sql = "SELECT usuario, senha_hash FROM tb_login WHERE email = ?";
        $stmt = $conecta_db->prepare($sql);

        if (!$stmt) {
            die("Erro no prepare: " . $conecta_db->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();

            if (password_verify($senha, $row['senha_hash'])) {
                $_SESSION['usuario'] = $row['usuario'];
                header("Location: homepage.html");
                exit;
            } else {
                $mensagem = "❌ Senha incorreta!";
            }
        } else {
            $mensagem = "❌ Usuário não encontrado!";
        }

        $stmt->close();
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Resultado do Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
<div class="container">
    <fieldset>
        <legend>Resultado do Login</legend>
        <p style="color:red;"><?php echo htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8'); ?></p>
        <p><a href="login.html">Voltar</a></p>
    </fieldset>
</div>
</body>
</html>
