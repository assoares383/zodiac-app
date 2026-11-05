<?php

declare(strict_types=1);

require_once __DIR__ . '/SignoXmlRepository.php';

/**
 * Identifica signos a partir de datas de nascimento.
 */
final class SignoService
{
    private const FORMATO_DATA = '!Y-m-d';
    private const FUSO_HORARIO = 'America/Sao_Paulo';

    public function __construct(
        private readonly SignoXmlRepository $repositorio,
        ?DateTimeImmutable $dataAtual = null,
    ) {
        $this->dataAtual = ($dataAtual ?? new DateTimeImmutable('now', new DateTimeZone(self::FUSO_HORARIO)))
            ->setTimezone(new DateTimeZone(self::FUSO_HORARIO))
            ->setTime(0, 0);
    }

    private readonly DateTimeImmutable $dataAtual;

    /**
     * Identifica o signo correspondente à data informada.
     *
     * @throws InvalidArgumentException quando a data é ausente, inválida ou futura.
     * @throws RuntimeException quando nenhum intervalo corresponde à data.
     */
    public function identificar(string $dataNascimento): SignoData
    {
        $data = $this->validarDataNascimento($dataNascimento);
        $dataMmdd = ((int) $data->format('m') * 100) + (int) $data->format('d');

        foreach ($this->repositorio->obterTodos() as $signo) {
            if ($this->estaNoIntervalo($dataMmdd, $signo)) {
                return $signo;
            }
        }

        throw new RuntimeException('Nenhum signo corresponde à data informada.');
    }

    /**
     * @throws InvalidArgumentException quando a data não é válida.
     */
    private function validarDataNascimento(string $valor): DateTimeImmutable
    {
        $valor = trim($valor);

        if ($valor === '') {
            throw new InvalidArgumentException('A data de nascimento é obrigatória.');
        }

        $data = DateTimeImmutable::createFromFormat(
            self::FORMATO_DATA,
            $valor,
            new DateTimeZone(self::FUSO_HORARIO),
        );
        $erros = DateTimeImmutable::getLastErrors();

        if (
            $data === false
            || ($erros !== false && ($erros['warning_count'] > 0 || $erros['error_count'] > 0))
            || $data->format('Y-m-d') !== $valor
        ) {
            throw new InvalidArgumentException('A data de nascimento informada é inválida.');
        }

        if ($data > $this->dataAtual) {
            throw new InvalidArgumentException('A data de nascimento não pode ser futura.');
        }

        return $data;
    }

    private function estaNoIntervalo(int $dataMmdd, SignoData $signo): bool
    {
        $inicio = $signo->getDataInicioMmdd();
        $fim = $signo->getDataFimMmdd();

        if ($inicio <= $fim) {
            return $dataMmdd >= $inicio && $dataMmdd <= $fim;
        }

        return $dataMmdd >= $inicio || $dataMmdd <= $fim;
    }
}
