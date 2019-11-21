<?php

namespace App\User\Gui\Controller;

use App\Common\Model\User;
use App\User\Gui\Form\UserEditForm;
use App\Common\Controller\ControllerBaseUser;
use Phalcon\Http\Response;

/**
 * Управление пользователями
 */
class UserController extends ControllerBaseUser
{
    /**
     * Переменная с данными о конфиге.
     *
     * @var array
     */
    private $result = [];

    /**
     * Переменная с данными о необходимости выводить дебаг.
     *
     * @var array
     */
    private $debug = true;

    /**
     * Переменная с данными о необходимости выводить log_level.
     *
     * @var array
     */
    private $logLevel = true;

    /**
     * список пользователей
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->setVar(
            'users',
            User::find(
                [
                    [],
                    'sort' => ['name' => 1],
                ]
            )
        );
        $this->view->pick('user/index');
    }

    /**
     * редактирование пользователя
     *
     * @param $userId
     *
     * @throws \Exception User does not exist
     * @return Response
     */
    public function editAction($userId = null)
    {
        if ($userId) {
            $user = User::findById($userId);
        } else {
            $user = new User();
        }

        if (!$user) {
            throw new \Exception('User does not exist.', 403);
        }

        $form = new UserEditForm($user);

        if ($this->request->isPost()) {
            $form->bind($this->request->getPost(), $user);
            if ($form->isValid()) {
                if (!$user->hash) {
                    $user->generateHash();
                }

                $user->pass = $this->security->hash($user->pass);
                $user->save();

                return $this->response->redirect('/user/index');
            }
        }

        $this->view->form = $form;
        $this->view->pick('user/edit');
    }

    /**
     * Метод для редактирования конфига.
     *
     * @param null $userId
     *
     * @return Response
     */
    public function configAction($userId = null)
    {
        $debug    = null;
        $logLevel = null;
        $configs  = [];

        // Получаем пользователя.
        $user = User::findById($userId);

        // Сохранение конфига.
        if ($this->request->isPost()) {
            if (!is_array($user->config)) {
                $user->config = [];
            }

            // Получение POST.
            $post = $this->request->getPost();

            // Записываем новые параметры.
            $this->checkConfig($post, $user);

            if ($user->save()) {
                return $this->response->redirect('/user/index');
            } else {
                $this->flash->error('Сохранение не удалось =(');
            }
        }

        // устанавливаем папку с конфигом
        $path = APP_PATH . '/user/' . $user->name . '/resource/config/setting.php';

        // получаем конфиг из файла
        if (is_readable($path)) {
            $configs = require $path;
        }

        // проверяем на пустоту
        if (!is_array($configs)) {
            $configs = [];
        }

        // получаем конфиг из монги
        $mongoConfig = $user->config;

        if (is_array($mongoConfig)) {
            $configs = array_replace_recursive($configs, $mongoConfig);
        }

        // Преобразуем конфиг в список.
        $this->pathConfig($configs);

        $this->view->setVar('configs', $this->result);
        $this->view->setVar('debug', $this->debug);
        $this->view->setVar('logLevel', $this->logLevel);
        $this->view->pick('user/config');
    }

    /**
     * @param $userId
     */
    public function dataAction($userId)
    {
        // Находим пользователя по ID
        $user = User::findById($userId);

        // Сохраняем ID
        $userId = $user->_id;
        // Подменяем ID на плейсхолдер
        $user->_id = "_&ID&_";
        // Сериализуем в JSON
        $json = json_encode($user, JSON_PRETTY_PRINT);

        // Задаем регекс паттерн и меняем по нему на нужный формат
        $pattern = '/"_&ID&_",?/';
        $json    = preg_replace($pattern, "ObjectId(\"$userId\"),", $json);

        // Убираем запятую перед последней фигурной скобкой, если она там есть
        $pattern = '/,\n}/';
        $json    = preg_replace($pattern, "\n}", $json);

        // Передаем в вьюху
        $this->view->setVar('user', $json);
        $this->view->pick('user/data');
    }

    /**
     * Метод для обработки POST запроса.
     *
     * @param $post
     * @param $user
     *
     * @return bool
     */
    private function checkConfig($post, &$user)
    {
        foreach ($post as $configPath => $configVal) {
            $configPath = ltrim($configPath, '/');
            $configPath = explode('/', $configPath);

            $user->config = array_replace_recursive(
                $user->config,
                $this->saveConfig(
                    $configPath,
                    $configVal
                )
            );
        }
    }

    /**
     * Метод для обработки формы из редактирования конфига.
     *
     * @param       $keys
     * @param       $val
     * @param array $newArr
     * @param int   $iter
     *
     * @return array
     */
    private function saveConfig($keys, $val, $newArr = [], $iter = 0)
    {
        $keyNow = $keys[$iter];
        $iter   += 1;

        // Специальная проверка для дебага
        if ($keys[0] == 'debug' && !empty($val) && $val != 'false') {
            $val = true;
        } elseif ($keys[0] == 'debug') {
            $val = false;
        }

        if (count($keys) == $iter) {
            $newArr[$keyNow] = $val;
        } else {
            $newArr[$keyNow] = $newArr + $this->saveConfig($keys, $val, $newArr, $iter);
        }

        return $newArr;
    }

    /**
     * @param array       $arr
     * @param null|string $path
     *
     * @return string - на самом деле, все находится в $this->$result.
     */
    private function pathConfig(array $arr, $path = null)
    {
        foreach ($arr as $k => $value) {
            if (is_array($value)) {
                $path = $path . '/' . $k;
                $path = $this->pathConfig($value, $path);
            } else {
                if ($path . '/' . $k == '/debug') {
                    $this->debug = false;
                } elseif ($path . '/' . $k == '/log_level') {
                    $this->logLevel = false;
                }

                $this->result[] = [
                    'config_name'  => $path . '/' . $k,
                    'config_value' => $value,
                ];
            }
        }

        return substr($path, 0, strripos($path, '/'));
    }
}