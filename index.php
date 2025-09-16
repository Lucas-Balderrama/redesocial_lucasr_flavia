<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<header id="header">
        <div id="container">
            <a href="index.php" id="box-img"><img class="logo" src="./img/HC-logo.svg" alt="logo"></li></a>
            <nav>
                <ul id="nav1">
                    <li>
                        <h3><a id="inicio" href="./index.php">Feed</a></h3>
                    </li>
                    <li>
                        <h3><a href="./servicos.php">Serviços</a></h3>
                    </li>
                    <li>
                        <h3><a href="./ocupacoes.php">Ocupações</a></h3>
                    </li>
                    <li>
                        <h3><a href="./contato.php">Contato</a></h3>
                    </li>
                </ul>
                <div id="user-div">
                    <?php
                    include 'conexao.php';
                    session_start();

                    if (isset($_SESSION['nome']) && $_SESSION['nome'] != '' && $_SESSION['tipo'] == 'Admin') {
                        echo "
                    <select id='user' onchange='redirecionar(this.value)'>
                        <option value='' id='opt-nome'>" . $_SESSION['nome'] . "</option>
                        <option value='admin.php'>Admin</option>
                        <option value='logout.php'>Sair</option>
                    </select>";
                    } elseif (isset($_SESSION['nome']) && $_SESSION['nome'] != '' && $_SESSION['tipo'] == 'Cliente') {
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
                <input type="checkbox" id="checkbox">
                <label for="checkbox" id="botao">☰</label>
                <ul id="nav2">
                    <li>
                        <h3><a href="./index.php">início</a></h3>
                    </li>
                    <li>
                        <h3><a href="./servicos.php">Serviços</a></h3>
                    </li>
                    <li>
                        <h3><a href="./ocupacoes.php">Ocupações</a></h3>
                    </li>
                    <li>
                        <h3><a href="./contato.php">Contato</a></h3>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
</body>
</html>