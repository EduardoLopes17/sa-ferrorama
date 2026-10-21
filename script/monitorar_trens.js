
document.addEventListener('DOMContentLoaded', function () {
    const seletor = document.getElementById('selecionar-trem');
    const elementoDados = document.getElementById('dados-monitoramento');

    if (!seletor || !elementoDados) {
        return;
    }

    const trens = JSON.parse(elementoDados.textContent);

    const codigo = document.getElementById('monitor-codigo');
    const modelo = document.getElementById('monitor-modelo');
    const velocidade = document.getElementById('monitor-velocidade');
    const status = document.getElementById('monitor-status');
    const rota = document.getElementById('monitor-rota');

    function atualizarMonitoramento() {
        const trem = trens.find(function (item) {
            return String(item.id) === seletor.value;
        });

        if (!trem) {
            codigo.textContent = '--';
            modelo.textContent = 'Selecione um trem';
            velocidade.textContent = '-- km/h';
            status.textContent = '--';
            rota.textContent = '--';
            return;
        }

        codigo.textContent = trem.codigo;
        modelo.textContent = trem.modelo;
        velocidade.textContent = Number(trem.velocidade).toLocaleString('pt-BR') + ' km/h';
        status.textContent = trem.status;
        rota.textContent = trem.nome_rota || 'Sem rota definida';
    }

    seletor.addEventListener('change', atualizarMonitoramento);

    atualizarMonitoramento();
});
