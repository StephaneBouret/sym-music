<?php

namespace App\Form;

use App\Data\SearchData;
use App\Repository\ArtistRepository;
use IntlDateFormatter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFormType extends AbstractType
{
    public function __construct(protected ArtistRepository $artistRepository)
    {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Récupération des dates uniques depuis la base de données
        $dates = $this->artistRepository->findDistinctProgramDates();
        $formatter = new IntlDateFormatter(
            'fr_FR',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            null,
            IntlDateFormatter::GREGORIAN
        );
        $formatter->setPattern('EEE dd/MM');

        $builder
            ->add('q', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher un artiste'
                ]
            ])
            ->add('programDateAt', ChoiceType::class, [
                'label' => false,
                'choices' => $dates,
                'choice_label' => function (\DateTimeImmutable $date) use ($formatter) {
                    return mb_strtoupper($formatter->format($date));
                },
                'choice_attr' => fn (\DateTimeImmutable $date) => [
                    'class' => 'date-choice'
                ],
                'required' => false,
                'expanded' => true,
                'multiple' => false,
                'placeholder' => 'LINE-UP',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
