<?php
require_once __DIR__ . '/includes/funcoes.php';
require_once __DIR__ . '/includes/agenda_prototipo.php';

$servicos = servicos_prototipo();
$total_servicos = count($servicos);
$total_ativos = count(servicos_ativos_prototipo());

$titulo_pagina = 'Serviços — AgendaSalão';
$menu_tipo = 'interno';
$menu_ativo = 'servicos';
require_once __DIR__ . '/includes/header.php';
?>

<main id="conteudo" class="conteudo">
    <div class="container">
        <header class="pagina-cabecalho">
            <div>
                <h1>Serviços</h1>
                <p>
                    <?php echo (int) $total_servicos; ?> serviços cadastrados ·
                    <?php echo (int) $total_ativos; ?> ativos
                </p>
            </div>
            <a class="btn btn-primario" href="servico_form.php">Novo serviço</a>
        </header>

        <div class="mensagem-sucesso" role="status">
            <p>Serviço “Penteado para evento” inativado com sucesso.</p>
        </div>

        <div class="tabela-container">
            <table class="tabela">
                <thead>
                    <tr>
                        <th scope="col">Nome</th>
                        <th scope="col">Duração</th>
                        <th scope="col">Valor</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="col-acoes">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($servicos as $servico): ?>
                        <tr>
                            <td><?php echo e($servico['nome']); ?></td>
                            <td><?php echo (int) $servico['duracao']; ?> min</td>
                            <td>R$ <?php echo e(number_format((float) $servico['valor'], 2, ',', '.')); ?></td>
                            <td>
                                <span class="etiqueta-status status-<?php echo $servico['ativo'] ? 'ativo' : 'inativo'; ?>">
                                    <?php echo $servico['ativo'] ? 'ativo' : 'inativo'; ?>
                                </span>
                            </td>
                            <td class="col-acoes">
                                <div class="acoes">
                                    <a class="btn btn-secundario btn-pequeno"
                                       href="servico_form.php?id=<?php echo (int) $servico['id']; ?>">Editar</a>
                                    <?php if ($servico['ativo']): ?>
                                        <!-- No sistema real, o token vem de csrf_campo(). -->
                                        <form method="post" action="servicos.php">
                                            <input type="hidden" name="csrf_token" value="prototipo">
                                            <input type="hidden" name="id" value="<?php echo (int) $servico['id']; ?>">
                                            <button class="btn btn-perigo btn-pequeno" type="submit">Inativar</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <p class="campo-ajuda">
            Serviços inativados continuam na lista, mas não aparecem nos formulários
            de agendamento. Combos têm valor menor que a soma dos serviços avulsos.
        </p>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
