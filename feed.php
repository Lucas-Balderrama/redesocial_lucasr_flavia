<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/nexa_logo_icone.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/feed.css">
    <link rel="stylesheet" href="./css/nav.css">
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
                        <h3><a id="inicio" href="./index.php">Feed</a></h3>
                    </li>

                    <li>
                        <h3><a id="perfil" href="./perfil.php">Perfil</a></h3>
                    </li>
                </ul>
                <div id="user-div">
                    <?php
                    include 'conexao.php';
                    session_start();

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
            <label>No que você está pensando?</label>
            <input placeholder="Faça sua publicação aqui!" type="text">
        </div>

        <div class="publicacoes">
            <div class="div"><p>oiii</p></div>
        </div>
    </div>
</body>
</html>