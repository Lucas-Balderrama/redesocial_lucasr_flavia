<?php
include 'conexao.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_usuario = $_POST['username'];
    $email_usuario = $_POST['emailForm'];
    $comentario = $_POST['comentario'];

    $id_usuario = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : null;

    if ($id_usuario) {
        $sql_insercao = "INSERT INTO comentarios (id_usuario, nome_usuario, email_usuario, comentario) VALUES (?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql_insercao);

        if ($stmt) {
            $stmt->bind_param("isss", $id_usuario, $nome_usuario, $email_usuario, $comentario);
            if ($stmt->execute()) {
                echo "<script>
                Swal.fire({
                    title: 'Comentário enviado com sucesso!'',
                    icon: 'success'
                });
        </script>";
            } else {
                echo "Erro ao enviar o comentário: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Erro na preparação da consulta: " . $conexao->error;
        }
    } else {
        echo "Erro: Usuário não está logado.";
    }
    $conexao->close();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/contato.css">
    <script src="js/contato.js" defer></script>
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>HostCenter - Sua estadia Ideal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&family=Plus+Jakarta+Sans:wght@200..800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Reem+Kufi:wght@400..700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<header id="header">
        <div id="container">
            <a href="index.php" id="box-img"><img class= "logo" src="./img/HC-logo.svg" alt="logo"></li></a>
            <nav>
                <ul id="nav1">
                    <li><h3><a id="inicio" href="./index.php">início</a></h3></li>
                    <li><h3><a href="./servicos.php">Serviços</a></h3></li>
                    <li><h3><a href="./ocupacoes.php">Ocupações</a></h3></li>
                    <li><h3><a href="./contato.php">Contato</a></h3></li>
                </ul>
                <div id="user-div">
                <?php
                if (isset($_SESSION['nome']) && $_SESSION['nome'] != '' && $_SESSION['tipo'] == 'Admin') {
                    echo "
                    <select id='user' onchange='redirecionar(this.value)'>
                        <option value='' id='opt-nome'>".$_SESSION['nome']."</option>
                        <option value='admin.php'>Admin</option>
                        <option value='logout.php'>Sair</option>
                    </select>";
                } elseif (isset($_SESSION['nome']) && $_SESSION['nome'] != '' && $_SESSION['tipo'] == 'Cliente') {
                    echo "
                    <select id='user' onchange='redirecionar(this.value)'>
                        <option value='' id='opt-nome'>".$_SESSION['nome']."</option>
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
                    <li><h3><a href="./index.php">início</a></h3></li>
                    <li><h3><a href="./servicos.php">Serviços</a></h3></li>
                    <li><h3><a href="./ocupacoes.php">Ocupações</a></h3></li>
                    <li><h3><a href="./contato.php">Contato</a></h3></li>
                </ul>
            </nav>
        </div>
    </header>
    <div id="centralizar">
    <div id="contato">

            <div class="caixinhaContato" id="insta">
                <i id="iconInsta" class="fa-brands fa-instagram"></i><br> 
                <h3>Instagram</h3>
                <a id="insta-smart" href="https://www.instagram.com/smartwebsn/" target="_blank">Acesse em:<br> @smartwebsn</a>
            </div>
            <div class="caixinhaContato" id="email">
                <i class="fa-regular fa-envelope"></i>
                <h3>Email</h3>
                <p>smartwebsn<br>@gmail.com</p>
            </div>
            <div class="caixinhaContato" id="equipe">
                <i class="fa-solid fa-users"></i>
                <h3>Equipe</h3>
                <ul>
                    <li><a href="https://linktr.ee/analivialopess" target="_blank">Ana Lívia Lopes</a></li>
                    <li><a href="https://linktr.ee/gabrielreiss" target="_blank">Gabriel Reis</a></li>
                    <li><a href="https://linktr.ee/isadoragomess" target="_blank">Isadora Gomes</a></li>
                    <li><a href="https://linktr.ee/lucasbalderrama" target="_blank">Lucas Randal</a></li>
                </ul>
            </div>
            <div class="caixinhaContato" id="local">
                <i class="fa-solid fa-location-dot"></i>
                <h3>Localização</h3>
                <p>Hotel localizado em Serra Serena, no bairro Vila Verde, número 31.</p>
            </div>

            <form method="POST">
                <h2>Envie seu comentario</h2>
                <div class="formulario">
                    <input type="text" name="username" id="username" placeholder="Digite seu nome" required>
                </div>
                <div class="formulario">
                    <input type="email" name="emailForm" id="emailForm" placeholder="Digite seu email" required>
                </div>
                <div class="formulario">
                    <input type="text" name="comentario" id="comentario" placeholder="Digite sua observação">
                </div>
                <div class="formulario">
                    <button type="submit" id="entrar">Enviar</button>  
                </div>
            </form>
        </div>
    </div>
    <footer>
            <div id="footer">
        
                <div class="contato">
                    <h2>Informações de Contato</h2>
                    <p><strong>SESI Caçapava:</strong></p>
                    <p>Endereço: Av. Monsenhor Theodomiro Lobo, 100, Caçapava - SP, 12285-050</p>
                    <p>Telefone: (12) 3653-1943</p>
                    <p>E-mail: contato@sesi-cacapava.com.br</p>
            
                    <p><strong>SENAI Taubaté:</strong></p>
                    <p>Endereço: Av. Independência, 846 - Independência, Taubaté - SP, 12031-001</p>
                    <p>Telefone: (12) 3609-5701</p>
                    <p>E-mail: senaitaubate@sp.senai.br</p>
                </div> 
            
                <div class="equipe">
                    <h2>Equipe Desenvolvedora</h2>
                    <ul>
                        <p>Ana Lívia dos Santos Lopes</p>
                        <li><a href="https://linktr.ee/analivialopess" target="_blank">Link para contato</a></li>
            
                        <p>Gabriel Reis de Brito</p>
                        <li><a href="https://linktr.ee/gabrielreiss" target="_blank">Link para contato</a></li>
            
                        <p>Isadora Gomes da Silva</p>
                        <li><a href="https://linktr.ee/isadoragomess" target="_blank">Link para contato</a></li>
            
                        <p>Lucas Randal Abreu Balderrama</p>
                        <li><a href="https://linktr.ee/lucasbalderrama" target="_blank">Link para contato</a></li>
                    </ul>
                </div>
            
                <div class="links-adicionais">
                    <h2>Links Adicionais</h2>
                    <ul>
                        <li><a href="termos.php" target="_blank">Termos de Uso</a></li>
                        <li><a href="privacidade.php" target="_blank">Política de Privacidade</a></li>
                    </ul>
                </div>
            </div>
        </footer> 

</body>
</html>