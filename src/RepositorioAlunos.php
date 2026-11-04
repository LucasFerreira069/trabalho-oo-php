<?php
declare(strict_types=1);

require_once __DIR__ . '/Aluno.php';


class RepositorioAlunos
{
    private string $arquivo;

    public function __construct(string $arquivo)
    {
        $this->arquivo = $arquivo;
    }

    /** @return Aluno[] */
    public function listar(): array
    {
        if (!file_exists($this->arquivo)) {
            return [];
        }
        $conteudo = file_get_contents($this->arquivo);
        if ($conteudo === false || trim($conteudo) === '') {
            return [];
        }
        $dados = json_decode($conteudo, true);
        if (!is_array($dados)) {
            throw new Exception('O arquivo de dados está corrompido.');
        }
        return array_map(fn(array $d): Aluno => Aluno::deArray($d), $dados);
    }

    public function buscar(int $matricula): ?Aluno
    {
        foreach ($this->listar() as $aluno) {
            if ($aluno->getMatricula() === $matricula) {
                return $aluno;
            }
        }
        return null;
    }

    public function proximaMatricula(): int
    {
        $maior = 0;
        foreach ($this->listar() as $aluno) {
            $maior = max($maior, $aluno->getMatricula());
        }
        return $maior + 1;
    }

    public function adicionar(Aluno $novo): void
    {
        $alunos = $this->listar();
        foreach ($alunos as $aluno) {
            if (strtolower($aluno->getNome()) === strtolower($novo->getNome())) {
                throw new Exception('Já existe um aluno cadastrado com esse nome.');
            }
        }
        $alunos[] = $novo;
        $this->gravar($alunos);
    }

    public function atualizar(Aluno $alterado): void
    {
        $alunos = $this->listar();
        foreach ($alunos as $i => $aluno) {
            if ($aluno->getMatricula() === $alterado->getMatricula()) {
                $alunos[$i] = $alterado;
                $this->gravar($alunos);
                return;
            }
        }
        throw new Exception('Aluno não encontrado para atualizar.');
    }

    /** @param Aluno[] $alunos */
    private function gravar(array $alunos): void
    {
        $pasta = dirname($this->arquivo);
        if (!is_dir($pasta)) {
            mkdir($pasta, 0777, true);
        }
        $json = json_encode(
            array_map(fn(Aluno $a): array => $a->paraArray(), $alunos),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
        if ($json === false || file_put_contents($this->arquivo, $json, LOCK_EX) === false) {
            throw new Exception('Não foi possível gravar o arquivo de dados.');
        }
    }
}
