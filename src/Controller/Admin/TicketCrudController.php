<?php

namespace App\Controller\Admin;

use App\Entity\Ticket;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class TicketCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ticket::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission('ROLE_ADMIN')
            ->setPageTitle('index', 'Billets :')
            ->setPageTitle('new', 'Créer un billet')
            ->setPageTitle('edit', fn(Ticket $ticket) => (string) $ticket->getName())
            ->setPageTitle('detail', fn(Ticket $ticket) => (string) $ticket->getName())
            ->setEntityLabelInSingular('un billet')
            ->setDefaultSort(['id' => 'ASC'])
            ->setPaginatorPageSize(10);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name', 'Nom du billet'),
            TextEditorField::new('content', 'Description du billet'),
            DateTimeField::new('startDateAt', 'Date de début de programmation'),
            DateTimeField::new('endDateAt', 'Date de fin de programmation'),
            MoneyField::new('price', 'Prix du billet')->setCurrency('EUR'),
            AssociationField::new('category', 'Catégorie')
            ->setQueryBuilder(
                fn (QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Ticket::class)->createQueryBuilder('g')->orderBy('g.name')
            )
            ->autocomplete(),
            ImageField::new('imageName', 'Image :')
            ->setBasePath('/images/tickets')
            ->setUploadDir('public/images/tickets')
            ->onlyOnIndex(),
            TextField::new('imageFile', 'Fichier image :')
            ->onlyOnForms()
            ->setFormType(VichImageType::class)
            ->setFormTypeOptions(['delete_label' => 'Supprimer l\'image']),
        ];
    }
}
