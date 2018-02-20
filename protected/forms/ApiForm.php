<?php

namespace app\forms;

use yii\base\Model;
use app\models\Todo;
use yii\db\ActiveRecord;

/**
 * Форма валидации api
 *
 * @package app\forms
 *
 */
class ApiForm extends Model
{
    /**
     * Ид записи
     *
     * @var integer
     */
    public $itemId;

    /**
     * Ид пользователя
     *
     * @var integer
     */
    public $userId;

    /**
     * Пароль пользователя
     *
     * @var string
     */
    public $text;

    /**
     * Статус
     *
     * @var integer
     */
    public $status;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_CHANGE_NAME = 'change_name';
    const SCENARIO_DELETE = 'delete';
    const SCENARIO_STATUS = 'status';

    /**
     * Переопределенный метод инициализации
     *
     * @return void
     */
    public function init()
    {
        $this->userId = \Yii::$app->user->getId();

        parent::init();
    }

    /**
     * Определение правил валидации формы.
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['itemId'],
                'required',
                'on' => [
                    static::SCENARIO_DELETE,
                    static::SCENARIO_STATUS,
                    static::SCENARIO_CHANGE_NAME
                ]
            ],
            [['text'],
                'required',
                'on' => [
                    static::SCENARIO_CREATE,
                    static::SCENARIO_CHANGE_NAME
                ]
            ],
            [['status'],
                'required',
                'on' => [
                    static::SCENARIO_STATUS
                ]
            ],
            [['itemId', 'status'], 'integer'],
            [['text'], 'string']
        ];
    }

    /**
     * Создание новой записи
     *
     * @return bool|ActiveRecord
     */
    public function createItem()
    {
        $model = new Todo;
        $model->setAttribute('text', $this->text);
        $model->setAttribute('userId', $this->userId);

        if(!$model->save()) {
            return false;
        }

        return $model;
    }

    /**
     * Удаление выполненых задач
     *
     * @return mixed
     */
    public function deleteCompletedTasks()
    {
        return \Yii::$app->db->createCommand()
            ->delete(Todo::tableName(), ['userId' => $this->userId, 'status' => 1])->execute();
    }

    /**
     * Поиск записи
     *
     * @return null|ActiveRecord
     */
    protected function searchItem() {
        return Todo::find()->where(['id' => $this->itemId, 'userId' => $this->userId])->one();
    }

    /**
     * Удаление записи
     *
     * @return mixed
     */
    public function deleteItem()
    {
        if(null === $model = $this->searchItem()) {
            return false;
        }

        return $model->delete();
    }

    /**
     * Изменение статуса
     *
     * @return bool
     */
    public function changeStatus()
    {
        if(null === $model = $this->searchItem()) {
            return false;
        }

        $model->setAttribute('status', $this->status);

        if(!$model->save()) {
            return false;
        }

        return true;
    }

    /**
     * Изменение названия задачи
     *
     * @return bool
     */
    public function changeName()
    {
        if(null === $model = $this->searchItem()) {
            return false;
        }

        $model->setAttribute('text', $this->text);

        if(!$model->save()) {
            return false;
        }

        return true;
    }
}
