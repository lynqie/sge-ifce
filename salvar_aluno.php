<?php
session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $conn->real_escape_string($_POST['nome']);
    $idade = $conn->real_escape_string($_POST['idade']);
    $curso = $conn->real_escape_string($_POST['curso']);
    $email = $conn->real_escape_string($_POST['email']);
    $telefone = $conn->real_escape_string($_POST['telefone']);
        $matricula_automatica = '';
    for($i = 0; $i < 8; $i++) {
        $matricula_automatica .= rand(1, 9);
    }

    $sql = "INSERT INTO alunos (matricula, nome, idade, curso, email, telefone) 
            VALUES ('$matricula_automatica', '$nome', '$idade', '$curso', '$email', '$telefone')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: painel.php?sucesso=1");
        exit;
    } else {
   
        echo "<h1 style='color: red; font-family: Arial;'>Deu Erro no Banco de Dados!</h1>";
        echo "<p style='font-family: Arial; font-size: 18px;'>O MySQL respondeu o seguinte: <strong>" . $conn->error . "</strong></p>";
        echo "<br><br><a href='painel.php' style='font-family: Arial;'>Voltar ao painel</a>";
    }
}
?>