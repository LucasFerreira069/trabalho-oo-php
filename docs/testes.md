# Testes

Saída real de `php teste.php`, seção por seção, com o que cada uma comprova.
Linhas `OK, aceito` mostram uma regra permitindo um dado válido; linhas `OK, recusado` mostram uma exceção sendo lançada e tratada com `try/catch`.

## Como cada seção é testada

1. **Polimorfismo:** um `foreach` percorre `PlanoMensal`, `PlanoTrimestral` e `PlanoAnual` chamando os mesmos métodos; cada classe responde com seus valores (R$ 170, R$ 349 e R$ 1.152 de total).
2. **Regras do plano:** valor base zero e tipo inexistente são recusados; o tipo em maiúsculas é aceito.
3. **Regras do aluno:** nome curto e idades fora de 16 a 100 são recusados.
4. **Regras do treino:** duração fora de 10 a 180, dia inválido e grupo muscular vazio são recusados.
5. **Composição:** a soma de minutos (60 + 45 + 50 = 155) comprova o cálculo sobre a lista; o segundo treino no mesmo dia e o sétimo treino são recusados.
6. **paraArray e JSON:** mostra a estrutura que a rota `?formato=json` devolve.
7. **Persistência (bônus):** grava em arquivo, lê com um repositório novo (como numa próxima requisição) e confirma que os dados voltam iguais; nome duplicado é recusado.

## Saída

```text

=== 1. Polimorfismo: cada plano calcula do seu jeito ===
mensal     | mensalidade R$  120.00 |  1 mes(es) | matricula R$ 50.00 | total R$   170.00
           Plano Mensal: R$ 120,00 por mês. Sem fidelidade, taxa de matrícula cheia.
trimestral | mensalidade R$  108.00 |  3 mes(es) | matricula R$ 25.00 | total R$   349.00
           Plano Trimestral: R$ 108,00 por mês. 3 meses com 10% de desconto e matrícula pela metade.
anual      | mensalidade R$   96.00 | 12 mes(es) | matricula R$  0.00 | total R$  1152.00
           Plano Anual: R$ 96,00 por mês. 12 meses com 20% de desconto e matrícula isenta.

=== 2. Regras do plano ===
OK, recusado (valor base zero): O valor base do plano deve ser maior que zero.
OK, recusado (tipo de plano inexistente): Tipo de plano inválido. Use mensal, trimestral ou anual.
OK, aceito: tipo "ANUAL" em maiusculas

=== 3. Regras do aluno ===
OK, recusado (nome com 2 letras): O nome do aluno deve ter pelo menos 3 caracteres.
OK, recusado (idade menor que 16): Menores de 16 anos não podem se matricular.
OK, recusado (idade maior que 100): Idade inválida: informe um valor até 100 anos.
OK, aceito: aluno valido

=== 4. Regras do treino ===
OK, recusado (duracao de 5 minutos): A duração do treino deve ficar entre 10 e 180 minutos.
OK, recusado (duracao de 200 minutos): A duração do treino deve ficar entre 10 e 180 minutos.
OK, recusado (dia da semana invalido): Dia da semana inválido. Use seg, ter, qua, qui, sex, sab ou dom.
OK, recusado (grupo muscular vazio): Informe o grupo muscular do treino.
OK, aceito: treino valido

=== 5. Composicao: aluno tem treinos ===
Treinos de Maria Souza: 3
Minutos por semana: 155 (esperado 155)
OK, recusado (segundo treino na segunda-feira): O aluno já tem um treino na Segunda.
OK, recusado (setimo treino (limite de 6)): Limite de 6 treinos por semana atingido.

=== 6. paraArray e JSON ===
{
    "matricula": 2,
    "nome": "Maria Souza",
    "idade": 28,
    "plano": {
        "tipo": "trimestral",
        "nome": "Plano Trimestral",
        "valor_base": 120,
        "mensalidade": 108,
        "duracao_meses": 3,
        "taxa_matricula": 25,
        "total": 349
    },
    "treinos": [
        {
            "nome": "Treino A",
            "dia_semana": "seg",
            "dia": "Segunda",
            "duracao_minutos": 60,
            "grupo_muscular": "Peito e triceps"
        },
        {
            "nome": "Treino B",
            "dia_semana": "qua",
            "dia": "Quarta",
            "duracao_minutos": 45,
            "grupo_muscular": "Costas e biceps"
        },
        {
            "nome": "Treino C",
            "dia_semana": "sex",
            "dia": "Sexta",
            "duracao_minutos": 50,
            "grupo_muscular": "Pernas"
        }
    ],
    "qtd_treinos": 3,
    "minutos_semanais": 155
}

=== 7. Persistencia em arquivo JSON (bonus) ===
OK, aceito: salvar Maria no arquivo
OK, aceito: salvar Carlos no arquivo
OK, recusado (cadastrar outro aluno com o nome "maria souza"): Já existe um aluno cadastrado com esse nome.
Alunos lidos do arquivo: 2 (esperado 2)
Maria recarregada: Maria Souza, plano trimestral, 155 min/semana
Proxima matricula: 4 (esperado 4)

TODOS OS TESTES PASSARAM
```