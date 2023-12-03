<?php

namespace App\Controller\Admin;

use App\Entity\CarGroup;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;

class CarGroupCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CarGroup::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index','entity.carGroup.name')
            ->setPageTitle('edit', 'entity.carGroup.name')
            ->setPageTitle('detail','entity.carGroup.name')
            ->setSearchFields(['gid'])
            ->setDefaultSort(['exportTime' => 'DESC', 'gid' => 'DESC',])
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
//        $importAction = Action::new('import')
//            ->linkToRoute('app_import_car')
//            ->createAsGlobalAction()
//            ->setCssClass('btn btn-primary')
//        ;
        $deleteAction = Action::new('remove')
            ->linkToRoute('app_delete_car')
            ->createAsGlobalAction()
            ->setCssClass('btn btn-primary')
        ;

//        $cimportAction = Action::new('aaimport')
//            ->linkToRoute('app_import_car')
//            ->setCssClass('btn btn-primary')
//        ;
        return $actions
//            ->add(Crud::PAGE_INDEX, $importAction)
            ->add(Crud::PAGE_INDEX, $exportAction)
            ->add(Crud::PAGE_INDEX, $deleteAction)
//            ->add(Crud::PAGE_DETAIL, $cimportAction)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnDetail(),
            TextField::new('gid')
                ->setLabel('entity.carGroup.gid'),
            AssociationField::new('cars') //TO DO - change field
                ->setLabel('entity.car.cars')
                ->setTemplatePath('admin\showCarGroup.html.twig'),
            TextField::new('frontLicensePlate')
                ->setLabel('entity.carGroup.front_license_plate'),
            TextField::new('backLicensePlate')
                ->setLabel('entity.carGroup.back_license_plate'),
            TextField::new('destination')
                -> onlyOnDetail()
                ->setLabel('entity.carGroup.destination'),
            DateTimeField::new('importTime')
                ->onlyOnDetail()
                ->setLabel('entity.carGroup.import_time'),
            DateTimeField::new('exportTime')
                ->setLabel('entity.carGroup.export_time'),
            ChoiceField::new('status')
            ->setTranslatableChoices([
                4 => ('entity.carGroup.status.approved'),
                3 => ('entity.carGroup.status.all_scanned'),
                2 => ('entity.carGroup.status.scanning'),
                1 => ('entity.carGroup.status.start'),
                0 => ('entity.carGroup.status.free'),
            ])
        ];
    }
}
