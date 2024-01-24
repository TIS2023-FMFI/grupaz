<?php

namespace App\Controller\Admin;

use App\Entity\Car;
use App\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CarCrudController extends AbstractCrudController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return Car::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'entity.car.cars')
            ->setPageTitle('edit', 'entity.car.name')
            ->setPageTitle('detail', 'entity.car.name')
            ->setEntityLabelInPlural('entity.car.cars')
            ->setEntityLabelInSingular('entity.car.name')
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

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $changes = $this->getEntityChanges($entityInstance);

        parent::updateEntity($entityManager, $entityInstance);

        if (!empty($changes)) {
            $log = new Log();
            $log->setTime(new \DateTimeImmutable());
            $log->setLog('Auto upravené. Zmeny: ' . implode(', ', $changes));
            $log->setAdminId((int)$this->getUser()->getId());
            $log->setObjectId((int)$entityInstance->getId());
            $log->setObjectClass('Car');

            $entityManager->persist($log);
            $entityManager->flush();
        }
    }

    private function getEntityChanges($entity): array
    {
        $changes = [];
        $unitOfWork = $this->entityManager->getUnitOfWork();
        $unitOfWork->computeChangeSets();

        $entityChangeSet = $unitOfWork->getEntityChangeSet($entity);

        foreach ($entityChangeSet as $field => $change) {
            if ($field === 'isDamaged') {
                $changes[] = sprintf('%s: %s => %s',
                    $field,
                    Car::translateIsDamaged($change[0]),
                    Car::translateIsDamaged($change[1]));
            } else if ($field === 'status') {
                $changes[] = sprintf('%s: %s => %s',
                    $field,
                    Car::translateStatus($change[0]),
                    Car::translateStatus($change[1]));
            } else {
                $changes[] = sprintf('%s: %s => %s', $field, $change[0], $change[1]);
            }
        }

        return $changes;
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $log = new Log();
        $log->setTime(new \DateTimeImmutable());
        $log->setLog('Auto vymazané.');
        $log->setAdminId((int)$this->getUser()->getId());
        $log->setObjectId((int)$entityInstance->getId());
        $log->setObjectClass('Car');

        $entityManager->persist($log);
        $entityManager->remove($entityInstance);
        $entityManager->flush();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm()
                ->setLabel('crud.id'),
            TextField::new('vis')
                ->setLabel('entity.car.vis'),
            AssociationField::new('carGroup')
                ->setLabel('entity.carGroup.name'),
            TextField::new('note')
                ->setLabel('entity.car.note'),
            AssociationField::new('replacedCar')
                ->setLabel('entity.car.replaced_car')
                ->setFormTypeOption('query_builder', function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('e')
                        ->andWhere('e.isDamaged = :damaged')
                        ->setParameter("damaged", 0)
                        ;
                }),
            ChoiceField::new('isDamaged')
                ->setLabel('entity.car.isDamaged.name')
                ->setTranslatableChoices([
                    Car::STATUS_IS_DAMAGED => ('entity.car.isDamaged.damaged'),
                    Car::STATUS_IS_NEW => ('entity.car.isDamaged.new'),
                ]),
            ChoiceField::new('status')
                ->setLabel('entity.car.status.name')
                ->setTranslatableChoices([
                    Car::STATUS_SCANNED => ('entity.car.status.scanned'),
                    Car::STATUS_FREE => ('entity.car.status.free'),
                ]),

        ];
    }

}
