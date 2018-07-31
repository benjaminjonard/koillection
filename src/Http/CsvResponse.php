<?php

namespace App\Http;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class CsvResponse
 * @package App\Http
 */
class CsvResponse extends Response
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    protected $filename;

    /**
     * CsvResponse constructor.
     * @param array $data
     * @param string $filename
     * @param int $status
     * @param array $headers
     */
    public function __construct(array $data = [], string $filename = 'export.csv', int $status = 200, array $headers = [])
    {
        parent::__construct('', $status, $headers);

        $this->data = $data;
        $this->filename = $filename;
        $this->render();
    }

    protected function render()
    {
        $output = fopen('php://temp', 'r+');

        foreach ($this->data as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $this->data = '';
        while ($line = fgets($output)) {
            $this->data .= $line;
        }

        $this->data .= fgets($output);

        $this->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $this->filename));

        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', 'text/csv');
        }

        return $this->setContent($this->data);
    }
}
