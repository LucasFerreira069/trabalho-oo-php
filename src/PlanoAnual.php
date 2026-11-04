<?php
declare(strict_types=1);

require_once __DIR__ . '/Plano.php';


class PlanoAnual extends Plano
{
    private const DESCONTO = 0.20;

    public function __construct(float $valorBase = 120.0)
    {
        parent::__construct('Plano Anual', $valorBase);
    }

    public function getTipo(): string
    {
        return 'anual';
    }

    public function getDuracaoMeses(): int
    {
        return 12;
    }

    public function getTaxaMatricula(): float
    {
        return 0.0;
    }

    public function calcularMensalidade(): float
    {
        return round($this->getValorBase() * (1 - self::DESCONTO), 2);
    }

    public function descricao(): string
    {
        return parent::descricao() . ' 12 meses com 20% de desconto e matrícula isenta.';
    }
}
