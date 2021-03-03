<?php

declare(strict_types=1);

namespace App\Form\Type\Entity;

use App\Entity\Collection;
use App\Entity\Template;
use App\Enum\VisibilityEnum;
use App\Form\DataTransformer\Base64ToImageTransformer;
use App\Service\FeatureChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as SymfonyCollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionType extends AbstractType
{
    private EntityManagerInterface $em;

    private Base64ToImageTransformer $base64ToImageTransformer;

    private FeatureChecker $featureChecker;

    public function __construct(Base64ToImageTransformer $base64ToImageTransformer, EntityManagerInterface $em, FeatureChecker $featureChecker)
    {
        $this->base64ToImageTransformer = $base64ToImageTransformer;
        $this->em = $em;
        $this->featureChecker = $featureChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $builder->getData();

        $builder
            ->add('title', TextType::class, [
                'attr' => ['length' => 255],
                'required' => true,
            ])
            ->add('childrenTitle', TextType::class, [
                'attr' => ['length' => 255],
                'required' => false
            ])
            ->add('itemsTitle', TextType::class, [
                'attr' => ['length' => 255],
                'required' => false
            ])
            ->add('visibility', ChoiceType::class, [
                'choices' => \array_flip(VisibilityEnum::getVisibilityLabels()),
                'required' => true,
            ])
            ->add('parent', EntityType::class, [
                'class' => Collection::class,
                'choice_label' => 'title',
                'choices' => $this->em->getRepository(Collection::class)->findAllExcludingItself($entity),
                'expanded' => false,
                'multiple' => false,
                'choice_name' => null,
                'empty_data' => '',
                'required' => false,
            ])
            ->add('data', SymfonyCollectionType::class, [
                'entry_type' => DatumType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
            ->add(
                $builder->create('file', TextType::class, [
                    'required' => false,
                    'label' => false,
                    'model_transformer' => $this->base64ToImageTransformer
                ])
            )
        ;

        if ($this->featureChecker->isFeatureEnabled('templates')) {
            $builder->add('template', EntityType::class, [
                'class' => Template::class,
                'choice_label' => 'name',
                'choices' => $this->em->getRepository(Template::class)->findAll(),
                'expanded' => false,
                'multiple' => false,
                'choice_name' => null,
                'required' => false,
                'mapped' => false
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Collection::class
        ]);
    }
}
