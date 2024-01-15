<?php

namespace App\Controller\Admin;

use App\Entity\Log;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_SUPER_ADMIN')]
class UserCrudController extends AbstractCrudController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    public static function getEntityFqcn(): string
    {
        return User::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index','entity.user.name')
            ->setPageTitle('edit', 'entity.user.name')
            ->setPageTitle('detail','entity.user.name')
            ->setEntityLabelInPlural('entity.user.users')
            ->setEntityLabelInSingular('entity.user.name')
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
        $registerAction = Action::new('register')
            ->setLabel('entity.user.new')
            ->linkToRoute('app_register')
            ->createAsGlobalAction()
            ->setCssClass('btn btn-primary')
        ;
        return $actions
            ->add(Crud::PAGE_INDEX, $registerAction)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ;
    }
//    if superadmin doesnt want to see all superadmins
//    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
//    {
//        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
//            ->andWhere('entity.roles NOT LIKE :role')
//                ->setParameter('role', '%ROLE_SUPER_ADMIN%');
//    }
    
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $changes = $this->getEntityChanges($entityInstance);

        parent::updateEntity($entityManager, $entityInstance);

        $log = new Log();
        $log->setTime(new DateTimeImmutable());
        $log->setLog('Používateľ upravený. Zmeny: ' . implode(', ', $changes));
        $log->setAdminId((int)$this->getUser()->getId());
        $log->setObjectId((int)$entityInstance->getId());
        $log->setObjectClass('User');

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
            if (is_array($change[0]) || is_array($change[1])) {
                $valueBefore = json_encode($change[0]);
                $valueAfter = json_encode($change[1]);

                if ($valueBefore !== $valueAfter) {
                    $changes[] = sprintf('%s: %s => %s', $field, $valueBefore, $valueAfter);
                }
            } else {
                if ($change[0] !== $change[1]) {
                    $changes[] = sprintf('%s: %s => %s', $field, $change[0], $change[1]);
                }
            }
        }

        return $changes;
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $log = new Log();
        $log->setTime(new DateTimeImmutable());
        $log->setLog('Používateľ vymazaný.');
        $log->setAdminId((int) $this->getUser()->getId());
        $log->setObjectId((int) $entityInstance->getId());
        $log->setObjectClass('User');

        $entityManager->persist($log);
        $entityManager->remove($entityInstance);
        $entityManager->flush();
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnIndex()
            ->setLabel('crud.id'),
            EmailField::new('email')
                ->setLabel('entity.user.email')
                ->setFormType(EmailType::class),
            ArrayField::new('roles')
                ->setLabel('entity.user.roles')

        ];
    }
}
