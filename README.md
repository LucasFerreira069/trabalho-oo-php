# Academia Força Total

> **Vídeo de apresentação:** https://youtu.be/6azZOgVSUsQ

Trabalho individual de Programação Web I (Tecnologia em ADS, IFCE Campus Boa Viagem).
Sistema web em PHP, orientado a objetos, para o tema **07 - Academia**: cadastro de alunos,
planos (mensal, trimestral e anual) e treinos da semana.

**Autor:** Lucas Ferreira da Silva Filho

## Como executar

Requer PHP 8.1 ou superior. Dentro da pasta do repositório:

```bash
php -S localhost:8000 -t public -d display_errors=1 -d output_buffering=0
```

Abra <http://localhost:8000>. Os alunos cadastrados são gravados em `data/alunos.json`
(criado automaticamente) e continuam lá na próxima requisição.

Roteiro de testes no terminal:

```bash
php teste.php
```

## Rotas

| Rota | O que faz |
|------|-----------|
| `?pagina=inicio` | Resumo: total de alunos e receita mensal prevista |
| `?pagina=alunos` | Lista de alunos |
| `?pagina=aluno&id=1` | Detalhes do aluno, seus treinos e formulário para adicionar treino |
| `?pagina=novo` | Formulário (POST) para matricular um aluno |
| `?pagina=planos` | Comparação dos três planos (polimorfismo) |
| `...&formato=json` | Mesma página em JSON (`inicio`, `alunos`, `aluno`, `planos`) |

Página ou aluno inexistente responde com status **404** (em HTML ou em JSON).
Dados recusados pelas regras de negócio respondem com status 400 e a mensagem da exceção aparece na página.

## Estrutura

```
src/        uma classe por arquivo (Plano, PlanoMensal, PlanoTrimestral, PlanoAnual, Treino, Aluno, RepositorioAlunos)
public/     index.php (páginas, formulários e JSON) e estilo.css
data/       alunos.json (gerado em execução, ignorado pelo Git)
docs/       modelagem.md, testes.md, llm.md, relatorio.pdf
teste.php   roteiro de testes no terminal
```

## Documentação

- [Modelagem e diagrama de classes](docs/modelagem.md)
- [Saídas dos testes](docs/testes.md)
- [Diário de uso de LLM](docs/llm.md)
- [Relatório (PDF)](docs/relatorio.pdf)