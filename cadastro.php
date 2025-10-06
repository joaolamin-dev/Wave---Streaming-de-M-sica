<?php
include 'conexao.php';

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $email   = isset($_POST['email']) ? strtolower(trim($_POST['email'])) : '';
    $senha   = isset($_POST['senha']) ? $_POST['senha'] : '';
    $nome    = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $dia     = isset($_POST['dia']) ? intval($_POST['dia']) : 0;
    $mes     = isset($_POST['mes']) ? intval($_POST['mes']) : 0;
    $ano     = isset($_POST['ano']) ? intval($_POST['ano']) : 0;
    $genero  = isset($_POST['genero']) ? $_POST['genero'] : '';

    // Validações básicas
    if ($usuario === '' || $email === '' || $senha === '') {
        $mensagem = "Preencha os campos obrigatórios (usuário, e-mail, senha).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "E-mail inválido.";
    } elseif (!checkdate($mes, $dia, $ano)) {
        $mensagem = "Data de nascimento inválida.";
    } else {
        // Verifica se já existe email ou usuario
        $checkSql = "SELECT id FROM tb_login WHERE email = ? OR usuario = ?";
        $stmt = $conecta_db->prepare($checkSql);
        if (!$stmt) {
            die("Erro no prepare: " . $conecta_db->error);
        }
        $stmt->bind_param("ss", $email, $usuario);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows > 0) {
            $mensagem = "Já existe um usuário com esse e-mail ou nome de usuário.";
            $stmt->close();
        } else {
            $stmt->close();

            $data_nascimento = sprintf('%04d-%02d-%02d', $ano, $mes, $dia);
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            $sql = "INSERT INTO tb_login (usuario, email, senha_hash, nome, data_nascimento, genero) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conecta_db->prepare($sql);

            if (!$stmt) {
                die("Erro no prepare: " . $conecta_db->error);
            }

            $stmt->bind_param("ssssss", $usuario, $email, $senha_hash, $nome, $data_nascimento, $genero);

            if ($stmt->execute()) {
                // Redireciona para o login com flag de sucesso
                header("Location: login.html?cadastro=1");
                exit;
            } else {
                $mensagem = "❌ Erro no cadastro: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cadastro - Resultado</title>
    <link rel="stylesheet" href="css/cadastro.css">
</head>
<body>
<div class="container">
    <fieldset>
        <legend>Resultado do Cadastro</legend>
        <?php if ($mensagem !== ""): ?>
            <p style="color:red;"><?php echo htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8'); ?></p>
            <p><a href="cadastro.html">Voltar</a></p>
        <?php endif; ?>
    </fieldset>
</div>
</body>
</html>
