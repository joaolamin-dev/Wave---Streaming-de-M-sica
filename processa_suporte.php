<?php
include 'conexao.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome     = trim($_POST['nome'] ?? '');
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $telefone = trim($_POST['telefone'] ?? '');
    $problema = trim($_POST['problema'] ?? '');

    if ($nome === '' || $email === '' || $telefone === '' || $problema === '') {
        $mensagem = "⚠️ Preencha todos os campos obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "⚠️ E-mail inválido.";
    } else {
        $sql = "INSERT INTO tb_suporte (nome, email, telefone, problema) VALUES (?, ?, ?, ?)";
        $stmt = $conecta_db->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssss", $nome, $email, $telefone, $problema);
            if ($stmt->execute()) {
                header("Location: agradecimento_suporte.html");
                exit;
            } else {
                $mensagem = "❌ Erro ao enviar: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $mensagem = "❌ Erro no prepare: " . $conecta_db->error;
        }
    }
}
?>
