<?php
$this->breadcrumbs=array(
	$model->username.' '.Yii::t('UserGroupsModule.general','profile'),
);
?>
<div id="userGroups-container">
	<div class="userGroupsMenu-container">
		<?php
		if (Yii::app()->user->pbac(array('user.admin', 'admin.admin')) && $model->id !== Yii::app()->user->id) {
			
			$mode = 'admin';
			$id = $model->id;
			$username = $model->username;
		} else if ($model->id === Yii::app()->user->id)
			$mode = 'edit';
		else
			$mode = 'profile'; 
		$this->renderPartial('/admin/menu', array(
			'mode' => $mode, 
			'id'=> isset($id) ? $id : NULL,
			'username' => isset($username) ? $username : NULL,
		)); 
		?>
	</div>

	<?php if(Yii::app()->user->hasFlash('user')):?>
    <div class="info">
        <?php echo Yii::app()->user->getFlash('user'); ?>
    </div>
	<?php endif; ?>
	<?php if(Yii::app()->user->hasFlash('mail')):?>
    <div class="info">
        <?php echo Yii::app()->user->getFlash('mail'); ?>
    </div>
	<?php endif; ?>
	<h1><?php echo ucfirst($model->username); ?></h1>
	
	<?php $this->renderPartial('/user/_view', array('data' => $model, 'profiles' => $profiles))?>
</div>