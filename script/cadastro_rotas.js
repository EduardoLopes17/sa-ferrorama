
document.addEventListener('DOMContentLoaded', function () {
    const botaoAdicionar = document.getElementById('adicionar-parada');
    const listaParadas = document.getElementById('lista-paradas');
    const modeloParada = document.getElementById('modelo-parada');

    botaoAdicionar.addEventListener('click', function () {
        if (listaParadas.children.length >= 50) {
            alert('O limite é de 50 paradas intermediárias.');
            return;
        }

        const novaParada = modeloParada.content.cloneNode(true);
        listaParadas.appendChild(novaParada);
    });

    listaParadas.addEventListener('click', function (evento) {
        if (evento.target.classList.contains('btn-remover-parada')) {
            evento.target.closest('.linha-parada').remove();
        }
    });
});
