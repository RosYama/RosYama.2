<?php $this->pageTitle=Yii::app()->name; ?>

<div id="userGroups-container">
	<h1>Modulo userGroups</h1>
	
	<p>Il Modulo <i>userGroups</i> non &egrave; installato.</p>
	
	<?php 
	if ($model->scenario === 'access_code')
		$this->renderPartial('access', array('model' => $model));
	else
		$this->renderPartial('form', array('model' => $model));
	?>
</div>