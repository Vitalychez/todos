<?php

namespace app\models;

use yii\db\ActiveRecord;

class Todo extends ActiveRecord
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
        if (!$insert) {
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
        return '{{%todo}}';
    }
}