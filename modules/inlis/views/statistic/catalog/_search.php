<?php
/**
 * View Inlis Sync Catalogs (view-inlis-sync-catalogs)
 * @var $this CatalogController
 * @var $model ViewInlisSyncCatalogs
 * @var $form CActiveForm
 * version: 0.0.1
 *
 * @author Putra Sudaryanto <putra.sudaryanto@gmail.com>
 * @copyright Copyright (c) 2016 Ommu Platform (ommu.co)
 * @created date 17 May 2016, 17:24 WIB
 * @link http://company.ommu.co
 * @contect (+62)856-299-4114
 *
 */
?>

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
	<ul>
		<li>
			<?php echo $model->getAttributeLabel('date_key'); ?><br/>
			<?php echo $form->textField($model,'date_key'); ?>
		</li>

		<li>
			<?php echo $model->getAttributeLabel('catalogs'); ?><br/>
			<?php echo $form->textField($model,'catalogs',array('size'=>21,'maxlength'=>21)); ?>
		</li>

		<li class="submit">
			<?php echo CHtml::submitButton(Yii::t('phrase', 'Search')); ?>
		</li>
	</ul>
<?php $this->endWidget(); ?>