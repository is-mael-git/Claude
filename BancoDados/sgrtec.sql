DROP DATABASE sgrtec;

CREATE DATABASE sgrtec;

USE sgrtec;

CREATE TABLE IF NOT EXISTS `perfil` (
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`nome` VARCHAR(50) NOT NULL UNIQUE,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `usuario` (
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`nome` VARCHAR(100) NOT NULL,
	`cpf` CHAR(11),
	`email` VARCHAR(320) NOT NULL UNIQUE,
	`senha` VARCHAR(255),
	`ativo` BIT NOT NULL DEFAULT 0,
	`criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`id_perfil` INTEGER NOT NULL,
	`telefone` BIGINT,
	`foto` VARCHAR(255),
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `permissao` (
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`nome` VARCHAR(50) NOT NULL,
	`descricao` VARCHAR(255) NOT NULL,
	`ativa` BIT NOT NULL DEFAULT 1,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `perfil_permissao` (
	`id_perfil` INTEGER NOT NULL,
	`id_permissao` INTEGER NOT NULL,
	PRIMARY KEY(`id_perfil`, `id_permissao`)
);


CREATE TABLE IF NOT EXISTS `termo` (
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`nome` VARCHAR(50) NOT NULL UNIQUE,
	`local` VARCHAR(255) NOT NULL UNIQUE,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `usuario_termo` (
	`id_usuario` INTEGER NOT NULL,
	`id_termo` INTEGER NOT NULL,
	`aceito` BIT NOT NULL DEFAULT 1,
	PRIMARY KEY(`id_usuario`, `id_termo`)
);


CREATE TABLE IF NOT EXISTS `area_atuacao` (
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`nome` VARCHAR(50) NOT NULL,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `parceiro` (
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`id_area_atuacao` INTEGER NOT NULL,
	`foto` VARCHAR(255) NOT NULL,
	`nome` VARCHAR(100) NOT NULL UNIQUE,
	`cnpj` VARCHAR(14) NOT NULL UNIQUE,
	`telefone` BIGINT NOT NULL,
	`site` VARCHAR(255),
	`instagram` VARCHAR(255),
	`linkedin` VARCHAR(255),
	`ativo` BIT DEFAULT 1,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `mentor` (
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`linkedin` VARCHAR(255) UNIQUE,
	`instagram` VARCHAR(255) UNIQUE,
	`biografia` VARCHAR(500) NOT NULL,
	`banner` VARCHAR(255),
	`id_parceiro` INTEGER NOT NULL,
	`id_usuario` INTEGER NOT NULL,
	`id_disponibilidade` INTEGER NOT NULL,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `competencia` (
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`nome` VARCHAR(50) NOT NULL UNIQUE,
	`descricao` VARCHAR(500) NOT NULL,
	`id_eixo` INTEGER NOT NULL,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `mentor_competencia` (
	`id_mentor` INTEGER NOT NULL,
	`id_competencia` INTEGER NOT NULL,
	PRIMARY KEY(`id_mentor`, `id_competencia`)
);


CREATE TABLE IF NOT EXISTS `semana` (
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`domingo` BIT NOT NULL DEFAULT 0,
	`segunda` BIT NOT NULL DEFAULT 0,
	`terca` BIT NOT NULL DEFAULT 0,
	`quarta` BIT NOT NULL DEFAULT 0,
	`quinta` BIT NOT NULL DEFAULT 0,
	`sexta` BIT NOT NULL DEFAULT 0,
	`sabado` BIT NOT NULL DEFAULT 0,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `horario` (
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`matutino` BIT NOT NULL DEFAULT 0,
	`vespertino` BIT NOT NULL DEFAULT 0,
	`noturno` BIT NOT NULL DEFAULT 0,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `startup` (
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`nome` VARCHAR(100) NOT NULL UNIQUE,
	`cnpj` VARCHAR(14) NOT NULL UNIQUE,
	`data_fundacao` DATE NOT NULL,
	`trl` INTEGER NOT NULL DEFAULT 1,
	`setor_atuacao` VARCHAR(150) NOT NULL,
	`site` VARCHAR(255),
	`instagram` VARCHAR(255),
	`linkedin` VARCHAR(255),
	`ativo` BIT NOT NULL DEFAULT 1,
	`criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`cep` VARCHAR(8),
	`logradouro` VARCHAR(255) NOT NULL,
	`numero` INTEGER NOT NULL DEFAULT 0,
	`complemento` VARCHAR(255),
	`bairro` VARCHAR(255) NOT NULL,
	`uf` CHAR(2) NOT NULL,
	`cidade` VARCHAR(255) NOT NULL,
	`logo` VARCHAR(255),
	`outras_participacoes` VARCHAR(255),
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `fundador` (
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`id_startup` INTEGER NOT NULL,
	`id_usuario` INTEGER NOT NULL,
	`funcao` VARCHAR(100) NOT NULL,
	`temp_empresa` DATE NOT NULL,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `representante_parceiro` (
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`id_parceiro` INTEGER NOT NULL,
	`nome` VARCHAR(100) NOT NULL,
	`email` VARCHAR(320) NOT NULL UNIQUE,
	`telefone` VARCHAR(20) NOT NULL,
	`cargo` VARCHAR(50) NOT NULL,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `questao` (
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`pergunta` VARCHAR(255) NOT NULL,
	`id_eixo` INTEGER NOT NULL,
	`ativo` BIT NOT NULL DEFAULT 1,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `questionario` (
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`nome` VARCHAR(100) NOT NULL,
	`descricao` VARCHAR(500),
	`ativo` BIT NOT NULL DEFAULT 1,
	`padrao` BIT NOT NULL DEFAULT 0,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `questionario_startup` (
	`id_startup` INTEGER NOT NULL,
	`id_questionario` INTEGER NOT NULL,
	PRIMARY KEY(`id_startup`, `id_questionario`)
);


CREATE TABLE IF NOT EXISTS `eixo` (
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`nome` VARCHAR(50) NOT NULL UNIQUE,
	`descricao` VARCHAR(255) NOT NULL,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `questionario_questao` (
	`id_questao` INTEGER NOT NULL,
	`id_questionario` INTEGER NOT NULL,
	PRIMARY KEY(`id_questao`, `id_questionario`)
);


CREATE TABLE IF NOT EXISTS `notificacao` (
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`id_usuario` INTEGER NOT NULL,
	`mensagem` VARCHAR(255) NOT NULL,
	`enviado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`status` ENUM('entrada', 'arquivado', 'lixeira') NOT NULL DEFAULT 'entrada',
	`tipo` ENUM('cadastro', 'mentoria', 'relato', 'reuniao') NOT NULL,
	`id_tipo` INTEGER NOT NULL,
	`visualizado` BIT NOT NULL DEFAULT 0,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `mentoria` (
	`id` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`id_mentor` INTEGER NOT NULL,
	`id_startup` INTEGER NOT NULL,
	`inici_em` DATE NOT NULL,
	`aprovado` BIT NOT NULL DEFAULT 0,
	`status` BIT NOT NULL DEFAULT 0,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `reuniao` (
	`id` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`id_startup` INTEGER NOT NULL,
	`id_mentor` INTEGER NOT NULL,
	`assunto` VARCHAR(50) NOT NULL,
	`status` BIT NOT NULL DEFAULT 1,
	`inicio` DATETIME NOT NULL,
	`fim` DATETIME NOT NULL,
	`local` VARCHAR(255) NOT NULL,
	`observacao` VARCHAR(255),
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `respos_questao` (
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`id_startup` INTEGER NOT NULL,
	`id_questao` INTEGER NOT NULL,
	`valor` TINYINT NOT NULL,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `resul_quest_eixo` (
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`id_eixo` INTEGER NOT NULL,
	`id_startup` INTEGER NOT NULL,
	`id_questionario` INTEGER NOT NULL,
	`valor` SMALLINT NOT NULL,
	`data` DATE NOT NULL,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `equipe` (
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`id_usuario` INTEGER NOT NULL,
	`cargo` VARCHAR(50) NOT NULL,
	`id_disponibilidade` INTEGER NOT NULL,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `relato` (
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`destinatario` ENUM('mentor', 'startup') NOT NULL,
	`pergunta` JSON NOT NULL,
	`ativo` BIT NOT NULL DEFAULT 0,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `respos_relato` (
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`id_reuniao` INTEGER NOT NULL,
	`resposta` JSON NOT NULL,
	`enviado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`emissor` ENUM('mentor', 'startup') NOT NULL,
	`id_relato` INTEGER NOT NULL,
	`id_mentor` INTEGER,
	`id_startup` INTEGER,
	`comentario` VARCHAR(500),
	`visto` BIT NOT NULL DEFAULT 0,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `disponibilidade` (
	`id` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`id_semana` INTEGER NOT NULL,
	`id_horario` INTEGER NOT NULL,
	`modalidade` ENUM('PRESENCIAL', 'REMOTO', 'HIBRIDO') NOT NULL,
	PRIMARY KEY(`id`)
);


ALTER TABLE `usuario`
ADD FOREIGN KEY(`id_perfil`) REFERENCES `perfil`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `perfil_permissao`
ADD FOREIGN KEY(`id_perfil`) REFERENCES `perfil`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `perfil_permissao`
ADD FOREIGN KEY(`id_permissao`) REFERENCES `permissao`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `usuario_termo`
ADD FOREIGN KEY(`id_usuario`) REFERENCES `usuario`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `usuario_termo`
ADD FOREIGN KEY(`id_termo`) REFERENCES `termo`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `parceiro`
ADD FOREIGN KEY(`id_area_atuacao`) REFERENCES `area_atuacao`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `mentor`
ADD FOREIGN KEY(`id_parceiro`) REFERENCES `parceiro`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `mentor`
ADD FOREIGN KEY(`id_usuario`) REFERENCES `usuario`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `mentor_competencia`
ADD FOREIGN KEY(`id_mentor`) REFERENCES `mentor`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `mentor_competencia`
ADD FOREIGN KEY(`id_competencia`) REFERENCES `competencia`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `fundador`
ADD FOREIGN KEY(`id_startup`) REFERENCES `startup`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `representante_parceiro`
ADD FOREIGN KEY(`id_parceiro`) REFERENCES `parceiro`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `questionario_startup`
ADD FOREIGN KEY(`id_startup`) REFERENCES `startup`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `questionario_startup`
ADD FOREIGN KEY(`id_questionario`) REFERENCES `questionario`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `fundador`
ADD FOREIGN KEY(`id_usuario`) REFERENCES `usuario`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `respos_questao`
ADD FOREIGN KEY(`id_startup`) REFERENCES `startup`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `mentoria`
ADD FOREIGN KEY(`id_mentor`) REFERENCES `mentor`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `mentoria`
ADD FOREIGN KEY(`id_startup`) REFERENCES `startup`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `equipe`
ADD FOREIGN KEY(`id_usuario`) REFERENCES `usuario`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `questionario_questao`
ADD FOREIGN KEY(`id_questionario`) REFERENCES `questionario`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `notificacao`
ADD FOREIGN KEY(`id_usuario`) REFERENCES `usuario`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `reuniao`
ADD FOREIGN KEY(`id_startup`) REFERENCES `startup`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `reuniao`
ADD FOREIGN KEY(`id_mentor`) REFERENCES `mentor`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `resul_quest_eixo`
ADD FOREIGN KEY(`id_eixo`) REFERENCES `eixo`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `resul_quest_eixo`
ADD FOREIGN KEY(`id_startup`) REFERENCES `startup`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `resul_quest_eixo`
ADD FOREIGN KEY(`id_questionario`) REFERENCES `questionario`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `respos_questao`
ADD FOREIGN KEY(`id_questao`) REFERENCES `questao`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `questionario_questao`
ADD FOREIGN KEY(`id_questao`) REFERENCES `questao`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `questao`
ADD FOREIGN KEY(`id_eixo`) REFERENCES `eixo`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `respos_relato`
ADD FOREIGN KEY(`id_reuniao`) REFERENCES `reuniao`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `respos_relato`
ADD FOREIGN KEY(`id_relato`) REFERENCES `relato`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `disponibilidade`
ADD FOREIGN KEY(`id_semana`) REFERENCES `semana`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `disponibilidade`
ADD FOREIGN KEY(`id_horario`) REFERENCES `horario`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `mentor`
ADD FOREIGN KEY(`id_disponibilidade`) REFERENCES `disponibilidade`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `equipe`
ADD FOREIGN KEY(`id_disponibilidade`) REFERENCES `disponibilidade`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `competencia`
ADD FOREIGN KEY(`id_eixo`) REFERENCES `eixo`(`id`)
ON UPDATE NO ACTION ON DELETE NO ACTION;

-- Inserções das tabelas ------------------------------

-- area de atuação
INSERT INTO area_atuacao(nome) VALUES ("Comércio");
INSERT INTO area_atuacao(nome) VALUES ("Indústria");
INSERT INTO area_atuacao(nome) VALUES ("Serviço");

-- representanto do parceiro

INSERT INTO parceiro (id_area_atuacao, foto, nome, cnpj, telefone, site, instagram, linkedin) VALUES (1, "logoSenac.png", "Senac Hub Academy", "03644843000119", 6733126260, "https://ms.senac.br/", "https://www.instagram.com/senacbrasil/", "https://www.linkedin.com/company/senacms/");

-- representanto do parceiro

INSERT INTO representante_parceiro (id_parceiro, nome, email, telefone, cargo) VALUES (1,  "Luiz Nicolas Da Silva", "luiz@gmail.com", 6792421028, "Gerente");

-- perfil de acesso

INSERT INTO perfil (nome) VALUES ("master");
INSERT INTO perfil (nome) VALUES ("adm");
INSERT INTO perfil (nome) VALUES ("mentor");
INSERT INTO perfil (nome) VALUES ("fundador");

-- Usuários
INSERT INTO usuario (nome, cpf, email, senha, ativo, id_perfil, telefone) VALUES ('Renata', '10956362182','renata@gmail.com', '210000$25831e120611829ebfb27515be6ea8d2$c7f9a6e58b72288767ff82270915546bb3bc97003bcb9e26c96799c78f65e4cd', 1, 1, 67948574682);
-- vai ser master
INSERT INTO usuario (nome, cpf, email, senha, ativo, id_perfil, telefone) VALUES ('Carlos', '01928163721','carlos@gmail.com', '210000$25831e120611829ebfb27515be6ea8d2$c7f9a6e58b72288767ff82270915546bb3bc97003bcb9e26c96799c78f65e4cd', 1, 2, 67948572943);
-- vai ser adm
INSERT INTO usuario (nome, email, senha, ativo, id_perfil, telefone) VALUES ('Roberto', 'roberto@gmail.com', '210000$25831e120611829ebfb27515be6ea8d2$c7f9a6e58b72288767ff82270915546bb3bc97003bcb9e26c96799c78f65e4cd', 1, 3, 67917243212);
-- vai ser mentor
INSERT INTO usuario (nome, cpf, email, senha, ativo, id_perfil, telefone) VALUES ('Rafaela', '09045623103','rafaela@gmail.com', '210000$25831e120611829ebfb27515be6ea8d2$c7f9a6e58b72288767ff82270915546bb3bc97003bcb9e26c96799c78f65e4cd', 1, 4, 67089461785);
-- vai ser fundador

-- horario
INSERT INTO horario(matutino) VALUES(1);

-- semana
INSERT INTO semana(segunda) VALUES (1);

-- disponibilidade
INSERT INTO disponibilidade(id_semana, id_horario, modalidade) VALUES (1, 1, 'presencial');

-- mentor
INSERT INTO mentor (linkedin, instagram, biografia, banner, id_parceiro, id_usuario, id_disponibilidade) VALUES ('https://www.linkedin.com/in/italo-almeida-966b42354/', 'https://www.instagram.com/it_1luan/', 'Entuasiata da tecnologia, formado em engenheiro de software na  Harvard Uniersity ', 'nothing', 1, 3, 1);

-- startup
INSERT INTO startup(nome, cnpj, data_fundacao, trl, setor_atuacao, site, instagram, linkedin, cep,  logradouro, numero, complemento, bairro, uf, cidade, logo, outras_participacoes) VALUES('Nubank', 18236120000158, '2023-05-06', 1, 'financeiro', 'https://nubank.com.br/', 'https://www.instagram.com/nubank/', 'https://www.linkedin.com/company/nubank/', '05409000', 'Rua Capote Valente, 39, Pinheiros, São Paulo - SP, CEP 05409-000', 39, '9º ao 14º andares, andares 17 e 18, e conjuntos 31, 41, 51 e 61', 'Pinheiros ', 'SP', 'São Paulo', 'nothing', 'acelerações de IA');

-- fundador
INSERT INTO fundador(id_startup, id_usuario, funcao, temp_empresa) VALUES (1, 4, 'estagiário', '2024-01-01');

-- equipe
INSERT INTO equipe (id_usuario, cargo, id_disponibilidade) VALUES (2, 'Representante da ParkTec', 1);

-- eixo
INSERT INTO eixo (nome, descricao) VALUES ('Empreendedor', 'Foca no fundador. Avalia sua capacidade de liderança, visão, tomada de decisão, maturidade empreendedora e habilidade de conduzir o negócio. É onde medimos se o empreendedor tem preparo e mentalidade para fazer a startup avançar.');
INSERT INTO eixo (nome, descricao) VALUES ('Mercado', 'Analisa o quanto a startup entende o cliente e o mercado que quer atender. Aqui medimos validação de problema, proposta de valor, tração, vendas, posicionamento e estratégias de crescimento. É o eixo que mostra se existe mercado real para o negócio.');
INSERT INTO eixo (nome, descricao) VALUES ('Produto', 'Avalia o desenvolvimento da solução: tecnologia, protótipo, testes, TRL, usabilidade e capacidade técnica. Esse eixo mostra se o produto está evoluindo de forma consistente e se tem potencial de entrega em escala.');
INSERT INTO eixo (nome, descricao) VALUES ('Gestão', 'Examina a estrutura interna da startup: organização, processos, finanças, jurídico, métricas e capacidade de gestão. É o eixo da profissionalização — o quanto a startup está preparada para operar com eficiência e crescer de forma estruturada.');
INSERT INTO eixo (nome, descricao) VALUES ('Capital', 'Avalia a capacidade de captar recursos e se relacionar com investidores ou fontes de financiamento. Inclui pitch, valuation, governança, editais e estratégias de investimento. Mostra se a startup está pronta para atrair e usar capital de forma inteligente');

-- competencia
INSERT INTO competencia (nome, descricao, id_eixo) VALUES ('Pessoas & Cultura', 'Estruturação de Time e Cultura Organizacional / Mentalidade Empreendedora', 1);
INSERT INTO competencia (nome, descricao, id_eixo) VALUES ('Estratégia', 'Estruturação de Time e Cultura Organizacional / Mentalidade Empreendedora', 1);
INSERT INTO competencia (nome, descricao, id_eixo) VALUES ('Produto & Tecnologia', 'Tecnologia e Desenvolvimento de Produtos', 3);
INSERT INTO competencia (nome, descricao, id_eixo) VALUES ('Operações', 'Parte técnica – execução do produto', 3);
INSERT INTO competencia (nome, descricao, id_eixo) VALUES ('Finanças', 'Gestão Financeira e Captação de Recursos', 5);
INSERT INTO competencia (nome, descricao, id_eixo) VALUES ('Capital & Investimentos', 'Gestão Financeira e Captação de Recursos', 5);
INSERT INTO competencia (nome, descricao, id_eixo) VALUES ('Mercado & Clientes', 'Validação de Produtos', 2);
INSERT INTO competencia (nome, descricao, id_eixo) VALUES ('Marketing & Vendas', 'Análise de Mercado / Marketing e Growth', 2);
INSERT INTO competencia (nome, descricao, id_eixo) VALUES ('Networking & Conexões', 'Networking e Conexões Estratégicas', 2);
INSERT INTO competencia (nome, descricao, id_eixo) VALUES ('Jurídico & PI', 'Estratégia e Modelagem de Negócios / Regulação e Compliance', 4);

-- relação mentor competencia
INSERT INTO mentor_competencia(id_mentor, id_competencia) VALUES(1, 1);
INSERT INTO mentor_competencia(id_mentor, id_competencia) VALUES(1, 2);

-- notificação
INSERT INTO notificacao (id_usuario, mensagem, tipo, id_tipo) VALUES (1, 'Startup e Mentor estão em mentoria', 'mentoria', 1);
INSERT INTO notificacao (id_usuario, mensagem, tipo, id_tipo) VALUES (1, 'Mentor cadastrado', 'cadastro', 1);
INSERT INTO notificacao (id_usuario, mensagem, tipo, id_tipo) VALUES (1, 'Startup X enviou o relato para avaliação', 'relato', 1);
INSERT INTO notificacao (id_usuario, mensagem, tipo, id_tipo) VALUES (1, 'Startup e Mentor agendaram a reunião', 'reuniao', 1);

-- questionario 
INSERT INTO questionario (nome, descricao, ativo, padrao) VALUES ('Questionário sobre a startup', 'Teste de descricão', 1, 0);
INSERT INTO questionario (nome, descricao, ativo, padrao) VALUES ('Questionário sobre a startup 2.0',  NULL, 1, 1);
INSERT INTO questionario (nome, descricao, ativo, padrao) VALUES ('Questionário sobre a startup Y', 'Teste de descricão', 0, 0);

-- questao
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa possui funções e responsabilidades bem definidas entre os membros da equipe?', 1, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa possui uma cultura organizacional clara e compartilhada pela equipe?', 1, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a equipe está alinhada com os objetivos e valores da empresa?', 1, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa incentiva o desenvolvimento e a capacitação dos membros da equipe?', 1, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a equipe demonstra mentalidade empreendedora na resolução de problemas e busca por oportunidades?', 1, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa possui objetivos estratégicos claramente definidos?', 1, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa possui um modelo de negócio bem estruturado e compreendido pela equipe?', 1, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto as decisões da empresa são tomadas com base em planejamento e informações do negócio?', 1, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa acompanha o cumprimento de suas metas estratégicas?', 1, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa consegue adaptar sua estratégia diante de mudanças e novas oportunidades?', 1, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto o produto ou serviço desenvolvido pela empresa atende às necessidades dos usuários?', 3, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa utiliza tecnologia de forma adequada no desenvolvimento de seus produtos ou serviços?', 3, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa realiza melhorias contínuas em seu produto com base nos feedbacks dos usuários?', 3, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto o produto da empresa apresenta diferenciais tecnológicos ou inovadores?', 3, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a tecnologia utilizada pela empresa está preparada para acompanhar o crescimento do negócio?', 3, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa possui processos operacionais claramente definidos?', 3, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa consegue executar e entregar seus produtos ou serviços dentro dos prazos estabelecidos?', 3, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa monitora a qualidade durante a execução de seus produtos ou serviços?', 3, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto os processos internos da empresa são eficientes e evitam retrabalho?', 3, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a operação da empresa está preparada para atender um aumento na demanda?', 3, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa possui controle organizado de receitas, despesas e fluxo de caixa?', 5, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa possui planejamento financeiro para curto, médio e longo prazo?', 5, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa conhece seus custos, despesas e margem de lucro?', 5, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa utiliza informações financeiras para apoiar suas decisões?', 5, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa está preparada financeiramente para manter suas operações e financiar seu crescimento?', 5, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa conhece suas necessidades de capital para continuar crescendo?', 5, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa conhece as diferentes formas de captação de recursos disponíveis?', 5, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa está preparada para apresentar seu negócio a possíveis investidores?', 5, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa possui projeções financeiras que demonstrem seu potencial de crescimento?', 5, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa possui uma estratégia definida para utilização de possíveis investimentos recebidos?', 5, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa conhece claramente o perfil e as necessidades do seu público-alvo?', 2, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto o produto ou serviço da empresa foi validado com clientes reais?', 2, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa coleta e utiliza feedback dos clientes para melhorar sua solução?', 2, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa conhece seus concorrentes e as alternativas existentes no mercado?', 2, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa possui evidências de que existe demanda pelo produto ou serviço oferecido?', 2, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa possui uma estratégia de marketing claramente definida?', 2, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa utiliza canais adequados para alcançar seu público-alvo?', 2, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa possui um processo estruturado para transformar potenciais clientes em compradores?', 2, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa acompanha indicadores de marketing e vendas para avaliar seus resultados?', 2, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa utiliza estratégias para aumentar a aquisição e retenção de clientes?', 2, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa possui uma rede de contatos que contribui para o desenvolvimento do negócio?', 2, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa busca estabelecer parcerias estratégicas com outras organizações?', 2, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa participa de eventos, programas ou ambientes que possibilitam novas conexões?', 2, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto as conexões da empresa contribuem para gerar oportunidades comerciais ou estratégicas?', 2, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa mantém relacionamento ativo com parceiros, mentores e outros integrantes do ecossistema empreendedor?', 2, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa possui sua estrutura jurídica adequada ao modelo de negócio?', 4, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa conhece e cumpre as normas e regulamentações relacionadas à sua atividade?', 4, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa utiliza contratos adequados em suas relações com clientes, parceiros e fornecedores?', 4, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa protege adequadamente sua marca, tecnologia, produto ou propriedade intelectual?', 4, 1);
INSERT INTO questao (pergunta, id_eixo, ativo) VALUES ('Numa escala de 0-4, quanto a empresa possui conhecimento e práticas relacionadas à proteção de dados, compliance e obrigações legais?', 4, 1);

-- permissões -----------------

-- Gerenciar Relatos
INSERT INTO permissao(nome, descricao) VALUES ('cadastrar formulário de relatos', 'Criar novo formulário de relato');
INSERT INTO permissao(nome, descricao) VALUES ('visualizar todos os formulários de relato', 'Ver as perguntas no formulário de relato que serão respondidas');
INSERT INTO permissao(nome, descricao) VALUES ('atualizar formulário de relato', 'Editar formulário de relato existente e selecionar qual formulário todos os mentores e startups irão realizar');
INSERT INTO permissao(nome, descricao) VALUES ('deletar formulário de relato', 'Remover formulário de relato cadastrado no sistema');
INSERT INTO permissao(nome, descricao) VALUES ('ativar/inativar formulário de relato', 'Ativar ou inativar formulário de relato');
INSERT INTO permissao(nome, descricao) VALUES ('responder relato', 'Responder ao relato conforme o que está assignado');
INSERT INTO permissao(nome, descricao) VALUES ('visualizar todos relato respondidos', 'Visualizar todas as respostas dos relatos tanto do mentor, quanto da startup');
INSERT INTO permissao(nome, descricao) VALUES ('visualizar resposta do seu relato', 'Visualizar as respostas que fez do seu próprio relato');
INSERT INTO permissao(nome, descricao) VALUES ('observações no relato', 'Adicionar comentários no relato respondido da startup e do mentor'); 

-- Gerenciar Termos
INSERT INTO permissao(nome, descricao) VALUES ('cadastrar termos', 'Adicionar novos termos de uso');
INSERT INTO permissao(nome, descricao) VALUES ('visualizar todos termos', 'Visualizar todos os termos de uso cadastrados');
INSERT INTO permissao(nome, descricao) VALUES ('atualizar termos', 'Editar termos de uso');
INSERT INTO permissao(nome, descricao) VALUES ('deletar termos', 'Remover termos de uso');
INSERT INTO permissao(nome, descricao) VALUES ('ativar/inativar termos', 'Ativar ou inativar um termo a ser assinado');

-- Gerenciar Perfis de Acesso
INSERT INTO permissao(nome, descricao) VALUES ('cadastrar perfis de acesso', 'Adicionar novos perfis de acesso');
INSERT INTO permissao(nome, descricao) VALUES ('visualizar todos perfis de acesso', 'Visualizar todos os perfis de acesso cadastrados');
INSERT INTO permissao(nome, descricao) VALUES ('atualizar perfis de acesso', 'Editar perfis de acesso');
INSERT INTO permissao(nome, descricao) VALUES ('deletar perfis de acesso', 'Remover perfis de acesso');
INSERT INTO permissao(nome, descricao) VALUES ('ativar/inativar perfil de acesso', 'Ativar ou inativar um perfil de acesso');

-- Gerenciar Startups
INSERT INTO permissao(nome, descricao) VALUES ('cadastrar startups', 'Cadastrar novas startups no sistema');
INSERT INTO permissao(nome, descricao) VALUES ('visualizar todas startups', 'Visualizar todas as startups cadastradas no sistema');
INSERT INTO permissao(nome, descricao) VALUES ('atualizar startups', 'Editar informações de startups do sistema');
INSERT INTO permissao(nome, descricao) VALUES ('deletar startups', 'Remover startups do sistema');
INSERT INTO permissao(nome, descricao) VALUES ('ativar/inativar startups', 'Ativar ou inativar startups no sistema');

-- Gerenciar Mentores
INSERT INTO permissao(nome, descricao) VALUES ('cadastrar mentores', 'Cadastrar novos mentores no sistema');
INSERT INTO permissao(nome, descricao) VALUES ('visualizar todos mentores', 'Visualizar todos os mentores cadastrados no sistema');
INSERT INTO permissao(nome, descricao) VALUES ('atualizar mentores', 'Editar informações dos mentores do sistema');
INSERT INTO permissao(nome, descricao) VALUES ('deletar mentores', 'Remover mentores do sistema');
INSERT INTO permissao(nome, descricao) VALUES ('ativar/inativar mentores', 'Ativar ou inativar mentores no sistema');
INSERT INTO permissao(nome, descricao) VALUES ('aprovar/recusar cadastro novo mentor', 'Aprovar ou recusar um mentor que solicitou o cadastro no sistema');

-- Gerenciar Parceiros
INSERT INTO permissao(nome, descricao) VALUES ('cadastrar parceiros', 'Cadastrar novos parceiros no sistema');
INSERT INTO permissao(nome, descricao) VALUES ('visualizar todos parceiros', 'Visualizar todos os parceiros cadastrados no sistema');
INSERT INTO permissao(nome, descricao) VALUES ('atualizar parceiros', 'Editar informações dos parceiros do sistema');
INSERT INTO permissao(nome, descricao) VALUES ('deletar parceiros', 'Remover parceiros do sistema');
INSERT INTO permissao(nome, descricao) VALUES ('ativar/inativar parceiros', 'Ativar ou inativar parceiros no sistema');

-- Gerenciar Questionários
INSERT INTO permissao(nome, descricao) VALUES ('cadastrar questionários', 'Criar novos questionários');
INSERT INTO permissao(nome, descricao) VALUES ('visualizar todos questionários', 'Visualizar todos os questionários cadastrados');
INSERT INTO permissao(nome, descricao) VALUES ('atualizar questionários', 'Editar perguntas e configurações dos questionários');
INSERT INTO permissao(nome, descricao) VALUES ('deletar questionários', 'Remover questionários do sistema');
INSERT INTO permissao(nome, descricao) VALUES ('ativar/inativar questionários', 'Ativar ou inativar questionários para preenchimento');
INSERT INTO permissao(nome, descricao) VALUES ('responder questionário', 'Responder a questionários conforme o que está destinado ao período da startup');

-- Gerenciar Questões
INSERT INTO permissao(nome, descricao) VALUES ('cadastrar questões', 'Cadastrar novas questões');
INSERT INTO permissao(nome, descricao) VALUES ('visualizar todas questões', 'Visualizar todas as questões cadastradas');
INSERT INTO permissao(nome, descricao) VALUES ('atualizar questões', 'Editar enunciados, eixos cernes e configurações das questões');
INSERT INTO permissao(nome, descricao) VALUES ('deletar questões', 'Remover questões do sistema');
INSERT INTO permissao(nome, descricao) VALUES ('ativar/inativar questões', 'Ativar ou inativar questões para uso em questionários');

-- Gerenciar Equipe 
INSERT INTO permissao(nome, descricao) VALUES ('cadastrar equipe', 'Cadastrar novos membros na equipe');
INSERT INTO permissao(nome, descricao) VALUES ('visualizar toda equipe', 'Visualizar todos os membros da equipe');
INSERT INTO permissao(nome, descricao) VALUES ('atualizar equipe', 'Editar informações e cargos dos membros da equipe');
INSERT INTO permissao(nome, descricao) VALUES ('deletar equipe', 'Remover membros da equipe');
INSERT INTO permissao(nome, descricao) VALUES ('ativar/inativar equipe', 'Ativar ou inativar acesso de membros da equipe'); 

-- perfil permissao
INSERT INTO perfil_permissao (id_perfil, id_permissao) VALUES (1, 1);

-- termo
INSERT INTO termo (nome, local) VALUES ('Termos de Uso v1', '/docs/termos_v1.pdf');

-- usuario termo
INSERT INTO usuario_termo (id_usuario, id_termo, aceito) VALUES (1, 1, 1);

-- questionario startup
INSERT INTO questionario_startup (id_startup, id_questionario) VALUES (1, 1);

-- questionario questão
INSERT INTO questionario_questao (id_questao, id_questionario) VALUES (1, 1);

-- mentoria
INSERT INTO mentoria (id_mentor, id_startup, inici_em, aprovado, status) VALUES (1, 1, '2026-03-01', 1, 1);

-- reunião
INSERT INTO reuniao ( id_startup, id_mentor, assunto, status, inicio, fim, local, observacao) VALUES (1, 1, 'Alinhamento Estratégico', 1, '2026-03-10 14:00:00', '2026-03-10 15:00:00', 'Google Meet', 'Trazer relatório de métricas.');

-- resposta questão
INSERT INTO respos_questao (id_startup, id_questao, valor) VALUES (1, 1, 5);

-- resultado da questão por eixo
INSERT INTO resul_quest_eixo (id_eixo, id_startup, id_questionario, valor, data) VALUES (1, 1, 1, 85, '2026-03-01');

INSERT INTO relato (destinatario, pergunta, ativo) VALUES ('startup',
    '[
        {
            "numero": 1,
            "tipo": "textarea",
            "pergunta": "Como você descreveria a mentoria recebida? Conte com suas palavras como foi a experiência, destacando pontos positivos."
        },
        {
            "numero": 2,
            "tipo": "textarea",
            "pergunta": "O mentor atendeu suas principais necessidades e dúvidas?"
        },
        {
            "numero": 3,
            "tipo": "textarea",
            "pergunta": "Como avalia a postura e a comunicação do mentor?"
        },
        {
            "numero": 4,
            "tipo": "textarea",
            "pergunta": "Qual foi o principal aprendizado ou insight que você leva desta mentoria?"
        },
        {
            "numero": 5,
            "tipo": "textarea",
            "pergunta": "O que poderia ser melhorado nas próximas mentorias?"
        },
        {
            "numero": 6,
            "tipo": "nota",
            "pergunta": "Nota geral para o mentor (de 1 a 5)",
            "opcoes": [1, 2, 3, 4, 5]
        }
    ]',
    1
);
 
INSERT INTO relato (destinatario, pergunta, ativo) VALUES ('mentor',
    '[
        {
            "numero": 1,
            "pergunta": "Descreva brevemente qual foi o foco principal da sessão de hoje, considerando as dificuldades e necessidades identificadas pelo residente."
        },
        {
            "numero": 2,
            "pergunta": "Marque e comente os principais eixos e critérios abordados na mentoria.",
            "eixos": {
                "Empreendedor": {
                    "criterios": [
                        "Estruturação de Time e Cultura Organizacional",
                        "Mentalidade Empreendedora"
                    ],
                    "comentarios": ""
                },
                "Tecnologia": {
                    "criterios": [
                        "Tecnologia e Desenvolvimento de Produtos"
                    ],
                    "comentarios": ""
                },
                "Capital": {
                    "criterios": [
                        "Gestão Financeira e Captação de Recursos"
                    ],
                    "comentarios": ""
                },
                "Mercado": {
                    "criterios": [
                        "Validação de Produtos",
                        "Análise de Mercado",
                        "Marketing e Growth Hacking",
                        "Networking e Conexões Estratégicas"
                    ],
                    "comentarios": ""
                },
                "Gestão": {
                    "criterios": [
                        "Estratégia e Modelagem de Negócios",
                        "Regulação e Compliance"
                    ],
                    "comentarios": ""
                }
            }
        },
        {
            "numero": 3,
            "pergunta": "Liste as orientações práticas que foram repassadas ao residente e os próximos passos sugeridos."
        },
        {
            "numero": 4,
            "pergunta": "Anote o que ficou combinado como ação ou entrega até a próxima mentoria."
        },
        {
            "numero": 5,
            "pergunta": "Avalie o aproveitamento do residente na sessão."
        },
        {
            "numero": 6,
            "pergunta": "Espaço livre para comentários, percepções ou pontos relevantes não cobertos acima."
        }
    ]',
    1
);
 
-- respota relato
INSERT INTO respos_relato(id_reuniao, resposta, emissor,id_relato,id_mentor,id_startup,comentario,visto) VALUES( 1,
    '[
        {
            "numero": 1,
            "resposta": "A mentoria foi muito produtiva e proporcionou orientações práticas e relevantes para o momento atual da startup."
        },
        {
            "numero": 2,
            "resposta": "Sim, o mentor atendeu às principais necessidades e esclareceu as dúvidas apresentadas durante a reunião."
        },
        {
            "numero": 3,
            "resposta": "O mentor apresentou uma postura profissional, demonstrando domínio do assunto e mantendo uma comunicação clara e objetiva."
        },
        {
            "numero": 4,
            "resposta": "O principal aprendizado foi compreender melhor como priorizar as ações da startup e estruturar os próximos passos do negócio."
        },
        {
            "numero": 5,
            "resposta": "Seria interessante disponibilizar mais tempo para aprofundar alguns dos temas discutidos durante a mentoria."
        },
        {
            "numero": 6,
            "resposta": 5
        }
    ]',
    'startup', 1, 1, 1, NULL, 0);
 
INSERT INTO respos_relato(id_reuniao,resposta,emissor,id_relato,id_mentor,id_startup,comentario,visto) VALUES(1,
    '[
        {
            "numero": 1,
            "resposta": "A sessão teve como foco principal a análise do estágio atual da startup, identificando os principais desafios relacionados à estruturação do negócio, validação do produto e definição de estratégias para ampliar sua presença no mercado."
        },
        {
            "numero": 2,
            "resposta": {
                "Empreendedor": {
                    "criterios": [
                        "Estruturação de Time e Cultura Organizacional",
                        "Mentalidade Empreendedora"
                    ],
                    "comentarios": "Foi trabalhada a necessidade de fortalecer a visão estratégica do residente, melhorar a definição de prioridades e desenvolver uma estrutura organizacional mais adequada para o crescimento da startup."
                }
            }
        },
        {
            "numero": 3,
            "resposta": "Foi recomendado revisar a proposta de valor, realizar uma análise dos principais concorrentes, coletar feedback de potenciais clientes e priorizar as funcionalidades essenciais do produto. Também foi orientado acompanhar indicadores de mercado e financeiros para apoiar as próximas decisões."
        },
        {
            "numero": 4,
            "resposta": "O residente ficou responsável por realizar uma análise dos concorrentes, organizar os principais feedbacks recebidos dos clientes, revisar a proposta de valor e definir as prioridades de desenvolvimento do produto antes da próxima mentoria."
        },
        {
            "numero": 5,
            "resposta": "O residente apresentou bom aproveitamento da sessão, demonstrando interesse nas orientações, participação ativa nas discussões e disposição para aplicar os direcionamentos apresentados. Ainda é necessário avançar na organização das prioridades e na execução dos próximos passos."
        },
        {
            "numero": 6,
            "resposta": "A startup demonstra potencial de evolução, mas deve concentrar esforços na validação das hipóteses mais relevantes antes de ampliar investimentos em desenvolvimento e crescimento. Recomenda-se acompanhar a execução dos compromissos definidos na próxima sessão."
        }
    ]', 'mentor', 2, 1, 1, NULL, 0);

