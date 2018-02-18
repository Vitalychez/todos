<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    /**
     * Предварительная инициализация данных.
     *
     * @param boolean $insert истина если создание нового токена.
     *
     * @return boolean
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->authKey = \Yii::$app->security->generateRandomString(32);
        } else {
            $this->updatedAt = (new \DateTime())->format('Y-m-d H:i:s');
        }

        return true;
    }

    /**
     * Определение таблицы где хранится модель.
     *
     * @return string
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * Поиск пользователя по ид
     *
     * @param int $id ид пользователя
     *
     * @return static|null
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }


    /**
     * Поиск пользователя по email
     *
     * @param string $email электронный адрес пользователя
     *
     * @return static|null
     */
    public static function findByUserEmail($email)
    {
        if(null !== $user = static::findOne(['email' => $email, 'isActivate' => 1])) {
            return $user;
        }

        return null;
    }

    /**
     * Получение ид пользователя
     *
     * @return mixed|null
     */
    public function getId()
    {
        return $this->getAttribute('id');
    }

    /**
     * Получение ключа авторизации пользователя
     *
     * @return mixed|null
     */
    public function getAuthKey()
    {
        return $this->getAttribute('authKey');
    }

    /**
     * Проверка ключа авторизации
     *
     * @return bool
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAttribute('authKey') === $authKey;
    }

    /**
     * Проверка пароля
     *
     * @return bool
     */
    public function validatePassword($password)
    {
        return \Yii::$app->getSecurity()->validatePassword($password, $this->getAttribute('passwordHash'));
    }

    public static function findIdentityByAccessToken($token, $type = null) {}
}
