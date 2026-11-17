<?php
$titulo_pagina = 'Histórico — AgendaSalão';
$menu_tipo = 'interno';
$menu_ativo = 'historico';
require_once __DIR__ . '/includes/header.php';
?>

<main id="conteudo" class="conteudo">
    <div class="container">
        <header class="pagina-cabecalho">
            <div>
                <h1>Histórico de atendimentos</h1>
                <p>Filtre por cliente, profissional, período ou status.</p>
            </div>
            <button class="btn btn-secundario" type="button" data-imprimir>Exportar PDF</button>
        </header>

        <form class="filtros no-print" method="get" action="historico.php">
            <div class="form-campo">
                <label class="label-text" for="campo-cliente">Cliente</label>
                <input class="input" id="campo-cliente" name="cliente" type="text"
                       value="Maria" placeholder="Busca parcial">
            </div>
            <div class="form-campo">
                <label class="label-text" for="campo-profissional-filtro">Profissional</label>
                <select class="input" id="campo-profissional-filtro" name="profissional_id">
                    <option value="">Todos</option>
                    <option value="1" selected>Ana</option>
                    <option value="2">Carla</option>
                    <option value="3">Juliana</option>
                </select>
            </div>
            <div class="form-campo">
                <label class="label-text" for="campo-data-inicial">Data inicial</label>
                <input class="input" id="campo-data-inicial" name="data_inicial" type="date"
                       value="2026-11-01">
            </div>
            <div class="form-campo">
                <label class="label-text" for="campo-data-final">Data final</label>
                <input class="input" id="campo-data-final" name="data_final" type="date"
                       value="2026-11-17">
            </div>
            <div class="form-campo">
                <label class="label-text" for="campo-status">Status</label>
                <select class="input" id="campo-status" name="status">
                    <option value="">Todos</option>
                    <option value="agendado">Agendado</option>
                    <option value="realizado" selected>Realizado</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
            <div class="filtros-acoes">
                <button class="btn btn-primario" type="submit">Filtrar</button>
                <a class="btn btn-secundario" href="historico.php">Limpar filtros</a>
            </div>
        </form>

        <p class="print-resumo">
            Filtros: cliente “Maria” · profissional Ana · 01/11/2026 a 17/11/2026 · status realizado.
        </p>
        <p class="campo-ajuda no-print">
            6 agendamentos encontrados · filtro de status “realizado” mostra o histórico de serviços.
        </p>

        <div class="tabela-container">
            <table class="tabela">
                <thead>
                    <tr>
                        <th scope="col">Data</th>
                        <th scope="col">Hora</th>
                        <th scope="col">Cliente</th>
                        <th scope="col">Telefone</th>
                        <th scope="col">Profissional</th>
                        <th scope="col">Serviço</th>
                        <th scope="col">Valor</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>17/11/2026</td><td>07:30</td><td>Maria Silva</td><td>(11) 98765-4321</td>
                        <td>Ana</td><td>Corte feminino</td><td>R$ 120,00</td>
                        <td><span class="etiqueta-status status-agendado">agendado</span></td>
                    </tr>
                    <tr>
                        <td>17/11/2026</td><td>10:30</td><td>Rosa Lima</td><td>(11) 98888-1234</td>
                        <td>Ana</td><td>Escova</td><td>R$ 85,00</td>
                        <td><span class="etiqueta-status status-realizado">realizado</span></td>
                    </tr>
                    <tr>
                        <td>17/11/2026</td><td>08:00</td><td>João Prado</td><td>(11) 97777-5678</td>
                        <td>Carla</td><td>Corte masculino</td><td>R$ 70,00</td>
                        <td><span class="etiqueta-status status-agendado">agendado</span></td>
                    </tr>
                    <tr>
                        <td>17/11/2026</td><td>14:00</td><td>Beatriz</td><td>(11) 96666-9012</td>
                        <td>Carla</td><td>Corte feminino</td><td>R$ 120,00</td>
                        <td><span class="etiqueta-status status-cancelado">cancelado</span></td>
                    </tr>
                    <tr>
                        <td>16/11/2026</td><td>18:00</td><td>Luiza Andrade</td><td>(11) 95555-3456</td>
                        <td>Juliana</td><td>Escova</td><td>R$ 85,00</td>
                        <td><span class="etiqueta-status status-realizado">realizado</span></td>
                    </tr>
                    <tr>
                        <td>15/11/2026</td><td>16:00</td><td>Paulo Rocha</td><td>(11) 94444-7890</td>
                        <td>Juliana</td><td>Corte masculino</td><td>R$ 70,00</td>
                        <td><span class="etiqueta-status status-realizado">realizado</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
