<?php

namespace app\components;

use yii\web\Controller;
use app\models\User;
use Yii;

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class BaseController extends Controller
{

    /**
     * @var string the default layout for the controller view. Defaults to 'column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = 'column1';

    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();

    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = array();
    public $me = NULL;
    public $title = "";

    /**
     * 当前时间
     * @var int
     */
    public $time = 0;

    /**
     * @var 提交表单校验变量
     */
    public $fkey = '';
    public $options = array();
    public $pageDescription;

    public function init()
    {
        parent::init();

        if (!Yii::$app->user->isGuest) {
            $uid = Yii::$app->user->getId();
            $this->me = User::findOne($uid);
            $this->time = time();
        }
//		$this->options = Yii::app()->cache->get('options');
//		if ($this->options == false) {
//			$models = Options::model()->findAll();
//			$options = array();
//			foreach($models as $t){
//				$options[$t->name] = $t->value;
//			}
//			Yii::app()->cache->set('options',$options,0);
//			$this->options = $options;
//		}
//		Yii::app()->params['mail'] = $this->options['mail'];
//		Yii::app()->params['timeoffset'] = $this->options['timeoffset'];
//
//		$this->checkInbox();
        $this->title = "";
    }

    public function checkInbox()
    {
        if (!Yii::app()->user->isGuest && !isset(Yii::app()->request->cookies['newmessages']->value)) {
            $uid = Yii::app()->user->getId();
            $newmessages = Inbox::model()->count("uid=:uid AND isnew=1", array(":uid" => $uid));
            if ($this->me->messagecount != $newmessages) {
                $this->me->messagecount = $newmessages;
                $this->me->update(array('messagecount'));
            }

            $cookie = new CHttpCookie('newmessages', 1);
            $cookie->expire = time() + 30;
            Yii::app()->request->cookies['newmessages'] = $cookie;
        }
    }

    public function beforeAction($action)
    {
//		if ($this->options['close'] && (($this->me && !$this->me->isAdmin()) || $this->me==null)) {
//			if (!($this->id == 'users' && $this->action->id=='login')) {
//				$this->render('/common/close');exit;
//			}
//		}
        parent::beforeAction($action);

        return true;
    }

}
