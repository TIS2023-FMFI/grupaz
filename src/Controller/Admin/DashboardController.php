<?php

namespace App\Controller\Admin;

use App\Entity\Car;
use App\Entity\CarGroup;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/{_locale<%app.supported_locales%>}/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Admin')
            ->setLocales([
                'en' => 'English',
                'sk' => 'Slovensky'
            ])
            ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('main.homepage', 'fa fa-home', 'app_index_no_locale');
        yield MenuItem::linkToDashboard('main.dashboard', 'fa fa-clipboard');
        yield MenuItem::linkToCrud('entity.car.cars', 'fas fa-car', Car::class);
        yield MenuItem::linkToCrud('entity.carGroup.name', 'fas fa-list', CarGroup::class);
        yield MenuItem::linkToLogout('main.logout', 'fa fa-exit');


        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
