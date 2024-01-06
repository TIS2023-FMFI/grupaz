<?php

namespace App\Controller\Admin;

use App\Entity\CarGroup;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class HistoryCarGroupCrudController extends CarGroupCrudController
{
    public static function getEntityFqcn(): string
    {
        return CarGroup::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('history.plural')
            ->setEntityLabelInSingular('history.singular')
            ->setDefaultSort(['exportTime' => 'DESC',])
            // the max number of entities to display per page
            ->setPaginatorPageSize(30)
            // the number of pages to display on each side of the current page
            // e.g. if num pages = 35, current page = 7 and you set ->setPaginatorRangeSize(4)
            // the paginator displays: [Previous]  1 ... 3  4  5  6  [7]  8  9  10  11 ... 35  [Next]
            // set this number to 0 to display a simple "< Previous | Next >" pager
            ->setPaginatorRangeSize(3);
    }
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): \Doctrine\ORM\QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.exportTime IS NOT NULL');
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ;
    }
}
