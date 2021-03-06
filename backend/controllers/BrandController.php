<?php

class BrandController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='/layouts/column2';

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
			array('allow', 'roles'=>array('content')),
			array('deny', 'users'=>array('*')),
		);
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Brand;

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Brand']))
		{
			$model->attributes=$_POST['Brand'];
			if($model->save()) {
                Yii::app()->user->setFlash('success', "Бренд добавлен");
                $this->redirect(array('update','id'=>$model->id));
            } else {
                Yii::app()->user->setFlash('error', "Бренд не добавлен");
            }
		}
        else if(isset($_GET['Brand']))
        {
            $model->attributes=$_GET['Brand'];
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
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Brand']))
		{
			$model->attributes=$_POST['Brand'];
			if($model->save()) {
                Yii::app()->user->setFlash('success', "Изменения сохранены");
                $this->redirect(array('update','id'=>$model->id));
            } else {
                Yii::app()->user->setFlash('error', "Изменения не сохранены");
            }
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

    public function actionAutoComplete($term) {
        $criteria=new CDbCriteria;
        if(strpos($term, ',')===false) {
            $criteria->addSearchCondition('t.name', $term);
        } else {
            $terms=array_map("trim", explode(',',$term));
            $criteria->addSearchCondition('t.name', array_pop($terms));
            if(count($terms))
                $criteria->addNotInCondition('t.name', $terms);
        }

        if(array_key_exists('limit',$_GET))
            $criteria->limit=max(1,$_GET['limit']);
        else
            $criteria->limit=5;

        if(array_key_exists('without',$_GET))
            $criteria->compare('t.id', '<>'.$_GET['without']);

        $brands=Brand::model()->findAll($criteria);
        $json=array();
        foreach($brands as $brand) {
            array_push($json, array(
                'id'=>$brand->id,
                'value'=>$brand->name,
                'label'=>$brand->name,
            ));
        }
        echo function_exists('json_encode')?json_encode($json):CJSON::encode($json);
        Yii::app()->end();
    }

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$model=$this->loadModel($id);
            Yii::app()->user->setFlash('success', "Бренд &quot;{$model->name}&quot; удален");
            $model->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex() {
		$this->render('index',array(
			'letters'=>Brand::model()->findAllGroupByFirstLetters(),
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Brand::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='brand-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
