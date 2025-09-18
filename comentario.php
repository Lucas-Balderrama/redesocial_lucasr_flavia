<?php
include 'conexao.php';
session_start();

if (isset($_POST['post_id'], $_POST['texto'], $_SESSION['id_usuario'])) {
    $post_id = $_POST['post_id'];
    $texto = trim($_POST['texto']);
    $usuario_id = $_SESSION['id_usuario'];

    if (!empty($texto)) {
        $stmt = $conexao->prepare("INSERT INTO comentarios (post_id, usuario_id, texto) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $post_id, $usuario_id, $texto);
        $stmt->execute();
    }
}
header("Location: feed.php");
exit;
