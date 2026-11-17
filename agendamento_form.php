<?php
require_once __DIR__ . '/includes/funcoes.php';
require_once __DIR__ . '/includes/agenda_prototipo.php';

$data = normalizar_data_prototipo(isset($_GET['data']) ? $_GET['data'] : data_referencia_prototipo());
$data_link = urlencode($data);
$voltar = 'agenda.php?data=' . $data_link;

$servicos = servicos_ativos_prototipo();
$servico_id = isset($_GET['servico']) ? (int) $_GET['servico'] : 1;
$servico = servico_prototipo($servico_id);
if ($servico === null || !$servico['ativo']) {
    $servico = $servicos[0];
    $servico_id = (int) $servico['id'];
}
$duracao = (int) $servico['duracao'];
$valor = $servico['valor'];

$agendamentos = agendamentos_prototipo($data);
$livres = horarios_livres_prototipo($agendamentos, 1, $duracao);
$hora_sugerida = isset($_GET['hora']) && in_array($_GET['hora'], $livres, true)
    ? (string) $_GET['hora']
    : (count($livres) > 0 ? $livres[0] : '');

$titulo_pagina = 'Agendamento — AgendaSalão';
$menu_tipo = 'interno';
$menu_ativo = 'agenda';
require_once __DIR__ . '/includes/header.php';
?>

<main id="conteudo" class="conteudo">
    <div class="container">
        <header class="pagina-cabecalho">
            <div>
                <h1>Novo agendamento</h1>
                <p>Preencha os dados do atendimento · <?php echo e(data_exibicao($data)); ?></p>
            </div>
            <a class="btn btn-secundario" href="<?php echo e($voltar); ?>">Voltar à agenda</a>
        </header>

        <?php if ($data === data_referencia_prototipo()): ?>
            <!-- F3/CA5: estado ilustrado de conflito, com horários livres para o profissional. -->
            <div class="mensagem-erro" role="alert">
                <p>Conflito de horário: Ana já tem atendimento nesse horário.</p>
                <p>
                    Expediente das 07:00 às 20:00, com almoço das 12:00 às 14:00.
                    Último início pela manhã: 11:30; a tarde recomeça às 14:00.
                </p>
        <?php else: ?>
            <div class="mensagem-aviso" role="note">
                <p>
                    Nenhum conflito para esta data. Expediente das 07:00 às 20:00,
                    com almoço das 12:00 às 14:00; o atendimento precisa terminar
                    até as 12:00 ou começar às 14:00.
                </p>
        <?php endif; ?>
                <p>Horários livres para Ana em <?php echo e(data_exibicao($data)); ?> (serviço de <?php echo (int) $duracao; ?> min):</p>
                <?php if (count($livres) > 0): ?>
                    <ul class="lista-horarios">
                        <?php foreach ($livres as $hora): ?>
                            <li><button class="horario-livre" type="button" data-hora="<?php echo e($hora); ?>"><?php echo e($hora); ?></button></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Nenhum horário livre para a duração escolhida nesta data.</p>
                <?php endif; ?>
            </div>

        <form class="formulario" method="post" action="agendamento_form.php">
            <input type="hidden" name="data" value="<?php echo e($data); ?>">

            <div class="form-linha">
                <div class="form-campo">
                    <label class="label-text" for="campo-cliente">Cliente</label>
                    <input class="input" id="campo-cliente" name="cliente" type="text"
                           value="Maria Silva" required placeholder="Nome da cliente">
                </div>
                <div class="form-campo">
                    <label class="label-text" for="campo-telefone">Telefone</label>
                    <input class="input" id="campo-telefone" name="telefone" type="text"
                           value="(11) 98765-4321" placeholder="Ex.: (11) 99999-0000">
                </div>
            </div>

            <div class="form-linha">
                <div class="form-campo">
                    <label class="label-text" for="campo-profissional">Profissional</label>
                    <select class="input" id="campo-profissional" name="profissional_id" required>
                        <?php foreach (profissionais_prototipo() as $profissional): ?>
                            <option value="<?php echo (int) $profissional['id']; ?>"><?php echo e($profissional['nome']); ?></option>
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
                    <label class="label-text" for="campo-data">Data</label>
                    <input class="input" id="campo-data" name="data" type="date"
                           value="<?php echo e($data); ?>" required>
                </div>
                <div class="form-campo<?php echo $data === data_referencia_prototipo() ? ' campo-erro' : ''; ?>">
                    <label class="label-text" for="campoHoraInicio">Hora de início</label>
                    <input class="input" id="campoHoraInicio" name="hora_inicio" type="time"
                           value="<?php echo e($hora_sugerida); ?>" step="1800" required
                           <?php echo $data === data_referencia_prototipo() ? 'aria-describedby="erro-hora"' : ''; ?>>
                    <?php if ($data === data_referencia_prototipo()): ?>
                        <p class="erro-campo" id="erro-hora">Horário ocupado. Escolha um dos horários livres acima.</p>
                    <?php endif; ?>
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
                <label class="label-text" for="campo-observacoes">Observações</label>
                <textarea class="input" id="campo-observacoes" name="observacoes" rows="3"
                          placeholder="Ex.: cliente prefere ser atendida pela Ana">Cliente prefere ser atendida pela Ana.</textarea>
            </div>

            <div class="form-rodape">
                <button class="btn btn-primario" type="submit">Salvar</button>
                <a class="btn btn-secundario" href="<?php echo e($voltar); ?>">Cancelar</a>
            </div>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
