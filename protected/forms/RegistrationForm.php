<?php

namespace app\forms;

use Yii;
use yii\base\Model;
use app\models\User;
use yii\base\UserException;

/**
 * Форма для регистрации пользователей
 *
 * @package app\forms
 *
 */
class RegistrationForm extends Model
{
    /**
     * Email адрес
     *
     * @var string
     */
    public $email;

    /**
     * Пароль
     *
     * @var string
     */
    public $password;

    /**
     * Подтверждение пароля
     *
     * @var string
     */
    public $confirm;

    /**
     * Соль для генерации хэша на подтверждение регистрации
     *
     * @var string
     */
    public $signature = 'TestGenerationCode %s CheckConfirmationHash %s';

    /**
     * Используемый алгоритм вычисления хэша
     *
     * @var string
     */
    public $hashAlgorithm = 'gost-crypto';

    /**
     * URL адрес подтверждения email.
     *
     * @var string
     */
    public $confirmEmailURL = '/registration/confirm';

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
            'confirm' => 'Повторите паполь',
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
            [['email', 'password', 'confirm'], 'required'],
            [['email'], 'email'],
            [
                ['password'],
                'string',
                'min' => 4,
            ],
            [
                'email',
                'unique',
                'targetClass' => User::class,
                'message' => 'Данные email адрес уже занят'
            ],
            [
                ['confirm'],
                'compare',
                'compareAttribute' => 'password',
                'message' => 'Паполи должны совпадать'
            ],
        ];
    }

    /**
     * Регистрация пользователя
     *
     * @return bool
     */
    public function registration()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->createUserProfile();
        $hash = $this->getHash($user);
        $this->sendingEmailToConfirm($user, $hash);

        return true;
    }

    /**
     * Метод генерирует хэш для подтверждения регистрации пользователя
     *
     * @param User $user данные пользователя
     *
     * @return null|string
     */
    public function getHash(User $user)
    {
        return hash($this->hashAlgorithm, sprintf($this->signature, $user->getId(), $user->getAuthKey()));
    }

    /**
     * Активация пользователя
     *
     * @param int $id ид пользователя
     * @param string $hash хэш для потверждения email адреса
     *
     * @throws UserException ошибка при сохранении данных
     *
     * @return bool
     */
    public function confirmEmail($id, $hash)
    {
        $user = User::findOne(['id' => $id, 'isActivate' => 0]);

        if(null === $user) {
            return false;
        }

        if($hash === $this->getHash($user)) {
            $user->setAttribute('isActivate', 1);

            if(!$user->save()) {
                throw new UserException('Can not save User');
            }

            return true;
        }

        return false;
    }

    /**
     * Активация пользователя
     *
     * @param User $user данные пользователя
     * @param string $hash хэш для потверждения email адреса
     *
     * @return void
     */
    protected function sendingEmailToConfirm(User $user, $hash)
    {
        $url = \Yii::$app->getUrlManager()->createAbsoluteUrl([
            $this->confirmEmailURL,
            'id'   => $user->id,
            'hash' => $hash,
        ]);

        Yii::$app->mailer->compose()
            ->setTo($this->email)
            ->setFrom(['testmailerdrom@yandex.ru' => 'Tester'])
            ->setSubject('Подтверждение почты')
            ->setTextBody('Ссылка подтверждения: '. $url)
            ->send();
    }

    /**
     * Метод создает профиль пользователя
     *
     * @throws UserException ошибка при сохранении данных
     *
     * @return User
     */
    protected function createUserProfile()
    {
        $user = new User();
        $user->setAttribute('email', $this->email);
        $user->setAttribute('passwordHash', Yii::$app->getSecurity()->generatePasswordHash($this->password));

        if (!$user->save()) {
            throw new UserException('Can not save User');
        }

        return $user;
    }
}