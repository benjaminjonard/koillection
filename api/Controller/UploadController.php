<?php

declare(strict_types=1);

namespace Api\Controller;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class UploadController extends AbstractController
{
    public function __invoke($data, Request $request)
    {
        switch ($request->get('_route')) {
            case 'api_data_file_item':
                $file = $request->files->get('fileFile');
                $data->setFileFile($file);
                break;
            case 'api_data_image_item':
                $file = $request->files->get('fileImage');
                $data->setFileImage($file);
                break;
            default:
                $file = $request->files->get('file');
                $data->setFile($file);
                break;
        }

        return $data;
    }
}
