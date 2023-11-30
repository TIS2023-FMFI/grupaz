<?php

namespace App\Controller\Admin;

use App\Entity\Car;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CarCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Car::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index','entity.car.cars')
            ->setPageTitle('edit', 'entity.car.name')
            ->setPageTitle('detail','entity.car.name')
            ->setSearchFields(['vis'])
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
        $exportAction = Action::new('export')
            ->linkToRoute('app_export_car')
            ->createAsGlobalAction()
            ->setCssClass('btn btn-primary')
        ;
        $deleteAction = Action::new('remove')
            ->linkToRoute('app_delete_car')
            ->createAsGlobalAction()
            ->setCssClass('btn btn-primary')
            ;
//        $importAction = Action::new('import')
//            ->linkToRoute('app_import_car')
//            ->createAsGlobalAction()
//            ->setCssClass('btn btn-primary')
//        ;
        return $actions
//            ->add(Crud::PAGE_INDEX, $importAction)
            ->add(Crud::PAGE_INDEX, $deleteAction)
            ->add(Crud::PAGE_INDEX, $exportAction)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnDetail(),
            TextField::new('vis')
                ->setLabel('entity.car.vis'),
            AssociationField::new('carGroup')
                ->setLabel('entity.carGroup.name'),
            TextEditorField::new('note')
                ->hideOnIndex()
                ->setLabel('entity.car.note'),
            AssociationField::new('replacedCar')
                ->setLabel('entity.car.replaced_car'),
            //TODO status
        ];
    }

}
