<?php

namespace App\Controller\Admin;

use App\Entity\Document;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

class DocumentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Document::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */


public function configureFields(string $pageName): iterable
{
    yield TextField::new('title');
    yield ChoiceField::new('type')
        ->setChoices([
            'Instruction' => 'instruction',
            'Order' => 'order',
            'Certificate' => 'certificate',
            // Add more types as needed
        ]);
    yield DateField::new('createdAt');
    yield DateField::new('expiryDate');
    yield TextField::new('filePath');
    yield AssociationField::new('responsibleEmployee');
}
public function configureCrud(Crud $crud): Crud
{
    return $crud
        ->setEntityLabelInSingular('Document')
        ->setEntityLabelInPlural('Documents')
        ->setDefaultSort(['createdAt' => 'DESC'])
        ->overrideTemplate('crud/index', 'admin/Document/list.html.twig'); //  шаблон
}



public function configureFilters(Filters $filters): Filters
{
    return $filters
        ->add(TextFilter::new('title'))
        ->add(ChoiceFilter::new('type')
            ->setChoices([
                'Instruction' => 'instruction',
                'Order' => 'order',
                'Certificate' => 'certificate',
            ]))
    ;
}
}

