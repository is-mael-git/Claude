<?php
include_once BASE_MENU;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
   


   
   
   
   
   <section class="menuLateralAreaTrabalho areaCardsSection">     
            <div class="areaCardsContainerCards">
                <?php foreach ($cardStartup as $Startup): ?>
                    <?php if ($Startup['ativo']): ?>                   
                        <div class="areaCardsCardStartup">
                            <div class="areaCardsDadosStartup">
                                <div class="areaCardsStartupImg">
                                    <div class="areaCardsFoto"><img src="<?php echo BASE_UPLOAD.'Startup/'.$Startup['logo'];?>" alt="logo-Startup" class="areaCardsImgStartup">
                                    </div>
                                </div>
                                <h3 class="areaCardsNomeStartup">
                                    <?= $Startup['nome']; ?>
                                </h3>
                                <span class="areaCardsAreaStartup">
                                    <?= $Startup['setor_atuacao']; ?>
                                </span>
                            </div>
                            <div class="areaCardsAreaMedia">
                                <a href="<?= $Startup['linkedin']; ?>" target="_blank">
                                    <i class="fa-brands fa-linkedin-in"></i>
                                </a>
                                <a href="mailto:<?= $Startup['email_representante']; ?>">
                                    <i class="fa-solid fa-envelope"></i>
                                </a>
                                <a href="https://www.instagram.com/<?= $Startup['instagram']; ?>" target="_blank">
                                    <i class="fa-brands fa-instagram"></i>
                                </a>
                            </div>
                        </div>
                    <?php endif;?>
                <?php endforeach;?>
            </div>
        
        <div class="areaCardsVisualizacao">
        <button class="btnDefault" onclick="window.location='<?=BASE_URL?>startups/index'" ><i class="fa-solid fa-arrow-right"></i>VOLTAR PARA LISTA</button>
        </div>

    </section>