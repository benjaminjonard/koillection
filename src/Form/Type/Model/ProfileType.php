<?php

declare(strict_types=1);

namespace App\Form\Type\Model;

use App\Entity\User;
use App\Enum\DateFormatEnum;
use App\Form\DataTransformer\Base64ToImageTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ProfileType
 *
 * @package App\Form\Type\Model
 */
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
                $builder->create('image', TextType::class, [
                    'required' => false,
                    'label' => false,
                    'property_path' => 'avatar'
                ])->addModelTransformer($this->base64ToImageTransformer)
            )
            ->add('timezone', TimezoneType::class, [
                'required' => true
            ])
            ->add('dateFormat', ChoiceType::class, [
                'choices' => DateFormatEnum::getChoicesList(),
                'required' => true
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
