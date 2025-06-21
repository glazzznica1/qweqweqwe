<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use App\Entity\Document;
use App\Entity\Employee;
use App\Entity\Notification;
use App\Controller\Admin\DocumentCrudController;
use App\Controller\Admin\EmployeeCrudController;
use App\Controller\Admin\NotificationCrudController;

class AdminController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(DocumentCrudController::class)->generateUrl());

        // Если вы хотите отображать кастомный шаблон, раскомментируйте и настройте это:
        // return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Панель администратора')
            ->setFaviconPath('/favicon.ico')  // Укажите путь к вашему favicon
            ->setTranslationDomain('admin') // Домен перевода (по желанию)
            ->setTextDirection('ltr') // Направление текста (по желанию)
            ->renderContentMaximized() //  Убрать боковое меню
            //->renderSidebarMinimized() // Sidebar отображается в свернутом виде
            ->disableUrlSignatures() // Отключает подписи URL для безопасности (по желанию)
            ->generateRelativeUrls() // Создает относительные URL (по желанию)
            ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Главная', 'fa fa-home');

        yield MenuItem::section('Управление');
        yield MenuItem::linkToCrud('Документы', 'fas fa-file-alt', Document::class)->setController(DocumentCrudController::class);
        yield MenuItem::linkToCrud('Сотрудники', 'fas fa-users', Employee::class)->setController(EmployeeCrudController::class);
        yield MenuItem::linkToCrud('Уведомления', 'fas fa-bell', Notification::class)->setController(NotificationCrudController::class);

        // Дополнительные элементы меню (по желанию)
        // yield MenuItem::section('Отчеты');
        // yield MenuItem::linkToRoute('Статистика', 'fas fa-chart-bar', 'report_stats');

        yield MenuItem::section('Настройки');
        yield MenuItem::linkToRoute('Профиль', 'fas fa-id-card', 'user_profile'); // Предполагаемый маршрут профиля
        yield MenuItem::linkToLogout('Выход', 'fas fa-sign-out-alt', 'main'); // Укажите имя firewall
    }
}