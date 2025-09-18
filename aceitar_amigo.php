<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['id_usuario'];
$solicitante_id = intval($_GET['id'] ?? 0);

if ($solicitante_id > 0) {
    $stmt = $conexao->prepare("UPDATE amigos SET status = 'aceito' WHERE usuario_id = ? AND amigo_id = ? AND status = 'pendente'");
    $stmt->bind_param("ii", $solicitante_id, $usuario_id);
    $stmt->execute();

    $stmt = $conexao->prepare("SELECT id_chat FROM chat WHERE (usuario1_id = ? AND usuario2_id = ?) OR (usuario1_id = ? AND usuario2_id = ?)");
    $stmt->bind_param("iiii", $usuario_id, $solicitante_id, $solicitante_id, $usuario_id);
    $stmt->execute();
    $chat = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$chat) {
        $stmt = $conexao->prepare("INSERT INTO chat (usuario1_id, usuario2_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $usuario_id, $solicitante_id);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: chat.php");
exit;
