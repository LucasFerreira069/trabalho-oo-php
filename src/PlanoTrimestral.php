<?php
declare(strict_types=1);

require_once __DIR__ . '/Plano.php';


class PlanoTrimestral extends Plano
{
    private const DESCONTO = 0.10;

    public function __construct(float $valorBase = 120.0)
    {
        parent::__construct('Plano Trimestral', $valorBase);
    }

    public function getTipo(): string
    {
        return 'trimestral';
    }

    public function getDuracaoMeses(): int
    {
        return 3;
    }

    public function getTaxaMatricula(): float
    {
        return 25.0;
    }

    public function calcularMensalidade(): float
    {
        return round($this->getValorBase() * (1 - self::DESCONTO), 2);
    }

    public function descricao(): string
    {
        return parent::descricao() . ' 3 meses com 10% de desconto e matrícula pela metade.';
    }
}
