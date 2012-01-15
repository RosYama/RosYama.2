<?php $this->pageTitle=Yii::app()->name; ?>

<div id="userGroups-container">
	<h1>userGroups Module</h1>
	
	<p>The <i>userGroups</i> Module is not installed on your system.</p>
	
	<?php 
	if ($model->scenario === 'access_code')
		$this->renderPartial('access', array('model' => $model));
	else
		$this->renderPartial('form', array('model' => $model));
	?>
</div>