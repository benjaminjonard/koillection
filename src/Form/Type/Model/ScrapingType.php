<?php

declare(strict_types=1);

namespace App\Form\Type\Model;

use App\Entity\Scraper;
use App\Model\Scraping;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
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
                'required' => true,
            ])
            ->add('scrapName', CheckboxType::class, [
                'required' => false,
            ])
            ->add('scrapImage', CheckboxType::class, [
                'required' => false,
            ])
            ->add('scrapData', CheckboxType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Scraping::class,
        ]);
    }
}
