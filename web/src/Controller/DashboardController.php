<?php

namespace App\Controller;

use App\Core\AuthManager;
use App\Core\Message;
use App\Core\Routing\Router;
use App\Core\Settings;
use App\Core\View;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use App\Services\CsvExporter;
use App\Services\CsvImporter;
use App\Services\RedisHelper;

class DashboardController
{
    /**
     * @param AuthManager    $authManager
     * @param BookRepository $bookRepository
     * @param UserRepository $userRepository
     * @param CsvExporter    $csvExporter
     * @param CsvImporter    $csvImporter
     * @param View           $view
     * @param RedisHelper    $redisHelper
     */
    public function __construct(
        private AuthManager $authManager,
        private BookRepository $bookRepository,
        private UserRepository $userRepository,
        private CsvExporter $csvExporter,
        private CsvImporter $csvImporter,
        private View $view,
        private RedisHelper $redisHelper
    ) {
    }

    public const string TEMPLATE_NAME = 'admin/dashboard';

    /**
     * @throws \Exception
     */
    public function index($params): void
    {

        if (isset($params['clearCache']) && $params['clearCache'] == 1) {
            $this->redisHelper->clearCacheAll();
            Message::setMessage('Cache vidé', 'success');
        }
        if (!$this->authManager->isAdmin()) {
            Router::accessDenied();
        }
        if (!empty($params['files']['csvFile'])) {
            $books = $this->csvImporter->process($params['files']['csvFile']);
            foreach ($books as $book) {
                $this->bookRepository->insertBook($book);
            }
        }
        $this->view->render(
            Settings::getTemplatePath(self::TEMPLATE_NAME),
            [
                'isAdmin' => $this->authManager->isAdmin(),
                'isLoggedIn' => $this->authManager->isLoggedIn(),
                'nbUsers' => $this->userRepository->countAll(),
                'nbBooks' => $this->bookRepository->countAll(),
                'monthlyBooks' => $this->bookRepository->getMonthlyBooks(),
                'userEditUrl' =>  isset($_SESSION['user_id']) ?
                    'user/' . $_SESSION['user_id'] . '/edit' : ''
            ]
        );
    }
}
