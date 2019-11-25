<?php

declare(strict_types=1);

namespace App\Form\Type\Entity;

use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\Template;
use App\Enum\VisibilityEnum;
use App\Form\DataTransformer\FileToMediumTransformer;
use App\Form\DataTransformer\JsonToTagTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as SymfonyCollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ItemType
 *
 * @package App\Form\Type\Entity
 */
class ItemType extends AbstractType
{
    /**
     * @var JsonToTagTransformer
     */
    private $jsonToTagTransformer;

    /**
     * @var FileToMediumTransformer
     */
    private $fileToMediumTransformer;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ItemType constructor.
     * @param JsonToTagTransformer $jsonToTagTransformer
     * @param FileToMediumTransformer $fileToMediumTransformer
     * @param EntityManagerInterface $em
     */
    public function __construct(JsonToTagTransformer $jsonToTagTransformer, FileToMediumTransformer $fileToMediumTransformer, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->jsonToTagTransformer = $jsonToTagTransformer;
        $this->fileToMediumTransformer = $fileToMediumTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['length' => 255],
                'required' => true,
            ])
            ->add('quantity', IntegerType::class, [
                'required' => true,
            ])
            ->add(
                $builder->create('image', FileType::class, [
                    'required' => false,
                    'label' => false,
                ])->addModelTransformer($this->fileToMediumTransformer)
            )
            ->add(
                $builder->create('tags', TextType::class, [
                    'required' => false,
                ])->addModelTransformer($this->jsonToTagTransformer)
            )
            ->add('collection', EntityType::class, [
                'class' => Collection::class,
                'choice_label' => 'title',
                'choices' => $this->em->getRepository(Collection::class)->findAll(),
                'expanded' => false,
                'multiple' => false,
                'choice_name' => null,
                'required' => true,
            ])
            ->add('template', EntityType::class, [
                'class' => Template::class,
                'choice_label' => 'name',
                'choices' => $this->em->getRepository(Template::class)->findAll(),
                'expanded' => false,
                'multiple' => false,
                'choice_name' => null,
                'required' => false,
            ])
            ->add('data', SymfonyCollectionType::class, [
                'entry_type' => DatumType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
            ->add('visibility', ChoiceType::class, [
                'choices' => \array_flip(VisibilityEnum::getVisibilityLabels()),
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Item::class
        ]);
    }
}
