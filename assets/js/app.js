// Melhorias de interface do protótipo: a validação continua no servidor no sistema final.

document.addEventListener('DOMContentLoaded', function () {
    var campoServico = document.getElementById('campoServico');
    var campoDuracao = document.getElementById('campoDuracao');
    var campoValor = document.getElementById('campoValor');

    if (campoServico && campoDuracao && campoValor) {
        campoServico.addEventListener('change', function () {
            var opcao = campoServico.options[campoServico.selectedIndex];
            if (!opcao) {
                return;
            }
            campoDuracao.value = opcao.dataset.duracao || '';
            campoValor.value = opcao.dataset.valor || '';
        });
    }

    document.querySelectorAll('.horario-livre').forEach(function (chip) {
        chip.addEventListener('click', function () {
            var campoHora = document.getElementById('campoHoraInicio');
            if (campoHora) {
                campoHora.value = chip.dataset.hora;
                campoHora.focus();
            }
        });
    });

    document.querySelectorAll('[data-imprimir]').forEach(function (botao) {
        botao.addEventListener('click', function () {
            window.print();
        });
    });
});
