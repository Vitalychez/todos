<?php

namespace app\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\Todo;

class TodoController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function() {
                    return $this->redirect('/login');
                },
            ],
        ];
    }

    public function actionIndex()
    {
        $model = Todo::find()
            ->where(['userId' => \Yii::$app->user->getId()])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'data' =>  $model,
        ]);
    }
}
