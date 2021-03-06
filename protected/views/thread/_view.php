<?php
/* @var $this ThreadController */
/* @var $model Thread */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('title')); ?>:</b>
	<?php 
		echo CHtml::link(CHtml::encode($data->title), array('/thread/view', 'id'=>$data->id,'cid'=>$data->category_id));
	 ?>
	<br />

        <b><?php echo CHtml::encode($data->getAttributeLabel('username')); ?>:</b>
	<?php echo CHtml::encode($data->user->username); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('category')); ?>:</b>
	<?php echo CHtml::encode($data->category->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created_at')); ?>:</b>
	<?php echo CHtml::encode($data->created_at); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('updated_at')); ?>:</b>
	<?php echo CHtml::encode($data->updated_at); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('content')); ?>:</b>
	<?php echo CHtml::encode($data->content); ?>
	<br />


</div>