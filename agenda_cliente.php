<?php
require_once __DIR__ . '/includes/funcoes.php';
require_once __DIR__ . '/includes/agenda_prototipo.php';

$data = normalizar_data_prototipo(isset($_GET['data']) ? $_GET['data'] : data_referencia_prototipo());
$data_anterior = deslocar_data_prototipo($data, -1);
$data_posterior = deslocar_data_prototipo($data, 1);
$data_link = urlencode($data);

// Modo visitante: consulta de horários sem cadastro (sem botão de agendar).
$visitante = isset($_GET['visitante']) && $_GET['visitante'] === '1';
$modo_visitante = $visitante;
$sufixo = $visitante ? '&visitante=1' : '';

$profissionais = profissionais_prototipo();
$agendamentos = agendamentos_prototipo($data);

$titulo_pagina = 'Minha agenda — AgendaSalão';
$menu_tipo = 'cliente';
$menu_ativo = 'agenda';
require_once __DIR__ . '/includes/header.php';
?>

<main id="conteudo" class="conteudo">
    <div class="container">
        <header class="pagina-cabecalho">
            <div>
                <h1><?php echo $visitante ? 'Horários disponíveis' : 'Minha agenda'; ?></h1>
                <p><?php echo e(data_exibicao($data)); ?> · <?php echo e(expediente_texto()); ?></p>
            </div>
            <?php if ($visitante): ?>
                <a class="btn btn-primario" href="cadastro_cliente.php">Criar cadastro para agendar</a>
            <?php else: ?>
                <a class="btn btn-primario" href="agendamento_cliente.php?data=<?php echo e($data_link); ?>">Novo agendamento</a>
            <?php endif; ?>
        </header>

        <?php if ($visitante): ?>
            <div class="mensagem-aviso" role="alert">
                <p>
                    Você está consultando horários <strong>sem cadastro</strong>.
                    Agendar só é permitido para clientes cadastrados e identificados.
                    <a href="index.php#acesso">Entrar</a> ou
                    <a href="cadastro_cliente.php">criar cadastro</a>.
                </p>
            </div>
        <?php else: ?>
            <div class="mensagem-sucesso" role="status">
                <?php if (isset($_GET['agendado']) && $_GET['agendado'] === '1'): ?>
                    <p>Agendamento registrado no protótipo. Seu atendimento aparecerá na agenda.</p>
                <?php endif; ?>
                <p>Você está agendando como <strong>Maria Silva</strong> · (11) 98765-4321.</p>
            </div>
        <?php endif; ?>

        <div class="agenda-controles no-print">
            <form method="get" action="agenda_cliente.php">
                <?php if ($visitante): ?>
                    <input type="hidden" name="visitante" value="1">
                <?php endif; ?>
                <div class="controle-data">
                    <label class="label-text" for="campo-data-cliente">Data</label>
                    <input class="input" id="campo-data-cliente" name="data" type="date" value="<?php echo e($data); ?>">
                    <button class="btn btn-secundario btn-pequeno" type="submit">Ir para a data</button>
                </div>
            </form>
            <div class="controles-linha">
                <a class="btn btn-secundario btn-pequeno" href="agenda_cliente.php?data=<?php echo e($data_anterior); ?><?php echo e($sufixo); ?>">Dia anterior</a>
                <a class="btn btn-secundario btn-pequeno" href="agenda_cliente.php?data=<?php echo e(data_referencia_prototipo()); ?><?php echo e($sufixo); ?>">Hoje</a>
                <a class="btn btn-secundario btn-pequeno" href="agenda_cliente.php?data=<?php echo e($data_posterior); ?><?php echo e($sufixo); ?>">Próximo dia</a>
            </div>
        </div>

        <?php if (count($agendamentos) === 0): ?>
            <div class="mensagem-aviso" role="note">
                <p>Nenhum agendamento nesta data. Avance pelos próximos dias ou escolha um horário livre abaixo para agendar.</p>
            </div>
        <?php endif; ?>

        <section class="card cliente-horarios" aria-labelledby="titulo-horarios-cliente">
            <div class="card-body">
                <h2 id="titulo-horarios-cliente">Horários disponíveis</h2>
                <p>
                    A lista abaixo considera serviços de 60 min: o último início da manhã
                    é 10:00, pois o atendimento precisa terminar até as 12:00. Para serviços
                    de 30 min, o último início é 11:30. A tarde começa às 14:00 e intervalos
                    que encostam também ficam ocupados.
                </p>
                <div class="horarios-profissionais">
                    <?php foreach ($profissionais as $profissional): ?>
                        <?php $livres = horarios_livres_prototipo($agendamentos, $profissional['id']); ?>
                        <div class="horarios-profissional">
                            <h3><?php echo e($profissional['nome']); ?></h3>
                            <ul class="lista-horarios">
                                <?php foreach ($livres as $hora): ?>
                                    <li>
                                        <?php if ($visitante): ?>
                                            <span class="horario-somente-leitura"><?php echo e($hora); ?></span>
                                        <?php else: ?>
                                            <a class="horario-livre"
                                               href="agendamento_cliente.php?data=<?php echo e($data_link); ?>&profissional=<?php echo (int) $profissional['id']; ?>&hora=<?php echo e($hora); ?>"
                                               aria-label="Agendar com <?php echo e($profissional['nome']); ?> às <?php echo e($hora); ?>">
                                                <?php echo e($hora); ?>
                                            </a>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="agenda cliente-agenda" aria-label="Agenda do cliente">
            <div class="agenda-grade">
                <div class="eixo-horas" aria-hidden="true">
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
                <div class="colunas-profissionais">
                    <?php foreach ($profissionais as $profissional): ?>
                        <div class="coluna-profissional">
                            <h3><?php echo e($profissional['nome']); ?></h3>
                            <div class="trilha">
                                <div class="faixa-almoco" aria-hidden="true"><span>Almoço · 12:00–14:00</span></div>
                                <?php foreach ($agendamentos as $agendamento): ?>
                                    <?php if ((int) $agendamento['profissional_id'] !== (int) $profissional['id']) { continue; } ?>
                                    <?php if ($agendamento['status'] === 'cancelado') { continue; } ?>
                                    <?php
                                    $topo = posicao_na_grade($agendamento['hora_inicio']);
                                    $altura = (int) $agendamento['duracao_min'];
                                    $fim = minutos_para_hora(hora_para_minutos($agendamento['hora_inicio']) + $altura);
                                    $visivel = !$visitante && $agendamento['proprio'];
                                    ?>
                                    <?php if ($visivel): ?>
                                        <div class="bloco status-<?php echo e($agendamento['status']); ?>"
                                             style="top: <?php echo (int) $topo; ?>px; height: <?php echo (int) $altura; ?>px"
                                             aria-label="Seu atendimento: <?php echo e($agendamento['hora_inicio']); ?> às <?php echo e($fim); ?>, <?php echo e($agendamento['servico']); ?>, <?php echo e($agendamento['status']); ?>">
                                            <strong><?php echo e($agendamento['hora_inicio'] . ' · ' . $agendamento['cliente']); ?></strong>
                                            <span><?php echo e($agendamento['servico'] . ' · R$ ' . number_format((float) $agendamento['valor'], 2, ',', '.')); ?></span>
                                            <span class="rotulo-status">seu agendamento · <?php echo e($agendamento['status']); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <div class="bloco bloco-ocupado"
                                             style="top: <?php echo (int) $topo; ?>px; height: <?php echo (int) $altura; ?>px"
                                             aria-label="Horário ocupado das <?php echo e($agendamento['hora_inicio']); ?> às <?php echo e($fim); ?>">
                                            <strong>Ocupado</strong>
                                            <span><?php echo e($agendamento['hora_inicio'] . '–' . $fim); ?></span>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <p class="campo-ajuda cliente-legenda">
            Atendimentos cancelados liberam o horário e não aparecem como ocupados.
            Os horários de datas futuras ficam disponíveis para agendamento.
        </p>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
