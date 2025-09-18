<?php
include 'conexao.php';
session_start();

if (!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Você precisa estar logado para curtir!');</script>";
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['id_usuario'];
$post_id = intval($_POST['post_id']);

$sql = "SELECT * FROM curtidas WHERE usuario_id = ? AND post_id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("ii", $usuario_id, $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $sql = "DELETE FROM curtidas WHERE usuario_id = ? AND post_id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ii", $usuario_id, $post_id);
    $stmt->execute();
} else {
    $sql = "INSERT INTO curtidas (usuario_id, post_id) VALUES (?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ii", $usuario_id, $post_id);
    $stmt->execute();
}

header("Location: feed.php");
exit();
?>
