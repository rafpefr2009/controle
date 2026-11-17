<?php
require_once __DIR__ . '/includes/funcoes.php';

// Protótipo: o envio do formulário apenas navega para a agenda do cliente.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    redirecionar('agenda_cliente.php');
}

$titulo_pagina = 'Criar cadastro — AgendaSalão';
$menu_tipo = 'publico';
$menu_ativo = 'acesso';
$estado = isset($_GET['estado']) ? (string) $_GET['estado'] : '';
require_once __DIR__ . '/includes/header.php';
?>

<main id="conteudo" class="conteudo">
    <div class="container">
        <header class="pagina-cabecalho">
            <div>
                <h1>Criar cadastro de cliente</h1>
                <p>Protótipo visual · nenhum dado é gravado.</p>
            </div>
            <a class="btn btn-secundario" href="index.php#acesso">Voltar para o acesso</a>
        </header>

        <?php if ($estado === 'sucesso'): ?>
            <div class="mensagem-sucesso" role="status">
                <p>Cadastro criado! Você já pode consultar horários e fazer seu agendamento.</p>
            </div>
        <?php elseif ($estado === 'erro'): ?>
            <div class="mensagem-erro" role="alert">
                <p>Não foi possível criar o cadastro. Confira os campos destacados.</p>
            </div>
        <?php else: ?>
            <div class="mensagem-aviso" role="note">
                <p>
                    <strong>Protótipo:</strong> ao enviar, você vai para a agenda do cliente.
                    Para ver o estado de validação, abra
                    <code>cadastro_cliente.php?estado=erro</code>.
                </p>
            </div>
        <?php endif; ?>

        <div class="cadastro-grade">
            <form class="formulario" method="post" action="cadastro_cliente.php">
                <input type="hidden" name="csrf_token" value="prototipo">

                <div class="form-campo">
                    <label class="label-text" for="campo-nome-cliente">Nome completo</label>
                    <input class="input" id="campo-nome-cliente" name="nome" type="text"
                           value="Maria Silva" autocomplete="name" required
                           placeholder="Ex.: Maria Silva" aria-describedby="ajuda-nome-cliente">
                    <p class="campo-ajuda" id="ajuda-nome-cliente">Como seu nome aparecerá no agendamento.</p>
                </div>

                <div class="form-linha">
                    <div class="form-campo<?php echo $estado === 'erro' ? ' campo-erro' : ''; ?>">
                        <label class="label-text" for="campo-usuario-cadastro">Usuário</label>
                        <input class="input" id="campo-usuario-cadastro" name="usuario" type="text"
                               value="maria.silva" autocomplete="username" required
                               placeholder="Ex.: maria.silva" aria-describedby="ajuda-usuario-cadastro">
                        <?php if ($estado === 'erro'): ?>
                            <p class="erro-campo" id="ajuda-usuario-cadastro">Esse usuário já está em uso.</p>
                        <?php else: ?>
                            <p class="campo-ajuda" id="ajuda-usuario-cadastro">Use letras, números ou ponto.</p>
                        <?php endif; ?>
                    </div>
                    <div class="form-campo">
                        <label class="label-text" for="campo-telefone-cadastro">Telefone</label>
                        <input class="input" id="campo-telefone-cadastro" name="telefone" type="tel"
                               value="(11) 98765-4321" autocomplete="tel"
                               placeholder="Ex.: (11) 99999-0000">
                    </div>
                </div>

                <div class="form-linha">
                    <div class="form-campo<?php echo $estado === 'erro' ? ' campo-erro' : ''; ?>">
                        <label class="label-text" for="campo-senha-cadastro">Senha</label>
                        <input class="input" id="campo-senha-cadastro" name="senha" type="password"
                               autocomplete="new-password" required
                               placeholder="••••••••" aria-describedby="ajuda-senha-cadastro">
                        <?php if ($estado === 'erro'): ?>
                            <p class="erro-campo" id="ajuda-senha-cadastro">A senha deve ter ao menos 8 caracteres.</p>
                        <?php else: ?>
                            <p class="campo-ajuda" id="ajuda-senha-cadastro">Mínimo de 8 caracteres.</p>
                        <?php endif; ?>
                    </div>
                    <div class="form-campo">
                        <label class="label-text" for="campo-confirmar-senha">Confirmar senha</label>
                        <input class="input" id="campo-confirmar-senha" name="confirmar_senha" type="password"
                               autocomplete="new-password" required placeholder="••••••••">
                    </div>
                </div>

                <div class="form-rodape">
                    <button class="btn btn-primario" type="submit">Criar cadastro</button>
                    <a class="btn btn-secundario" href="index.php#acesso">Cancelar</a>
                </div>
            </form>

            <aside class="card cadastro-lateral" aria-labelledby="titulo-regras-cadastro">
                <div class="card-body">
                    <h2 id="titulo-regras-cadastro">Por que preciso me cadastrar?</h2>
                    <ul class="lista-regras">
                        <li>Para agendar é obrigatório ter cadastro e estar identificado.</li>
                        <li>Você vê os horários livres antes de escolher.</li>
                        <li>Seus agendamentos aparecem completos na sua agenda.</li>
                        <li>Os demais atendimentos aparecem somente como "Ocupado".</li>
                    </ul>
                    <p class="acesso-nota">Já tem cadastro? <a href="index.php#acesso">Entrar</a></p>
                </div>
            </aside>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
