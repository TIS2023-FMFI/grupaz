<?php

namespace App\Controller\Admin;

use App\Entity\CarGroup;
use App\Entity\Car;
use App\Entity\Log;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
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

class DashboardCrudController extends AbstractCrudController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return CarGroup::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index','main.dashboard')
            ->setPageTitle('detail','main.dashboard')
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
        $importAction = Action::new('import')
            ->linkToRoute('app_import_car')
            ->createAsGlobalAction()
            ->setCssClass('btn btn-primary')
        ;
        $deleteAction = Action::new('remove')
            ->linkToRoute('app_delete_car')
            ->createAsGlobalAction()
            ->setCssClass('btn btn-primary')
        ;
        $approveAction = Action::new('approve')
            ->setTemplatePath('admin/approve.html.twig')
            ->linkToCrudAction('approve')
            ->addCssClass('btn btn-success')
            ->setIcon('fa fa-check-circle')
            ->displayAsButton();
        //TODO approve carGroup after all cars were scanned
//        $cimportAction = Action::new('aaimport')
//            ->linkToRoute('app_import_car')
//            ->setCssClass('btn btn-primary')
//        ;
        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, $importAction)
            ->add(Crud::PAGE_INDEX, $exportAction)
            ->add(Crud::PAGE_INDEX, $deleteAction)
            ->add(Crud::PAGE_INDEX, $approveAction)
            ->add(Crud::PAGE_DETAIL, $approveAction)
//            ->add(Crud::PAGE_DETAIL, $cimportAction)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ;
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $changes = $this->getEntityChanges($entityInstance);

        parent::updateEntity($entityManager, $entityInstance);

        $log = new Log();
        $log->setTime(new \DateTimeImmutable());
        $log->setLog('Grupáž upravená. Zmeny: ' . implode(', ', $changes));
        $log->setAdminId((int)$this->getUser()->getId());
        $log->setObjectId((int)$entityInstance->getId());
        $log->setObjectClass('Cargroup');

        $entityManager->persist($log);
        $entityManager->flush();
    }

    private function getEntityChanges($entity): array
    {
        $changes = [];
        $unitOfWork = $this->entityManager->getUnitOfWork();
        $unitOfWork->computeChangeSets();

        $entityChangeSet = $unitOfWork->getEntityChangeSet($entity);

        foreach ($entityChangeSet as $field => $change) {
            if ($change[0] instanceof \DateTimeInterface && $change[1] instanceof \DateTimeInterface) {
                $changes[] = sprintf('%s: %s => %s', $field, $change[0]->format('Y-m-d H:i:s'), $change[1]->format('Y-m-d H:i:s'));
            } else {
                $changes[] = sprintf('%s: %s => %s', $field, $change[0], $change[1]);
            }
        }
        return $changes;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addTab('main.info'),
            IdField::new('id')
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
                    4 => ('entity.carGroup.status.approved'),
                    3 => ('entity.carGroup.status.all_scanned'),
                    2 => ('entity.carGroup.status.scanning'),
                    1 => ('entity.carGroup.status.start'),
                    0 => ('entity.carGroup.status.free'),
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

    /**
     * @throws \DateMalformedStringException
     */
    public function approve(String $gid, CarRepository $car): ?Car
    {
        return $car->confirmCarGroup($gid);
    }
}
