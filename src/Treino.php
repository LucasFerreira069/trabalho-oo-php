<?php
declare(strict_types=1);

/**
 * Um treino da semana de um aluno (ex.: "Treino A - Peito", na segunda, 60 min).
 */
class Treino
{
    private const DIAS = [
        'seg' => 'Segunda',
        'ter' => 'Terça',
        'qua' => 'Quarta',
        'qui' => 'Quinta',
        'sex' => 'Sexta',
        'sab' => 'Sábado',
        'dom' => 'Domingo',
    ];

    private string $nome;
    private string $diaSemana;
    private int $duracaoMinutos;
    private string $grupoMuscular;

    public function __construct(
        string $nome,
        string $diaSemana,
        int $duracaoMinutos,
        string $grupoMuscular
    ) {
        if (strlen(trim($nome)) < 3) {
            throw new Exception('O nome do treino deve ter pelo menos 3 caracteres.');
        }
        if (!array_key_exists($diaSemana, self::DIAS)) {
            throw new Exception('Dia da semana inválido. Use seg, ter, qua, qui, sex, sab ou dom.');
        }
        if (trim($grupoMuscular) === '') {
            throw new Exception('Informe o grupo muscular do treino.');
        }
        $this->nome = trim($nome);
        $this->diaSemana = $diaSemana;
        $this->grupoMuscular = trim($grupoMuscular);
        $this->setDuracaoMinutos($duracaoMinutos);
    }

    /** @return array<string, string> */
    public static function diasDaSemana(): array
    {
        return self::DIAS;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getDiaSemana(): string
    {
        return $this->diaSemana;
    }

    public function getNomeDia(): string
    {
        return self::DIAS[$this->diaSemana];
    }

    public function getDuracaoMinutos(): int
    {
        return $this->duracaoMinutos;
    }

    public function setDuracaoMinutos(int $duracaoMinutos): void
    {
        if ($duracaoMinutos < 10 || $duracaoMinutos > 180) {
            throw new Exception('A duração do treino deve ficar entre 10 e 180 minutos.');
        }
        $this->duracaoMinutos = $duracaoMinutos;
    }

    public function getGrupoMuscular(): string
    {
        return $this->grupoMuscular;
    }

    /** @return array<string, mixed> */
    public function paraArray(): array
    {
        return [
            'nome' => $this->nome,
            'dia_semana' => $this->diaSemana,
            'dia' => $this->getNomeDia(),
            'duracao_minutos' => $this->duracaoMinutos,
            'grupo_muscular' => $this->grupoMuscular,
        ];
    }
}
