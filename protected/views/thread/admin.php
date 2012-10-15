<?php
/* @var $this ThreadController */
/* @var $model Thread */


$this->widget('bootstrap.widgets.TbBreadcrumbs', array(
    'links'=>array('Category'=>'index.php?r=category/view&id='.$model->category_id,'Manage'),
));


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


<?php $this->widget('bootstrap.widgets.TbGridView', array(

	'id'=>'threads-grid',
    'type'=>'striped bordered condensed',
    'dataProvider'=>$model->search(),
    'template'=>"{items}",
    'filter'=>$model,
    'columns'=>array(
    	/*
        array('name'=>'id', 'header'=>'#'),
        array('user_id', 'header'=>'First name'),
        array('name'=>'lastName', 'header'=>'Last name'),
        array('name'=>'language', 'header'=>'Language'),*/
		'id',
		'user_id',
		'title',
		'created_at',
		'updated_at',

        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'htmlOptions'=>array('style'=>'width: 50px'),
        ),
    ),
)); ?>

