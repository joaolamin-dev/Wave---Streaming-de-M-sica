<?php
$servidor = "127.0.0.1";
$usuario  = "root";
$senha    = "";
$banco    = "banco_wave";

// Criar conexão com MySQL (MySQLi orientado a objeto)
$conecta_db = new mysqli($servidor, $usuario, $senha, $banco);

// Verificar conexão
if ($conecta_db->connect_error) {
    die("Erro na conexão: " . $conecta_db->connect_error);
}

// Definir charset para evitar problemas com acentos
$conecta_db->set_charset("utf8mb4");
?>
