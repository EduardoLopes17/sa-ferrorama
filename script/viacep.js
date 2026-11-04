
document.addEventListener("DOMContentLoaded", () => {
    const cepInput = document.getElementById("cep");
    const mensagem = document.getElementById("cep-mensagem");

    if (!cepInput || !mensagem) return;

    const campos = {
        logradouro: document.getElementById("logradouro"),
        bairro: document.getElementById("bairro"),
        cidade: document.getElementById("cidade"),
        uf: document.getElementById("uf")
    };

    let consultaAtual = 0;

    function limparEndereco() {
        Object.values(campos).forEach(campo => {
            if (campo) campo.value = "";
        });
    }

    function mostrarMensagem(texto, erro = false) {
        mensagem.textContent = texto;
        mensagem.className = erro
            ? "form-text text-danger"
            : "form-text text-success";
    }

    cepInput.addEventListener("input", () => {
        consultaAtual++;

        const cep = cepInput.value.replace(/\D/g, "").slice(0, 8);

        cepInput.value = cep.length > 5
            ? `${cep.slice(0, 5)}-${cep.slice(5)}`
            : cep;

        limparEndereco();
        mensagem.textContent = "";
    });

    cepInput.addEventListener("blur", async () => {
        const cep = cepInput.value.replace(/\D/g, "");

        if (cep.length !== 8) {
            mostrarMensagem("Informe um CEP válido com 8 números.", true);
            return;
        }

        const identificador = ++consultaAtual;
        mostrarMensagem("Consultando CEP...");

        try {
            const resposta = await fetch(
                `https://viacep.com.br/ws/${cep}/json/`
            );

            if (!resposta.ok) {
                throw new Error("Falha na comunicação com a API");
            }

            const dados = await resposta.json();

            if (identificador !== consultaAtual) return;

            if (dados.erro === true) {
                limparEndereco();
                mostrarMensagem("CEP não encontrado.", true);
                return;
            }

            campos.logradouro.value = dados.logradouro || "";
            campos.bairro.value = dados.bairro || "";
            campos.cidade.value = dados.localidade || "";
            campos.uf.value = dados.uf || "";

            mostrarMensagem("CEP consultado com sucesso.");

            document.getElementById("numero").focus();
        } catch (erro) {
            if (identificador !== consultaAtual) return;

            limparEndereco();
            mostrarMensagem(
                "Não foi possível consultar o CEP. Tente novamente.",
                true
            );

            console.error("Erro ViaCEP:", erro);
        }
    });
});
