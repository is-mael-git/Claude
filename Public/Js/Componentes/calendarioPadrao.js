const calendario = document.getElementById("calendario");
console.log(calendario);
const mesAno = document.getElementById("mesAno");
 
const btnAnterior = document.getElementById("anterior");
const btnProximo = document.getElementById("proximo");
 
const meses = [
    "Janeiro",
    "Fevereiro",
    "Março",
    "Abril",
    "Maio",
    "Junho",
    "Julho",
    "Agosto",
    "Setembro",
    "Outubro",
    "Novembro",
    "Dezembro"
];
 
const diasSemana = [
    "Domingo",
    "Segunda",
    "Terça",
    "Quarta",
    "Quinta",
    "Sexta",
    "Sábado"
];
 
let dataAtual = new Date();
 
let mes = dataAtual.getMonth();
let ano = dataAtual.getFullYear();
 
function renderizarCalendario(){
    console.log("Renderizando calendário...");
 
    calendario.innerHTML = "";
 
    mesAno.textContent =
        `${meses[mes]} ${ano}`;
 
    const ultimoDia =
        new Date(ano, mes + 1, 0).getDate();
 
    for(let dia = 1; dia <= ultimoDia; dia++){
 
        const data =
            new Date(ano, mes, dia);
 
        const nomeSemana =
            diasSemana[data.getDay()];
 
        const divDia =
            document.createElement("div");
 
        divDia.classList.add("dia");
 
        if(
            dia === new Date().getDate() &&
            mes === new Date().getMonth() &&
            ano === new Date().getFullYear()
        ){
            divDia.classList.add("hoje");
        }
 
        divDia.innerHTML = `
            <span class="semana">
                ${nomeSemana}
            </span>
 
            <span class="numero">
                ${String(dia).padStart(2,'0')}
            </span>
        `;
 
        calendario.appendChild(divDia);
    }
}
 
btnProximo.addEventListener("click",()=>{
 
    mes++;
 
    if(mes > 11){
        mes = 0;
        ano++;
    }
 
    renderizarCalendario();
});
 
btnAnterior.addEventListener("click",()=>{
 
    mes--;
 
    if(mes < 0){
        mes = 11;
        ano--;
    }
 
    renderizarCalendario();
});
 
renderizarCalendario();