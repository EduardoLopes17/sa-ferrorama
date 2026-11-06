
function limparParadasRota() {
    document.getElementById('lista-paradas-modal').innerHTML = '';
}

function adicionarParadaRota(valor = '') {
    const lista = document.getElementById('lista-paradas-modal');

    if (lista.children.length >= 50) {
        alert('O limite é de 50 paradas intermediárias.');
        return;
    }

    const linha = document.createElement('div');
    linha.className = 'd-flex gap-2 mb-2';

    const select = document.getElementById('saidaRota').cloneNode(true);
    select.removeAttribute('id');
    select.name = 'intermediarias[]';
    select.value = valor;

    const remover = document.createElement('button');
    remover.type = 'button';
    remover.className = 'btn btn-outline-danger btn-sm';
    remover.textContent = 'Remover';
    remover.addEventListener('click', () => linha.remove());

    linha.append(select, remover);
    lista.appendChild(linha);
}

function novaRota() {
    const form = document.getElementById('formRota');

    form.reset();
    form.action = 'public/cadastro_rotas.php';

    document.getElementById('idRota').value = '';
    document.getElementById('tituloModalRota').textContent = 'Cadastrar Rota';
    document.getElementById('botaoSalvarRota').textContent = 'Cadastrar Rota';

    limparParadasRota();
}

async function editarRota(botao) {
    const id = botao.dataset.id;
    const form = document.getElementById('formRota');
    const salvar = document.getElementById('botaoSalvarRota');

    salvar.disabled = true;

    try {
        const resposta = await fetch(
            'backend/buscar_rota.php?id=' + encodeURIComponent(id)
        );

        if (!resposta.ok) {
            throw new Error('Não foi possível carregar a rota.');
        }

        const rota = await resposta.json();

        form.reset();
        form.action = 'public/editar_rota.php?id=' + encodeURIComponent(id);

        document.getElementById('idRota').value = rota.id;
        document.getElementById('nomeRota').value = rota.nome;
        document.getElementById('statusRota').value = rota.status;
        document.getElementById('tremRota').value = rota.trem_id || '';
        document.getElementById('saidaRota').value = rota.saida;
        document.getElementById('chegadaRota').value = rota.chegada;

        limparParadasRota();

        rota.intermediarias.forEach(parada => {
            adicionarParadaRota(parada);
        });

        document.getElementById('tituloModalRota').textContent = 'Editar Rota';
        salvar.textContent = 'Salvar Alterações';

    } catch (erro) {
        alert(erro.message);
        bootstrap.Modal.getInstance(
            document.getElementById('modalRota')
        )?.hide();
    } finally {
        salvar.disabled = false;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const botaoNova = document.querySelector('.btn-nova-rota');
    const botaoAdicionar = document.getElementById('adicionar-parada-modal');

    if (botaoNova) {
        botaoNova.addEventListener('click', novaRota);
    }

    if (botaoAdicionar) {
        botaoAdicionar.addEventListener('click', function () {
            adicionarParadaRota();
        });
    }
});
