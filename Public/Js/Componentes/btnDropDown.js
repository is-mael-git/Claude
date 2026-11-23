const select = document.querySelector(".select");
const selected = document.querySelector(".selected");
const labels = document.querySelectorAll(".options label");

selected.addEventListener("click", () => {
    select.classList.toggle("active");
});

labels.forEach(label => {
    label.addEventListener("click", () => {
        select.classList.remove("active");
    });
});