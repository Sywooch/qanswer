<?php

class EditorhelpController extends Controller
{
	public $layout='column1';

	/**
	 * @var CActiveRecord the currently loaded data model instance.
	 */
	private $_model;

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', // allow authenticated users to access all actions
				'users'=>array('*'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex()
	{
		$this->pageTitle = "markdown语法说明和帮助";
		$this->pageDescription = "Markdown 是一种轻量级的标记语言，最初由John Gruber 和 Aaron Swartz 创建，用于允许人们“以一种书写便利，容易阅读的纯文件格式来书写，然后将之转换为结构化的标准XHTML或HTML。下面是一份完整的Markdown语法说明列表";
		$this->render('index');
	}
}
?>