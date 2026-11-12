function mostrarConteudo(pagina = 'home') {
    const conteudoSelecionado = document.getElementById(pagina);

    if (!conteudoSelecionado || !conteudoSelecionado.classList.contains('content')) {
        return;
    }

    document.querySelectorAll('.content').forEach(conteudo => {
        conteudo.classList.remove('active');
    });

    document.querySelectorAll('.nav-item').forEach(botao => {
        botao.classList.remove('active');
    });

    conteudoSelecionado.classList.add('active');

    document.querySelectorAll('.nav-item').forEach(botao => {
        if (botao.getAttribute('onclick')?.includes("'" + pagina + "'")) {
            botao.classList.add('active');
        }
    });

    sessionStorage.setItem('abaAtualHELG', pagina);
}

document.addEventListener('DOMContentLoaded', function () {
    const parametros = new URLSearchParams(window.location.search);
    const abaURL = parametros.get('aba');
    const abaSalva = sessionStorage.getItem('abaAtualHELG');

    mostrarConteudo(abaURL || abaSalva || 'home');
});