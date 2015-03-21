<?php
namespace app\modules\question\controllers;
use app\components\BaseController;

use app\models\Revision;

class RevisionsController extends BaseController
{

    public $layout = '/column1';


    public function actionSource()
    {
        $revision = Revision::find()->where(['id' => \Yii::$app->request->get('id')])->one();
        echo $this->renderPartial('source', ['data' => $revision]);
    }

}
