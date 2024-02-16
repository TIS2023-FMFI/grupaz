<?php

namespace App\Controller\Admin;

use App\Entity\HistoryCarGroup;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class HistoryCarGroupCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return HistoryCarGroup::class;
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
    public function configureActions(Actions $actions): Actions
    {
        $deleteAction = Action::new('remove')
            ->setLabel('crud.remove')
            ->linkToRoute('app_delete_car')
            ->createAsGlobalAction()
            ->setCssClass('btn btn-primary')
        ;
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->add(Crud::PAGE_INDEX, $deleteAction)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ;
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addTab('main.info'),
            IdField::new('id')
                ->setLabel('crud.id')
                ->onlyOnIndex(),
            TextField::new('gid')
                ->setLabel('entity.carGroup.gid'),
            TextField::new('frontLicensePlate')
                ->setLabel('entity.carGroup.front_license_plate'),
            TextField::new('backLicensePlate')
                ->setLabel('entity.carGroup.back_license_plate'),
            TextField::new('destination')
                -> onlyOnDetail()
                ->setLabel('entity.carGroup.destination'),
            TextField::new('receiver')
                -> onlyOnDetail()
                ->setLabel('entity.carGroup.receiver'),
            DateTimeField::new('importTime')
                ->onlyOnDetail()
                ->setLabel('entity.carGroup.import_time'),
            DateTimeField::new('exportTime')
                ->setLabel('entity.carGroup.export_time'),
            ChoiceField::new('status')
                ->setLabel('entity.carGroup.status.name')
                ->setTranslatableChoices([
                    HistoryCarGroup::STATUS_APPROVED => ('entity.carGroup.status.approved'),
                    HistoryCarGroup::STATUS_ALL_SCANNED => ('entity.carGroup.status.all_scanned'),
                    HistoryCarGroup::STATUS_SCANNING => ('entity.carGroup.status.scanning'),
                    HistoryCarGroup::STATUS_START => ('entity.carGroup.status.start'),
                    HistoryCarGroup::STATUS_FREE => ('entity.carGroup.status.free'),
                ]),
            FormField::addTab('entity.car.cars'),
            AssociationField::new('cars')
                ->onlyOnDetail()
                ->setLabel('entity.car.cars')
                ->setTemplatePath('admin\show_cars_in_car_group.html.twig'),
            AssociationField::new('cars')
                ->hideOnDetail()
                ->setLabel('entity.car.cars')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ]),
        ];
    }
}
