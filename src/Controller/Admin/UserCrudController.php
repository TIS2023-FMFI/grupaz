<?php

namespace App\Controller\Admin;

use App\Entity\Log;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;

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
        $log->setTime(new \DateTimeImmutable());
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
        $log->setTime(new \DateTimeImmutable());
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
            IdField::new('id')->onlyOnDetail(),
            EmailField::new('email')
                ->setFormType(EmailType::class),
            ArrayField::new('roles')
                ->setLabel('entity.user.roles')

        ];
    }
}
