<?php
/**
 * InlisWorksheetSub
 * version: 0.0.1
 *
 * @author Putra Sudaryanto <putra.sudaryanto@gmail.com>
 * @copyright Copyright (c) 2016 Ommu Platform (ommu.co)
 * @created date 29 March 2016, 09:53 WIB
 * @link http://company.ommu.co
 * @contact (+62)856-299-4114
 *
 * This is the template for generating the model class of a specified table.
 * - $this: the ModelCode object
 * - $tableName: the table name for this class (prefix is already removed if necessary)
 * - $modelClass: the model class name
 * - $columns: list of table columns (name=>CDbColumnSchema)
 * - $labels: list of attribute labels (name=>label)
 * - $rules: list of validation rules
 * - $relations: list of relations (name=>relation declaration)
 *
 * --------------------------------------------------------------------------------------
 *
 * This is the model class for table "akuisisi_worksheet".
 *
 * The followings are the available columns in table 'akuisisi_worksheet':
 * @property integer $ID
 * @property string $Name
 * @property integer $Main_Worksheet_ID
 *
 * The followings are the available model relations:
 * @property Akuisisi[] $akuisisis
 * @property AkuisisiLog[] $akuisisiLogs
 * @property AkuisisiMap $akuisisiMap
 * @property AkuisisiRaw[] $akuisisiRaws
 * @property AkuisisiRawLog[] $akuisisiRawLogs
 * @property Worksheets $mainWorksheet
 */
class InlisWorksheetSub extends OActiveRecord
{
	public $defaultColumns = array();

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return InlisWorksheetSub the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return CDbConnection the database connection used for this class
	 */
	public function getDbConnection()
	{
		return Yii::app()->inlis;
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'akuisisi_worksheet';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Main_Worksheet_ID', 'numerical', 'integerOnly'=>true),
			array('Name', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('ID, Name, Main_Worksheet_ID', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'akuisisis_relation' => array(self::HAS_MANY, 'Akuisisi', 'WorksheetID'),
			'akuisisiLogs_relation' => array(self::HAS_MANY, 'AkuisisiLog', 'WorksheetID'),
			'akuisisiMap_relation' => array(self::HAS_ONE, 'AkuisisiMap', 'WorksheetID'),
			'akuisisiRaws_relation' => array(self::HAS_MANY, 'AkuisisiRaw', 'WorksheetID'),
			'akuisisiRawLogs_relation' => array(self::HAS_MANY, 'AkuisisiRawLog', 'WorksheetID'),
			'mainWorksheet_relation' => array(self::BELONGS_TO, 'Worksheets', 'Main_Worksheet_ID'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'ID' => Yii::t('attribute', 'ID'),
			'Name' => Yii::t('attribute', 'Name'),
			'Main_Worksheet_ID' => Yii::t('attribute', 'Main Worksheet'),
		);
		/*
			'ID' => 'ID',
			'Name' => 'Name',
			'Main Worksheet' => 'Main Worksheet',
		
		*/
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('t.ID',$this->ID);
		$criteria->compare('t.Name',strtolower($this->Name),true);
		if(isset($_GET['Main']))
			$criteria->compare('t.Main_Worksheet_ID',$_GET['Main']);
		else
			$criteria->compare('t.Main_Worksheet_ID',$this->Main_Worksheet_ID);

		if(!isset($_GET['InlisWorksheetSub_sort']))
			$criteria->order = 't.ID DESC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>30,
			),
		));
	}


	/**
	 * Get column for CGrid View
	 */
	public function getGridColumn($columns=null) {
		if($columns !== null) {
			foreach($columns as $val) {
				/*
				if(trim($val) == 'enabled') {
					$this->defaultColumns[] = array(
						'name'  => 'enabled',
						'value' => '$data->enabled == 1? "Ya": "Tidak"',
					);
				}
				*/
				$this->defaultColumns[] = $val;
			}
		} else {
			//$this->defaultColumns[] = 'ID';
			$this->defaultColumns[] = 'Name';
			$this->defaultColumns[] = 'Main_Worksheet_ID';
		}

		return $this->defaultColumns;
	}

	/**
	 * Set default columns to display
	 */
	protected function afterConstruct() {
		if(count($this->defaultColumns) == 0) {
			/*
			$this->defaultColumns[] = array(
				'class' => 'CCheckBoxColumn',
				'name' => 'id',
				'selectableRows' => 2,
				'checkBoxHtmlOptions' => array('name' => 'trash_id[]')
			);
			*/
			$this->defaultColumns[] = array(
				'header' => 'No',
				'value' => '$this->grid->dataProvider->pagination->currentPage*$this->grid->dataProvider->pagination->pageSize + $row+1'
			);
			$this->defaultColumns[] = 'Name';
			$this->defaultColumns[] = 'Main_Worksheet_ID';
		}
		parent::afterConstruct();
	}

	/**
	 * User get information
	 */
	public static function getInfo($id, $column=null)
	{
		if($column != null) {
			$model = self::model()->findByPk($id,array(
				'select' => $column
			));
			return $model->$column;
			
		} else {
			$model = self::model()->findByPk($id);
			return $model;			
		}
	}

	/**
	 * before validate attributes
	 */
	/*
	protected function beforeValidate() {
		if(parent::beforeValidate()) {
			// Create action
		}
		return true;
	}
	*/

	/**
	 * after validate attributes
	 */
	/*
	protected function afterValidate()
	{
		parent::afterValidate();
			// Create action
		return true;
	}
	*/
	
	/**
	 * before save attributes
	 */
	/*
	protected function beforeSave() {
		if(parent::beforeSave()) {
		}
		return true;	
	}
	*/
	
	/**
	 * After save attributes
	 */
	/*
	protected function afterSave() {
		parent::afterSave();
		// Create action
	}
	*/

	/**
	 * Before delete attributes
	 */
	/*
	protected function beforeDelete() {
		if(parent::beforeDelete()) {
			// Create action
		}
		return true;
	}
	*/

	/**
	 * After delete attributes
	 */
	/*
	protected function afterDelete() {
		parent::afterDelete();
		// Create action
	}
	*/

}