# Diário de uso de LLM

**Ferramenta usada em todo o projeto:** Claude (Anthropic), em conversa única entre 02/11/2026 e 03/11/2026.

**Resumo de uso:** enviei o enunciado para o Claude e pedi para ele me explicar cada etapa para que eu possa entender melhor a proposta de projeto, então comecei o desenvolvimento do projeto e pedi também em quais partes eu devo commitar para deixar o projeto organizado. Na parte da estilização eu pedi para ele criar um css para deixar o projeto bonito.

---

### Interação 1
- **Ferramenta e data:** Claude, 02/11/2026
- **Prompt:** enviei o enunciado do projeto e pedi para ele me explicar detalhadamente para eu entender melhor a proposta de projeto.
- **Resumo da resposta:** o Claude me mandou o resumo e dando uma atenção para a parte de orientação a objetos como:
- pelo menos 5 classes com construtor.
- uma classe mãe com pelo menos 2 filhas.
- um foreach que percorre objetos de classes diferentes classes e chama o mesmo método.
- encapsulamento sem nenhum atributo public
- pelo menos 3 regras de negócio recusadas com throw new Exception e tratadas com try/catch
- um objeto que guarda uma lista de outros
- método paraArray() nas classes que aparecem no JSON.
- uma classe por arquivo em src/

- **Aceitei / modifiquei / rejeitei, e por quê:** 
Aceitei pois conseguir entender melhor a proposta do projeto e dei uma atenção a parte de POO.


### Interação 2
- **Ferramenta e data:** Claude, 02/11/2026
- **Prompt:** pedi para ele separar em quais partes fazer os commits até o fim do projeto.
- **Resumo da resposta:** ele separou em 7 commits desde os arquivos de planos até a parte da documentação.
- **Aceitei / modifiquei / rejeitei, e por quê:** 
Aceitei pois ficou bem separado os commits, porém fiz alguns ajustes nos commits como o texto e alguns commits juntos pois dava pra fazer determinadas tarefas em um commit só.

### Interação 3
- **Ferramenta e data:** Claude, 02/11/2026
- **Prompt:** mandei print do navegador com "Not Found" em localhost:8000/index.php.
- **Resumo da resposta:** o erro era esperado, pois public/index.php estava dentro de mais uma pasta. Ele orientou a dar cd .. e entrar dentro da pasta certa e rodar de novo.
- **Aceitei / modifiquei / rejeitei, e por quê:** 
Aceitei e entrei na pasta certa rodei de novo e funcionou.

### Interação 4
- **Ferramenta e data:** Claude, 02/11/2026
- **Prompt:** colei a saída do PowerShell com "Could not open input file: teste.php" e um aviso de mbstring.
- **Resumo da resposta:** o teste.php não estava na pasta do projeto, e o aviso do mbstring vem da instalação do PHP e pode ser ignorado.
- **Aceitei / modifiquei / rejeitei, e por quê:** aceitei o diagnóstico sobre o arquivo; ignorei o mbstring porque o projeto não usa essa extensão.

### Interação 5
- **Ferramenta e data:** Claude, 02/11/2026
- **Prompt:** pedi para ele criar um arquivo css para estilizar o projeto.
- **Resumo da resposta:** ele criou o arquivo css e me mandou.
- **Aceitei / modifiquei / rejeitei, e por quê:** Eu rejeitei o arquivo por que ficou uma estilização feia, não tinha nada haver com um sistema de academia e uma cor muito chamativa que chegou a doer a vista. alterei para cores mais suaves e mais minimalistas que combinasem com o tema do projeto.
