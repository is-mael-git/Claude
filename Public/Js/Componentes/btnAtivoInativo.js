
const toggles = document.querySelectorAll(".toggle");
const status = document.querySelectorAll(".status");

console.log(toggles)

toggles.forEach((toggle, index) => {
    toggle.addEventListener("change", (evento) => {
        
        evento.preventDefault();
        evento.stopPropagation();
        
        if(toggle.checked) {
            status[index].textContent = "Ativo";
            status[index].style.color = "#86AC18";
        }else{
            status[index].textContent = "Inativo"
            status[index].style.color = "#DA0000"
        }

    });
})
