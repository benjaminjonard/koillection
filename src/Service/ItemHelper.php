<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Collection;
use App\Entity\Tag;
use App\Entity\Template;
use App\Enum\DatumTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class ItemHelper
 *
 * @package App\Service
 */
class ItemHelper
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * ItemHelper constructor.
     * @param EntityManagerInterface $em
     * @param \Twig_Environment $twig
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(EntityManagerInterface $em, \Twig_Environment $twig, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->twig = $twig;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Get options for from creation.
     *
     * @return mixed
     */
    public function getFormOptions()
    {
        $collections = $this->em->getRepository(Collection::class)->findAll();
        $templates = $this->em->getRepository(Template::class)->findAll();

        return ['tagRepository' => $this->em->getRepository(Tag::class), 'collections' => $collections, 'templates' => $templates];
    }

    /**
     * @param $data
     * @return array
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function formatData($data) : array
    {
        $fields = [];
        foreach ($data as $iteration => $datum) {
            $field = [];
            $field['html'] = $this->twig->render('App/Datum/'.DatumTypeEnum::getTypeSlug($datum->getType()).'.html.twig', [
                'iteration' => $iteration,
                'datum' => $datum,
            ]);
            $field['type'] = $datum->getType();
            $fields[] = $field;
        }

        return $fields;
    }
}
