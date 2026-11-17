<?php
require_once __DIR__ . '/includes/funcoes.php';
require_once __DIR__ . '/includes/agenda_prototipo.php';

$data = normalizar_data_prototipo(isset($_GET['data']) ? $_GET['data'] : data_referencia_prototipo());
$data_anterior = deslocar_data_prototipo($data, -1);
$data_posterior = deslocar_data_prototipo($data, 1);
$data_link = urlencode($data);
$profissionais = profissionais_prototipo();
$agendamentos = agendamentos_prototipo($data);
$hoje = $data === data_referencia_prototipo();

$titulo_pagina = 'Agenda — AgendaSalão';
$menu_tipo = 'interno';
$menu_ativo = 'agenda';
require_once __DIR__ . '/includes/header.php';
?>

<main id="conteudo" class="conteudo">
    <div class="container">
        <header class="pagina-cabecalho">
            <div>
                <h1>Agenda do dia</h1>
                <p>
                    <?php echo e(data_exibicao($data)); ?> ·
                    <?php echo (int) count($agendamentos); ?> atendimentos ·
                    <?php echo (int) count($profissionais); ?> profissionais ·
                    <?php echo e(expediente_texto()); ?>
                </p>
            </div>
            <a class="btn btn-primario" href="agendamento_form.php?data=<?php echo e($data_link); ?>">Novo agendamento</a>
        </header>

        <?php if (isset($_GET['agendado']) && $_GET['agendado'] === '1'): ?>
            <div class="mensagem-sucesso" role="status">
                <p>Agendamento registrado no protótipo para <?php echo e(data_exibicao($data)); ?>.</p>
            </div>
        <?php endif; ?>

        <div class="agenda-controles no-print">
            <form method="get" action="agenda.php">
                <div class="controle-data">
                    <label class="label-text" for="campo-data">Data</label>
                    <input class="input" id="campo-data" name="data" type="date" value="<?php echo e($data); ?>">
                    <button class="btn btn-secundario btn-pequeno" type="submit">Ir para a data</button>
                </div>
            </form>
            <div class="controles-linha">
                <a class="btn btn-secundario btn-pequeno" href="agenda.php?data=<?php echo e($data_anterior); ?>">Dia anterior</a>
                <a class="btn btn-secundario btn-pequeno" href="agenda.php?data=<?php echo e(data_referencia_prototipo()); ?>">Hoje</a>
                <a class="btn btn-secundario btn-pequeno" href="agenda.php?data=<?php echo e($data_posterior); ?>">Próximo dia</a>
                <button class="btn btn-secundario btn-pequeno" type="button" data-imprimir>Exportar PDF</button>
            </div>
        </div>

        <?php if (count($agendamentos) === 0): ?>
            <div class="vazio">
                <h2>Nenhum agendamento neste dia</h2>
                <p>Escolha um horário livre ou avance para os próximos dias para agendar com antecedência.</p>
            </div>
        <?php endif; ?>

        <section class="agenda" aria-label="Agenda de <?php echo e(data_exibicao($data)); ?>">
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
                                <?php if ($hoje): ?>
                                    <div class="linha-agora" style="top: 480px" aria-hidden="true"></div>
                                <?php endif; ?>
                                <?php foreach ($agendamentos as $agendamento): ?>
                                    <?php if ((int) $agendamento['profissional_id'] !== (int) $profissional['id']) { continue; } ?>
                                    <?php
                                    $topo = posicao_na_grade($agendamento['hora_inicio']);
                                    $altura = (int) $agendamento['duracao_min'];
                                    $fim = minutos_para_hora(hora_para_minutos($agendamento['hora_inicio']) + $altura);
                                    ?>
                                    <a class="bloco status-<?php echo e($agendamento['status']); ?>"
                                       href="agendamento_form.php?id=<?php echo (int) $agendamento['id']; ?>&data=<?php echo e($data_link); ?>"
                                       style="top: <?php echo (int) $topo; ?>px; height: <?php echo (int) $altura; ?>px"
                                       aria-label="<?php echo e($profissional['nome'] . ', ' . $agendamento['hora_inicio'] . ' às ' . $fim . ', ' . $agendamento['cliente'] . ', ' . $agendamento['servico'] . ', ' . $agendamento['status']); ?>">
                                        <strong><?php echo e($agendamento['hora_inicio'] . ' · ' . $agendamento['cliente']); ?></strong>
                                        <span><?php echo e($agendamento['servico'] . ' · R$ ' . number_format((float) $agendamento['valor'], 2, ',', '.')); ?></span>
                                        <span class="rotulo-status"><?php echo e($agendamento['status']); ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <p class="campo-ajuda">
            Use “Próximo dia” para avançar para datas futuras. Atendimentos cancelados
            continuam visíveis, mas liberam o horário para novo agendamento.
        </p>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
