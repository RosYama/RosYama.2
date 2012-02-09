<?
$this->pageTitle=Yii::app()->name . ' - Справочник ГИБДД';
$this->title=CHtml::link($model->gibdd_name, Array('local','id'=>$model->id)).' > '.'Редактирование';
?>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>