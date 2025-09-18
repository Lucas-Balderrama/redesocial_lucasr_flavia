<?php
include 'conexao.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    if (!isset($_SESSION['id_usuario'])) {
        echo "<script>alert('Você precisa estar logado para fazer um post!');</script>";
        $_SESSION['url_anterior'] = $_SERVER['REQUEST_URI'];
        echo "<script>window.location.href = './index.php';</script>";
        exit;
    }

    $id_usuario = $_SESSION['id_usuario'];
    $texto = trim($_POST['texto']);

    $imagem = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $nomeImagem = uniqid() . "_" . basename($_FILES['imagem']['name']);
        $caminho = "uploads/" . $nomeImagem;
        move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho);
        $imagem = $caminho;
    }

    if (!empty($texto)) {
        $stmt = $conexao->prepare("INSERT INTO posts (usuario_id, texto, imagem) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $id_usuario, $texto, $imagem);

        if ($stmt->execute()) {
            echo '<script>
                    Swal.fire({
                        text: "Post efetuado com sucesso!",
                        icon: "success"
                    }).then(() => { window.location.href = "feed.php"; });
                  </script>';
        } else {
            echo "Erro ao inserir os dados: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "<script>alert('Digite algo para publicar!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/nexa_logo_icone.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/feed.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="./js/feed.js"></script>
    <title>Feed</title>
</head>

<body>
    <header id="header">
        <div id="container">
            <a href="feed.php" id="box-img"><img class="logo" src="./img/nexa_logo.png" alt="logo"></li></a>
            <nav>
                <ul id="nav1">
                    <li>
                        <h3><a id="inicio" href="./feed.php">Feed</a></h3>
                    </li>

                    <li>
                        <h3><a id="perfil" href="./perfil.php">Perfil</a></h3>
                    </li>
                </ul>
                <div id="user-div">
                    <?php
                    $id = $_SESSION['id_usuario'];
                    $sql = "SELECT foto_perfil FROM usuarios WHERE id_usuario = ?";
                    $stmt = $conexao->prepare($sql);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $usuario = $result->fetch_assoc();

                    $fotoPerfil = !empty($usuario['foto_perfil']) ? $usuario['foto_perfil'] : './img/user_default.jpg';

                    if (isset($_SESSION['nome']) && $_SESSION['nome'] != '') {
                        $fotoPerfil = isset($_SESSION['foto_perfil']) && $_SESSION['foto_perfil'] != ''
                            ? $_SESSION['foto_perfil']
                            : './img/user_default.jpg';
                        echo "
                    <div class='user-menu'>
                        <button id='user-btn'>
                            <img class='user-foto' src='{$fotoPerfil}' alt='Foto de Perfil'>
                        </button>
                        
                        <div id='user-modal' class='modal'>
                            <div class='modal-content'>
                                <span id='close-modal'>&times;</span>
                                
                                <div class='user-info'>
                                    <img class='user-foto-modal' src='{$fotoPerfil}' alt='Foto de Perfil'>
                                    <div class='info'>
                                        <h3 id='nome-modal' >{$_SESSION['nome']}</h3>
                                        <a id='link-modal' href='./perfil.php'>Acessar Perfil</a>
                                    </div>
                                </div>
                                
                                <div class='logout'>
                                    <a id='sair-modal' href='logout.php'>Sair</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    ";
                    } else {
                        echo "<h3><a id='login' href='./index.php'>Entrar</a></h3>";
                    }
                    ?>
                </div>

            </nav>
        </div>
    </header>

    <div class="feed">
        <div class="publicar">
            <form method="POST" action="feed.php" enctype="multipart/form-data">
                <label>No que você está pensando?</label>
                <input name="texto" placeholder="Faça sua publicação aqui!" type="text" required>
                <input type="file" name="imagem" accept="image/*">
                <button type="submit" name="submit">Publicar</button>
            </form>
        </div>


        <div class="publicacoes">
    <?php
    $sql = "SELECT p.id_post, p.texto, p.imagem, p.criado_em, u.nome, u.foto_perfil 
            FROM posts p 
            JOIN usuarios u ON p.usuario_id = u.id_usuario 
            ORDER BY p.criado_em DESC";
    $result = $conexao->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $foto = !empty($row['foto_perfil']) ? $row['foto_perfil'] : './img/user_default.jpg';
            $post_id = $row['id_post'];

            $sqlCurtidas = "SELECT COUNT(*) AS total FROM curtidas WHERE post_id = $post_id";
            $resCurtidas = $conexao->query($sqlCurtidas);
            $totalCurtidas = ($resCurtidas->num_rows > 0) ? $resCurtidas->fetch_assoc()['total'] : 0;

            $jaCurtiu = false;
            if (isset($_SESSION['id_usuario'])) {
                $usuario_id = $_SESSION['id_usuario'];
                $sqlJaCurtiu = "SELECT * FROM curtidas WHERE usuario_id = $usuario_id AND post_id = $post_id";
                $resJaCurtiu = $conexao->query($sqlJaCurtiu);
                $jaCurtiu = ($resJaCurtiu->num_rows > 0);
            }

            echo "
            <div class='publicacao'>
                <div class='usuario'>
                    <img src='{$foto}' alt='Foto de Perfil'>
                    <h3>{$row['nome']}</h3>
                </div>
                <p>{$row['texto']}</p>
            ";

            if (!empty($row['imagem'])) {
                echo "<img src='{$row['imagem']}' class='post-img'>";
            }

            echo "<small id='data-publicacao'>{$row['criado_em']}</small>";

            if (isset($_SESSION['id_usuario'])) {
                echo "
                <form action='curtir.php' method='POST' style='display:inline;'>
                    <input type='hidden' name='post_id' value='{$post_id}'>
                    <button type='submit'>" . ($jaCurtiu ? "Descurtir" : "Curtir") . "</button>
                </form>
                ";
            }

            echo "<p><small><i class='fa-regular fa-heart'></i> {$totalCurtidas} curtida(s)</small></p>";

            $sqlComentarios = "SELECT c.texto, c.criado_em, u.nome, u.foto_perfil
                               FROM comentarios c
                               JOIN usuarios u ON c.usuario_id = u.id_usuario
                               WHERE c.post_id = $post_id
                               ORDER BY c.criado_em ASC";
            $resComentarios = $conexao->query($sqlComentarios);

            echo "<div class='comentarios'>";
            if ($resComentarios->num_rows > 0) {
                while ($coment = $resComentarios->fetch_assoc()) {
                    $fotoComent = !empty($coment['foto_perfil']) ? $coment['foto_perfil'] : './img/user_default.jpg';
                    echo "
                    <div class='comentario'>
                        <div>
                            <div class='identificacao-comentario'>
                                <img id='foto-comentario' src='{$fotoComent}' alt='Foto de Perfil'>
                                <strong id='nome-comentario'>{$coment['nome']}</strong>
                            </div>
                            <p id='texto-comentario'>{$coment['texto']}</p>
                            <small id='data-comentario'>{$coment['criado_em']}</small>
                        </div>
                    </div>
                    ";
                }
            } else {
                echo "<p><small>Sem comentários ainda.</small></p>";
            }
            echo "</div>";

            if (isset($_SESSION['id_usuario'])) {
                echo "
                <form action='comentario.php' method='POST' class='form-comentario'>
                    <input type='hidden' name='post_id' value='{$post_id}'>
                    <input type='text' name='texto' placeholder='Escreva um comentário...' required>
                    <button type='submit'><i class='fa-regular fa-paper-plane'></i></button>
                </form>
                ";
            }

            echo "</div>";
        }
    } else {
        echo "<p>Nenhuma publicação ainda.</p>";
    }
    ?>
</div>



    </div>
    <?php $conexao->close(); ?>
</body>

</html>