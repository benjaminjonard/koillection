<?php

declare(strict_types=1);

namespace App\Form\Type\Entity;

use App\Entity\Inventory;
use App\Form\DataTransformer\StringToInventoryContentTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InventoryType extends AbstractType
{
    private EntityManagerInterface $em;

    private StringToInventoryContentTransformer $stringToInventoryContentTransformer;

    public function __construct(EntityManagerInterface $em, StringToInventoryContentTransformer $stringToInventoryContentTransformer)
    {
        $this->em = $em;
        $this->stringToInventoryContentTransformer = $stringToInventoryContentTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['length' => 255],
                'required' => true,
            ])
            ->add(
                $builder->create('content', HiddenType::class, [
                    'required' => false,
                    'model_transformer' => $this->stringToInventoryContentTransformer
                ])
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Inventory::class
        ]);
    }
}
