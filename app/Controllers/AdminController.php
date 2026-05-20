<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\HttpException;
use App\Core\Request;

final class AdminController extends BaseController
{
    private const LOGIN = 'admin';
    private const PASS  = 'admin';

    /**
     * Редиректит на /admin/login, если пользователь не авторизован.
     * Вызывается в начале каждого защищённого действия.
     */
    private function requireAuth(): void
    {
        if (empty($_SESSION['admin'])) {
            header('Location: /admin/login');
            exit;
        }
    }

    /**
     * Показывает форму входа.
     * Если сессия уже активна — сразу редиректит в дашборд.
     */
    public function loginForm(Request $request, array $params): void
    {
        if (!empty($_SESSION['admin'])) {
            header('Location: /admin');
            exit;
        }

        $this->view->render('admin/login', ['error' => null]);
    }

    /**
     * Обрабатывает POST-данные формы входа.
     * Логин/пароль проверяются через жёстко заданные константы (static credentials).
     */
    public function login(Request $request, array $params): void
    {
        $login = $request->getPost('login', '');
        $pass  = $request->getPost('password', '');

        if ($login === self::LOGIN && $pass === self::PASS) {
            $_SESSION['admin'] = true;
            header('Location: /admin');
            exit;
        }

        $this->view->render('admin/login', ['error' => 'Неверный логин или пароль']);
    }

    /**
     * Уничтожает сессию и редиректит на страницу входа.
     */
    public function logout(Request $request, array $params): void
    {
        session_destroy();
        header('Location: /admin/login');
        exit;
    }

    /**
     * Главная страница админки — таблица всех статей.
     */
    public function dashboard(Request $request, array $params): void
    {
        $this->requireAuth();

        $this->view->render('admin/dashboard', [
            'posts' => $this->postService->getAllPosts(),
        ]);
    }

    /**
     * Показывает пустую форму создания статьи.
     */
    public function createForm(Request $request, array $params): void
    {
        $this->requireAuth();

        $this->view->render('admin/posts/create', [
            'categories' => $this->categoryService->getAllCategories(),
            'errors'     => [],
            'old'        => [],
        ]);
    }

    /**
     * Обрабатывает создание статьи.
     * При ошибках валидации перерендеривает форму с сохранением введённых данных.
     * При успехе редиректит на публичную страницу созданной статьи.
     */
    public function create(Request $request, array $params): void
    {
        $this->requireAuth();

        $title       = trim((string) $request->getPost('title', ''));
        $description = trim((string) $request->getPost('description', ''));
        $content     = trim((string) $request->getPost('content', ''));
        $image       = trim((string) $request->getPost('image', '')) ?: null;
        $categoryIds = (array) $request->getPost('categories', []);

        $errors = [];

        if ($title === '') {
            $errors[] = 'Заголовок обязателен';
        }
        if ($description === '') {
            $errors[] = 'Краткое описание обязательно';
        }
        if ($content === '') {
            $errors[] = 'Текст статьи обязателен';
        }

        if (!empty($errors)) {
            $this->view->render('admin/posts/create', [
                'categories' => $this->categoryService->getAllCategories(),
                'errors'     => $errors,
                'old'        => compact('title', 'description', 'content', 'image', 'categoryIds'),
            ]);
            return;
        }

        $postId = $this->postService->createPost($title, $description, $content, $image, $categoryIds);

        header("Location: /post/{$postId}");
        exit;
    }

    /**
     * Показывает форму редактирования статьи с предзаполненными полями.
     * selectedCategories — ID уже привязанных категорий, используются для чекбоксов.
     *
     * @throws HttpException 404 если статья не найдена
     */
    public function editPostForm(Request $request, array $params): void
    {
        $this->requireAuth();

        $id   = (int) $params['id'];
        $post = $this->postService->findById($id);

        if ($post === null) {
            throw new HttpException(404);
        }

        $this->view->render('admin/posts/edit', [
            'post'               => $post,
            'categories'         => $this->categoryService->getAllCategories(),
            'selectedCategories' => $this->categoryService->getCategoryIdsByPostId($id),
            'errors'             => [],
        ]);
    }

    /**
     * Обрабатывает обновление статьи.
     * При ошибках — перерендеривает форму с сообщениями.
     * При успехе — редиректит обратно в список статей.
     *
     * @throws HttpException 404 если статья не найдена
     */
    public function updatePost(Request $request, array $params): void
    {
        $this->requireAuth();

        $id          = (int) $params['id'];
        $title       = trim((string) $request->getPost('title', ''));
        $description = trim((string) $request->getPost('description', ''));
        $content     = trim((string) $request->getPost('content', ''));
        $image       = trim((string) $request->getPost('image', '')) ?: null;
        $categoryIds = (array) $request->getPost('categories', []);

        $errors = [];

        if ($title === '') {
            $errors[] = 'Заголовок обязателен';
        }
        if ($description === '') {
            $errors[] = 'Краткое описание обязательно';
        }
        if ($content === '') {
            $errors[] = 'Текст статьи обязателен';
        }

        if (!empty($errors)) {
            $post = $this->postService->findById($id);
            $this->view->render('admin/posts/edit', [
                'post'               => $post,
                'categories'         => $this->categoryService->getAllCategories(),
                'selectedCategories' => $categoryIds,
                'errors'             => $errors,
            ]);
            return;
        }

        $this->postService->updatePost($id, $title, $description, $content, $image, $categoryIds);

        header("Location: /admin");
        exit;
    }

    // ── Categories ────────────────────────────────────────────────────────────

    /**
     * Список всех категорий в админке.
     */
    public function categoriesList(Request $request, array $params): void
    {
        $this->requireAuth();

        $this->view->render('admin/categories/index', [
            'categories' => $this->categoryService->getAllCategories(),
        ]);
    }

    /**
     * Показывает пустую форму создания категории.
     */
    public function createCategoryForm(Request $request, array $params): void
    {
        $this->requireAuth();

        $this->view->render('admin/categories/form', [
            'category' => null,
            'errors'   => [],
            'old'      => [],
        ]);
    }

    /**
     * Обрабатывает создание категории.
     * Форма create и edit используют один шаблон admin/categories/form.tpl.
     */
    public function createCategory(Request $request, array $params): void
    {
        $this->requireAuth();

        ['name' => $name, 'description' => $description, 'errors' => $errors] =
            $this->validateCategoryInput($request);

        if (!empty($errors)) {
            $this->view->render('admin/categories/form', [
                'category' => null,
                'errors'   => $errors,
                'old'      => ['name' => $name, 'description' => $description],
            ]);
            return;
        }

        $this->categoryService->createCategory($name, $description);

        header('Location: /admin/categories');
        exit;
    }

    /**
     * Показывает форму редактирования категории с предзаполненными данными.
     *
     * @throws HttpException 404 если категория не найдена
     */
    public function editCategoryForm(Request $request, array $params): void
    {
        $this->requireAuth();

        $id       = (int) $params['id'];
        $category = $this->categoryService->findById($id);

        if ($category === null) {
            throw new HttpException(404);
        }

        $this->view->render('admin/categories/form', [
            'category' => $category,
            'errors'   => [],
            'old'      => [],
        ]);
    }

    /**
     * Обрабатывает обновление категории.
     *
     * @throws HttpException 404 если категория не найдена
     */
    public function updateCategory(Request $request, array $params): void
    {
        $this->requireAuth();

        $id       = (int) $params['id'];
        $category = $this->categoryService->findById($id);

        if ($category === null) {
            throw new HttpException(404);
        }

        ['name' => $name, 'description' => $description, 'errors' => $errors] =
            $this->validateCategoryInput($request);

        if (!empty($errors)) {
            $this->view->render('admin/categories/form', [
                'category' => $category,
                'errors'   => $errors,
                'old'      => ['name' => $name, 'description' => $description],
            ]);
            return;
        }

        $this->categoryService->updateCategory($id, $name, $description);

        header('Location: /admin/categories');
        exit;
    }

    /**
     * Общая валидация полей формы категории (create и edit).
     * Вынесена в приватный метод, чтобы не дублировать код в двух контроллерных методах.
     *
     * @return array{name: string, description: string, errors: string[]}
     */
    private function validateCategoryInput(Request $request): array
    {
        $name        = trim((string) $request->getPost('name', ''));
        $description = trim((string) $request->getPost('description', ''));
        $errors      = [];

        if ($name === '') {
            $errors[] = 'Название обязательно';
        }

        return ['name' => $name, 'description' => $description, 'errors' => $errors];
    }
}
