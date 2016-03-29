<?php
/**
 * UserController
 * @var $this UserController
 * @var $model InlisUsers
 * @var $form CActiveForm
 * version: 0.0.1
 * Reference start
 *
 * TOC :
 *	Index
 *	GetMember
 *	Generate
 *
 *	LoadModel
 *	performAjaxValidation
 *
 * @author Putra Sudaryanto <putra.sudaryanto@gmail.com>
 * @copyright Copyright (c) 2016 Ommu Platform (ommu.co)
 * @created date 29 March 2016, 17:15 WIB
 * @link http://company.ommu.co
 * @contect (+62)856-299-4114
 *
 *----------------------------------------------------------------------------------------------------------
 */

class UserController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	//public $layout='//layouts/column2';
	public $defaultAction = 'index';

	/**
	 * @return array action filters
	 */
	public function filters() 
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			//'postOnly + delete', // we only allow deletion via POST request
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
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','getmember','generate'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array(),
				'users'=>array('@'),
				'expression'=>'isset(Yii::app()->user->level)',
				//'expression'=>'isset(Yii::app()->user->level) && (Yii::app()->user->level != 1)',
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array(),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	/**
	 * Lists all models.
	 */
	public function actionIndex() 
	{
		$this->redirect(Yii::app()->createUrl('site/index'));
	}
	
	/**
	 * Lists all models.
	 */
	public function actionGetMember() 
	{
		if(Yii::app()->request->isPostRequest) {
			$criteria=new CDbCriteria;
			$criteria->compare('MemberNo',trim($_POST['membernumber']));
			
			$model = SyncMembers::model()->find($criteria);
			$return = '';
				
			if($model == null) {
				$return['success'] = '0';
				$return['error'] = 'NULL';
				$return['message'] = 'error, member tidak ditemukan';
			} else {
				if($model->users == null) {
					if($model->StatusAnggota == 'ACTIVE') {
						$return['success'] = '1';
						$return['message'] = 'success';
					} else {
						$return['success'] = '0';
						if($model->StatusAnggota == 'SUSPEND')
							$return['error'] = 'SUSPEND';
						if($model->StatusAnggota == 'PENDING')
							$return['error'] = 'PENDING';
						if($model->StatusAnggota == 'NOTACTIVE')
							$return['error'] = 'NOTACTIVE';
						$return['message'] = 'error, member sudah tidak berlaku';	
					}					
				} else {
					$return['success'] = '0';
					$return['error'] = 'ENABLE';
					$return['message'] = 'error, member online sudah terdaftar silahkan login';
				}
				$return['member_id'] = $model->ID;
				$return['fullname'] = ucwords(strtolower(trim($model->Fullname)));
				$return['birthday'] = $model->PlaceOfBirth != '' ? ucwords(strtolower(trim($model->PlaceOfBirth.', '.Utility::dateFormat($model->DateOfBirth)))) : Utility::dateFormat($model->DateOfBirth);
				$return['phone_number'] = $model->NoHp;
				$return['member_type'] = ucwords(strtolower(trim($model->JenisAnggota)));
			}
			echo CJSON::encode($return);
			
		} else
			$this->redirect(Yii::app()->createUrl('site/index'));
	}
	
	/**
	 * Lists all models.
	 */
	public function actionGenerate() 
	{
		if(Yii::app()->request->isPostRequest) {
			$email = trim($_POST['email']);
			$memberid = trim($_POST['memberid']);
			$displayname = trim($_POST['displayname']);
			
			$userFind = Users::model()->findByAttributes(array('email' => $email));
			if($userFind == null) {
				$memberFind = InlisUsers::model()->findByAttributes(array('member_id' => trim($_POST['memberid'])));
				if($memberFind == null) {
					$return['success'] = '1';
					$user=new Users;
					$user->email = $email;
					$user->displayname = $displayname;
					if($user->save()) {
						$userInlis=new InlisUsers;
						$userInlis->user_id = $user->user_id;
						$userInlis->member_id = $memberid;
						if($userInlis->save())
							$return['message'] = 'success';
					}
				} else {
					$return['success'] = '0';
					$return['error'] = 'ENABLE';
					$return['message'] = 'error, member online sudah terdaftar silahkan login';					
				}				
			} else {
				$return['success'] = '0';
				$return['error'] = 'NOTNULL';
				$return['message'] = 'error, email sudah terdaftar sebagai member';
			}
			echo CJSON::encode($return);
			
		} else
			$this->redirect(Yii::app()->createUrl('site/index'));
	}
	
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id) 
	{
		$arrThemes = Utility::getCurrentTemplate('public');
		Yii::app()->theme = $arrThemes['folder'];
		$this->layout = $arrThemes['layout'];
		Utility::applyCurrentTheme($this->module);
		
		$setting = VideoSetting::model()->findByPk(1,array(
			'select' => 'meta_keyword',
		));

		$model=$this->loadModel($id);

		$this->pageTitle = Yii::t('phrase', 'View Inlis Users');
		$this->pageDescription = '';
		$this->pageMeta = $setting->meta_keyword;
		$this->render('front_view',array(
			'model'=>$model,
		));
		/*
		$this->render('admin_view',array(
			'model'=>$model,
		));
		*/
	}	

	/**
	 * Manages all models.
	 */
	public function actionManage() 
	{
		$model=new InlisUsers('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['InlisUsers'])) {
			$model->attributes=$_GET['InlisUsers'];
		}

		$columnTemp = array();
		if(isset($_GET['GridColumn'])) {
			foreach($_GET['GridColumn'] as $key => $val) {
				if($_GET['GridColumn'][$key] == 1) {
					$columnTemp[] = $key;
				}
			}
		}
		$columns = $model->getGridColumn($columnTemp);

		$this->pageTitle = Yii::t('phrase', 'Inlis Users Manage');
		$this->pageDescription = '';
		$this->pageMeta = '';
		$this->render('admin_manage',array(
			'model'=>$model,
			'columns' => $columns,
		));
	}	
	
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionAdd() 
	{
		$model=new InlisUsers;

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['InlisUsers'])) {
			$model->attributes=$_POST['InlisUsers'];

			/* 
			$jsonError = CActiveForm::validate($model);
			if(strlen($jsonError) > 2) {
				//echo $jsonError;
				$errors = $model->getErrors();
				$summary['msg'] = "<div class='errorSummary'><strong>Please fix the following input errors:</strong>";
				$summary['msg'] .= "<ul>";
				foreach($errors as $key => $value) {
					$summary['msg'] .= "<li>{$value[0]}</li>";
				}
				$summary['msg'] .= "</ul></div>";

				$message = json_decode($jsonError, true);
				$merge = array_merge_recursive($summary, $message);
				$encode = json_encode($merge);
				echo $encode;

			} else {
				if(isset($_GET['enablesave']) && $_GET['enablesave'] == 1) {
					if($model->save()) {
						echo CJSON::encode(array(
							'type' => 5,
							'get' => Yii::app()->controller->createUrl('manage'),
							'id' => 'partial-inlis-users',
							'msg' => '<div class="errorSummary success"><strong>'.Yii::t('phrase', 'InlisUsers success created.').'</strong></div>',
						));
					} else {
						print_r($model->getErrors());
					}
				}
			}
			Yii::app()->end();
			*/

			if(isset($_GET['enablesave']) && $_GET['enablesave'] == 1) {
				if($model->save()) {
					Yii::app()->user->setFlash('success', Yii::t('phrase', 'InlisUsers success created.'));
					//$this->redirect(array('view','id'=>$model->id));
					$this->redirect(array('manage'));
				}
			}
		}

		$this->pageTitle = Yii::t('phrase', 'Create Inlis Users');
		$this->pageDescription = '';
		$this->pageMeta = '';
		$this->render('admin_add',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionEdit($id) 
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['InlisUsers'])) {
			$model->attributes=$_POST['InlisUsers'];

			/* 
			$jsonError = CActiveForm::validate($model);
			if(strlen($jsonError) > 2) {
				//echo $jsonError;
				$errors = $model->getErrors();
				$summary['msg'] = "<div class='errorSummary'><strong>Please fix the following input errors:</strong>";
				$summary['msg'] .= "<ul>";
				foreach($errors as $key => $value) {
					$summary['msg'] .= "<li>{$value[0]}</li>";
				}
				$summary['msg'] .= "</ul></div>";

				$message = json_decode($jsonError, true);
				$merge = array_merge_recursive($summary, $message);
				$encode = json_encode($merge);
				echo $encode;

			} else {
				if(isset($_GET['enablesave']) && $_GET['enablesave'] == 1) {
					if($model->save()) {
						echo CJSON::encode(array(
							'type' => 5,
							'get' => Yii::app()->controller->createUrl('manage'),
							'id' => 'partial-inlis-users',
							'msg' => '<div class="errorSummary success"><strong>'.Yii::t('phrase', 'InlisUsers success updated.').'</strong></div>',
						));
					} else {
						print_r($model->getErrors());
					}
				}
			}
			Yii::app()->end();
			*/

			if(isset($_GET['enablesave']) && $_GET['enablesave'] == 1) {
				if($model->save()) {
					Yii::app()->user->setFlash('success', Yii::t('phrase', 'InlisUsers success updated.'));
					//$this->redirect(array('view','id'=>$model->id));
					$this->redirect(array('manage'));
				}
			}
		}

		$this->pageTitle = Yii::t('phrase', 'Update Inlis Users');
		$this->pageDescription = '';
		$this->pageMeta = '';
		$this->render('admin_edit',array(
			'model'=>$model,
		));
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionRunAction() {
		$id       = $_POST['trash_id'];
		$criteria = null;
		$actions  = $_GET['action'];

		if(count($id) > 0) {
			$criteria = new CDbCriteria;
			$criteria->addInCondition('id', $id);

			if($actions == 'publish') {
				InlisUsers::model()->updateAll(array(
					'publish' => 1,
				),$criteria);
			} elseif($actions == 'unpublish') {
				InlisUsers::model()->updateAll(array(
					'publish' => 0,
				),$criteria);
			} elseif($actions == 'trash') {
				InlisUsers::model()->updateAll(array(
					'publish' => 2,
				),$criteria);
			} elseif($actions == 'delete') {
				InlisUsers::model()->deleteAll($criteria);
			}
		}

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax'])) {
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('manage'));
		}
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id) 
	{
		$model=$this->loadModel($id);
		
		if(Yii::app()->request->isPostRequest) {
			// we only allow deletion via POST request
			if(isset($id)) {
				if($model->delete()) {
					echo CJSON::encode(array(
						'type' => 5,
						'get' => Yii::app()->controller->createUrl('manage'),
						'id' => 'partial-inlis-users',
						'msg' => '<div class="errorSummary success"><strong>'.Yii::t('phrase', 'InlisUsers success deleted.').'</strong></div>',
					));
				}
			}

		} else {
			$this->dialogDetail = true;
			$this->dialogGroundUrl = Yii::app()->controller->createUrl('manage');
			$this->dialogWidth = 350;

			$this->pageTitle = Yii::t('phrase', 'InlisUsers Delete.');
			$this->pageDescription = '';
			$this->pageMeta = '';
			$this->render('admin_delete');
		}
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionPublish($id) 
	{
		$model=$this->loadModel($id);
		
		if($model->publish == 1) {
		//if($model->actived == 1) {
		//if($model->enabled == 1) {
		//if($model->status == 1) {
			$title = Yii::t('phrase', 'Unpublish');
			//$title = Yii::t('phrase', 'Deactived');
			//$title = Yii::t('phrase', 'Disabled');
			//$title = Yii::t('phrase', 'Unresolved');
			$replace = 0;
		} else {
			$title = Yii::t('phrase', 'Publish');
			//$title = Yii::t('phrase', 'Actived');
			//$title = Yii::t('phrase', 'Enabled');
			//$title = Yii::t('phrase', 'Resolved');
			$replace = 1;
		}

		if(Yii::app()->request->isPostRequest) {
			// we only allow deletion via POST request
			if(isset($id)) {
				//change value active or publish
				$model->publish = $replace;
				//$model->actived = $replace;
				//$model->enabled = $replace;
				//$model->status = $replace;

				if($model->update()) {
					echo CJSON::encode(array(
						'type' => 5,
						'get' => Yii::app()->controller->createUrl('manage'),
						'id' => 'partial-inlis-users',
						'msg' => '<div class="errorSummary success"><strong>'.Yii::t('phrase', 'InlisUsers success updated.').'</strong></div>',
					));
				}
			}

		} else {
			$this->dialogDetail = true;
			$this->dialogGroundUrl = Yii::app()->controller->createUrl('manage');
			$this->dialogWidth = 350;

			$this->pageTitle = $title;
			$this->pageDescription = '';
			$this->pageMeta = '';
			$this->render('admin_publish',array(
				'title'=>$title,
				'model'=>$model,
			));
		}
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionHeadline($id) 
	{
		$model=$this->loadModel($id);

		if(Yii::app()->request->isPostRequest) {
			// we only allow deletion via POST request
			if(isset($id)) {
				//change value active or publish
				$model->headline = 1;
				$model->publish = 1;

				if($model->update()) {
					echo CJSON::encode(array(
						'type' => 5,
						'get' => Yii::app()->controller->createUrl('manage'),
						'id' => 'partial-inlis-users',
						'msg' => '<div class="errorSummary success"><strong>'.Yii::t('phrase', 'InlisUsers success updated.').'</strong></div>',
					));
				}
			}

		} else {
			$this->dialogDetail = true;
			$this->dialogGroundUrl = Yii::app()->controller->createUrl('manage');
			$this->dialogWidth = 350;

			$this->pageTitle = Yii::t('phrase', 'Headline');
			$this->pageDescription = '';
			$this->pageMeta = '';
			$this->render('admin_headline');
		}
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id) 
	{
		$model = InlisUsers::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404, Yii::t('phrase', 'The requested page does not exist.'));
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model) 
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='inlis-users-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}