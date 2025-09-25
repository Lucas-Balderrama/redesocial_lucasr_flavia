<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['id_usuario'];
$id_amigo_ativo = intval($_GET['id_amigo'] ?? 0);
$caminho_foto_padrao = './img/user_default.jpg';

$chat_id = null;
$amigo_info = null;

if ($id_amigo_ativo > 0) {
    $stmt = $conexao->prepare("SELECT id_usuario, nome, foto_perfil FROM usuarios WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_amigo_ativo);
    $stmt->execute();
    $amigo_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($amigo_info) {
        $sql_chat = "SELECT id_chat FROM chat 
                     WHERE (usuario1_id = ? AND usuario2_id = ?) 
                        OR (usuario1_id = ? AND usuario2_id = ?)";
        $stmt = $conexao->prepare($sql_chat);
        $stmt->bind_param("iiii", $usuario_id, $id_amigo_ativo, $id_amigo_ativo, $usuario_id);
        $stmt->execute();
        $chat = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($chat) {
            $chat_id = $chat['id_chat'];
        } else {
            $stmt = $conexao->prepare("INSERT INTO chat (usuario1_id, usuario2_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $usuario_id, $id_amigo_ativo);
            $stmt->execute();
            $chat_id = $stmt->insert_id;
            $stmt->close();
        }
    }
}

$sql_amigos = "
    SELECT u.id_usuario, u.nome, u.foto_perfil 
    FROM amigos a JOIN usuarios u ON u.id_usuario = a.amigo_id
    WHERE a.usuario_id = ? AND a.status = 'aceito'
    UNION
    SELECT u.id_usuario, u.nome, u.foto_perfil 
    FROM amigos a JOIN usuarios u ON u.id_usuario = a.usuario_id
    WHERE a.amigo_id = ? AND a.status = 'aceito'
";
$stmt_amigos = $conexao->prepare($sql_amigos);
$stmt_amigos->bind_param("ii", $usuario_id, $usuario_id);
$stmt_amigos->execute();
$lista_amigos = $stmt_amigos->get_result();
$stmt_amigos->close();

$sql_solicitacoes = "
    SELECT u.id_usuario, u.nome, u.foto_perfil
    FROM amigos a JOIN usuarios u ON u.id_usuario = a.usuario_id
    WHERE a.amigo_id = ? AND a.status = 'pendente'
";
$stmt_solicitacoes = $conexao->prepare($sql_solicitacoes);
$stmt_solicitacoes->bind_param("i", $usuario_id);
$stmt_solicitacoes->execute();
$solicitacoes = $stmt_solicitacoes->get_result();
$stmt_solicitacoes->close();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Chat | Nexa</title>
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
                    <li>
                        <h3><a id="inicio" href="./feed.php">Feed</a></h3>
                    </li>
                    <li>
                        <h3><a id="perfil" href="./perfil.php">Perfil</a></h3>
                    </li>
                    <li>
                        <h3><a id="chat" href="./chat.php">Conversas</a></h3>
                    </li>
                </ul>
                <div id="user-div">
                    <?php if (!empty($_SESSION['nome'])):
                        $fotoPerfil = !empty($_SESSION['foto_perfil']) ? $_SESSION['foto_perfil'] : './img/user_default.jpg'; ?>
                        <div class="user-menu">
                            <button id="user-btn">
                                <img class="user-foto" src="<?php echo htmlspecialchars($fotoPerfil); ?>"
                                    alt="Foto de Perfil">
                            </button>

                            <div id="user-modal" class="modal">
                                <div class="modal-content">
                                    <span id="close-modal">&times;</span>
                                    <div class="user-info">
                                        <img class="user-foto-modal" src="<?php echo htmlspecialchars($fotoPerfil); ?>"
                                            alt="Foto de Perfil">
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
                            <img src="<?php echo !empty($sol['foto_perfil']) ? htmlspecialchars($sol['foto_perfil']) : htmlspecialchars($caminho_foto_padrao); ?>"
                                alt="Foto de <?php echo htmlspecialchars($sol['nome']); ?>">
                            <span><?php echo htmlspecialchars($sol['nome']); ?></span>
                        </div>
                        <div>
                            <a class="aceitar" href="aceitar_amigo.php?id=<?php echo $sol['id_usuario']; ?>">Aceitar</a>
                            <a class="rejeitar" href="rejeitar_amigo.php?id=<?php echo $sol['id_usuario']; ?>">Rejeitar</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Nenhuma solicitação pendente.</p>
            <?php endif; ?>

            <h3>Suas conversas</h3>
            <button id="adicionar-amigo-btn" class="btn-amigo">+ Adicionar amigo</button>


            <?php while ($row = $lista_amigos->fetch_assoc()): ?>
                <?php
                $classe_ativo = ($row['id_usuario'] == $id_amigo_ativo) ? 'active' : '';
                ?>
                <a href="chat.php?id_amigo=<?php echo $row['id_usuario']; ?>" class="<?php echo $classe_ativo; ?>">
                    <img src="<?php echo !empty($row['foto_perfil']) ? htmlspecialchars($row['foto_perfil']) : htmlspecialchars($caminho_foto_padrao); ?>"
                        alt="Foto de <?php echo htmlspecialchars($row['nome']); ?>">
                    <span><?php echo htmlspecialchars($row['nome']); ?></span>
                </a>
            <?php endwhile; ?>
        </div>

        <div id="mensagens-box">
            <?php if ($amigo_info): ?>
                <div class="chat-header">
                    <img src="<?php echo !empty($amigo_info['foto_perfil']) ? htmlspecialchars($amigo_info['foto_perfil']) : htmlspecialchars($caminho_foto_padrao); ?>"
                        alt="Foto de <?php echo htmlspecialchars($amigo_info['nome']); ?>">
                    <h2><?php echo htmlspecialchars($amigo_info['nome']); ?></h2>
                </div>

                <div id="mensagens"></div>

                <form id="formMsg">
                    <input type="text" id="texto" name="texto" placeholder="Digite uma mensagem..." required
                        autocomplete="off">
                    <button type="submit" title="Enviar Mensagem">&#10148;</button>
                </form>
            <?php else: ?>
                <div class="placeholder-chat">
                    <h2>Selecione uma conversa</h2>
                    <p>Escolha um amigo ao lado para começar a conversar.</p>
                </div>
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