<?php
session_start();
include 'conexao.php';

// Proteção de rota
if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] !== 'admin') {
    header("Location: login.html");
    exit;
}

// Exclusão
if (isset($_GET['apagar'])) {
    $usuarioDel = $_GET['apagar'];

    $stmt = $conecta_db->prepare("DELETE FROM tb_login WHERE usuario = ?");
    $stmt->bind_param("s", $usuarioDel);
    $stmt->execute();
    $msg = "Usuário <b>$usuarioDel</b> excluído com sucesso!";
    $stmt->close();
}

// Filtro
$busca = $_POST['busca_nome'] ?? '';
if ($busca !== '') {
    $stmt = $conecta_db->prepare("SELECT usuario, email FROM tb_login WHERE usuario LIKE CONCAT(?, '%') ORDER BY usuario ASC");
    $stmt->bind_param("s", $busca);
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
    $resultado = $conecta_db->query("SELECT usuario, email FROM tb_login ORDER BY usuario ASC");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Admin - Listagem de Contas</title>
  <link rel="stylesheet" href="css/login.css">
  <style>
    .content { max-width: 800px; }
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
      padding: 12px;
      text-align: center;
      border-bottom: 1px solid #ddd;
    }
    th {
      background: #7a1e78;
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
      background: #4a1d7c;
      color: white;
      cursor: pointer;
    }
    .form-filtro button:hover { background: #7a1e78; }
  </style>
</head>
<body>
  <div class="container content">
    <h2>📋 Listagem de Contas</h2>

    <?php if (!empty($msg)): ?>
      <div class="msg"><?= $msg ?></div>
    <?php endif; ?>

    <form method="POST" action="" class="form-filtro">
      <input type="text" name="busca_nome" placeholder="Buscar usuário...">
      <button type="submit">Filtrar</button>
    </form>

    <table>
      <tr>
        <th>Usuário</th>
        <th>Email</th>
        <th>Ação</th>
      </tr>
      <?php while ($linha = $resultado->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($linha['usuario']) ?></td>
          <td><?= htmlspecialchars($linha['email']) ?></td>
          <td>
            <a href="?apagar=<?= urlencode($linha['usuario']) ?>" class="delete-btn">Deletar</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>

    <br>
    <a href="login.html" style="color:white;">⬅ Sair</a>
  </div>
</body>
</html>
