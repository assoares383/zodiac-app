<?php

declare(strict_types=1);

/**
 * Representa um signo carregado do arquivo XML.
 */
final readonly class SignoData
{
    public function __construct(
        public string $dataInicio,
        public string $dataFim,
        public string $nome,
        public string $descricao,
    ) {
    }

    /**
     * Retorna o limite no formato numérico MMDD.
     */
    public function getDataInicioMmdd(): int
    {
        return self::converterMmdd($this->dataInicio);
    }

    /**
     * Retorna o limite no formato numérico MMDD.
     */
    public function getDataFimMmdd(): int
    {
        return self::converterMmdd($this->dataFim);
    }

    /**
     * Converte uma data DD/MM para o valor numérico MMDD.
     */
    private static function converterMmdd(string $data): int
    {
        [$dia, $mes] = array_map('intval', explode('/', $data, 2));

        return ($mes * 100) + $dia;
    }
}

/**
 * Lê e valida os signos armazenados em XML.
 */
final class SignoXmlRepository
{
    private const QUANTIDADE_ESPERADA = 12;
    private const FORMATO_DATA = '~^(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[0-2])$~';

    public function __construct(
        private readonly string $caminhoXml = __DIR__ . '/data/signos.xml',
    ) {
    }

    /**
     * @return list<SignoData>
     *
     * @throws RuntimeException quando o arquivo ou sua estrutura são inválidos.
     */
    public function obterTodos(): array
    {
        if (!is_file($this->caminhoXml) || !is_readable($this->caminhoXml)) {
            throw new RuntimeException('O arquivo de signos não está disponível.');
        }

        $errosLibxml = libxml_use_internal_errors(true);

        try {
            $xml = simplexml_load_file($this->caminhoXml);
            $erros = libxml_get_errors();
            libxml_clear_errors();

            if ($xml === false || $erros !== []) {
                throw new RuntimeException('O arquivo de signos está malformado.');
            }

            $signos = $xml->signo;

            if (count($signos) !== self::QUANTIDADE_ESPERADA) {
                throw new RuntimeException('O arquivo de signos deve conter doze registros.');
            }

            $resultado = [];

            foreach ($signos as $signo) {
                $resultado[] = $this->criarSigno($signo);
            }

            return $resultado;
        } finally {
            libxml_use_internal_errors($errosLibxml);
        }
    }

    /**
     * @throws RuntimeException quando um registro XML é inválido.
     */
    private function criarSigno(SimpleXMLElement $signo): SignoData
    {
        $dataInicio = trim((string) $signo->dataInicio);
        $dataFim = trim((string) $signo->dataFim);
        $nome = trim((string) $signo->signoNome);
        $descricao = trim((string) $signo->descricao);

        if (
            $dataInicio === ''
            || $dataFim === ''
            || $nome === ''
            || $descricao === ''
        ) {
            throw new RuntimeException('O arquivo de signos possui campos obrigatórios ausentes.');
        }

        $this->validarData($dataInicio);
        $this->validarData($dataFim);

        return new SignoData($dataInicio, $dataFim, $nome, $descricao);
    }

    /**
     * @throws RuntimeException quando a data não está no formato DD/MM.
     */
    private function validarData(string $data): void
    {
        if (!preg_match(self::FORMATO_DATA, $data, $partes)) {
            throw new RuntimeException('O arquivo de signos possui uma data inválida.');
        }

        $dia = (int) $partes[1];
        $mes = (int) $partes[2];

        if (!checkdate($mes, $dia, 2000)) {
            throw new RuntimeException('O arquivo de signos possui uma data inválida.');
        }
    }
}
