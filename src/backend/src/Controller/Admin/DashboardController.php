<?php

namespace App\Controller\Admin;

use App\Entity\Database\DatabaseDump;
use App\Entity\Database\DatabaseDumpDeleteRules;
use App\Entity\Database\DatabaseRuleTemplate;
use App\Entity\Server;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Database\Database;
use App\Entity\Database\DatabaseRule;
use App\Entity\Workspace\Workspace;
use App\Entity\Workspace\User;
use App\Entity\Webhook;
use App\Entity\Admin\User as AdminUser;
use App\Controller\Admin\Database\DatabaseCrudController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/%admin_path%', name: 'admin')]
    public function index(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(DatabaseCrudController::class)->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Database Management');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Servers');
        yield MenuItem::linkToCrud('Server', 'fas fa-server',  Server::class);

        yield MenuItem::section('Database');
        yield MenuItem::linkToCrud('Databases', 'fas fa-database',  Database::class);
        yield MenuItem::linkToCrud('Database Dumps', 'fas fa-database',  DatabaseDump::class);
        yield MenuItem::linkToCrud(
            'Database Dumps Delete Rules',
            'fas fa-database',
            DatabaseDumpDeleteRules::class
        );
        yield MenuItem::linkToCrud('Rules', 'fas fa-gears',  DatabaseRule::class);
        yield MenuItem::linkToCrud(
            'Rules Templates',
            'fas fa-gears',
            DatabaseRuleTemplate::class
        );
        yield MenuItem::linkToCrud('Webhooks', 'fas fa-retweet',  Webhook::class);

        yield MenuItem::section('Workspace');
        yield MenuItem::linkToCrud('Workspaces', 'fas fa-hotel',  Workspace::class);
        yield MenuItem::linkToCrud('Users', 'fas fa-users',  User::class);

        yield MenuItem::section('Admin');
        yield MenuItem::linkToCrud('Admin Users', 'fas fa-users',  AdminUser::class);
    }
}
