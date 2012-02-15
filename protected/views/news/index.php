<?
$this->pageTitle=Yii::app()->name . ' :: Новости';
?>
<?php
$this->breadcrumbs=array(
	'News',
);

$this->menu=array(
	array('label'=>'Create News', 'url'=>array('create')),
	array('label'=>'Manage News', 'url'=>array('admin')),
);
?>
<div class="lCol">
<?php $this->widget('application.widgets.social.socialWidget'); ?>
</div>
<div class="rCol">
<h1>Новости</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
</div>