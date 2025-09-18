<?php
session_start();
require 'conexao.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Usuário não autenticado.']);
    exit;
}

$usuario_id = $_SESSION['id_usuario'];
$email = trim($_POST['email'] ?? '');

if (empty($email)) {
    echo json_encode(['status' => 'error', 'msg' => 'Digite um email válido.']);
    exit;
}

$stmt = $conexao->prepare("SELECT id_usuario FROM usuarios WHERE email = ? AND id_usuario != ?");
$stmt->bind_param("si", $email, $usuario_id);
$stmt->execute();
$amigo = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($amigo) {
    $id_amigo = $amigo['id_usuario'];

    $sql_check = "SELECT * FROM amigos 
                  WHERE (usuario_id = ? AND amigo_id = ?) 
                     OR (usuario_id = ? AND amigo_id = ?)";
    $stmt = $conexao->prepare($sql_check);
    $stmt->bind_param("iiii", $usuario_id, $id_amigo, $id_amigo, $usuario_id);
    $stmt->execute();
    $existe = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($existe) {
        echo json_encode(['status' => 'info', 'msg' => 'Já existe uma solicitação ou amizade com esse usuário.']);
    } else {
        $sql_add = "INSERT INTO amigos (usuario_id, amigo_id, status) VALUES (?, ?, 'pendente')";
        $stmt = $conexao->prepare($sql_add);
        $stmt->bind_param("ii", $usuario_id, $id_amigo);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['status' => 'success', 'msg' => 'Pedido de amizade enviado!']);
    }
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Usuário não encontrado.']);
}
