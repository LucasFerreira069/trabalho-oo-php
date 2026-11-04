<?php
declare(strict_types=1);

class Plano
{
    private string $nome;
    private float $valorBase;

    public function __construct(string $nome, float $valorBase)
    {
        if (trim($nome) === '') {
            throw new Exception('O plano precisa ter um nome.');
        }
        if ($valorBase <= 0) {
            throw new Exception('O valor base do plano deve ser maior que zero.');
        }
        $this->nome = trim($nome);
        $this->valorBase = $valorBase;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getValorBase(): float
    {
        return $this->valorBase;
    }

    public function getTipo(): string
    {
        return 'basico';
    }

    public function getDuracaoMeses(): int
    {
        return 1;
    }

    public function getTaxaMatricula(): float
    {
        return 50.0;
    }

    public function calcularMensalidade(): float
    {
        return round($this->valorBase, 2);
    }

    public function calcularTotal(): float
    {
        $total = $this->calcularMensalidade() * $this->getDuracaoMeses()
            + $this->getTaxaMatricula();
        return round($total, 2);
    }

    public function descricao(): string
    {
        return sprintf(
            '%s: R$ %s por mês.',
            $this->nome,
            number_format($this->calcularMensalidade(), 2, ',', '.')
        );
    }

    /**
     * Cria o plano certo a partir do texto recebido (formulário ou arquivo).
     */
    public static function criarPorTipo(string $tipo): Plano
    {
        return match (strtolower(trim($tipo))) {
            'mensal' => new PlanoMensal(),
            'trimestral' => new PlanoTrimestral(),
            'anual' => new PlanoAnual(),
            default => throw new Exception(
                'Tipo de plano inválido. Use mensal, trimestral ou anual.'
            ),
        };
    }

    /** @return array<string, mixed> */
    public function paraArray(): array
    {
        return [
            'tipo' => $this->getTipo(),
            'nome' => $this->nome,
            'valor_base' => round($this->valorBase, 2),
            'mensalidade' => $this->calcularMensalidade(),
            'duracao_meses' => $this->getDuracaoMeses(),
            'taxa_matricula' => $this->getTaxaMatricula(),
            'total' => $this->calcularTotal(),
        ];
    }
}
