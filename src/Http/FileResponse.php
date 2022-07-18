<?php

declare(strict_types=1);

namespace App\Http;

use Symfony\Component\HttpFoundation\Response;

class FileResponse extends Response
{
    public function __construct(
        private readonly array $data = [],
        private readonly string $filename = 'koillection.txt',
        int $status = 200,
        array $headers = []
    ) {
        parent::__construct('', $status, $headers);
        $this->render();
    }

    private function render(): FileResponse
    {
        $output = fopen('php://temp', 'r+');

        foreach ($this->data as $row) {
            fwrite($output, $row);
        }

        rewind($output);
        $dataString = '';
        while ($line = fgets($output)) {
            $dataString .= $line;
        }

        $dataString .= fgets($output);

        $this->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $this->filename));

        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', 'text/plain');
        }

        return $this->setContent($dataString);
    }
}
