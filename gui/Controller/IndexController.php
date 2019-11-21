<?php

namespace App\User\Gui\Controller;

use App\Common\Controller\ControllerBaseUser;

/**
 * Обязательная главная страница для каждого пользователя
 */
class IndexController extends ControllerBaseUser
{
    /**
     * Main page
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->pick('index/index');
    }
}
