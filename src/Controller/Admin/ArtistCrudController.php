<?php

namespace App\Controller\Admin;

use App\Entity\Artist;
use App\Entity\Genre;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ArtistCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Artist::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission('ROLE_ADMIN')
            ->setPageTitle('index', 'Artiste :')
            ->setPageTitle('new', 'Créer un artiste')
            ->setPageTitle('edit', fn (Artist $artist) => (string) $artist->getName())
            ->setPageTitle('detail', fn (Artist $artist) => (string) $artist->getName())
            ->setEntityLabelInSingular('un artiste')
            ->setDefaultSort(['id' => 'ASC'])
            ->setPaginatorPageSize(10);
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);
        $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name', 'Nom de l\'artiste'),
            TextEditorField::new('description', 'Description de l\'artiste'),
            DateTimeField::new('programDateAt', 'Date de programmation'),
            AssociationField::new('genre', 'Genre musical')
            ->setQueryBuilder(
                fn (QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Genre::class)->createQueryBuilder('g')->orderBy('g.name')
            )
            ->autocomplete(),
            ImageField::new('imageName', 'Image :')
            ->setBasePath('/images/artists')
            ->setUploadDir('public/images/artists')
            ->onlyOnIndex(),
            TextField::new('imageFile', 'Fichier image :')
            ->onlyOnForms()
            ->setFormType(VichImageType::class)
            ->setFormTypeOptions(['delete_label' => 'Supprimer l\'image']),
        ];
    }
}
