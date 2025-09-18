<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(403);
    exit;
}

$usuario_id = $_SESSION['id_usuario'];
$chat_id = intval($_POST['chat_id'] ?? 0);
$texto = trim($_POST['texto'] ?? '');

if ($chat_id > 0 && $texto !== '') {
    $stmt = $conexao->prepare("INSERT INTO mensagens (chat_id, remetente_id, texto) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $chat_id, $usuario_id, $texto);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['status' => 'ok']);
} else {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'msg' => 'Dados inválidos']);
}
