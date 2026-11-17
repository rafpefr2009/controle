<?php
require_once __DIR__ . '/includes/funcoes.php';

// Protótipo: o POST apenas escolhe a agenda de destino; não há autenticação real.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $perfil = isset($_POST['perfil']) ? (string) $_POST['perfil'] : '';
    redirecionar($perfil === 'cliente' ? 'agenda_cliente.php' : 'agenda.php');
}

$titulo_pagina = 'AgendaSalão — agenda de atendimentos do salão';
$menu_tipo = 'publico';
$menu_ativo = 'inicio';
require_once __DIR__ . '/includes/header.php';
?>

<main id="conteudo">
    <!-- Página de abertura do site. Protótipo visual com dados fictícios. -->
    <section class="hero">
        <div class="container hero-grade">
            <div class="hero-texto">
                <p class="hero-etiqueta">Feito para salões de beleza</p>
                <h1>Sua agenda de atendimentos, <span>sem confusão.</span></h1>
                <p>
                    Substitua o controle por WhatsApp por uma agenda visual: veja a
                    disponibilidade de cada profissional, evite horários sobrepostos
                    e exporte o dia em PDF.
                </p>
                <div class="hero-acoes">
                    <a class="btn btn-primario" href="#acesso">Abrir minha agenda</a>
                    <a class="btn btn-secundario" href="#recursos">Ver recursos</a>
                </div>
                <ul class="hero-metricas">
                    <li><strong>3</strong> profissionais</li>
                    <li><strong>6</strong> atendimentos hoje</li>
                    <li><strong>07:00–20:00</strong> de expediente · almoço 12:00–14:00</li>
                </ul>
            </div>

            <div class="previa" aria-label="Prévia da agenda do dia">
                <div class="previa-cabecalho">
                    <h2>Agenda do dia</h2>
                    <p>Quinta, 17/11/2026 · 07:00–20:00 · almoço 12:00–14:00</p>
                </div>
                <div class="previa-grade">
                    <div class="previa-eixo" aria-hidden="true">
                        <div class="eixo-cabecalho"></div>
                        <div class="eixo-marcas">
                            <span style="top: 0">07:00</span>
                            <span style="top: 120px">09:00</span>
                            <span style="top: 240px">11:00</span>
                            <span style="top: 300px">12:00</span>
                            <span class="marca-almoco" style="top: 360px">almoço</span>
                            <span style="top: 420px">14:00</span>
                            <span style="top: 540px">16:00</span>
                            <span style="top: 660px">18:00</span>
                            <span style="top: 776px">20:00</span>
                        </div>
                    </div>
                    <div class="previa-colunas">
                        <div class="previa-coluna">
                            <h3>Ana</h3>
                            <div class="previa-trilha">
                                <div class="faixa-almoco" aria-hidden="true"><span>Almoço · 12:00–14:00</span></div>
                                <a class="previa-bloco status-agendado" href="#acesso"
                                   style="top: 30px; height: 60px"
                                   aria-label="Ana, 07:30, Maria Silva, corte feminino, agendado">
                                    <strong>07:30 · Maria Silva</strong>
                                    <span>Corte feminino · R$ 120,00</span>
                                    <span class="previa-status">agendado</span>
                                </a>
                                <a class="previa-bloco status-realizado" href="#acesso"
                                   style="top: 270px; height: 30px"
                                   aria-label="Ana, 11:30, Rosa Lima, corte masculino, realizado">
                                    <strong>11:30 · Rosa Lima</strong>
                                    <span>Corte masculino · R$ 70,00</span>
                                    <span class="previa-status">realizado</span>
                                </a>
                            </div>
                        </div>
                        <div class="previa-coluna">
                            <h3>Carla</h3>
                            <div class="previa-trilha">
                                <div class="faixa-almoco" aria-hidden="true"><span>Almoço · 12:00–14:00</span></div>
                                <a class="previa-bloco status-agendado" href="#acesso"
                                   style="top: 60px; height: 30px"
                                   aria-label="Carla, 08:00, João Prado, corte masculino, agendado">
                                    <strong>08:00 · João Prado</strong>
                                    <span>Corte masculino · R$ 70,00</span>
                                    <span class="previa-status">agendado</span>
                                </a>
                                <a class="previa-bloco status-cancelado" href="#acesso"
                                   style="top: 420px; height: 60px"
                                   aria-label="Carla, 14:00, Beatriz, corte feminino, cancelado">
                                    <strong>14:00 · Beatriz</strong>
                                    <span>Corte feminino · R$ 120,00</span>
                                    <span class="previa-status">cancelado</span>
                                </a>
                            </div>
                        </div>
                        <div class="previa-coluna">
                            <h3>Juliana</h3>
                            <div class="previa-trilha">
                                <div class="faixa-almoco" aria-hidden="true"><span>Almoço · 12:00–14:00</span></div>
                                <a class="previa-bloco status-agendado" href="#acesso"
                                   style="top: 180px; height: 60px"
                                   aria-label="Juliana, 10:00, Paulo Rocha, corte feminino, agendado">
                                    <strong>10:00 · Paulo Rocha</strong>
                                    <span>Corte feminino · R$ 120,00</span>
                                    <span class="previa-status">agendado</span>
                                </a>
                                <a class="previa-bloco status-realizado" href="#acesso"
                                   style="top: 540px; height: 30px"
                                   aria-label="Juliana, 16:00, Maria Silva, corte masculino, realizado">
                                    <strong>16:00 · Maria Silva</strong>
                                    <span>Corte masculino · R$ 70,00</span>
                                    <span class="previa-status">realizado</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="secao" id="recursos">
        <div class="container">
            <h2>Tudo que a recepcionista precisa</h2>
            <p>Uma tela por decisão, pensada para quem atende o dia inteiro.</p>
            <div class="cartoes">
                <article class="card cartao-recurso">
                    <div class="card-body">
                        <h3>Agenda visual</h3>
                        <p>Ana, Carla e Juliana lado a lado, com cada atendimento posicionado no horário certo — das 07:00 às 20:00, com almoço de 12:00 às 14:00.</p>
                    </div>
                </article>
                <article class="card cartao-recurso">
                    <div class="card-body">
                        <h3>Sem conflito de horário</h3>
                        <p>Se um novo atendimento sobrepor ou encostar em outro do mesmo profissional, o sistema avisa e mostra os horários livres.</p>
                    </div>
                </article>
                <article class="card cartao-recurso">
                    <div class="card-body">
                        <h3>Histórico e PDF</h3>
                        <p>Filtre por cliente, profissional, período ou status e exporte exatamente o que está na tela em PDF.</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="secao secao-suave" id="como-funciona">
        <div class="container">
            <h2>Como funciona no dia a dia</h2>
            <p>Do pedido recebido pelo WhatsApp ao atendimento registrado.</p>
            <div class="cartoes">
                <article class="card cartao-recurso">
                    <div class="card-body">
                        <span class="etapa" aria-hidden="true">1</span>
                        <h3>O cliente pede o horário</h3>
                        <p>A recepcionista recebe a mensagem e confere a agenda antes de responder.</p>
                    </div>
                </article>
                <article class="card cartao-recurso">
                    <div class="card-body">
                        <span class="etapa" aria-hidden="true">2</span>
                        <h3>Escolhe serviço e profissional</h3>
                        <p>A duração e o valor vêm do serviço cadastrado e podem ser ajustados.</p>
                    </div>
                </article>
                <article class="card cartao-recurso">
                    <div class="card-body">
                        <span class="etapa" aria-hidden="true">3</span>
                        <h3>Confirma e acompanha</h3>
                        <p>O bloco aparece na coluna do profissional, com status agendado, realizado ou cancelado.</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="secao" id="acesso">
        <div class="container">
            <div class="acesso-cabecalho">
                <h2>Entre para abrir a agenda ou agendar</h2>
                <p>
                    Escolha seu perfil. Clientes precisam ter cadastro no site para agendar;
                    sem login você só consegue consultar os horários disponíveis.
                </p>
            </div>

            <div class="acesso-perfis">
                <section class="card acesso-cartao" aria-labelledby="titulo-acesso-recepcao">
                    <div class="card-body">
                        <h3 id="titulo-acesso-recepcao">Sou recepcionista</h3>
                        <p class="acesso-perfil-descricao">Acesso à agenda completa, serviços e histórico.</p>

                        <!-- Protótipo: o envio apenas navega para a agenda das atendentes. -->
                        <form method="post" action="index.php">
                            <input type="hidden" name="perfil" value="recepcionista">
                            <input type="hidden" name="csrf_token" value="prototipo">
                            <div class="form-campo">
                                <label class="label-text" for="campo-usuario-recepcao">Usuário</label>
                                <input class="input" id="campo-usuario-recepcao" name="usuario" type="text"
                                       value="recepcao" autocomplete="username" required
                                       placeholder="Ex.: recepcao" aria-describedby="ajuda-usuario-recepcao">
                                <p class="campo-ajuda" id="ajuda-usuario-recepcao">Usuário de demonstração: recepcao</p>
                            </div>
                            <div class="form-campo">
                                <label class="label-text" for="campo-senha-recepcao">Senha</label>
                                <input class="input" id="campo-senha-recepcao" name="senha" type="password"
                                       autocomplete="current-password" required
                                       placeholder="••••••••" aria-describedby="ajuda-senha-recepcao">
                                <p class="campo-ajuda" id="ajuda-senha-recepcao">Senha de demonstração: senha123</p>
                            </div>
                            <div class="form-rodape">
                                <button class="btn btn-primario acesso-botao" type="submit">Entrar na agenda</button>
                            </div>
                        </form>
                        <p class="acesso-atalho">Último acesso: 17/11/2026, 08:42 — recepcao</p>
                    </div>
                </section>

                <section class="card acesso-cartao" aria-labelledby="titulo-acesso-cliente">
                    <div class="card-body">
                        <h3 id="titulo-acesso-cliente">Sou cliente</h3>
                        <p class="acesso-perfil-descricao">Veja os horários livres e faça seu agendamento manual.</p>

                        <!-- Protótipo: o envio apenas navega para a agenda do cliente. -->
                        <form method="post" action="index.php">
                            <input type="hidden" name="perfil" value="cliente">
                            <input type="hidden" name="csrf_token" value="prototipo">
                            <div class="form-campo">
                                <label class="label-text" for="campo-usuario-cliente">Usuário</label>
                                <input class="input" id="campo-usuario-cliente" name="usuario" type="text"
                                       value="maria.silva" autocomplete="username" required
                                       placeholder="Ex.: maria.silva" aria-describedby="ajuda-usuario-cliente">
                                <p class="campo-ajuda" id="ajuda-usuario-cliente">Usuário de demonstração: maria.silva</p>
                            </div>
                            <div class="form-campo">
                                <label class="label-text" for="campo-senha-cliente">Senha</label>
                                <input class="input" id="campo-senha-cliente" name="senha" type="password"
                                       autocomplete="current-password" required
                                       placeholder="••••••••" aria-describedby="ajuda-senha-cliente">
                                <p class="campo-ajuda" id="ajuda-senha-cliente">Senha de demonstração: cliente123</p>
                            </div>
                            <div class="form-rodape">
                                <button class="btn btn-primario acesso-botao" type="submit">Entrar como cliente</button>
                            </div>
                        </form>
                        <div class="acesso-cliente-acoes">
                            <a class="btn btn-secundario btn-pequeno" href="cadastro_cliente.php">Criar cadastro</a>
                            <a class="btn btn-secundario btn-pequeno" href="agenda_cliente.php?visitante=1">Ver horários sem entrar</a>
                        </div>
                        <p class="acesso-atalho">Dica: pressione <kbd>Enter</kbd> para entrar.</p>
                    </div>
                </section>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
