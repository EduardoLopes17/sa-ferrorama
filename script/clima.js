
document.addEventListener("DOMContentLoaded", async () => {
    const temperatura = document.getElementById("clima-temperatura");
    const condicao = document.getElementById("clima-condicao");
    const atualizacao = document.getElementById("clima-atualizacao");

    if (!temperatura || !condicao || !atualizacao) return;

    const url = "https://api.open-meteo.com/v1/forecast?latitude=-26.3044&longitude=-48.8487&current=temperature_2m,weather_code&timezone=America%2FSao_Paulo";

    try {
        const resposta = await fetch(url);

        if (!resposta.ok) {
            throw new Error("Falha na consulta meteorológica");
        }

        const dados = await resposta.json();
        const clima = dados.current;

        if (!clima || typeof clima.temperature_2m !== "number") {
            throw new Error("Dados meteorológicos indisponíveis");
        }

        const codigo = clima.weather_code;
        let descricao = "Condição não identificada";

        if (codigo === 0) {
            descricao = "Céu limpo";
        } else if ([1, 2, 3].includes(codigo)) {
            descricao = "Parcialmente nublado ou nublado";
        } else if ([45, 48].includes(codigo)) {
            descricao = "Neblina";
        } else if ([51, 53, 55, 56, 57].includes(codigo)) {
            descricao = "Garoa";
        } else if ([61, 63, 65, 66, 67, 80, 81, 82].includes(codigo)) {
            descricao = "Chuva";
        } else if ([71, 73, 75, 77, 85, 86].includes(codigo)) {
            descricao = "Neve";
        } else if ([95, 96, 99].includes(codigo)) {
            descricao = "Trovoadas";
        }

        temperatura.textContent = `${clima.temperature_2m} °C`;
        condicao.textContent = descricao;

        const horario = clima.time.replace("T", " às ");

        atualizacao.textContent = `Dados meteorológicos de ${horario} (horário de Brasília)`;
    } catch (erro) {
        temperatura.textContent = "-- °C";
        condicao.textContent = "Não foi possível consultar o clima.";
        atualizacao.textContent = "Verifique sua conexão e tente novamente.";
        console.error("Erro na API de clima:", erro);
    }
});
