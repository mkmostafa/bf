<?php

class ThreadController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';
	public $_category=null;

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
			'categoryContext + create,admin',
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
			array('allow',
				'actions'=>array('hotTopics'),
				'users'=>array('*'),
				),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','admin','delete','index','view','lock'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

public function actionHotTopics()
{	
	$categoriesID= Category::model()->findAll(
	array(
    'condition'=>'name=:name',
    'params'=>array(':name'=> 'Hot Topics'),
	));

	$model=Thread::model()->findAll(array(
    'condition'=>'category_id=:category_id',
    'params'=>array(':category_id'=> $categoriesID[0]->id,),
));


$gridDataProvider = new CArrayDataProvider($model);


	$this->render('HotTopics',array(
		'model'=>$model,
		'gridDataProvider'=>$gridDataProvider,



		));


}
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$commentDataProvider=new CActiveDataProvider('Comment',array(
			'criteria'=>array(
				'condition'=>'thread_id=:thread_id',
				'params'=>array(':thread_id'=>$this->loadModel($id)->id),
				),
			    'pagination'=>array('pageSize'=>100),
				));


		$this->render('view',array(
			'model'=>$this->loadModel($id),
			'commentDataProvider'=>$commentDataProvider,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		if(User::model()->findByPk(Yii::app()->user->id)->locked)
		{
			throw new CHttpException('You are locked due to offensive action');
		}
		$model=new Thread;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Thread']))
		{
			$model->user_id=Yii::app()->user->id;
			$model->created_at=new CDbExpression('Now()');
			$model->updated_at=new CDbExpression('Now()');
			$model->attributes=$_POST['Thread'];
			$model->category_id=$this->_category->id;
			if($model->save())
				$this->redirect(array('view','id'=>$model->id,'cid'=>$this->_category->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}



	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		if(!Yii::app()->user->checkAccess('admin'))
		{
			throw new CHttpException(403,'You are not authorized to perform this action.');
		}
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Thread']))
		{
			$model->attributes=$_POST['Thread'];
			$model->updated_at=new CDbExpression('Now()');
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	public function actionLock($id)
	{

		$model=$this->loadModel($id);
		if(!(Yii::app()->user->checkAccess('admin')||Yii::app()->user->id==$model->user_id))
		{
			throw new CHttpException(403,'You are not authorized to perform this action.');
		}	

		if(User::model()->findByPk(Yii::app()->user->id)->locked)
		{
			throw new CHttpException(403,'You are BANNED due to offensive actions.');
		}
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		
		$model->locked=1;
		if($model->save(false))
			$this->redirect(array('view','id'=>$model->id));
		

		$this->render('index',array(
			'model'=>$model,
		));
	}


	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(!Yii::app()->user->checkAccess('admin'))
		{
			throw new CHttpException(403,'You are not authorized to perform this action.');
		}
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	public function actionReport($id)
	{
		$emails = Yii::app()->db->createCommand("SELECT email
FROM (

SELECT *
FROM AuthAssignment
WHERE itemname = 'admin'
)admins
LEFT JOIN tbl_user ON admins.userid = tbl_user.id")->query();

		$date=new CDbExpression('Now()');
		//$reason
		//$link has to be changed depending on the server 
		$link='http://localhost/yii/breastfeeding/index.php?r=thread/view&id='.$id;
		$mailSubject='[BreastFeeding Forum] Thread reported';
		$mailContent=
'Dear Admin,
A thread on Breast Feeding forum has been reported on '.$date.' Please check the thread for more information.

Link: '.$link.' 

Breast Feeding Forum
This is an autogenerated message, please do not reply.';
	
		foreach ($emails as $email){
			$myemail = mail($email,$mailSubject,$mailContent);
			var_export($myemail);
		}			
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		if(!Yii::app()->user->checkAccess('admin'))
		{
			throw new CHttpException(403,'You are not authorized to perform this action.');
		}
		$model=new Thread('search');
		$model->unsetAttributes();  // clear any default values
		$model->category_id=$this->_category->id;
		if(isset($_GET['Thread']))
			$model->attributes=$_GET['Thread'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Thread::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	public function filterCategoryContext($filterChain)
	{
		$categoryId=null;
		if(isset($_GET['cid']))
		{
			$categoryId=$_GET['cid'];
		}
		else
		{
			if(isset($_POST['cid']))
			{
				$categoryId=$_POST['cid'];
			}
		}
		$this->loadCategory($categoryId);
		$filterChain->run();
	}

	protected function loadCategory($category_id)
	{
		if($this->_category===null)
		{
			$this->_category=Category::model()->findByPk($category_id);
			if($this->_category===null)
			{
				throw new CHttpException(404,'The requested category does not exist.');
			}
		}

		return $this->_category;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='thread-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
