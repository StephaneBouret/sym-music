<?php

namespace App\Controller\Admin;

use App\Entity\Purchase;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PurchaseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Purchase::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission('ROLE_ADMIN')
            ->setPageTitle('index', 'Commandes :')
            ->setPageTitle('detail', fn(Purchase $purchase) => (string) $purchase->getId())
            ->setEntityLabelInSingular('une commande')
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(10);
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);
        $actions->disable(Action::NEW, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('user', 'Utilisateur'),
            MoneyField::new('total', 'Total de la commande')->setCurrency('EUR'),
            DateTimeField::new('purchasedAt', 'Date de la commande :'),
            TextField::new('status', 'Statut de la commande :')
                ->formatValue(function ($value) {
                    if ($value === Purchase::STATUS_PAID) {
                        return '<span class="badge text-bg-success">Payée</span>';
                    }
                    if ($value === Purchase::STATUS_PENDING) {
                        return '<span class="badge text-bg-warning">En attente</span>';
                    }
                    return $value;
                })
            ->renderAsHtml(),
            CollectionField::new('purchaseItems', 'Billets :')
            ->onlyOnDetail()
            ->allowAdd(false)
            ->setTemplatePath('admin/fields/purchase_items.html.twig'),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return parent::configureFilters($filters)
            ->add(BooleanFilter::new('status')
            ->setFormTypeOption('choices', [
                'En attente' => Purchase::STATUS_PENDING,
                'Payée' => Purchase::STATUS_PAID,
            ]));
    }
}
