<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['id_usuario'];
$id_amigo = intval($_GET['id_amigo'] ?? 0);

$chat_id = null;
$amigo = null;

if ($id_amigo > 0) {
    $stmt = $conexao->prepare("SELECT id_usuario, nome, foto_perfil FROM usuarios WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_amigo);
    $stmt->execute();
    $amigo = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($amigo) {
        $sql_chat = "SELECT id_chat FROM chat 
                     WHERE (usuario1_id = ? AND usuario2_id = ?) 
                        OR (usuario1_id = ? AND usuario2_id = ?)";
        $stmt = $conexao->prepare($sql_chat);
        $stmt->bind_param("iiii", $usuario_id, $id_amigo, $id_amigo, $usuario_id);
        $stmt->execute();
        $chat = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($chat) {
            $chat_id = $chat['id_chat'];
        } else {
            $stmt = $conexao->prepare("INSERT INTO chat (usuario1_id, usuario2_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $usuario_id, $id_amigo);
            $stmt->execute();
            $chat_id = $stmt->insert_id;
            $stmt->close();
        }
    }
}

$sql_amigos = "
SELECT u.id_usuario, u.nome, u.foto_perfil 
FROM amigos a
JOIN usuarios u ON u.id_usuario = a.amigo_id
WHERE a.usuario_id = ? AND a.status = 'aceito'
UNION
SELECT u.id_usuario, u.nome, u.foto_perfil 
FROM amigos a
JOIN usuarios u ON u.id_usuario = a.usuario_id
WHERE a.amigo_id = ? AND a.status = 'aceito'
";
$stmt = $conexao->prepare($sql_amigos);
$stmt->bind_param("ii", $usuario_id, $usuario_id);
$stmt->execute();
$lista_amigos = $stmt->get_result();
$stmt->close();

$sql_solicitacoes = "
SELECT u.id_usuario, u.nome, u.foto_perfil
FROM amigos a
JOIN usuarios u ON u.id_usuario = a.usuario_id
WHERE a.amigo_id = ? AND a.status = 'pendente'
";
$stmt = $conexao->prepare($sql_solicitacoes);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$solicitacoes = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Chat</title>
    <link rel="stylesheet" href="./css/nav.css">
    <link rel="stylesheet" href="./css/chat.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<header id="header">
    <div id="container">
        <a href="feed.php" id="box-img">
            <img class="logo" src="./img/nexa_logo.png" alt="logo">
        </a>
        <nav>
            <ul id="nav1">
                <li><h3><a id="inicio" href="./feed.php">Feed</a></h3></li>
                <li><h3><a id="perfil" href="./perfil.php">Perfil</a></h3></li>
                <li><h3><a id="chat" href="./chat.php">Conversas</a></h3></li>
            </ul>
            <div id="user-div">
                <?php if (!empty($_SESSION['nome'])): 
                    $fotoPerfil = !empty($_SESSION['foto_perfil']) ? $_SESSION['foto_perfil'] : './img/user_default.jpg'; ?>
                    <div class="user-menu">
                        <button id="user-btn">
                            <img class="user-foto" src="<?php echo htmlspecialchars($fotoPerfil); ?>" alt="Foto de Perfil">
                        </button>
                        
                        <div id="user-modal" class="modal">
                            <div class="modal-content">
                                <span id="close-modal">&times;</span>
                                <div class="user-info">
                                    <img class="user-foto-modal" src="<?php echo htmlspecialchars($fotoPerfil); ?>" alt="Foto de Perfil">
                                    <div class="info">
                                        <h3><?php echo htmlspecialchars($_SESSION['nome']); ?></h3>
                                        <a href="./perfil.php">Acessar Perfil</a>
                                    </div>
                                </div>
                                <div class="logout">
                                    <a href="logout.php">Sair</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <h3><a id="login" href="./index.php">Entrar</a></h3>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>

<div id="chat-container">
    <div id="amigos">
        <h3>Solicitações de amizade</h3>
        <?php if ($solicitacoes->num_rows > 0): ?>
            <?php while ($sol = $solicitacoes->fetch_assoc()): ?>
                <div class="solicitacao">
                    <div>
                        <img src="<?php echo !empty($sol['foto_perfil']) ? $sol['foto_perfil'] : './img/user_default.jpg'; ?>" alt="">
                        <span><?php echo htmlspecialchars($sol['nome']); ?></span>
                    </div>
                    <div>
                        <a class="aceitar" href="aceitar_amigo.php?id=<?php echo $sol['id_usuario']; ?>">Aceitar</a>
                        <a class="rejeitar" href="rejeitar_amigo.php?id=<?php echo $sol['id_usuario']; ?>">Rejeitar</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Nenhuma solicitação.</p>
        <?php endif; ?>

        <h3>Suas conversas</h3>
        <button id="adicionar-amigo-btn">+ Adicionar amigo</button>

        <?php while ($row = $lista_amigos->fetch_assoc()): ?>
            <a href="chat.php?id_amigo=<?php echo $row['id_usuario']; ?>">
                <img src="<?php echo !empty($row['foto_perfil']) ? $row['foto_perfil'] : './img/user_default.jpg'; ?>" alt="">
                <span><?php echo htmlspecialchars($row['nome']); ?></span>
            </a>
        <?php endwhile; ?>
    </div>

    <div id="mensagens-box">
        <?php if ($amigo): ?>
            <h2 style="padding:10px;">Chat com <?php echo htmlspecialchars($amigo['nome']); ?></h2>
            <div id="mensagens"></div>

            <form id="formMsg">
                <input type="text" id="texto" name="texto" placeholder="Digite uma mensagem..." required>
                <button type="submit">Enviar</button>
            </form>
        <?php else: ?>
            <div style="padding:20px;">Selecione um amigo ao lado para começar a conversar.</div>
        <?php endif; ?>
    </div>
</div>

<?php if ($chat_id): ?>
<script>
let lastId = 0;
const chatId = <?php echo $chat_id; ?>;
const userId = <?php echo $usuario_id; ?>;
const mensagensDiv = document.getElementById('mensagens');

async function buscarMensagens() {
    const resp = await fetch(`get_mensagens.php?chat_id=${chatId}&last_id=${lastId}`);
    const msgs = await resp.json();

    msgs.forEach(m => {
        const div = document.createElement('div');
        div.classList.add('msg', m.remetente_id == userId ? 'eu' : 'ele');
        div.textContent = m.texto;
        mensagensDiv.appendChild(div);
        mensagensDiv.scrollTop = mensagensDiv.scrollHeight;
        lastId = m.id_mensagem;
    });
}

setInterval(buscarMensagens, 2000);

document.getElementById('formMsg').addEventListener('submit', async (e) => {
    e.preventDefault();
    const texto = document.getElementById('texto').value.trim();
    if (!texto) return;

    const formData = new FormData();
    formData.append('chat_id', chatId);
    formData.append('texto', texto);

    await fetch('enviar_mensagem.php', {
        method: 'POST',
        body: formData
    });

    document.getElementById('texto').value = '';
});
</script>
<?php endif; ?>

<script>
document.getElementById('adicionar-amigo-btn').addEventListener('click', async () => {
    const { value: email } = await Swal.fire({
        title: 'Adicionar amigo',
        input: 'email',
        inputLabel: 'Digite o email do amigo',
        inputPlaceholder: 'exemplo@dominio.com',
        showCancelButton: true,
        confirmButtonText: 'Enviar'
    });

    if (email) {
        const formData = new FormData();
        formData.append('email', email);

        const res = await fetch('add_amigo.php', {
            method: 'POST',
            body: formData
        });

        const data = await res.json();

        Swal.fire({
            icon: data.status,
            title: data.msg
        });
    }
});
</script>
</body>
</html>
