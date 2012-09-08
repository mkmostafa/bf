<?php
/* @var $this ThreadController */
/* @var $model Thread */

$this->breadcrumbs=array(
	'Threads'=>array('index'),
	'Manage',
);
$cid=Thread::model()->findByPk($model->id)->category_id;
$this->menu=array(
	array('label'=>'Create Thread', 'url'=>array('create','cid'=>$cid)),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('thread-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Threads</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'thread-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'user_id',
		'category_id',
		'title',
		'created_at',
		'updated_at',
		/*
		'content',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
