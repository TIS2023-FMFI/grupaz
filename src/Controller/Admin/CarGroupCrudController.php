<?php

namespace App\Controller\Admin;

use App\Entity\Car;
use App\Entity\CarGroup;
use App\Entity\HistoryCar;
use App\Entity\HistoryCarGroup;
use App\Entity\Log;
use App\Repository\CarRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Routing\Annotation\Route;

class CarGroupCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;
    private CarRepository $carRepository;

    public function __construct(EntityManagerInterface $entityManager, CarRepository $carRepository)
    {
        $this->entityManager = $entityManager;
        $this->carRepository = $carRepository;
    }
    
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
            ->setEntityLabelInPlural('entity.carGroup.car_groups')
            ->setEntityLabelInSingular('entity.carGroup.name')
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
            ->setLabel('crud.export')
            ->linkToRoute('app_export_car')
            ->createAsGlobalAction()
            ->setCssClass('btn btn-primary')
        ;
        $importAction = Action::new('import')
            ->setLabel('crud.import')
            ->linkToRoute('app_import_car')
            ->createAsGlobalAction()
            ->setCssClass('btn btn-primary')
        ;
        $approveAction = Action::new('approve')
            ->setLabel('crud.approve')
            ->linkToCrudAction('approve')
            ->addCssClass('btn btn-success')
            ->setIcon('fa fa-check-circle')
            ->displayIf(static function (CarGroup $carGroup): bool {
                return $carGroup->getStatus() == 3;
            });

        return $actions
            ->add(Crud::PAGE_INDEX, $importAction)
            ->add(Crud::PAGE_INDEX, $exportAction)
            ->add(Crud::PAGE_DETAIL, $approveAction)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ;
    }
    public function createEntity(string $entityFqcn)
    {
        $carGroup = new CarGroup();
        $carGroup->setImportTime(new DateTimeImmutable());

        return $carGroup;
    }
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);

        $logText = 'Grupáž vytvorená. ID: ' . $entityInstance->getGid();
        $logText .= ', status: ' . CarGroup::translateStatus($entityInstance->getStatus());

        if ($entityInstance->getFrontLicensePlate() != null)
            $logText .= ', frontLicensePlate: ' . $entityInstance->getFrontLicensePlate();
        if ($entityInstance->getBackLicensePlate() != null)
            $logText .= ', backLicensePlate: ' . $entityInstance->getBackLicensePlate();
        if ($entityInstance->getExportTime() != null)
            $logText .= ', exportTime: ' . $entityInstance->getExportTime()->format('Y-m-d H:i:s');
        if (!empty($entityInstance->getCars())){
            $logText .= ', cars: ';
            foreach ($entityInstance->getCars() as $car){
                $logText .= $car . ', ';
            }
            $logText = rtrim($logText, ', ');
        }

        $log = new Log();
        $log->setTime(new DateTimeImmutable());
        $log->setLog($logText);
        $log->setAdminId((int)$this->getUser()->getId());
        $log->setObjectId((int)$entityInstance->getId());
        $log->setObjectClass('Cargroup');

        $entityManager->persist($log);
        $entityManager->flush();
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $changes = $this->getEntityChanges($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);

        if (!empty($changes)) {
            $log = new Log();
            $log->setTime(new DateTimeImmutable());
            $log->setLog('Grupáž upravená. Zmeny: ' . implode(', ', $changes));
            $log->setAdminId((int)$this->getUser()->getId());
            $log->setObjectId((int)$entityInstance->getId());
            $log->setObjectClass('Cargroup');

            $entityManager->persist($log);
            $entityManager->flush();
        }
    }

    private function getEntityChanges($entity): array
    {
        $changes = [];
        $unitOfWork = $this->entityManager->getUnitOfWork();
        $unitOfWork->computeChangeSets();

        $scheduledForUpdate = $unitOfWork->getScheduledEntityUpdates();
        foreach ($scheduledForUpdate as $item) {
            $entityChangeSet = $unitOfWork->getEntityChangeSet($item);
            foreach ($entityChangeSet as $field => $change) {
                if ($field === 'exportTime') {
                    $changes[] = sprintf('%s: %s => %s',
                        $field,
                        ($change[0] === null) ? 'nenastavené' : $change[0]->format('Y-m-d H:i:s'),
                        ($change[1] === null) ? 'nenastavené' : $change[1]->format('Y-m-d H:i:s')
                    );
                }
                else if ($field === 'status') {
                    $changes[] = sprintf('%s: %s => %s',
                        $field,
                        CarGroup::translateStatus($change[0]),
                        CarGroup::translateStatus($change[1]));
                }
                else if ($field === 'carGroup') {
                    $changes[] = sprintf('Auto %s -> %s: %s => %s',
                        $item->getVis(),
                        $field,
                        ($change[0] === null) ? 'nenastavené' : $change[0],
                        ($change[1] === null) ? 'nenastavené' : $change[1]
                    );
                }
                else{
                    $changes[] = sprintf('%s: %s => %s', $field, $change[0], $change[1]);
                }
            }
        }
        return $changes;
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
                    CarGroup::STATUS_ALL_SCANNED => ('entity.carGroup.status.all_scanned'),
                    CarGroup::STATUS_SCANNING => ('entity.carGroup.status.scanning'),
                    CarGroup::STATUS_START => ('entity.carGroup.status.start'),
                    CarGroup::STATUS_FREE => ('entity.carGroup.status.free'),
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

    public function approve(AdminContext $adminContext, AdminUrlGenerator $adminUrlGenerator)
    {
        $carGroup = $adminContext->getEntity()->getInstance();
        if (!$carGroup instanceof CarGroup) {
            throw new \LogicException('Entity is missing or not a CarGroup');
        }
        $carGroup->setStatus(CarGroup::STATUS_APPROVED);
        $carGroup->setExportTime(new DateTimeImmutable());
        $this->createHistoryEntity($carGroup);
        $this->entityManager->flush();
        $this->addFlash('success', 'entity.carGroup.approved');
        return $this->redirectToRoute('admin');
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/admin/{_locale<%app.supported_locales%>/remove_from_progress/{id}', name: 'remove_from_progress')]
    public function removeCarGroupFromProgress(CarGroup $carGroup)
    {
        $carGroup->setStatus(CarGroup::STATUS_FREE);
        $this->carRepository->unloadAllCarInGroup($carGroup->getId());

        $this->entityManager->persist($carGroup);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin');
    }

    private function createHistoryEntity(CarGroup $carGroup)
    {
        $historyCarGroup = new HistoryCarGroup();

        $historyCarGroup->setImportTime($carGroup->getImportTime());
        $historyCarGroup->setStatus($carGroup->getStatus());
        $historyCarGroup->setGid($carGroup->getGid());
        $historyCarGroup->setExportTime($carGroup->getExportTime());
        $historyCarGroup->setReceiver($carGroup->getReceiver());
        $historyCarGroup->setDestination($carGroup->getDestination());
        $historyCarGroup->setBackLicensePlate($carGroup->getBackLicensePlate());
        $historyCarGroup->setFrontLicensePlate($carGroup->getFrontLicensePlate());

        foreach ($carGroup->getCars() as $car) {
            $historyCar = new HistoryCar();

            $historyCar->setStatus($car->getStatus());
            $historyCar->setVis($car->getVis());
            $historyCar->setIsDamaged($car->getIsDamaged());
            $historyCar->setNote($car->getNote());

            $historyCar->setReplacedCar($this->recursed($car));

            $historyCarGroup->addCar($historyCar);
            $this->entityManager->persist($historyCar);
            $this->entityManager->remove($car);
        }

        $this->entityManager->persist($historyCarGroup);
        $this->entityManager->remove($carGroup);
    }

    private function recursed(Car $car): ?HistoryCar
    {
        if ($car->getReplacedCar() == null){
            return null;
        }
        $replacedCar = $car->getReplacedCar();
        $historyCar = new HistoryCar();

        $historyCar->setStatus($replacedCar->getStatus());
        $historyCar->setVis($replacedCar->getVis());
        $historyCar->setIsDamaged($replacedCar->getIsDamaged());
        $historyCar->setNote($replacedCar->getNote());

        $historyCar->setReplacedCar($this->recursed($replacedCar));

        $this->entityManager->persist($historyCar);
        $this->entityManager->remove($replacedCar);
        return $historyCar;
    }
}
