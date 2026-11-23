<?php
abrirModal("visualizarParceiro", "Visualizar Parceiro", 'fa-solid fa-eye');
?>
<div class="visContainerParceiro">
    <div class="visCardPrincipalParceiro">
        <div class="visConteudoScrollParceiro">
        <div class="visCabecalhoParceiro">
            <div class="visLogoBoxParceiro">
                <div class="visIconeFotoParceiro" id="visIconeFotoParceiro">
                    <i class="fa-solid fa-building"></i>
                </div>
                <img src="" alt="Logo da empresa parceira" id="visFotoParceiro" hidden>
            </div>
            <div class="visTituloBoxParceiro">
                <h2 class="visNomeParceiro" id="visNomeParceiro"></h2>
                <span class="visStatusParceiro" id="visStatusParceiro">Ativo</span>
            </div>
        </div>

        <div class="visDadosEmpresaGrid">
            <div class="visBlocoCampoParceiro">
                <span class="visRotuloParceiro">CNPJ</span>
                <span class="visValorParceiro" id="visCnpjParceiro"></span>
            </div>

            <div class="visBlocoCampoParceiro">
                <span class="visRotuloParceiro">Telefone</span>
                <span class="visValorParceiro" id="visTelefoneParceiro"></span>
            </div>

            <div class="visBlocoCampoParceiro">
                <span class="visRotuloParceiro">Área de Atuação</span>
                <span class="visValorParceiro" id="visAreaAtuacaoParceiro"></span>
            </div>
        </div>

        <hr class="visDivisorParceiro">

        <div class="visListaRedesParceiro">
            <div class="visItemRedeParceiro">
                <i class="fa-solid fa-globe visIconeRede"></i>
                <div class="visInfoRedeParceiro">
                    <span class="visRotuloParceiro">Site</span>
                    <a href="#" target="_blank" rel="noopener noreferrer" class="visLinkParceiro" id="visSiteParceiro"></a>
                </div>
            </div>

            <div class="visItemRedeParceiro">
                <i class="fa-brands fa-instagram visIconeRede"></i>
                <div class="visInfoRedeParceiro">
                    <span class="visRotuloParceiro">Instagram</span>
                    <a href="#" target="_blank" rel="noopener noreferrer" class="visLinkParceiro" id="visInstagramParceiro"></a>
                </div>
            </div>

            <div class="visItemRedeParceiro">
                <i class="fa-brands fa-linkedin visIconeRede"></i>
                <div class="visInfoRedeParceiro">
                    <span class="visRotuloParceiro">LinkedIn</span>
                    <a href="#" target="_blank" rel="noopener noreferrer" class="visLinkParceiro" id="visLinkedinParceiro"></a>
                </div>
            </div>
        </div>

        <hr class="visDivisorParceiro">

        <section class="visSecaoRepresentante">
            <h3 class="visTituloSecaoRepresentante">Representante Legal</h3>

            <div class="visRepresentanteLinha">
                <div class="visRepresentanteIdentidade">
                    <h4 class="visNomeRepresentante" id="visNomeRepresentanteParceiro"></h4>
                    <span class="visCargoRepresentante" id="visCargoRepresentanteParceiro"></span>
                </div>

                <div class="visRepresentanteContatos">
                    <div class="visRepresentanteContatoItem">
                        <i class="fa-solid fa-phone"></i>
                        <span class="visValorParceiro" id="visTelefoneRepresentanteParceiro"></span>
                    </div>

                    <div class="visRepresentanteContatoItem">
                        <i class="fa-solid fa-envelope"></i>
                        <a href="#" class="visLinkParceiro" id="visEmailRepresentanteParceiro"></a>
                    </div>
                </div>
            </div>
        </section>
        </div>

        <div class="visRodapeParceiro">
            <button type="button" onclick="fecharModal('visualizarParceiro')" class="btnCancelar visBtnFecharParceiro">FECHAR</button>
        </div>
    </div>
</div>
<?php fecharModal() ?>
