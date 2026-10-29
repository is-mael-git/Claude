
# Padronização ParkTecCG
<table>
<tr><td>

<details>
<summary>


# 📋 Pastas, arquivos, classes, IDs, funções e variáveis.

</summary>

<table align="center">
<tr>
<td width="50%">

## 🔑 Legenda
| Símbolo | Significado |
|---|---|
| ✅ | Correto |
| ➖ | Quase correto |
| ❌ | Errado |

</td>
<td width="50%">

## 💡 Exemplos reais
| Nome da função | Tipo |
|---|---|
| Componente dos modais | Pasta |
| Area de trabalho do mentor | PHP |
| Body do menu lateral | class |

</td>
</tr>
</table>

---

## 📁 Nome de Pasta — PascalCase (UpperCamelCase)
> A primeira letra de todas as palavras é maiúscula.

| Exemplo | Status |
|---|---|
| ComponenteDosModais | ➖ |
| componenteDosModais | ❌ |
| componentedosmodais | ❌ |
| componente-dos-modais | ❌ |
| componente-Dos-Modais | ❌ |
| ComponenteModal | ✅ |
| componenteModal | ❌ |
| componente-modal | ❌ |
| componente-Modal | ❌ |

---

## 🏷️ Nome de classe, ID, função, variavel e arquivo — camelCase (LowerCamelCase)
> A primeira letra é minúscula. As demais palavras recebem inicial maiúscula.

| Exemplo | Status |
|---|---|
| BodyDoMenuLateral | ❌ |
| bodyDoMenuLateral | ➖ |
| bodydomenulateral | ❌ |
| body-do-menu-lateral | ❌ |
| body-Do-Menu-Lateral | ❌ |
| BodyMenuLateral | ❌ |
| bodyMenuLateral | ✅ |
| body-menu-lateral | ❌ |
| body-Menu-Lateral | ❌ |
| AreaDeTrabalhoDoMentor | ❌ |
| areaDeTrabalhoDoMentor | ❌ |
| areadetrabalhodomentor | ➖ |
| area-de-trabalho-do-mentor | ❌ |
| area-De-Trabalho-Do-Mentor | ❌ |
| AreaTrabalhoMentor | ❌ |
| areaTrabalhoMentor | ✅ |
| area-trabalho-mentor | ❌ |
| area-Trabalho-Mentor | ❌ |

</details>



</td></tr>
</table>




<table>
<tr><td>

<details>
<summary>

# 🔀 Commit e branchs

</summary>

## 🚀 Nomenclatura de commit
> Ao digitar -> git commit -m "exemplo"

| Tipo | Quando usar? | Exemplo |
|---|---|---|
| feat | Nova funcionalidade | feat: botão adiconar parceiro |
| fix | Arrumei alguma coisa | fix: sobreposição de menu |
| remove | Removi algo | remove: pasta PAGES |
| style | Modifiquei o estilo de algo | style: botão adicionar para azul |
| rename | Renomeou algo | rename: public para Public |
| initial commit | Primeiro commit do repositório | initial commit |
| refactor | Reorganizou/limpou o código sem alterar o comportamento | refactor: tela de login |
| perf | Melhorou a perfomance | perf: scroll da tabela |
| doc | Fez algo com documentação | doc: novo readme |
| test | Teste temporário | test: banco de dados |
| chore | Manutenção do código que não altera o funcionamento da aplicação | chore: alterei arquivo de configuração |

---

## 🔧 Nomenclatura de branchs
> Estrutura -> TIPO DE BRANCH + / + NUMERO DA TAREFA + - + DESCRICAO-DELA

<pre style="background-color: #fff3cd; color: #856404; padding: 15px; border-left: 5px solid #ffeeba; border-radius: 4px;">
⚠️ <b>ATENÇÃO:</b> Todas as letras são em <b>minúsculas</b>, separadas por <b>-</b> na numeração da tarefa e no nome dela.
Enquanto entre o tipo da tarefa e a numeração dela, é separada por <b>/</b>. Veja os exemplos abaixo
</pre>

| Tipo | Quando usar? | Exemplo |
|---|---|---|
| main | Função principal | main |
| feat | Funcionalidade nova | feat/32-tela-login |
| bugfix | Resolver algum problema, sem enviar direto na main | fix/67-tela-trabalho-mentor |
| hotfix | Resolver algum problema na versão principal | hotfix/57-adiconar-parceiro |
| release | Criar versão para teste internos | release/v1.2.0 |
| chore | Mexeu em apenas dependências ou documentação | chore/50-readme |
| perf | Melhorando a performance de algo | perf/23-cadastro-mentor |
| refactor | Organizou o código sem alterar a lógica dele | refactor/68-menu-lateral |
| style | Modificou o estilo de algo | style/21-botao-adiconar |
| test | Adicinando ou corrigindo teste | teste/100-fluxo-telas |

<pre style="background-color: #fff3cd; color: #856404; padding: 15px; border-left: 5px solid #ffeeba; border-radius: 4px;">
⚠️ <b>ATENÇÃO:</b> O número da tarefa é visto na # do github projects. No exemblo abaixo a tarefa tem o número 90.
Então ficaria <b>feat/90-crud-startups-banco</b>
</pre>
<img width="402" height="111" alt="image" src="https://github.com/user-attachments/assets/2ac81b9c-cf67-473d-9ef0-7fc5c966d066" />
 
</details>
</td></tr>
</table>

<table>
<tr><td>

<details>
<summary>

# 📱 Responsividade

</summary>

## 🔖 Tamanho de width
> Como o projeto é feito baseado primeiramente em desktop, deve-se ter como base o max, já que ele trabalha de 0 à o máximo imposto. O min será usado para telas maióres que o comum, já que ele vai do mínimo imposto e não determina um fim.

| Tipo | Tamanho max: width |
|---|---|
| mobile | 480px | 
| tablet | 768px | 
| desktop pequeno | 1024px |
 
</details>
</td></tr>
</table>

<table>
<tr><td>

<details>
<summary>

# 🔗 Referências

</summary>

## 🏗️ MVC
> https://github.com/rafavini/php-basic-boilerplate
## 🔀 Coventional branchs
> https://conventionalbranch.org/pt-br/
</details>
</td></tr>
</table>
