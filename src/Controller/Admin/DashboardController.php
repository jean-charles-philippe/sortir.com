<?php

namespace App\Controller\Admin;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Location;
use App\Entity\State;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }



    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Sortir.Com');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Users', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Campus', 'fas fa-tags', Campus::class);
        yield MenuItem::linkToCrud('Location', 'fas fa-tags', Location::class);
        yield MenuItem::linkToCrud('City', 'fas fa-tags', City::class);
        yield MenuItem::linkToCrud('State', 'fas fa-tags', State::class);
        yield MenuItem::linkToUrl('Retour site', null, '/');
    }
}
