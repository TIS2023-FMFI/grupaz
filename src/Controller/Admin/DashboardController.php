<?php

namespace App\Controller\Admin;

use App\Entity\Car;
use App\Entity\CarGroup;
use App\Entity\HistoryCar;
use App\Entity\HistoryCarGroup;
use App\Entity\User;
use App\Entity\Log;
use App\Repository\CarGroupRepository;
use App\Repository\CarRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Transport\Receiver\MessageCountAwareInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;


class DashboardController extends AbstractDashboardController
{
    private ServiceLocator $serviceLocator;
    private CarGroupRepository $carGroupRepository;
    private CarRepository $carRepository;

    public function __construct(ServiceLocator $serviceLocator, CarGroupRepository $carGroupRepository, CarRepository $carRepository)
    {
        $this->serviceLocator = $serviceLocator;
        $this->carGroupRepository = $carGroupRepository;
        $this->carRepository = $carRepository;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route('/admin/{_locale<%app.supported_locales%>}', name: 'admin')]
    public function index(): Response
    {
        $importCarTransport = $this->serviceLocator->get('messenger.transport.import_car');
        if ($importCarTransport instanceof MessageCountAwareInterface) {
            $carsInQueue = $importCarTransport->getMessageCount();
        }

        $importCarsTransport = $this->serviceLocator->get('messenger.transport.import_cars');
        if ($importCarsTransport instanceof MessageCountAwareInterface) {
            $filesInQueue = $importCarsTransport->getMessageCount();
        }

        return $this->render('admin/dashboard.html.twig', [
            'carsInQueue' => $carsInQueue ?? null,
            'filesInQueue' => $filesInQueue ?? null,
            'toApproveNotifications' => $this->getToApproveNotifications(),
            'workInProgressNotifications' => $this->getWorkInProgressNotifications(),
        ]);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route('/admin/{_locale<%app.supported_locales%>}/notifications', name: 'admin_notifications')]
    public function notifications(): Response
    {
        return $this->render('admin/_notification.html.twig', [
            'toApproveNotifications' => $this->getToApproveNotifications(),
            'workInProgressNotifications' => $this->getWorkInProgressNotifications(),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Admin')
            ->setLocales([
                'en' => 'English',
                'sk' => 'Slovenčina'
            ])
            ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('main.homepage', 'fa fa-home', 'app_index_no_locale');
        yield MenuItem::linkToDashboard('main.dashboard', 'fa fa-clipboard');
        yield MenuItem::linkToCrud('history.car_group.singular', 'fas fa-list', HistoryCarGroup::class);
        yield MenuItem::linkToCrud('history.car.singular', 'fas fa-list', HistoryCar::class);
        yield MenuItem::section("");
        yield MenuItem::linkToCrud('entity.car.cars', 'fas fa-car', Car::class);
        yield MenuItem::linkToCrud('entity.carGroup.name', 'fas fa-list', CarGroup::class);
        yield MenuItem::section("");
        yield MenuItem::linkToCrud('entity.user.users', 'fas fa-users', User::class)
            ->setPermission("ROLE_SUPER_ADMIN");
        yield MenuItem::linkToCrud('log.logs', 'fas fa-list', Log::class)
            ->setPermission("ROLE_SUPER_ADMIN");
        yield MenuItem::linkToLogout('main.logout', 'fa fa-exit');
    }
    private function getToApproveNotifications(): array
    {
        return $this->carGroupRepository->findToAdminApprove();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    private function getWorkInProgressNotifications(): array
    {
        $notifications = [];
        $groupsWorkInProgress = $this->carGroupRepository->findInProgress();
        foreach ($groupsWorkInProgress as $groupWorkInProgress)
        {
            $countAll = $this->carRepository->countAll($groupWorkInProgress->getId());
            $countAllLoaded = $this->carRepository->countAllLoaded($groupWorkInProgress->getId());
            $notifications[] = [$groupWorkInProgress, $countAll, $countAllLoaded];
        }
        return $notifications;
    }
}
