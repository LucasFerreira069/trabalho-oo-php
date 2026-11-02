<?php
declare(strict_types=1);

require_once __DIR__ . '/Plano.php';

/**
 * Plano mensal: sem desconto, 1 mês, taxa de matrícula cheia.
 */
class PlanoMensal extends Plano
{
    public function __construct(float $valorBase = 120.0)
    {
        parent::__construct('Plano Mensal', $valorBase);
    }

    public function getTipo(): string
    {
        return 'mensal';
    }

    public function descricao(): string
    {
        return parent::descricao() . ' Sem fidelidade, taxa de matrícula cheia.';
    }
}
