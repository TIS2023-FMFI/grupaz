<?php

namespace App\Controller\Admin;

use App\Entity\HistoryCar;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class HistoryCarCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return HistoryCar::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'history.car.plural')
            ->setPageTitle('edit', 'entity.car.name')
            ->setPageTitle('detail', 'entity.car.name')
            ->setEntityLabelInPlural('history.car.plural')
            ->setEntityLabelInSingular('history.car.singular')
            ->setDefaultSort(['id' => 'ASC'])
            // the max number of entities to display per page
            ->setPaginatorPageSize(30)
            // the number of pages to display on each side of the current page
            // e.g. if num pages = 35, current page = 7 and you set ->setPaginatorRangeSize(4)
            // the paginator displays: [Previous]  1 ... 3  4  5  6  [7]  8  9  10  11 ... 35  [Next]
            // set this number to 0 to display a simple "< Previous | Next >" pager
            ->setPaginatorRangeSize(3);
    }


    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::EDIT);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm()
                ->setLabel('crud.id'),
            TextField::new('vis')
                ->setLabel('entity.car.vis'),
            AssociationField::new('historyCarGroup')
                ->setLabel('entity.carGroup.name'),
            TextField::new('note')
                ->setLabel('entity.car.note'),
            TextField::new('replacedCar')
                ->setLabel('entity.car.replaced_car'),
            ChoiceField::new('isDamaged')
                ->setLabel('entity.car.isDamaged.name')
                ->setTranslatableChoices([
                    HistoryCar::STATUS_IS_DAMAGED => ('entity.car.isDamaged.damaged'),
                    HistoryCar::STATUS_IS_NEW => ('entity.car.isDamaged.new'),
                ]),
            ChoiceField::new('status')
                ->setLabel('entity.car.status.name')
                ->setTranslatableChoices([
                    HistoryCar::STATUS_SCANNED => ('entity.car.status.scanned'),
                    HistoryCar::STATUS_FREE => ('entity.car.status.free'),
                ]),

        ];
    }

}
