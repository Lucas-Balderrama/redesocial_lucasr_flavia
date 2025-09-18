<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(403);
    exit;
}

$chat_id = intval($_GET['chat_id'] ?? 0);
$last_id = intval($_GET['last_id'] ?? 0);

if ($chat_id > 0) {
    $stmt = $conexao->prepare("SELECT id_mensagem, remetente_id, texto, enviado_em 
                               FROM mensagens 
                               WHERE chat_id = ? AND id_mensagem > ? 
                               ORDER BY id_mensagem ASC");
    $stmt->bind_param("ii", $chat_id, $last_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $mensagens = [];
    while ($row = $result->fetch_assoc()) {
        $mensagens[] = $row;
    }

    $stmt->close();

    header('Content-Type: application/json');
    echo json_encode($mensagens);
} else {
    http_response_code(400);
    echo json_encode([]);
}
