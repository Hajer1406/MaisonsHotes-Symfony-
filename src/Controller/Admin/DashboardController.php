<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use App\Controller\Admin\MaisonCrudController;
use App\Controller\Admin\ClientCrudController;
use App\Controller\Admin\ReservationCrudController;
use App\Controller\Admin\ProprietaireCrudController;
use App\Controller\Admin\UserCrudController;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MaisonRepository;
use App\Repository\ClientRepository;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator,
        private EntityManagerInterface $entityManager,
        private MaisonRepository $maisonRepository,
        private ClientRepository $clientRepository,
        private ReservationRepository $reservationRepository
    ) {}

    public function index(): Response
    {
        // Get statistics
        $stats = [
            'maisons' => $this->maisonRepository->countAll(),
            'clients' => $this->clientRepository->countAll(),
            'reservations' => $this->reservationRepository->countAll(),
            'pendingPayments' => $this->reservationRepository->countPending(),
            'paidReservations' => $this->reservationRepository->countPaid(),
            'totalRevenue' => $this->reservationRepository->getTotalRevenue(),
            'upcomingReservations' => $this->reservationRepository->countUpcoming(),
            'thisMonthReservations' => $this->reservationRepository->countThisMonth(),
        ];

        // Get most reserved houses
        $mostReserved = $this->reservationRepository->findMostReservedMaisons();

        // Get houses by city
        $housesByCity = $this->maisonRepository->findByCity();

        // Get latest reservations
        $latestReservations = $this->reservationRepository->findLatest(5);

        // Get latest houses
        $latestMaisons = $this->maisonRepository->findLatest(5);

        // Get monthly revenue for chart
        $rawRevenue = $this->reservationRepository->getMonthlyRevenue();
$monthlyRevenue = array_fill(0, 12, 0);
foreach ($rawRevenue as $row) {
    $monthlyRevenue[(int)$row['month'] - 1] = (float)$row['revenue'];
}

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
            'mostReserved' => $mostReserved,
            'housesByCity' => $housesByCity,
            'latestReservations' => $latestReservations,
            'latestMaisons' => $latestMaisons,
            'monthlyRevenue' => $monthlyRevenue,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<div style="display: flex; align-items: center; gap: 10px;"><div style="width: 36px; height: 36px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;"><i class="fas fa-home"></i></div><span style="font-family: Poppins, sans-serif; font-weight: 700; font-size: 1.3rem; color: #1e293b;">Maisons d\'hôtes</span></div>')
            ->renderContentMaximized()
            ->disableDarkMode()
            ->generateRelativeUrls()
            ->setFaviconPath('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 128 128%22><text y=%221.2em%22 font-size=%2296%22>🏡</text></svg>');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-chart-line');

        yield MenuItem::linkTo(MaisonCrudController::class, 'Maisons', 'fas fa-home');

        yield MenuItem::linkTo(ClientCrudController::class, 'Clients', 'fas fa-user');

        yield MenuItem::linkTo(ReservationCrudController::class, 'Réservations', 'fas fa-calendar');

        yield MenuItem::linkTo(ProprietaireCrudController::class, 'Propriétaires', 'fas fa-chalkboard-user');

        yield MenuItem::linkTo(UserCrudController::class, 'Utilisateurs', 'fas fa-users');

        yield MenuItem::linkToRoute('Retour au site', 'fas fa-arrow-left', 'app_maison_index');
    }
}
