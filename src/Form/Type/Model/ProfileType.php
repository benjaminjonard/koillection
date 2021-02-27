<?php

declare(strict_types=1);

namespace App\Form\Type\Model;

use App\Entity\User;
use App\Form\DataTransformer\Base64ToImageTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType as SymfonyPasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    private Base64ToImageTransformer $base64ToImageTransformer;

    public function __construct(Base64ToImageTransformer $base64ToImageTransformer)
    {
        $this->base64ToImageTransformer = $base64ToImageTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                $builder->create('file', TextType::class, [
                    'required' => false,
                    'label' => false,
                    'model_transformer' => $this->base64ToImageTransformer
                ])
            )
            ->add('plainPassword', RepeatedType::class, [
                'type' => SymfonyPasswordType::class,
                'required' => false,
                'invalid_message'  => 'error.password.not_matching'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}
