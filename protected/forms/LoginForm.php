<?php

namespace app\forms;

use Yii;
use yii\base\Model;
use app\models\User;

/**
 * Форма авторизации пользователей
 *
 * @package app\forms
 *
 */
class LoginForm extends Model
{
    /**
     * Email адрес
     *
     * @var string
     */
    public $email;

    /**
     * Пароль пользователя
     *
     * @var string
     */
    public $password;

    /**
     * Параметр для запоминания пользователя
     *
     * @var bool
     */
    public $rememberMe = true;

    /**
     * Объект данных пользователя
     *
     * @var User|false
     */
    private $_user = false;

    /**
     * Метод возвращает заголовки для аттрибутов.
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'email' => 'Email',
            'password' => 'Пароль',
            'rememberMe' => 'Запомнить',
        ];
    }

    /**
     * Определение правил валидации
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            [['email'], 'email'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Валидация введенных данных
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неправильный логин или пароль');
            }
        }
    }

    /**
     * Авторизация пользователя
     *
     * @return bool
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    /**
     * Получение данных пользователя
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUserEmail($this->email);
        }

        return $this->_user;
    }
}
