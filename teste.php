<?php
declare(strict_types=1);

require_once __DIR__ . '/src/Plano.php';
require_once __DIR__ . '/src/PlanoMensal.php';
require_once __DIR__ . '/src/PlanoTrimestral.php';
require_once __DIR__ . '/src/PlanoAnual.php';
require_once __DIR__ . '/src/Treino.php';
require_once __DIR__ . '/src/Aluno.php';
require_once __DIR__ . '/src/RepositorioAlunos.php';

$falhas = 0;

function titulo(string $texto): void
{
    echo "\n=== {$texto} ===\n";
}

function aceita(string $descricao, callable $acao): void
{
    global $falhas;
    try {
        $acao();
        echo "OK, aceito: {$descricao}\n";
    } catch (Exception $e) {
        $falhas++;
        echo "ERRO: {$descricao} foi recusado: {$e->getMessage()}\n";
    }
}

function recusa(string $descricao, callable $acao): void
{
    global $falhas;
    try {
        $acao();
        $falhas++;
        echo "ERRO: {$descricao} foi aceito\n";
    } catch (Exception $e) {
        echo "OK, recusado ({$descricao}): {$e->getMessage()}\n";
    }
}

// ---------------------------------------------------------------
titulo('1. Polimorfismo: cada plano calcula do seu jeito');

$planos = [new PlanoMensal(), new PlanoTrimestral(), new PlanoAnual()];
foreach ($planos as $plano) {
    printf(
        "%-10s | mensalidade R$ %7.2f | %2d mes(es) | matricula R$ %5.2f | total R$ %8.2f\n",
        $plano->getTipo(),
        $plano->calcularMensalidade(),
        $plano->getDuracaoMeses(),
        $plano->getTaxaMatricula(),
        $plano->calcularTotal()
    );
    echo "           " . $plano->descricao() . "\n";
}

// ---------------------------------------------------------------
titulo('2. Regras do plano');

recusa('valor base zero', fn() => new PlanoMensal(0.0));
recusa('tipo de plano inexistente', fn() => Plano::criarPorTipo('semestral'));
aceita('tipo "ANUAL" em maiusculas', fn() => Plano::criarPorTipo('ANUAL'));

// ---------------------------------------------------------------
titulo('3. Regras do aluno');

recusa('nome com 2 letras', fn() => new Aluno(1, 'Jo', 20, new PlanoMensal()));
recusa('idade menor que 16', fn() => new Aluno(1, 'Joao Silva', 15, new PlanoMensal()));
recusa('idade maior que 100', fn() => new Aluno(1, 'Joao Silva', 101, new PlanoMensal()));
aceita('aluno valido', fn() => new Aluno(1, 'Joao Silva', 20, new PlanoMensal()));

// ---------------------------------------------------------------
titulo('4. Regras do treino');

recusa('duracao de 5 minutos', fn() => new Treino('Treino A', 'seg', 5, 'Peito'));
recusa('duracao de 200 minutos', fn() => new Treino('Treino A', 'seg', 200, 'Peito'));
recusa('dia da semana invalido', fn() => new Treino('Treino A', 'xyz', 60, 'Peito'));
recusa('grupo muscular vazio', fn() => new Treino('Treino A', 'seg', 60, ' '));
aceita('treino valido', fn() => new Treino('Treino A', 'seg', 60, 'Peito'));

// ---------------------------------------------------------------
titulo('5. Composicao: aluno tem treinos');

$maria = new Aluno(2, 'Maria Souza', 28, new PlanoTrimestral());
$maria->adicionarTreino(new Treino('Treino A', 'seg', 60, 'Peito e triceps'));
$maria->adicionarTreino(new Treino('Treino B', 'qua', 45, 'Costas e biceps'));
$maria->adicionarTreino(new Treino('Treino C', 'sex', 50, 'Pernas'));
echo "Treinos de {$maria->getNome()}: " . count($maria->getTreinos()) . "\n";
echo "Minutos por semana: {$maria->calcularMinutosSemanais()} (esperado 155)\n";
if ($maria->calcularMinutosSemanais() !== 155) {
    $falhas++;
    echo "ERRO: soma de minutos incorreta\n";
}

recusa(
    'segundo treino na segunda-feira',
    fn() => $maria->adicionarTreino(new Treino('Treino D', 'seg', 30, 'Ombros'))
);

$carlos = new Aluno(3, 'Carlos Lima', 35, new PlanoAnual());
foreach (['seg', 'ter', 'qua', 'qui', 'sex', 'sab'] as $dia) {
    $carlos->adicionarTreino(new Treino("Treino {$dia}", $dia, 40, 'Geral'));
}
recusa(
    'setimo treino (limite de 6)',
    fn() => $carlos->adicionarTreino(new Treino('Treino dom', 'dom', 40, 'Geral'))
);

// ---------------------------------------------------------------
titulo('6. paraArray e JSON');

echo json_encode($maria->paraArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

// ---------------------------------------------------------------
titulo('7. Persistencia em arquivo JSON (bonus)');

$arquivo = sys_get_temp_dir() . '/academia_teste_' . uniqid() . '.json';
$repo = new RepositorioAlunos($arquivo);

aceita('salvar Maria no arquivo', fn() => $repo->adicionar($maria));
aceita('salvar Carlos no arquivo', fn() => $repo->adicionar($carlos));
recusa(
    'cadastrar outro aluno com o nome "maria souza"',
    fn() => $repo->adicionar(new Aluno(4, 'maria souza', 30, new PlanoMensal()))
);

$repo2 = new RepositorioAlunos($arquivo); // leitura "na proxima requisicao"
$lidos = $repo2->listar();
echo "Alunos lidos do arquivo: " . count($lidos) . " (esperado 2)\n";
$m = $repo2->buscar(2);
echo "Maria recarregada: {$m?->getNome()}, plano {$m?->getPlano()->getTipo()}, "
    . "{$m?->calcularMinutosSemanais()} min/semana\n";
if (count($lidos) !== 2 || $m === null || $m->calcularMinutosSemanais() !== 155) {
    $falhas++;
    echo "ERRO: a persistencia nao devolveu os mesmos dados\n";
}
echo "Proxima matricula: {$repo2->proximaMatricula()} (esperado 4)\n";

unlink($arquivo);

// ---------------------------------------------------------------
echo "\n" . ($falhas === 0 ? "TODOS OS TESTES PASSARAM\n" : "FALHAS: {$falhas}\n");
exit($falhas === 0 ? 0 : 1);