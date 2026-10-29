<?php
include_once BASE_MENU;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
   


   
   
   
   
   <section class="menuLateralAreaTrabalho areaCardsSection">     
            <div class="areaCardsContainerCards">
                <?php foreach ($cards as $parceiro): ?>
                    <?php if ($parceiro['ativo']): ?>                   
                        <div class="areaCardsCards">
                            <div class="areaCardsDadosParceiros">
                                <div class="areaCardsParceiroImg">
                                    <div class="areaCardsFoto"><img src="<?php echo BASE_UPLOAD.'Parceiros/'.$parceiro['foto'];?>" alt="logo-parceiro" class="areaCardsImgParceiro">
                                    </div>

                                </div>
                                <h3 class="areaCardsNomeParceiro">
                                    <?= $parceiro['nome']; ?>
                                </h3>
                                <span class="areaCardsAreaParceiro">
                                    <?= $parceiro['area_nome']; ?>
                                </span>
                            </div>
                            <div class="areaCardsAreaMedia">
                                <a href="<?= $parceiro['linkedin']; ?>">
                                    <i class="fa-brands fa-linkedin-in"></i>
                                </a>
                                <a href="mailto:<?= $parceiro['email_representante']; ?>">
                                    <i class="fa-solid fa-envelope"></i>
                                </a>
                                <a href="<?= $parceiro['instagram']; ?>">
                                    <i class="fa-brands fa-instagram"></i>
                                </a>
                            </div>
                        </div>
                    <?php endif;?>
                <?php endforeach;?>
            </div>
        
        <div class="areaCardsVisualizacao">
        <button class="btnDefault" onclick="window.location='<?=BASE_URL?>parceiros/index'" ><i class="fa-solid fa-arrow-right"></i>VOLTAR PARA LISTA</button>
        </div>

    </section>