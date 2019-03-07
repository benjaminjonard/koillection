<?php

declare(strict_types=1);

namespace App\Form\Type\Model;

use App\Entity\User;
use App\Enum\DateFormatEnum;
use App\Form\DataTransformer\Base64ToMediumTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
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
     * @var Base64ToMediumTransformer
     */
    private $base64ToMediumTransformer;

    /**
     * ProfileType constructor.
     * @param Base64ToMediumTransformer $base64ToMediumTransformer
     */
    public function __construct(Base64ToMediumTransformer $base64ToMediumTransformer)
    {
        $this->base64ToMediumTransformer = $base64ToMediumTransformer;
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
                ])->addModelTransformer($this->base64ToMediumTransformer)
            )
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'label' => false,
                'required' => false,
            ])
            ->add('timezone', TimezoneType::class, [
                'required' => true
            ])
            ->add('dateFormat', ChoiceType::class, [
                'choices' => array_flip(DateFormatEnum::getLabels()),
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
