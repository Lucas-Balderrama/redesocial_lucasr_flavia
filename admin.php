<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['tipo'] !== 'Admin') {
    echo '<p style="color:rgba(45, 68, 85, 0.93);; text-align:center; font-family: \'Inter\', sans-serif; font-size:80px;margin-top:180px;">Você não tem permissão para acessar esta página</p>';
    echo '<a href="index.php" style="text-decoration: none;"><button style="display: block; margin: 0 auto; padding: 22px; background: rgba(45, 68, 85, 0.93); color: white; font-size: 22px; border: none; cursor: pointer; transition: background 0.3s, transform 0.3s;" onmouseover="this.style.background=\'rgba(45, 68, 85, 1)\'" onmouseout="this.style.background=\'rgba(45, 68, 85, 0.93)\'; this.style.transform=\'scale(1)\'">Voltar</button></a>';
    exit();
}

$sql = "
    SELECT 
        reservas.id_reserva, 
        reservas.tp_quarto, 
        reservas.plano_refeicao, 
        reservas.entrada, 
        reservas.saida, 
        reservas.forma_pagamento,
        usuarios.nome_usuario, 
        usuarios.email_usuario,
        reservas.valor_total
    FROM reservas
    JOIN usuarios ON reservas.id_usuario = usuarios.id_usuario
";

$resultado = $conexao->query($sql);
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Painel do Administrador - Reservas</title>
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
                    if (isset($_SESSION['nome']) && $_SESSION['nome'] != ''){
                        echo "<select name='' id='user' onchange='sair()'>
                                <option value='' id='opt-nome'>".$_SESSION['nome']."</option>
                                <a><option value='' id='opt-sair'>Sair</option></a>
                            </select>";
                    } elseif (isset($_SESSION['nome']) && $_SESSION['nome'] == '') {
                        echo "<h3><a id='login' href='./login.php'>Entrar</a></h3>";
                    }
                    ?>
                    <script>
                        function sair(){
                            window.location.href = "./logout.php";
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
    
    <h1>Reservas</h1>
    <div class="container" style="overflow-x:auto;">

        <?php if ($resultado->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID da reserva</th>
                        <th>Nome do cliente</th>
                        <th>Email</th>
                        <th>Tipo de quarto</th>
                        <th>Plano refeição</th>
                        <th>Data de entrada</th>
                        <th>Data de saída</th>
                        <th>Forma de pagamento</th>
                        <th>Valor total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo ($row['id_reserva']); ?></td>
                            <td><?php echo ($row['nome_usuario']); ?></td>
                            <td><?php echo ($row['email_usuario']); ?></td>
                            <td><?php echo ($row['tp_quarto']); ?></td>
                            <td><?php echo ($row['plano_refeicao']); ?></td>
                            <td><?php echo ($row['entrada']); ?></td>
                            <td><?php echo ($row['saida']); ?></td>
                            <td><?php echo ($row['forma_pagamento']); ?></td>
                            <td><?php echo ($row['valor_total']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color:white; margin-top: 30px; text-align: center;">Nenhuma reserva encontrada.</p>
        <?php endif; ?>
    </div>
</body>
</html>