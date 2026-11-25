<?php
session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_aluno = $_POST['id_aluno'];
    $tipo = $_POST['tipo'];
    $descricao = $_POST['descricao'];

    $sql = "INSERT INTO ocorrencias (id_aluno, tipo, descricao) VALUES ('$id_aluno', '$tipo', '$descricao')";
    $conn->query($sql);
    
    header("Location: painel.php");
    exit;
}
?>