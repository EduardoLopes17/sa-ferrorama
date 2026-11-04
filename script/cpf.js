
document.addEventListener("DOMContentLoaded", () => {
    const cpf = document.getElementById("cpf");

    if (!cpf) return;

    cpf.addEventListener("input", () => {
        let valor = cpf.value.replace(/\D/g, "").slice(0, 11);

        if (valor.length > 9) {
            valor = valor.replace(
                /^(\d{3})(\d{3})(\d{3})(\d{1,2})$/,
                "$1.$2.$3-$4"
            );
        } else if (valor.length > 6) {
            valor = valor.replace(
                /^(\d{3})(\d{3})(\d{1,3})$/,
                "$1.$2.$3"
            );
        } else if (valor.length > 3) {
            valor = valor.replace(
                /^(\d{3})(\d{1,3})$/,
                "$1.$2"
            );
        }

        cpf.value = valor;
    });
});
