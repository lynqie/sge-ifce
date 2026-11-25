<?php
session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['perfil'] == 'Professor') {
    $id_aluno = $conn->real_escape_string($_POST['id_aluno']);
    $disciplina = $_SESSION['disciplina'];

    $n1 = $conn->real_escape_string($_POST['n1']);
    $n2 = $conn->real_escape_string($_POST['n2']);
    $n3 = $conn->real_escape_string($_POST['n3']);
    $n4 = $conn->real_escape_string($_POST['n4']);

    $colunas = ['id_aluno', 'disciplina'];
    $valores = ["'$id_aluno'", "'$disciplina'"];
    $updates = [];

    if ($n1 !== '') { $colunas[] = 'n1'; $valores[] = "'$n1'"; $updates[] = "n1='$n1'"; }
    if ($n2 !== '') { $colunas[] = 'n2'; $valores[] = "'$n2'"; $updates[] = "n2='$n2'"; }
    if ($n3 !== '') { $colunas[] = 'n3'; $valores[] = "'$n3'"; $updates[] = "n3='$n3'"; }
    if ($n4 !== '') { $colunas[] = 'n4'; $valores[] = "'$n4'"; $updates[] = "n4='$n4'"; }

    if (!empty($updates)) {
        $cols_str = implode(', ', $colunas);
        $vals_str = implode(', ', $valores);
        $upd_str  = implode(', ', $updates);

        $sql = "INSERT INTO notas ($cols_str) VALUES ($vals_str)
                ON DUPLICATE KEY UPDATE $upd_str";

        $conn->query($sql);
    }

    header("Location: painel.php?sucesso=1");
    exit;
} else {
    header("Location: painel.php");
    exit;
}
?>