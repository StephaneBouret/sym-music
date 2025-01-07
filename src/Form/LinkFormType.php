<?php

namespace App\Form;

use App\Entity\Artist;
use App\Entity\Link;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class LinkFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('url', UrlType::class, [
                'label' => 'URL du réseau social :'
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Deezer' => Link::TYPE_DEEZER,
                    'Facebook' => Link::TYPE_FACEBOOK,
                    'Instagram' => Link::TYPE_INSTAGRAM,
                    'Web' => Link::TYPE_WEB,
                    'X' => Link::TYPE_X,
                    'Youtube' => Link::TYPE_YOUTUBE,
                ],
                'label' => 'Type de réseau social'
            ])
            ->add('svgFile', VichFileType::class, [
                'required' => false,
                'label' => false,
                'delete_label' => 'Supprimer le svg',
                'download_uri' => false,
                'attr' => [
                    'class' => 'form-control mb-2'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Link::class,
        ]);
    }
}
