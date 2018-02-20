<?php

namespace app\controllers;

use yii\web\Controller;
use yii\web\Response;
use app\forms\ApiForm;
use yii\filters\AccessControl;

class ApiController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                    'create',
                    'delete',
                    'change-status',
                    'clear-completed',
                    'change-name'
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'create',
                            'delete',
                            'change-status',
                            'clear-completed',
                            'change-name'
                        ],
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function() {
                    return $this->addError('Нужно авторизироваться', 401);
                },
            ],
        ];
    }

    public function actionCreate()
    {
        $request = \Yii::$app->request;

        $form = new ApiForm;
        $form->setScenario($form::SCENARIO_CREATE);
        $form->setAttributes($request->post());

        if(!$form->validate() || !$model = $form->createItem()) {
            return $this->addError('Не удалось сохранить данные');
        }

        return $this->renderJson([
            'data' => [
                'status' => 'success',
                'id' => $model->id,
            ]
        ]);
    }

    public function actionDelete()
    {
        $request = \Yii::$app->request;
        $form = new ApiForm;
        $form->setScenario($form::SCENARIO_DELETE);
        $form->setAttributes($request->post());

        if(!$form->validate() || !$form->deleteItem()) {
            return $this->addError('Данной записи не существует', 404);
        }

        return $this->renderJson([
            'data' => [
                'status' => 'success'
            ]
        ]);
    }

    public function actionChangeStatus()
    {
        $request = \Yii::$app->request;
        $form = new ApiForm;
        $form->setScenario($form::SCENARIO_STATUS);
        $form->setAttributes($request->post());

        if(!$form->validate() || !$form->changeStatus()) {
            return $this->addError('Данной записи не существует', 404);
        }

        return $this->renderJson([
            'data' => [
                'status' => 'success'
            ]
        ]);
    }

    public function actionClearCompleted()
    {
        if(!(new ApiForm)->deleteCompletedTasks()) {
            return $this->addError('Не удалось очистить данные');
        }

        return $this->renderJson([
            'data' => [
                'status' => 'success'
            ]
        ]);
    }

    public function actionChangeName()
    {
        $request = \Yii::$app->request;
        $form = new ApiForm;
        $form->setScenario($form::SCENARIO_CHANGE_NAME);
        $form->setAttributes($request->post());

        if(!$form->validate() || !$form->changeName()) {
            return $this->addError('Не удалось сохранить данные');
        }

        return $this->renderJson([
            'data' => [
                'status' => 'success'
            ]
        ]);
    }

    /**
     * Метод формирует отрицательный ответ пользователю в виде JSON данных.
     *
     * @param mixed $error данные для формирования тела ошибки.
     * @param integer $statusCode код ответа сервера.
     *
     * @return Response
     */
    public function addError($error, $statusCode = 400)
    {
        return $this->renderJson(['error' => $error], $statusCode);
    }

    /**
     * Метод формирует ответ пользователю в виде JSON данных.
     *
     * @param array $params данные для формирования тела.
     * @param integer $statusCode код ответа сервера.
     *
     * @return Response
     */
    public function renderJson($params = null, $statusCode = 200)
    {
        $response          = \Yii::$app->response;
        $response->format  = Response::FORMAT_JSON;
        $response->statusCode = $statusCode;
        $response->content = json_encode($params);
        return $response;
    }
}
