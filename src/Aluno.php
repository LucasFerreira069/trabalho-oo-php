<?php
declare(strict_types=1);

require_once __DIR__ . '/Plano.php';
require_once __DIR__ . '/Treino.php';

class Aluno
{
    private const MAX_TREINOS = 6;

    private int $matricula;
    private string $nome;
    private int $idade;
    private Plano $plano;
    /** @var Treino[] */
    private array $treinos = [];

    public function __construct(int $matricula, string $nome, int $idade, Plano $plano)
    {
        if ($matricula < 1) {
            throw new Exception('A matrícula deve ser um número positivo.');
        }
        if (strlen(trim($nome)) < 3) {
            throw new Exception('O nome do aluno deve ter pelo menos 3 caracteres.');
        }
        if ($idade < 16) {
            throw new Exception('Menores de 16 anos não podem se matricular.');
        }
        if ($idade > 100) {
            throw new Exception('Idade inválida: informe um valor até 100 anos.');
        }
        $this->matricula = $matricula;
        $this->nome = trim($nome);
        $this->idade = $idade;
        $this->plano = $plano;
    }

    public function getMatricula(): int
    {
        return $this->matricula;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getIdade(): int
    {
        return $this->idade;
    }

    public function getPlano(): Plano
    {
        return $this->plano;
    }

    /** @return Treino[] */
    public function getTreinos(): array
    {
        return $this->treinos;
    }

    public function adicionarTreino(Treino $treino): void
    {
        if (count($this->treinos) >= self::MAX_TREINOS) {
            throw new Exception(
                'Limite de ' . self::MAX_TREINOS . ' treinos por semana atingido.'
            );
        }
        foreach ($this->treinos as $existente) {
            if ($existente->getDiaSemana() === $treino->getDiaSemana()) {
                throw new Exception(
                    'O aluno já tem um treino na ' . $treino->getNomeDia() . '.'
                );
            }
        }
        $this->treinos[] = $treino;
    }
    
    public function calcularMinutosSemanais(): int
    {
        $total = 0;
        foreach ($this->treinos as $treino) {
            $total += $treino->getDuracaoMinutos();
        }
        return $total;
    }

    /** @return array<string, mixed> */
    public function paraArray(): array
    {
        return [
            'matricula' => $this->matricula,
            'nome' => $this->nome,
            'idade' => $this->idade,
            'plano' => $this->plano->paraArray(),
            'treinos' => array_map(
                fn(Treino $t): array => $t->paraArray(),
                $this->treinos
            ),
            'qtd_treinos' => count($this->treinos),
            'minutos_semanais' => $this->calcularMinutosSemanais(),
        ];
    }

    /**
     * Reconstrói um aluno a partir do array salvo no arquivo JSON.
     *
     * @param array<string, mixed> $dados
     */
    public static function deArray(array $dados): Aluno
    {
        $aluno = new Aluno(
            (int) $dados['matricula'],
            (string) $dados['nome'],
            (int) $dados['idade'],
            Plano::criarPorTipo((string) $dados['plano']['tipo'])
        );
        foreach ($dados['treinos'] as $t) {
            $aluno->adicionarTreino(new Treino(
                (string) $t['nome'],
                (string) $t['dia_semana'],
                (int) $t['duracao_minutos'],
                (string) $t['grupo_muscular']
            ));
        }
        return $aluno;
    }
}
