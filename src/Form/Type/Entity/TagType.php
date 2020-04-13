<?php

declare(strict_types=1);

namespace App\Form\Type\Entity;

use App\Entity\Tag;
use App\Entity\TagCategory;
use App\Enum\VisibilityEnum;
use App\Form\Type\ImageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TagType
 *
 * @package App\Form\Type\Entity
 */
class TagType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * TagType constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, [
                'required' => true,
                'label' => false,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('visibility', ChoiceType::class, [
                'choices' => \array_flip(VisibilityEnum::getVisibilityLabels()),
                'required' => false,
            ])
            ->add('category', EntityType::class, [
                'class' => TagCategory::class,
                'choice_label' => 'label',
                'choices' => $this->em->getRepository(TagCategory::class)->findAll(),
                'expanded' => false,
                'multiple' => false,
                'choice_name' => null,
                'required' => false,
            ])
            ->add('image', ImageType::class, [
                'required' => false,
                'label' => false
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tag::class
        ]);
    }
}
