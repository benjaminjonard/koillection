<?php

declare(strict_types=1);

namespace App\Form\Type\Entity;

use App\Entity\Tag;
use App\Entity\TagCategory;
use App\Enum\VisibilityEnum;
use App\Repository\TagCategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType
{
    public function __construct(
        private readonly TagCategoryRepository $tagCategoryRepository
    ) {
    }

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
                'choices' => array_flip(VisibilityEnum::getVisibilityLabels()),
                'required' => true,
            ])
            ->add('itemsDisplayConfiguration', DisplayConfigurationType::class)
            ->add('category', EntityType::class, [
                'class' => TagCategory::class,
                'choice_label' => 'label',
                'choices' => $this->tagCategoryRepository->findAll(),
                'expanded' => false,
                'multiple' => false,
                'choice_name' => null,
                'required' => false,
            ])
            ->add('file', FileType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('deleteImage', CheckboxType::class, [
                'label' => false,
                'required' => false,
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT,
            static function (FormEvent $event): void {
                $data = $event->getData();
                if (!empty($data['deleteImage'])) {
                    $data['file'] = null;
                }

                $event->setData($data);
            }
        );
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
        ]);
    }
}
