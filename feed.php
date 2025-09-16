<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/nexa_logo_icone.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/feed.css">
    <title>Feed</title>
</head>
<body>
<header id="header">
        <div id="container">
            <a href="index.php" id="box-img"><img class="logo" src="./img/nexa_logo.png" alt="logo"></li></a>
            <nav>
                <ul id="nav1">
                    <li>
                        <h3><a id="inicio" href="./index.php">Feed</a></h3>
                        <h3><a id="perfil" href="./perfil.php">Perfil</a></h3>
                    </li>
                </ul>
                <div id="user-div">
                    <?php
                    include 'conexao.php';
                    session_start();

                    if (isset($_SESSION['nome']) && $_SESSION['nome'] != '' ) {
                        echo "
                    <select id='user' onchange='redirecionar(this.value)'>
                        <option value='' id='opt-nome'>" . $_SESSION['nome'] . "</option>
                        <option value='logout.php'>Sair</option>
                    </select>";
                    } else {
                        echo "<h3><a id='login' href='./login.php'>Entrar</a></h3>";
                    }
                    ?>

                    <script>
                        function redirecionar(url) {
                            if (url) {
                                window.location.href = url;
                            }
                        }
                    </script>
                </div>
            </nav>
        </div>
    </header>
</body>
</html>