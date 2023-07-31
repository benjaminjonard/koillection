<?php

declare(strict_types=1);

namespace App\Form\Type\Model;

use App\Entity\Scraper;
use App\Model\Scraping;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScrapingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('url', UrlType::class, [
                'required' => true,
            ])
            ->add('entity', HiddenType::class, [
                'required' => true,
            ])
            ->add('scraper', EntityType::class, [
                'class' => Scraper::class,
                'choice_label' => 'name',
                'expanded' => false,
                'multiple' => false,
                'choice_name' => null,
                'required' => false,
            ])
            ->add('scrapName', CheckboxType::class, [
                'required' => false,
            ])
            ->add('scrapImage', CheckboxType::class, [
                'required' => false,
            ])
        ;

        $formModifier = function (FormInterface $form, Scraper $scraper = null): void {
            $choices = [];
            if ($scraper) {
                foreach ($scraper->getDataPaths() as $key => $choice) {
                    $choices[$choice['name']] = $choice['name'];
                }
            }

            $form->add('dataToScrap', ChoiceType::class, [
                'choices' => $choices,
                'expanded' => true,
                'multiple' => true,
                'required' => false,
            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier): void {
                $data = $event->getData();
                $formModifier($event->getForm(), $data->getScraper());
            }
        );

        $builder->get('scraper')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier): void {
                $scraper = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $scraper);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Scraping::class,
            'choices' => []
        ]);
    }
}
