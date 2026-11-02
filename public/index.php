<?php
declare(strict_types=1);

// ===================================================================
// LÓGICA PHP (tudo aqui em cima; o HTML vem depois)
// ===================================================================

require_once __DIR__ . '/../src/Plano.php';
require_once __DIR__ . '/../src/PlanoMensal.php';
require_once __DIR__ . '/../src/PlanoTrimestral.php';
require_once __DIR__ . '/../src/PlanoAnual.php';
require_once __DIR__ . '/../src/Treino.php';
require_once __DIR__ . '/../src/Aluno.php';
require_once __DIR__ . '/../src/RepositorioAlunos.php';

const PAGINAS = ['inicio', 'alunos', 'aluno', 'novo', 'planos'];

/** Escapa qualquer dado exibido no HTML (W4). */
function h(string|int|float $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

/** Lê um campo de texto do POST de forma segura. */
function campoPost(string $campo): string
{
    $valor = $_POST[$campo] ?? '';
    return is_string($valor) ? trim($valor) : '';
}

function dinheiro(float $valor): string
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

$repo = new RepositorioAlunos(__DIR__ . '/../data/alunos.json');

$pagina = (isset($_GET['pagina']) && is_string($_GET['pagina'])) ? $_GET['pagina'] : 'inicio';
$formato = (isset($_GET['formato']) && is_string($_GET['formato'])) ? $_GET['formato'] : 'html';

$status = 200;
$erro = null;
$mensagem404 = 'Página não encontrada.';
$aluno = null;
$antigo = ['nome' => '', 'idade' => '', 'plano' => 'mensal'];
$antigoTreino = ['nome' => '', 'dia' => 'seg', 'duracao' => '', 'grupo' => ''];

// --- W1: página escolhida pela query string; 404 se não existir ---
if (!in_array($pagina, PAGINAS, true)) {
    $status = 404;
    $pagina = '404';
}

// --- 404 também para aluno inexistente ---
if ($pagina === 'aluno') {
    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    $aluno = ($id === false) ? null : $repo->buscar($id);
    if ($aluno === null) {
        $status = 404;
        $pagina = '404';
        $mensagem404 = 'Aluno não encontrado.';
    }
}

// --- W3: formulários POST que criam objetos ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $formato !== 'json') {
    if ($pagina === 'novo') {
        // Cria um Aluno
        $antigo = [
            'nome' => campoPost('nome'),
            'idade' => campoPost('idade'),
            'plano' => campoPost('plano'),
        ];
        try {
            $idade = filter_var($antigo['idade'], FILTER_VALIDATE_INT);
            if ($idade === false) {
                throw new Exception('Informe a idade com um número inteiro.');
            }
            $novo = new Aluno(
                $repo->proximaMatricula(),
                $antigo['nome'],
                $idade,
                Plano::criarPorTipo($antigo['plano'])
            );
            $repo->adicionar($novo);
            header('Location: index.php?pagina=aluno&id=' . $novo->getMatricula(), true, 303);
            exit;
        } catch (Exception $e) {
            $erro = $e->getMessage();
            $status = 400;
        }
    } elseif ($pagina === 'aluno' && $aluno !== null) {
        // Cria um Treino e adiciona ao aluno (composição)
        $antigoTreino = [
            'nome' => campoPost('nome'),
            'dia' => campoPost('dia'),
            'duracao' => campoPost('duracao'),
            'grupo' => campoPost('grupo'),
        ];
        try {
            $duracao = filter_var($antigoTreino['duracao'], FILTER_VALIDATE_INT);
            if ($duracao === false) {
                throw new Exception('Informe a duração em minutos com um número inteiro.');
            }
            $aluno->adicionarTreino(new Treino(
                $antigoTreino['nome'],
                $antigoTreino['dia'],
                $duracao,
                $antigoTreino['grupo']
            ));
            $repo->atualizar($aluno);
            header('Location: index.php?pagina=aluno&id=' . $aluno->getMatricula(), true, 303);
            exit;
        } catch (Exception $e) {
            $erro = $e->getMessage();
            $status = 400;
            $aluno = $repo->buscar($aluno->getMatricula()) ?? $aluno; // descarta o treino recusado
        }
    }
}

// --- Dados usados pelas páginas ---
$alunos = $repo->listar();
$planos = [new PlanoMensal(), new PlanoTrimestral(), new PlanoAnual()];

$receitaMensal = 0.0;
foreach ($alunos as $a) {
    $receitaMensal += $a->getPlano()->calcularMensalidade();
}

// --- W2: rota JSON ---
if ($formato === 'json') {
    $json = null;
    if ($pagina === 'inicio') {
        $json = [
            'sistema' => 'Academia',
            'total_alunos' => count($alunos),
            'receita_mensal_prevista' => round($receitaMensal, 2),
            'rotas' => [
                '?pagina=alunos&formato=json',
                '?pagina=aluno&id=1&formato=json',
                '?pagina=planos&formato=json',
            ],
        ];
    } elseif ($pagina === 'alunos') {
        $json = array_map(fn(Aluno $a): array => $a->paraArray(), $alunos);
    } elseif ($pagina === 'aluno' && $aluno !== null) {
        $json = $aluno->paraArray();
    } elseif ($pagina === 'planos') {
        $json = array_map(fn(Plano $p): array => $p->paraArray(), $planos);
    } else {
        if ($pagina === 'novo') {
            $mensagem404 = 'Esta página não tem formato JSON.';
        }
        $status = 404;
        $json = ['erro' => $mensagem404];
    }

    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code($status);

// ===================================================================
// HTML
// ===================================================================
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Academia Força Total</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
<header class="topo">
    <h1>Academia Força Total</h1>
    <nav>
        <a href="?pagina=inicio">Início</a>
        <a href="?pagina=alunos">Alunos</a>
        <a href="?pagina=novo">Novo aluno</a>
        <a href="?pagina=planos">Planos</a>
    </nav>
</header>

<main>
<?php if ($pagina === 'inicio'): ?>
    <h2>Bem-vindo</h2>
    <p>Sistema de gestão de alunos, planos e treinos da academia.</p>
    <div class="cartoes">
        <div class="cartao"><strong><?= h(count($alunos)) ?></strong><span>alunos matriculados</span></div>
        <div class="cartao"><strong><?= h(dinheiro($receitaMensal)) ?></strong><span>receita mensal prevista</span></div>
    </div>
    <p><a class="json" href="?pagina=inicio&amp;formato=json">Ver em JSON</a></p>

<?php elseif ($pagina === 'alunos'): ?>
    <h2>Alunos</h2>
    <?php if (count($alunos) === 0): ?>
        <p>Nenhum aluno cadastrado ainda. <a href="?pagina=novo">Cadastre o primeiro</a>.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr><th>Matrícula</th><th>Nome</th><th>Plano</th><th>Mensalidade</th><th>Treinos</th><th>Min/semana</th></tr>
            </thead>
            <tbody>
            <?php foreach ($alunos as $a): ?>
                <tr>
                    <td><?= h($a->getMatricula()) ?></td>
                    <td><a href="?pagina=aluno&amp;id=<?= h($a->getMatricula()) ?>"><?= h($a->getNome()) ?></a></td>
                    <td><?= h($a->getPlano()->getNome()) ?></td>
                    <td><?= h(dinheiro($a->getPlano()->calcularMensalidade())) ?></td>
                    <td><?= h(count($a->getTreinos())) ?></td>
                    <td><?= h($a->calcularMinutosSemanais()) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <p><a class="json" href="?pagina=alunos&amp;formato=json">Ver em JSON</a></p>

<?php elseif ($pagina === 'aluno' && $aluno !== null): ?>
    <h2><?= h($aluno->getNome()) ?></h2>
    <ul class="detalhes">
        <li><strong>Matrícula:</strong> <?= h($aluno->getMatricula()) ?></li>
        <li><strong>Idade:</strong> <?= h($aluno->getIdade()) ?> anos</li>
        <li><strong>Plano:</strong> <?= h($aluno->getPlano()->getNome()) ?> (<?= h(dinheiro($aluno->getPlano()->calcularMensalidade())) ?>/mês)</li>
        <li><strong>Total do contrato:</strong> <?= h(dinheiro($aluno->getPlano()->calcularTotal())) ?></li>
        <li><strong>Minutos de treino por semana:</strong> <?= h($aluno->calcularMinutosSemanais()) ?></li>
    </ul>

    <h3>Treinos da semana</h3>
    <?php if (count($aluno->getTreinos()) === 0): ?>
        <p>Este aluno ainda não tem treinos.</p>
    <?php else: ?>
        <table>
            <thead><tr><th>Dia</th><th>Treino</th><th>Grupo muscular</th><th>Duração</th></tr></thead>
            <tbody>
            <?php foreach ($aluno->getTreinos() as $t): ?>
                <tr>
                    <td><?= h($t->getNomeDia()) ?></td>
                    <td><?= h($t->getNome()) ?></td>
                    <td><?= h($t->getGrupoMuscular()) ?></td>
                    <td><?= h($t->getDuracaoMinutos()) ?> min</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h3>Adicionar treino</h3>
    <?php if ($erro !== null): ?>
        <p class="erro"><?= h($erro) ?></p>
    <?php endif; ?>
    <form method="post" action="?pagina=aluno&amp;id=<?= h($aluno->getMatricula()) ?>">
        <label>Nome do treino
            <input type="text" name="nome" value="<?= h($antigoTreino['nome']) ?>">
        </label>
        <label>Dia da semana
            <select name="dia">
                <?php foreach (Treino::diasDaSemana() as $sigla => $nomeDia): ?>
                    <option value="<?= h($sigla) ?>" <?= $antigoTreino['dia'] === $sigla ? 'selected' : '' ?>><?= h($nomeDia) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Duração (minutos)
            <input type="text" name="duracao" value="<?= h($antigoTreino['duracao']) ?>">
        </label>
        <label>Grupo muscular
            <input type="text" name="grupo" value="<?= h($antigoTreino['grupo']) ?>">
        </label>
        <button type="submit">Adicionar treino</button>
    </form>
    <p><a class="json" href="?pagina=aluno&amp;id=<?= h($aluno->getMatricula()) ?>&amp;formato=json">Ver em JSON</a></p>

<?php elseif ($pagina === 'novo'): ?>
    <h2>Novo aluno</h2>
    <?php if ($erro !== null): ?>
        <p class="erro"><?= h($erro) ?></p>
    <?php endif; ?>
    <form method="post" action="?pagina=novo">
        <label>Nome
            <input type="text" name="nome" value="<?= h($antigo['nome']) ?>">
        </label>
        <label>Idade
            <input type="text" name="idade" value="<?= h($antigo['idade']) ?>">
        </label>
        <label>Plano
            <select name="plano">
                <?php foreach ($planos as $p): ?>
                    <option value="<?= h($p->getTipo()) ?>" <?= $antigo['plano'] === $p->getTipo() ? 'selected' : '' ?>>
                        <?= h($p->getNome()) ?> - <?= h(dinheiro($p->calcularMensalidade())) ?>/mês
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Matricular</button>
    </form>

<?php elseif ($pagina === 'planos'): ?>
    <h2>Planos</h2>
    <p>Cada plano calcula a mensalidade, a matrícula e o total do seu jeito.</p>
    <table>
        <thead>
            <tr><th>Plano</th><th>Duração</th><th>Mensalidade</th><th>Matrícula</th><th>Total</th><th>Descrição</th></tr>
        </thead>
        <tbody>
        <?php foreach ($planos as $p): ?>
            <tr>
                <td><?= h($p->getNome()) ?></td>
                <td><?= h($p->getDuracaoMeses()) ?> mês(es)</td>
                <td><?= h(dinheiro($p->calcularMensalidade())) ?></td>
                <td><?= h(dinheiro($p->getTaxaMatricula())) ?></td>
                <td><?= h(dinheiro($p->calcularTotal())) ?></td>
                <td><?= h($p->descricao()) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <p><a class="json" href="?pagina=planos&amp;formato=json">Ver em JSON</a></p>

<?php else: ?>
    <h2>404</h2>
    <p class="erro"><?= h($mensagem404) ?></p>
    <p><a href="?pagina=inicio">Voltar ao início</a></p>
<?php endif; ?>
</main>

<footer>Programação Web I - IFCE Boa Viagem</footer>
</body>
</html>
