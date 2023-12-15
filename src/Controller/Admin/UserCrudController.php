<?php

namespace App\Controller\Admin;

use App\Entity\User;
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
