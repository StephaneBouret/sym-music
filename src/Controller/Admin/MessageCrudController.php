<?php

namespace App\Controller\Admin;

use App\Entity\Message;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class MessageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Message::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission('ROLE_ADMIN')
            ->setPageTitle('index', 'Message :')
            ->setPageTitle('new', 'Créer un message')
            ->setPageTitle('edit', fn (Message $message) => (string) $message->getContent())
            ->setEntityLabelInSingular('un message')
            ->setDefaultSort(['id' => 'ASC'])
            ->setPaginatorPageSize(10);
    }

    public function configureFields(string $pageName): iterable
    {
        $types = [
            Message::TYPE_WARNING,
            Message::TYPE_DANGER
        ];
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('content', 'Contenu du message'),
            DateTimeField::new('createdAt', 'Date de message')->hideOnForm(),
            ChoiceField::new('type', 'Type de message')
                ->setChoices(array_combine($types, $types))
        ];
    }
}
