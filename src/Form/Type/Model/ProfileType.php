<?php

declare(strict_types=1);

namespace App\Form\Type\Model;

use App\Entity\User;
use App\Enum\CurrencyEnum;
use App\Enum\DateFormatEnum;
use App\Enum\LocaleEnum;
use App\Enum\ThemeEnum;
use App\Enum\VisibilityEnum;
use App\Form\DataTransformer\Base64ToImageTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
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
            ->add('timezone', TimezoneType::class, [
                'required' => true
            ])
            ->add('dateFormat', ChoiceType::class, [
                'choices' => DateFormatEnum::getChoicesList(),
                'required' => true
            ])
            ->add('currency', ChoiceType::class, [
                'choices' => array_flip(CurrencyEnum::getCurrencyLabels()),
                'required' => true
            ])
            ->add('theme', ChoiceType::class, [
                'choices' => array_flip(ThemeEnum::getThemeLabels()),
                'required' => true
            ])
            ->add('locale', ChoiceType::class, [
                'choices' => array_flip(LocaleEnum::getLocaleLabels()),
                'required' => true
            ])
            ->add('visibility', ChoiceType::class, [
                'choices' => array_flip(VisibilityEnum::getVisibilityLabels()),
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
