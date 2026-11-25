<?php
session_start();
include 'conexao.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['perfil'] == 'Professor') {
    $id_aluno = $conn->real_escape_string($_POST['id_aluno']);
    $disciplina = $_SESSION['disciplina'];
    
    $data_aula = $conn->real_escape_string($_POST['data_aula']);
    $status = $conn->real_escape_string($_POST['status']);
    $justificativa = $conn->real_escape_string($_POST['justificativa']);
    
  
    $sql = "INSERT INTO frequencia (id_aluno, disciplina, data_aula, status, justificativa) 
            VALUES ('$id_aluno', '$disciplina', '$data_aula', '$status', '$justificativa')";
            
    $conn->query($sql);
    header("Location: painel.php?sucesso=1");
    exit;
} else {
    header("Location: painel.php");
    exit;
}
?>