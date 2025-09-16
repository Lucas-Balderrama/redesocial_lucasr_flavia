<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $id_usuario = $_SESSION['id_usuario'];

    $pasta = "./uploads/";

    if (!is_dir($pasta)) {
        mkdir($pasta, 0755, true);
    }

    $nomeArquivo = uniqid() . "-" . basename($_FILES['foto']['name']);
    $caminhoFinal = $pasta . $nomeArquivo;

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminhoFinal)) {
        $sql = "UPDATE usuarios SET foto_perfil = ? WHERE id_usuario = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("si", $caminhoFinal, $id_usuario);
        $stmt->execute();

        $_SESSION['foto_perfil'] = $caminhoFinal;

        header("Location: perfil.php");
        exit;
    } else {
        echo "Erro ao mover a foto para a pasta de uploads.";
    }
} else {
    echo "Nenhum arquivo enviado ou erro no upload.";
}
