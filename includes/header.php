<?php
require_once __DIR__ . '/funcoes.php';

$titulo_pagina = isset($titulo_pagina) ? $titulo_pagina : 'AgendaSalão';
$menu_tipo = isset($menu_tipo) ? $menu_tipo : 'interno';
$menu_ativo = isset($menu_ativo) ? $menu_ativo : '';
$modo_visitante = isset($modo_visitante) && $modo_visitante;
$base_publico = (basename($_SERVER['PHP_SELF']) === 'index.php') ? '' : 'index.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="AgendaSalão — agenda de atendimentos do salão.">
    <title><?php echo e($titulo_pagina); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flyonui@2.4.1/flyonui.css">
    <link rel="stylesheet" href="assets/css/estilo.css">
</head>
<body>
    <a class="skip-link" href="#conteudo">Pular para o conteúdo</a>

    <header class="topo no-print">
        <div class="container topo-interno">
            <a class="marca" href="index.php">
                <span class="marca-simbolo" aria-hidden="true">AS</span>
                AgendaSalão
            </a>

            <nav class="menu" aria-label="Navegação principal">
                <?php if ($menu_tipo === 'publico'): ?>
                    <a href="<?php echo e($base_publico); ?>#recursos">Recursos</a>
                    <a href="<?php echo e($base_publico); ?>#como-funciona">Como funciona</a>
                    <a href="<?php echo e($base_publico); ?>#acesso">Acesso</a>
                <?php elseif ($menu_tipo === 'cliente'): ?>
                    <a href="agenda_cliente.php"<?php echo $menu_ativo === 'agenda' ? ' class="ativo" aria-current="page"' : ''; ?>>Minha agenda</a>
                    <?php if (!$modo_visitante): ?>
                        <a href="agendamento_cliente.php"<?php echo $menu_ativo === 'agendamento' ? ' class="ativo" aria-current="page"' : ''; ?>>Novo agendamento</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="agenda.php"<?php echo $menu_ativo === 'agenda' ? ' class="ativo" aria-current="page"' : ''; ?>>Agenda</a>
                    <a href="servicos.php"<?php echo $menu_ativo === 'servicos' ? ' class="ativo" aria-current="page"' : ''; ?>>Serviços</a>
                    <a href="historico.php"<?php echo $menu_ativo === 'historico' ? ' class="ativo" aria-current="page"' : ''; ?>>Histórico</a>
                <?php endif; ?>
            </nav>

            <?php if ($menu_tipo === 'publico' || ($menu_tipo === 'cliente' && $modo_visitante)): ?>
                <a class="btn btn-primario btn-pequeno" href="<?php echo e($base_publico); ?>#acesso">Entrar</a>
            <?php elseif ($menu_tipo === 'cliente'): ?>
                <a class="btn btn-secundario btn-pequeno" href="index.php#acesso">Sair</a>
            <?php else: ?>
                <a class="btn btn-secundario btn-pequeno" href="logout.php">Sair</a>
            <?php endif; ?>
        </div>
    </header>
