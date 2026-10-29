
console.log("aaaa")
const statusCheck = document.querySelectorAll(".statusCheck");

const trocaQuest = document.querySelector("#trocaQuest");

const menuItems = document.querySelectorAll(".menu-item");

let etapaAtual = 0;

function atualizarBarra(){

    menuItems.forEach((item,index)=>{

        item.classList.remove("active");
        item.classList.remove("completed");

        if(index < etapaAtual){

            item.classList.add("completed");

        }

        if(index === etapaAtual){

            item.classList.add("active");

        }

    });

}

atualizarBarra();

trocaQuest.addEventListener("click", (e) => {
    e.preventDefault();
    console.log("toquenouma");
});