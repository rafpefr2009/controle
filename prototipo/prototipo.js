/*
    prototipo.js — comportamento mínimo dos mockups (mesma ideia da T4.4/T6.5).
    1) Ao trocar o serviço no agendamento, preenche duração e valor (editáveis).
    2) Na tela de conflito, clicar em um horário livre preenche a hora de início.
*/

(function () {
    "use strict";

    var campoServico = document.getElementById("campo-servico");
    if (campoServico) {
        campoServico.addEventListener("change", function () {
            var opcao = campoServico.selectedOptions[0];
            var duracao = document.getElementById("campo-duracao");
            var valor = document.getElementById("campo-valor");
            if (opcao && opcao.dataset.duracao && duracao) {
                duracao.value = opcao.dataset.duracao;
            }
            if (opcao && opcao.dataset.valor && valor) {
                valor.value = opcao.dataset.valor;
            }
        });
    }

    var campoHora = document.getElementById("campo-hora");
    var botoes = document.querySelectorAll(".horario-livre");
    for (var i = 0; i < botoes.length; i++) {
        botoes[i].addEventListener("click", function () {
            if (campoHora && this.dataset.hora) {
                campoHora.value = this.dataset.hora;
            }
        });
    }
})();
