<?php
require_once __DIR__ . '/includes/funcoes.php';
require_once __DIR__ . '/includes/agenda_prototipo.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$servico = $id > 0 ? servico_prototipo($id) : null;
$editando = $servico !== null;

if (isset($_POST['id'])) {
    $id_post = (int) $_POST['id'];
    $servico_post = $id_post > 0 ? servico_prototipo($id_post) : null;
    if ($servico_post !== null) {
        $servico = $servico_post;
        $editando = true;
    }
}

$nome = $editando ? $servico['nome'] : '';
$duracao = $editando ? (string) (int) $servico['duracao'] : '';
$valor = $editando ? number_format((float) $servico['valor'], 2, '.', '') : '';
$erro_geral = '';
$erro_duracao = '';

// Protótipo: não há persistência. O sistema final valida no banco e grava depois.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '';
    $nome = isset($_POST['nome']) ? trim((string) $_POST['nome']) : '';
    $duracao = isset($_POST['duracao_min']) ? (string) $_POST['duracao_min'] : '';
    $valor = isset($_POST['valor_padrao']) ? (string) $_POST['valor_padrao'] : '';

    if (!hash_equals('prototipo', $token)) {
        $erro_geral = 'Não foi possível salvar: token de segurança inválido.';
    } elseif ($nome === '' || (int) $duracao <= 0 || (float) $valor < 0) {
        $erro_geral = 'Não foi possível salvar. Confira os campos destacados.';
        if ((int) $duracao <= 0) {
            $erro_duracao = 'A duração deve ser maior que zero.';
        }
    } else {
        redirecionar('servicos.php');
    }
}

if (isset($_GET['id']) && !$editando && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $titulo_pagina = 'Serviço não encontrado — AgendaSalão';
} else {
    $titulo_pagina = ($editando ? 'Editar serviço' : 'Novo serviço') . ' — AgendaSalão';
}
$menu_tipo = 'interno';
$menu_ativo = 'servicos';
require_once __DIR__ . '/includes/header.php';
?>

<main id="conteudo" class="conteudo">
    <div class="container">
        <header class="pagina-cabecalho">
            <div>
                <h1><?php echo $editando ? 'Editar serviço' : 'Novo serviço'; ?></h1>
                <p>
                    <?php if ($editando): ?>
                        Atualize a duração e o valor padrão de “<?php echo e($servico['nome']); ?>”.
                    <?php else: ?>
                        Cadastre a duração e o valor padrão do atendimento.
                    <?php endif; ?>
                </p>
            </div>
            <a class="btn btn-secundario" href="servicos.php">Voltar à lista</a>
        </header>

        <?php if (isset($_GET['id']) && !$editando && $_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
            <div class="mensagem-erro" role="alert">
                <p>Serviço não encontrado. <a href="servicos.php">Voltar à lista de serviços</a>.</p>
            </div>
        <?php endif; ?>

        <?php if ($erro_geral !== ''): ?>
            <div class="mensagem-erro" role="alert">
                <p><?php echo e($erro_geral); ?></p>
            </div>
        <?php endif; ?>

        <?php if ($editando || !isset($_GET['id']) || $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <form class="formulario" method="post" action="servico_form.php">
                <input type="hidden" name="csrf_token" value="prototipo">
                <?php if ($editando): ?>
                    <input type="hidden" name="id" value="<?php echo (int) $servico['id']; ?>">
                <?php endif; ?>

                <div class="form-campo">
                    <label class="label-text" for="campo-nome">Nome</label>
                    <input class="input" id="campo-nome" name="nome" type="text"
                           value="<?php echo e($nome); ?>" required placeholder="Ex.: Corte feminino">
                </div>

                <div class="form-linha">
                    <div class="form-campo<?php echo $erro_duracao !== '' ? ' campo-erro' : ''; ?>">
                        <label class="label-text" for="campo-duracao">Duração (min)</label>
                        <input class="input" id="campo-duracao" name="duracao_min" type="number"
                               value="<?php echo e($duracao); ?>" min="1" required
                               <?php echo $erro_duracao !== '' ? 'aria-describedby="erro-duracao"' : ''; ?>>
                        <?php if ($erro_duracao !== ''): ?>
                            <p class="erro-campo" id="erro-duracao"><?php echo e($erro_duracao); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="form-campo">
                        <label class="label-text" for="campo-valor-padrao">Valor padrão (R$)</label>
                        <input class="input" id="campo-valor-padrao" name="valor_padrao" type="number"
                               value="<?php echo e($valor); ?>" min="0" step="0.01" required>
                    </div>
                </div>

                <?php if ($editando && !$servico['ativo']): ?>
                    <p class="campo-ajuda">Este serviço está inativo e não aparece nos formulários de agendamento.</p>
                <?php endif; ?>

                <div class="form-rodape">
                    <button class="btn btn-primario" type="submit">
                        <?php echo $editando ? 'Salvar alterações' : 'Salvar'; ?>
                    </button>
                    <a class="btn btn-secundario" href="servicos.php">Cancelar</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
