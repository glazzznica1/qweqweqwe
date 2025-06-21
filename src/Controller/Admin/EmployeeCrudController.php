<?php

namespace App\Controller\Admin;

use App\Entity\Employee;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField; // Для фото
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField; // Для должности, если она может быть длинной
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class EmployeeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Employee::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm(); // Скрываем ID при создании и редактировании
        yield TextField::new('firstName', 'Имя');
        yield TextField::new('lastName', 'Фамилия');
        yield TextField::new('position', 'Должность');
        yield DateField::new('hireDate', 'Дата приема на работу');
        yield TextField::new('department', 'Отдел');

        // Конфигурация для поля photo
        yield TextField::new('photo', 'Ссылка на фото')
            ->onlyOnForms() // Показывать только в формах создания и редактирования
            ->setHelp('Введите URL изображения');

        yield ImageField::new('photo', 'Фото')
            ->setBasePath('/uploads/employees/') // Путь к каталогу с изображениями (от public/)
            ->setUploadDir('public/uploads/employees/') // Каталог для загрузки изображений
            ->setUploadedFileNamePattern('[randomhash].[extension]') // Шаблон имени файла
            ->onlyOnIndex() // Показывать только в списке
            ->setSortable(false); // Отключаем сортировку по этому полю в списке

        // Возможный альтернативный вариант для должности, если нужно много текста:
        // yield TextareaField::new('position', 'Должность');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Сотрудник')
            ->setEntityLabelInPlural('Сотрудники')
            ->setDefaultSort(['lastName' => 'ASC', 'firstName' => 'ASC']);
    }
}