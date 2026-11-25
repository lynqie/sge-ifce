<?php
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "sge_escola";

$conn = new mysqli($servidor, $usuario, $senha, $banco);
if ($conn->connect_error) {
    die("Erro de conexão com o banco de dados. Verifique se o XAMPP está rodando. Erro: " . $conn->connect_error);
}
$conn->set_charset("utf8");
?>