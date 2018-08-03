<?php

namespace App\Controller;

use App\Entity\Collection;
use App\Http\CsvResponse;
use App\Http\FileResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ToolsController
 *
 * @package App\Controller
 */
class ToolsController extends AbstractController
{
    /**
     * @Route("/tools", name="app_tools_index")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function index() : Response
    {
        return $this->render('App/Tools/index.html.twig', []);
    }

    /**
     * @Route("/tools/export/printable-list", name="app_tools_export_printable_list")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function exportPrintableList() : Response
    {
        $collections = $this->getDoctrine()->getRepository(Collection::class)->findAllWithItems();

        return $this->render('App/Tools/printable-list.html.twig', [
            'collections' => $collections,
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/tools/export/csv", name="app_tools_export_csv")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function exportCsv() : Response
    {
        $collections = $this->getDoctrine()->getRepository(Collection::class)->findAllWithItems();

        $rows = [];
        foreach ($collections as $collection) {
            foreach ($collection->getItems() as $item) {
                $rows[] = [$item->getId(), $item->getName(), $collection->getTitle()];
            }
        }

        return new CsvResponse($rows, (new \DateTime())->format('Ymd') . '-koillection-export.csv');
    }

    /**
     * @Route("/tools/export/sql", name="app_tools_export_csv")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function exportSql() : Response
    {
        $userId = $this->getUser()->getId();

        $selects = [
            //"SELECT * FROM doctrine_migration_version",
            "SELECT * FROM koi_user WHERE id = '$userId'",
            "SELECT * FROM koi_medium WHERE owner_id = '$userId'",
            "SELECT * FROM koi_log WHERE user_id = '$userId'",
            "SELECT * FROM koi_collection WHERE owner_id = '$userId'",
            "SELECT * FROM koi_item WHERE owner_id = '$userId'",
            "SELECT * FROM koi_datum WHERE owner_id = '$userId'",
            "SELECT * FROM koi_loan WHERE owner_id = '$userId'",
            "SELECT * FROM koi_tag WHERE owner_id = '$userId'",
            "SELECT * FROM koi_item_tag LEFT JOIN koi_item i ON item_id = i.id WHERE i.owner_id = '$userId'",
            "SELECT * FROM koi_template WHERE owner_id = '$userId'",
            "SELECT * FROM koi_field LEFT JOIN koi_template t ON template_id = t.id WHERE t.owner_id = '$userId'",
            "SELECT * FROM koi_wishlist WHERE owner_id = '$userId'",
            "SELECT * FROM koi_wish WHERE owner_id = '$userId'",
            "SELECT * FROM koi_album WHERE owner_id = '$userId'",
            "SELECT * FROM koi_photo WHERE owner_id = '$userId'",
        ];

        $rows = [];

        foreach ($selects as $select) {
            $stmt = $this->getDoctrine()->getConnection()->prepare($select);
            $stmt->execute();
            $results = $stmt->fetchAll();

            if (empty($results)) {
                continue;
            }

            $explodedSelect = explode(' ', $select);
            $tableName = $explodedSelect[3];
            $entityName = ucfirst(substr($tableName, 4));

            $metadata = null;
            if (class_exists("App\Entity\\$entityName")) {
                $metadata = $this->getDoctrine()->getManager()->getClassMetadata("App\Entity\\$entityName");
            }

            $headers = implode(',', array_keys($results[0]));
            $rows[] = "INSERT INTO $tableName ($headers) VALUES ".PHP_EOL;

            $count = \count($results);
            foreach ($results as $key => $result) {
                $values = [];
                foreach ($result as $property => $value) {
                    $value = str_replace(['\\', "'"], ['\\\\', "''"] , $value);
                    if (empty($value)) {
                        $value = 'NULL';
                    } else {
                        if ($metadata && $metadata->getTypeOfField($property) === 'boolean') {
                            $value = $value === true ? 'true' : 'false';
                        }

                        if ($metadata === null || \in_array($metadata->getTypeOfField($property), [null, 'string', 'datetime', 'uuid', 'array', 'text'], true)) {
                            $value = "'" . $value . "'";
                        }
                    }

                    $values[] = $value;
                }

                $content = '('.implode(',', $values) .')';
                $content .= $key === $count - 1 ? PHP_EOL : ','.PHP_EOL;
                $rows[] = $content;
            }

            $rows[] = ';'.PHP_EOL;
            $rows[] = PHP_EOL;
        }

        return new FileResponse($rows, (new \DateTime())->format('Ymd') . '-koillection-export.sql');
    }
}
