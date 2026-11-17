<?php
require_once __DIR__ . '/includes/funcoes.php';
require_once __DIR__ . '/includes/agenda_prototipo.php';

// Sem cadastro/login não existe ação de agendamento no protótipo.
if (isset($_GET['visitante']) && $_GET['visitante'] === '1') {
    redirecionar('agenda_cliente.php?visitante=1');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_post = normalizar_data_prototipo(isset($_POST['data']) ? $_POST['data'] : data_referencia_prototipo());
    redirecionar('agenda_cliente.php?data=' . urlencode($data_post) . '&agendado=1');
}

$data = normalizar_data_prototipo(isset($_GET['data']) ? $_GET['data'] : data_referencia_prototipo());
$data_link = urlencode($data);
$voltar = 'agenda_cliente.php?data=' . $data_link;

$profissionais = profissionais_prototipo();
$profissional_id = isset($_GET['profissional']) ? (int) $_GET['profissional'] : 1;
$ids_validos = array(1, 2, 3);
if (!in_array($profissional_id, $ids_validos, true)) {
    $profissional_id = 1;
}

$servicos = servicos_ativos_prototipo();
$servico_id = isset($_GET['servico']) ? (int) $_GET['servico'] : 1;
$servico = servico_prototipo($servico_id);
if ($servico === null || !$servico['ativo']) {
    $servico = $servicos[0];
    $servico_id = (int) $servico['id'];
}
$duracao = (int) $servico['duracao'];
if (!isset($_GET['servico']) && isset($_GET['duracao']) && in_array((int) $_GET['duracao'], duracoes_prototipo(), true)) {
    $duracao = (int) $_GET['duracao'];
}
$valor = $servico['valor'];

$agendamentos = agendamentos_prototipo($data);
$livres = horarios_livres_prototipo($agendamentos, $profissional_id, $duracao);
$hora = isset($_GET['hora']) ? (string) $_GET['hora'] : '';
$hora_invalida = $hora !== '' && !in_array($hora, $livres, true);
if ($hora_invalida) {
    $hora = '';
}

$titulo_pagina = 'Novo agendamento — AgendaSalão';
$menu_tipo = 'cliente';
$menu_ativo = 'agendamento';
require_once __DIR__ . '/includes/header.php';
?>

<main id="conteudo" class="conteudo">
    <div class="container">
        <header class="pagina-cabecalho">
            <div>
                <h1>Novo agendamento</h1>
                <p>Cliente identificado: Maria Silva · <?php echo e(data_exibicao($data)); ?></p>
            </div>
            <a class="btn btn-secundario" href="<?php echo e($voltar); ?>">Voltar à agenda</a>
        </header>

        <div class="mensagem-aviso" role="note">
            <p>
                Agendamentos só podem ser feitos por clientes cadastrados.
                Expediente das 07:00 às 20:00, com almoço das 12:00 às 14:00.
                O último início pela manhã é 11:30 para serviços de 30 min;
                a tarde recomeça às 14:00. Horários ocupados ou que encostam
                em outro atendimento são recusados.
            </p>
        </div>

        <?php if ($hora_invalida): ?>
            <div class="mensagem-erro" role="alert">
                <p>Horário ocupado ou fora da duração do serviço. Escolha um dos horários livres listados abaixo.</p>
            </div>
        <?php endif; ?>

        <form class="form-profissional no-print" method="get" action="agendamento_cliente.php">
            <input type="hidden" name="data" value="<?php echo e($data); ?>">
            <label class="label-text" for="campo-profissional-selecao">Ver horários de</label>
            <select class="input" id="campo-profissional-selecao" name="profissional">
                <?php foreach ($profissionais as $profissional): ?>
                    <option value="<?php echo (int) $profissional['id']; ?>"<?php echo (int) $profissional['id'] === $profissional_id ? ' selected' : ''; ?>>
                        <?php echo e($profissional['nome']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label class="label-text" for="campoServicoConsulta">Serviço</label>
            <select class="input" id="campoServicoConsulta" name="servico">
                <?php foreach ($servicos as $opcao): ?>
                    <option value="<?php echo (int) $opcao['id']; ?>"<?php echo (int) $opcao['id'] === (int) $servico_id ? ' selected' : ''; ?>>
                        <?php echo e($opcao['nome'] . ' · ' . (int) $opcao['duracao'] . ' min'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-secundario btn-pequeno" type="submit">Atualizar horários</button>
        </form>

        <div class="cliente-agendamento-grade">
            <form class="formulario" method="post" action="agendamento_cliente.php">
                <input type="hidden" name="csrf_token" value="prototipo">

                <div class="form-linha">
                    <div class="form-campo">
                        <label class="label-text" for="campo-cliente-cliente">Cliente</label>
                        <input class="input" id="campo-cliente-cliente" name="cliente" type="text"
                               value="Maria Silva" readonly aria-describedby="ajuda-cliente-cliente">
                        <p class="campo-ajuda" id="ajuda-cliente-cliente">Seu cadastro foi identificado automaticamente.</p>
                    </div>
                    <div class="form-campo">
                        <label class="label-text" for="campo-telefone-cliente">Telefone</label>
                        <input class="input" id="campo-telefone-cliente" name="telefone" type="tel"
                               value="(11) 98765-4321" readonly>
                    </div>
                </div>

                <div class="form-linha">
                    <div class="form-campo">
                        <label class="label-text" for="campo-profissional-cliente">Profissional</label>
                        <select class="input" id="campo-profissional-cliente" name="profissional_id">
                            <?php foreach ($profissionais as $profissional): ?>
                                <option value="<?php echo (int) $profissional['id']; ?>"<?php echo (int) $profissional['id'] === $profissional_id ? ' selected' : ''; ?>>
                                    <?php echo e($profissional['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-campo">
                        <label class="label-text" for="campoServico">Serviço</label>
                        <select class="input" id="campoServico" name="servico_id" required>
                            <?php foreach ($servicos as $opcao): ?>
                                <option value="<?php echo (int) $opcao['id']; ?>"
                                        data-duracao="<?php echo (int) $opcao['duracao']; ?>"
                                        data-valor="<?php echo e($opcao['valor']); ?>"<?php echo (int) $opcao['id'] === (int) $servico_id ? ' selected' : ''; ?>>
                                    <?php echo e($opcao['nome'] . ' · ' . (int) $opcao['duracao'] . ' min · R$ ' . number_format((float) $opcao['valor'], 2, ',', '.')); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="campo-ajuda">Duração e valor são preenchidos automaticamente.</p>
                    </div>
                </div>

                <div class="form-linha">
                    <div class="form-campo">
                        <label class="label-text" for="campo-data-cliente">Data</label>
                        <input class="input" id="campo-data-cliente" name="data" type="date"
                               value="<?php echo e($data); ?>" required>
                    </div>
                    <div class="form-campo">
                        <label class="label-text" for="campoHoraInicio">Hora de início</label>
                        <input class="input" id="campoHoraInicio" name="hora_inicio" type="time"
                               value="<?php echo e($hora); ?>" step="1800" required
                               aria-describedby="ajuda-hora-cliente">
                        <p class="campo-ajuda" id="ajuda-hora-cliente">Escolha um horário livre da lista ao lado.</p>
                    </div>
                </div>

                <div class="form-linha">
                    <div class="form-campo">
                        <label class="label-text" for="campoDuracao">Duração (min)</label>
                        <input class="input" id="campoDuracao" name="duracao_min" type="number"
                               value="<?php echo (int) $duracao; ?>" min="1" required>
                    </div>
                    <div class="form-campo">
                        <label class="label-text" for="campoValor">Valor (R$)</label>
                        <input class="input" id="campoValor" name="valor" type="number"
                               value="<?php echo e($valor); ?>" min="0" step="0.01" required>
                    </div>
                </div>

                <div class="form-campo">
                    <label class="label-text" for="campo-observacoes-cliente">Observações</label>
                    <textarea class="input" id="campo-observacoes-cliente" name="observacoes" rows="3"
                              placeholder="Ex.: prefiro ser atendida no início da tarde"></textarea>
                </div>

                <div class="form-rodape">
                    <button class="btn btn-primario" type="submit">Confirmar agendamento</button>
                    <a class="btn btn-secundario" href="<?php echo e($voltar); ?>">Cancelar</a>
                </div>
            </form>

            <aside class="card cliente-livres" aria-labelledby="titulo-livres-cliente">
                <div class="card-body">
                    <h2 id="titulo-livres-cliente">Horários livres</h2>
                    <p>
                        Profissional: <strong><?php echo e($profissional_id === 1 ? 'Ana' : ($profissional_id === 2 ? 'Carla' : 'Juliana')); ?></strong>
                        · serviço: <strong><?php echo e($servico['nome']); ?></strong>
                        · <strong><?php echo (int) $duracao; ?> min</strong>
                    </p>
                    <p>
                        <?php if ($duracao === 30): ?>
                            Último início da manhã: 11:30. Retorno às 14:00.
                        <?php else: ?>
                            O atendimento precisa terminar até 12:00; a tarde começa às 14:00.
                        <?php endif; ?>
                    </p>
                    <?php if (count($livres) > 0): ?>
                        <ul class="lista-horarios lista-horarios-vertical">
                            <?php foreach ($livres as $livre): ?>
                                <li>
                                    <button class="horario-livre" type="button" data-hora="<?php echo e($livre); ?>">
                                        <?php echo e($livre); ?>
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Nenhum horário livre para este serviço na data escolhida.</p>
                    <?php endif; ?>
                    <p class="acesso-nota">Clique em um horário para preencher o formulário.</p>
                </div>
            </aside>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
