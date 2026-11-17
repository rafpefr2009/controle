<?php
$titulo_pagina = 'Entrar — AgendaSalão';
$menu_tipo = 'publico';
$menu_ativo = 'acesso';
require_once __DIR__ . '/includes/header.php';
?>

<main id="conteudo">
    <!-- F1: estado ilustrado de credenciais inválidas. A senha nunca reaparece. -->
    <div class="login-central">
        <section class="card acesso-cartao" aria-label="Formulário de acesso">
            <div class="card-body">
                <div class="mensagem-erro" role="alert">
                    <p>Usuário ou senha inválidos.</p>
                </div>

                <form method="post" action="login.php">
                    <div class="form-campo">
                        <label class="label-text" for="campo-usuario">Usuário</label>
                        <input class="input" id="campo-usuario" name="usuario" type="text"
                               value="recepcao" autocomplete="username" autofocus required
                               placeholder="Ex.: recepcao" aria-describedby="ajuda-usuario">
                        <p class="campo-ajuda" id="ajuda-usuario">Use o usuário fornecido pelo salão.</p>
                    </div>
                    <div class="form-campo">
                        <label class="label-text" for="campo-senha">Senha</label>
                        <input class="input" id="campo-senha" name="senha" type="password"
                               autocomplete="current-password" required
                               placeholder="••••••••" aria-describedby="ajuda-senha">
                        <p class="campo-ajuda" id="ajuda-senha">Digite a senha cadastrada para esta usuária.</p>
                    </div>
                    <div class="form-rodape">
                        <button class="btn btn-primario acesso-botao" type="submit">Entrar</button>
                    </div>
                </form>

                <p class="acesso-atalho">Dica: pressione <kbd>Enter</kbd> para entrar.</p>
                <p class="acesso-nota">
                    Demonstração: recepcao / senha123 · cliente: maria.silva / cliente123.
                    <a href="index.php#acesso">Voltar ao acesso</a>
                </p>
            </div>
        </section>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
