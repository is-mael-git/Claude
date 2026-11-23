<?php 
function paginacao() : void{
    ?>
    <div class="juntarPaginacaoPagina">
        <div class="paginacao">
            <div class="primeiro setasPaginacao"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.5 3.5 6 8l4.5 4.5"></path>
                    <path d="M5.5 3.5v9"></path>
                </svg></div>
            <div class="anterior setasPaginacao"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10 3.5 5.5 8l4.5 4.5"></path>
                </svg></div>
            <div class="numeros">
            </div>
            <div class="proximo setasPaginacao"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 3.5 10.5 8 6 12.5"></path>
                </svg></div>
            <div class="ultimo setasPaginacao"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5.5 3.5 10 8l-4.5 4.5"></path>
                    <path d="M10.5 3.5v9"></path>
                </svg></div>
        </div>
        <div class="wrapperQuantidadePagina">
            <select name="quantidadePagina" id="quantidadePagina"></select>
        </div>
    </div>
    <?php } ?>