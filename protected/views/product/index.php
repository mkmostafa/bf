<?php
/* @var $this ProductController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Products',
);

$this->menu=array();
if(Yii::app()->user->checkAccess('admin'))
{
	array_push($this->menu,array('label'=>'Create Product', 'url'=>array('create')));
	array_push($this->menu,	array('label'=>'Manage Product', 'url'=>array('admin')));
}
?>

<h1>Products</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
