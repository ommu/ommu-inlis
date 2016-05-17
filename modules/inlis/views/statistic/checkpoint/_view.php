<?php
/**
 * View Inlis Sync Checkpoints (view-inlis-sync-checkpoints)
 * @var $this CheckpointController
 * @var $data ViewInlisSyncCheckpoints
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

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('date_key')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->date_key), array('view', 'id'=>$data->date_key)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('checkpoints')); ?>:</b>
	<?php echo CHtml::encode($data->checkpoints); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('checkpoint_member')); ?>:</b>
	<?php echo CHtml::encode($data->checkpoint_member); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('checkpoint_non_member')); ?>:</b>
	<?php echo CHtml::encode($data->checkpoint_non_member); ?>
	<br />


</div>