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
    /**
     * @var Base64ToImageTransformer
     */
    private Base64ToImageTransformer $base64ToImageTransformer;

    /**
     * ProfileType constructor.
     * @param Base64ToImageTransformer $base64ToImageTransformer
     */
    public function __construct(Base64ToImageTransformer $base64ToImageTransformer)
    {
        $this->base64ToImageTransformer = $base64ToImageTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                $builder->create('file', TextType::class, [
                    'required' => false,
                    'label' => false,
                ])->addModelTransformer($this->base64ToImageTransformer)
            )
            ->add('plainPassword', RepeatedType::class, [
                'type' => SymfonyPasswordType::class,
                'required' => false,
                'invalid_message'  => 'error.password.not_matching'
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}
