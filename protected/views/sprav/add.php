<?
$this->pageTitle=Yii::app()->name . ' - Справочник ГИБДД';
$this->title='Добавить территориальный отдел ГИБДД в регион "'.$subject->name.'"';
?>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
