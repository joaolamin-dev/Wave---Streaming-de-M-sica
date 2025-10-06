<?php
session_start();
include 'conexao.php';

// Proteção de rota para RH
if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] !== 'rh') {
    header("Location: login.html");
    exit;
}

// Exclusão de candidatura
if (isset($_GET['apagar'])) {
    $idDel = $_GET['apagar'];

    $stmt = $conecta_db->prepare("DELETE FROM tb_empregos WHERE id = ?");
    $stmt->bind_param("i", $idDel);
    $stmt->execute();
    $msg = "Candidatura ID <b>$idDel</b> excluída com sucesso!";
    $stmt->close();
}

// Filtro por nome
$busca = $_POST['busca_nome'] ?? '';
if ($busca !== '') {
    $stmt = $conecta_db->prepare("SELECT id, nome, email, telefone, cargo, curriculo, apresentacao FROM tb_empregos WHERE nome LIKE CONCAT(?, '%') ORDER BY nome ASC");
    $stmt->bind_param("s", $busca);
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
    $resultado = $conecta_db->query("SELECT id, nome, email, telefone, cargo, curriculo, apresentacao FROM tb_empregos ORDER BY nome ASC");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>RH - Candidaturas Recebidas</title>
  <link rel="stylesheet" href="css/login.css">
  <style>
    .content { max-width: 1000px; }
    table {
      border-collapse: collapse;
      margin-top: 20px;
      width: 100%;
      background: rgba(255, 255, 255, 0.9);
      color: #333;
      border-radius: 10px;
      overflow: hidden;
    }
    th, td {
      padding: 10px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    th {
      background: #4a1d7c;
      color: white;
    }
    tr:hover { background: #f2f2f2; }
    .delete-btn {
      color: red;
      font-weight: bold;
      text-decoration: none;
    }
    .delete-btn:hover { text-decoration: underline; }
    .msg {
      margin: 15px;
      padding: 10px;
      background: #d4edda;
      color: #155724;
      border-radius: 8px;
    }
    .form-filtro {
      margin: 20px 0;
    }
    .form-filtro input {
      padding: 8px;
      border-radius: 6px;
      border: none;
      margin-right: 10px;
    }
    .form-filtro button {
      padding: 8px 15px;
      border: none;
      border-radius: 20px;
      background: #7a1e78;
      color: white;
      cursor: pointer;
    }
    .form-filtro button:hover { background: #4a1d7c; }
  </style>
</head>
<body>
  <div class="container content">
    <h2>📄 Candidaturas Recebidas</h2>

    <?php if (!empty($msg)): ?>
      <div class="msg"><?= $msg ?></div>
    <?php endif; ?>

    <form method="POST" action="" class="form-filtro">
      <input type="text" name="busca_nome" placeholder="Buscar por nome...">
      <button type="submit">Filtrar</button>
    </form>

    <table>
      <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Email</th>
        <th>Telefone</th>
        <th>Cargo</th>
        <th>Currículo</th>
        <th>Apresentação</th>
        <th>Ação</th>
      </tr>
      <?php while ($linha = $resultado->fetch_assoc()): ?>
        <tr>
          <td><?= $linha['id'] ?></td>
          <td><?= htmlspecialchars($linha['nome']) ?></td>
          <td><?= htmlspecialchars($linha['email']) ?></td>
          <td><?= htmlspecialchars($linha['telefone']) ?></td>
          <td><?= htmlspecialchars($linha['cargo']) ?></td>
          <td><a href="<?= htmlspecialchars($linha['curriculo']) ?>" target="_blank">Ver currículo</a></td>
          <td><?= nl2br(htmlspecialchars($linha['apresentacao'])) ?></td>
          <td>
            <a href="?apagar=<?= $linha['id'] ?>" class="delete-btn">Deletar</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>

    <br>
    <a href="login.html" style="color:white;">⬅ Sair</a>
  </div>
</body>
</html>
