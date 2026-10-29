const padroes = [
    "quantidadeQuestionario",
    "quantidadeStartups",
    "quantidadeMentores"
];
 
async function quantidade(padroes) {
    const response = await fetch("/ParkTec/areaTrabalho/adm/quantidadeDash");
    const dados = await response.json();
 
    for (const padrao of padroes) {
        const div = document.getElementById(padrao);
 
        if (div) {
            div.innerText = dados[padrao];
        }
    }
}
 
quantidade(padroes);