<?php
session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_aluno = $conn->real_escape_string($_POST['id_aluno']);
    $senha = $_POST['senha_servidor'];
    
    if ($senha === 'ifce123') {
        $sql = "DELETE FROM alunos WHERE id = '$id_aluno'";
        $conn->query($sql);
        header("Location: painel.php?sucesso=excluido");
        exit;
    } else {
        header("Location: painel.php?erro=senha");
        exit;
    }
}
?>