<?php

namespace App\Controller\Admin;

use App\Entity\Log;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_SUPER_ADMIN')]
class LogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Log::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index','log.logs')
            ->setPageTitle('edit', 'log.logs')
            ->setPageTitle('detail','log.logs')
            ->setSearchFields(['time', 'log'])
            ->setDefaultSort(['time' => 'DESC', 'id' => 'DESC',])
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
        return parent::configureActions($actions);
        //TODO set actions - detail, edit, delete for logCrud
//            ->remove();
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnDetail(),
            DateTimeField::new('time')
            ->setLabel('log.time'),
            TextField::new('log')

        ];
    }
}
