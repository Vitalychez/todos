<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\forms\RegistrationForm;

class RegistrationController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'confirm', 'success'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'confirm', 'success'],
                        'roles' => ['?'],
                    ],
                ],
                'denyCallback' => [
                    $this,
                    'goHome',
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $model = new RegistrationForm();
        if ($model->load(Yii::$app->request->post()) && $model->registration()) {
            return $this->redirect(['success']);
        }


        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionConfirm($id, $hash)
    {
        $model = new RegistrationForm();
        if ($model->confirmEmail($id, $hash)) {
            return $this->redirect('/login');
        }

        return $this->goHome();
    }

    public function actionSuccess()
    {
        return $this->render('success');
    }
}
