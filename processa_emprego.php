<?php
include 'conexao.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome         = trim($_POST['nome'] ?? '');
    $email        = strtolower(trim($_POST['email'] ?? ''));
    $telefone     = trim($_POST['telefone'] ?? '');
    $cargo        = trim($_POST['cargo'] ?? '');
    $curriculo    = trim($_POST['curriculo'] ?? '');
    $apresentacao = trim($_POST['apresentacao'] ?? '');

    if ($nome === '' || $email === '' || $telefone === '' || $cargo === '' || $apresentacao === '') {
        $mensagem = "⚠️ Preencha todos os campos obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "⚠️ E-mail inválido.";
    } else {
        $sql = "INSERT INTO tb_empregos (nome, email, telefone, cargo, curriculo, apresentacao) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conecta_db->prepare($sql);

        if (!$stmt) {
            $mensagem = "❌ Erro no prepare: " . $conecta_db->error;
        } else {
            $stmt->bind_param("ssssss", $nome, $email, $telefone, $cargo, $curriculo, $apresentacao);

            if ($stmt->execute()) {
                $mensagem = "✅ Sua candidatura foi enviada com sucesso!";

            } else {
                $mensagem = "❌ Erro ao enviar candidatura: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Resultado da Candidatura</title>
  <link rel="stylesheet" href="css/empregos.css">
</head>
<body>
  <main>
    <div class="container">
      <h1>Confirmação</h1>
      <p><?php echo $mensagem; ?></p>
      <a href="empregos.html">← Voltar ao formulário</a>
    </div>
  </main>
</body>
</html>
