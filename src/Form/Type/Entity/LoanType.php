<?php

declare(strict_types=1);

namespace App\Form\Type\Entity;

use App\Entity\Loan;
use App\Service\DateFormatter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LoanType
 *
 * @package App\Form\Type\Entity
 */
class LoanType extends AbstractType
{
    /**
     * @var DateFormatter
     */
    private $dateFormatter;

    /**
     * LoanType constructor.
     * @param DateFormatter $dateFormatter
     */
    public function __construct(DateFormatter $dateFormatter)
    {
        $this->dateFormatter = $dateFormatter;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lentAt', DateType::class, [
                'required' => true,
                'html5' => false,
                'widget' => 'single_text',
                'format' => $this->dateFormatter->guessForForm(),
            ])
            ->add('lentTo', TextType::class, [
                'attr' => ['length' => 255],
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Loan::class
        ]);
    }
}
