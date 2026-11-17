<?php

// Dados fictícios e regras de horário do protótipo visual (sem banco de dados).

// Expediente: 07:00–20:00, com almoço de 12:00 às 14:00.
define('HORARIO_ABERTURA_MANHA', 7 * 60);
define('HORARIO_FIM_MANHA', 12 * 60);
define('HORARIO_INICIO_TARDE', 14 * 60);
define('HORARIO_FECHAMENTO_TARDE', 20 * 60);
define('INTERVALO_MINUTOS', 30);

function expediente_texto()
{
    return '07:00–20:00 · almoço 12:00–14:00';
}

// Data usada como "hoje" no protótipo: mantém os atendimentos fictícios coerentes.
function data_referencia_prototipo()
{
    return '2026-11-17';
}

// Aceita qualquer data válida (inclusive futura). Inválida volta à referência.
function normalizar_data_prototipo($data)
{
    $data = (string) $data;
    $partes = explode('-', $data);
    if (count($partes) !== 3) {
        return data_referencia_prototipo();
    }

    $ano = (int) $partes[0];
    $mes = (int) $partes[1];
    $dia = (int) $partes[2];
    if (!checkdate($mes, $dia, $ano)) {
        return data_referencia_prototipo();
    }

    return sprintf('%04d-%02d-%02d', $ano, $mes, $dia);
}

// Desloca a data em N dias, usado pelos botões "Dia anterior" e "Próximo dia".
function deslocar_data_prototipo($data, $dias)
{
    $data = normalizar_data_prototipo($data);
    $sinal = (int) $dias < 0 ? '-' : '+';
    $timestamp = strtotime($data . ' ' . $sinal . abs((int) $dias) . ' days');

    return $timestamp ? date('Y-m-d', $timestamp) : $data;
}

// Nome amigável da data, válido para qualquer dia da semana.
function data_exibicao($data)
{
    $data = normalizar_data_prototipo($data);
    $nomes = array(
        1 => 'Segunda',
        2 => 'Terça',
        3 => 'Quarta',
        4 => 'Quinta',
        5 => 'Sexta',
        6 => 'Sábado',
        7 => 'Domingo',
    );
    $dia_semana = (int) date('N', strtotime($data . ' 12:00:00'));
    $partes = explode('-', $data);

    return $nomes[$dia_semana] . ', ' . $partes[2] . '/' . $partes[1] . '/' . $partes[0];
}

function profissionais_prototipo()
{
    return array(
        array('id' => 1, 'nome' => 'Ana'),
        array('id' => 2, 'nome' => 'Carla'),
        array('id' => 3, 'nome' => 'Juliana'),
    );
}

// Catálogo do protótipo. Valores em reais, coerentes com um salão de bairro:
// os combos saem mais baratos que a soma dos serviços avulsos.
function servicos_prototipo()
{
    return array(
        array('id' => 1, 'nome' => 'Corte feminino', 'duracao' => 60, 'valor' => '120.00', 'ativo' => true),
        array('id' => 2, 'nome' => 'Corte masculino', 'duracao' => 30, 'valor' => '70.00', 'ativo' => true),
        array('id' => 3, 'nome' => 'Escova', 'duracao' => 45, 'valor' => '85.00', 'ativo' => true),
        array('id' => 4, 'nome' => 'Penteado para evento', 'duracao' => 90, 'valor' => '180.00', 'ativo' => false),
        array('id' => 5, 'nome' => 'Progressiva', 'duracao' => 180, 'valor' => '250.00', 'ativo' => true),
        array('id' => 6, 'nome' => 'Selagem térmica', 'duracao' => 120, 'valor' => '180.00', 'ativo' => true),
        array('id' => 7, 'nome' => 'Botox capilar', 'duracao' => 120, 'valor' => '220.00', 'ativo' => true),
        array('id' => 8, 'nome' => 'Barba', 'duracao' => 30, 'valor' => '50.00', 'ativo' => true),
        array('id' => 9, 'nome' => 'Barba + cabelo', 'duracao' => 45, 'valor' => '100.00', 'ativo' => true),
        array('id' => 10, 'nome' => 'Barba + cabelo + sobrancelha', 'duracao' => 60, 'valor' => '120.00', 'ativo' => true),
        array('id' => 11, 'nome' => 'Sobrancelha', 'duracao' => 30, 'valor' => '40.00', 'ativo' => true),
    );
}

function servicos_ativos_prototipo()
{
    $ativos = array();
    foreach (servicos_prototipo() as $servico) {
        if ($servico['ativo']) {
            $ativos[] = $servico;
        }
    }

    return $ativos;
}

function servico_prototipo($id)
{
    foreach (servicos_prototipo() as $servico) {
        if ((int) $servico['id'] === (int) $id) {
            return $servico;
        }
    }

    return null;
}

// Durações disponíveis para a consulta de horários livres.
function duracoes_prototipo()
{
    $duracoes = array();
    foreach (servicos_ativos_prototipo() as $servico) {
        $duracoes[] = (int) $servico['duracao'];
    }
    $duracoes = array_values(array_unique($duracoes));
    sort($duracoes, SORT_NUMERIC);

    return $duracoes;
}

// Agendamentos de exemplo. "proprio" identifica os atendimentos do cliente
// usado no protótipo (Maria Silva); os demais aparecem apenas como "Ocupado".
function agendamentos_prototipo($data)
{
    $agendas = array(
        '2026-11-17' => array(
            array('id' => 101, 'profissional_id' => 1, 'hora_inicio' => '07:30', 'duracao_min' => 60, 'cliente' => 'Maria Silva', 'servico' => 'Corte feminino', 'valor' => '120.00', 'status' => 'agendado', 'proprio' => true),
            array('id' => 102, 'profissional_id' => 1, 'hora_inicio' => '11:30', 'duracao_min' => 30, 'cliente' => 'Rosa Lima', 'servico' => 'Corte masculino', 'valor' => '70.00', 'status' => 'realizado', 'proprio' => false),
            array('id' => 103, 'profissional_id' => 2, 'hora_inicio' => '08:00', 'duracao_min' => 30, 'cliente' => 'João Prado', 'servico' => 'Corte masculino', 'valor' => '70.00', 'status' => 'agendado', 'proprio' => false),
            array('id' => 104, 'profissional_id' => 2, 'hora_inicio' => '14:00', 'duracao_min' => 60, 'cliente' => 'Beatriz', 'servico' => 'Corte feminino', 'valor' => '120.00', 'status' => 'cancelado', 'proprio' => false),
            array('id' => 105, 'profissional_id' => 3, 'hora_inicio' => '10:00', 'duracao_min' => 60, 'cliente' => 'Paulo Rocha', 'servico' => 'Corte feminino', 'valor' => '120.00', 'status' => 'agendado', 'proprio' => false),
            array('id' => 106, 'profissional_id' => 3, 'hora_inicio' => '16:00', 'duracao_min' => 30, 'cliente' => 'Maria Silva', 'servico' => 'Corte masculino', 'valor' => '70.00', 'status' => 'agendado', 'proprio' => true),
        ),
        '2026-11-16' => array(
            array('id' => 111, 'profissional_id' => 1, 'hora_inicio' => '07:30', 'duracao_min' => 60, 'cliente' => 'Maria Silva', 'servico' => 'Escova', 'valor' => '85.00', 'status' => 'realizado', 'proprio' => true),
            array('id' => 112, 'profissional_id' => 2, 'hora_inicio' => '11:30', 'duracao_min' => 30, 'cliente' => 'João Prado', 'servico' => 'Corte masculino', 'valor' => '70.00', 'status' => 'realizado', 'proprio' => false),
            array('id' => 113, 'profissional_id' => 3, 'hora_inicio' => '18:00', 'duracao_min' => 45, 'cliente' => 'Rosa Lima', 'servico' => 'Escova', 'valor' => '85.00', 'status' => 'realizado', 'proprio' => false),
        ),
        '2026-11-18' => array(
            array('id' => 121, 'profissional_id' => 2, 'hora_inicio' => '14:00', 'duracao_min' => 60, 'cliente' => 'Maria Silva', 'servico' => 'Corte feminino', 'valor' => '120.00', 'status' => 'agendado', 'proprio' => true),
            array('id' => 122, 'profissional_id' => 1, 'hora_inicio' => '09:30', 'duracao_min' => 45, 'cliente' => 'Rosa Lima', 'servico' => 'Escova', 'valor' => '85.00', 'status' => 'agendado', 'proprio' => false),
            array('id' => 123, 'profissional_id' => 3, 'hora_inicio' => '19:00', 'duracao_min' => 60, 'cliente' => 'Paulo Rocha', 'servico' => 'Corte feminino', 'valor' => '120.00', 'status' => 'agendado', 'proprio' => false),
        ),
    );

    return isset($agendas[$data]) ? $agendas[$data] : array();
}

function hora_para_minutos($hora)
{
    $partes = explode(':', (string) $hora);
    if (count($partes) < 2) {
        return 0;
    }

    return ((int) $partes[0] * 60) + (int) $partes[1];
}

function minutos_para_hora($minutos)
{
    $minutos = (int) $minutos;

    return sprintf('%02d:%02d', intdiv($minutos, 60), $minutos % 60);
}

// Posição do bloco na grade: manhã, faixa de almoço e tarde (1 px por minuto).
function posicao_na_grade($hora)
{
    $minutos = hora_para_minutos($hora);
    if ($minutos <= HORARIO_FIM_MANHA) {
        return max(0, $minutos - HORARIO_ABERTURA_MANHA);
    }

    $ate_tarde = (HORARIO_FIM_MANHA - HORARIO_ABERTURA_MANHA)
        + (HORARIO_INICIO_TARDE - HORARIO_FIM_MANHA);

    return $ate_tarde + max(0, $minutos - HORARIO_INICIO_TARDE);
}

// Intervalos que encostam também contam como ocupados, igual à regra do MVP.
// O almoço (12:00–14:00) bloqueia só quando há sobreposição real: terminar às
// 12:00 ou começar às 14:00 é permitido.
function horarios_livres_prototipo($agendamentos, $profissional_id, $duracao_min = 60)
{
    $duracao_min = (int) $duracao_min;
    $ocupados = array();

    foreach ($agendamentos as $agendamento) {
        if ((int) $agendamento['profissional_id'] !== (int) $profissional_id) {
            continue;
        }
        if ($agendamento['status'] === 'cancelado') {
            continue;
        }
        $inicio = hora_para_minutos($agendamento['hora_inicio']);
        $ocupados[] = array($inicio, $inicio + (int) $agendamento['duracao_min']);
    }

    $periodos = array(
        array(HORARIO_ABERTURA_MANHA, HORARIO_FIM_MANHA),
        array(HORARIO_INICIO_TARDE, HORARIO_FECHAMENTO_TARDE),
    );
    $livres = array();

    foreach ($periodos as $periodo) {
        for ($inicio = $periodo[0]; $inicio + $duracao_min <= $periodo[1]; $inicio += INTERVALO_MINUTOS) {
            $conflita = $inicio < HORARIO_INICIO_TARDE
                && $inicio + $duracao_min > HORARIO_FIM_MANHA;

            foreach ($ocupados as $intervalo) {
                if ($inicio <= $intervalo[1] && $inicio + $duracao_min >= $intervalo[0]) {
                    $conflita = true;
                    break;
                }
            }

            if (!$conflita) {
                $livres[] = minutos_para_hora($inicio);
            }
        }
    }

    return $livres;
}
