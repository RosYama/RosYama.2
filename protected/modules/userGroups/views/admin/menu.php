<?php

$userGroupsMenu = array();

if ($mode === 'profile') {
	$userGroupsMenu[] = array('label'=>Yii::t('userGroupsModule.general','My Profile'), 'url'=>array('/userGroups'), 'linkOptions'=> array('onclick' => 'js: return loadPage(this)'), 'visible' => !Yii::app()->user->isGuest);
	if (isset($username))
		$userGroupsMenu[] = array('label'=>Yii::t('userGroupsModule.general','{username}\'s Profile',array('{username}'=>$username)), 'url'=>array('/userGroups?u='.$username), 'linkOptions'=> array('onclick' => 'js: return loadPage(this)'), 'visible' => !Yii::app()->user->isGuest);
} else if ($mode === 'edit')
	$userGroupsMenu[] = array('label'=>Yii::t('userGroupsModule.general','Edit Profile'), 'url'=>array('user/update', 'id'=>Yii::app()->user->id), 'linkOptions'=> array('onclick' => 'js: return loadPage(this)'), 'visible' => !Yii::app()->user->isGuest);
else if ($mode === 'admin') {
	$userGroupsMenu[] = array('label'=>Yii::t('userGroupsModule.general','My Profile'), 'url'=>array('/userGroups'), 'linkOptions'=> array('onclick' => 'js: return loadPage(this)'), 'visible' => !Yii::app()->user->isGuest);
	$userGroupsMenu[] = array('label'=>Yii::t('userGroupsModule.general','Edit {username}\'s Profile', array('{username}'=>$username)), 'url'=>array('user/update', 'id'=>$id), 'linkOptions'=> array('onclick' => 'js: return loadPage(this)'), 'visible' => !Yii::app()->user->isGuest);
}

$userGroupsMenu[] = array('label'=>Yii::t('userGroupsModule.general','Invite User'), 'url'=>array('user/invite'), 'linkOptions'=> array('onclick' => 'js: return loadPage(this)'), 'visible' => Yii::app()->user->pbac(array('user.admin', 'admin.admin')), 'active' => isset($invite));
$userGroupsMenu[] = array('label'=>Yii::t('userGroupsModule.general','User List'), 'url'=>array('user/index'), 'linkOptions'=> array('onclick' => 'js: return loadPage(this)'), 'visible' => Yii::app()->user->pbac(array('user.admin', 'admin.admin'), 'public_user_list', 'OR'), 'active' => isset($list));
$userGroupsMenu[] = array('label'=>Yii::t('userGroupsModule.admin','Root Tools'), 'url'=>array('admin/'), 'linkOptions'=> array('onclick' => 'js: return loadPage(this)'), 'visible' => Yii::app()->user->pbac(array('admin.write', 'admin.admin')), 'active' => isset($root));
$userGroupsMenu[] = array('label'=>Yii::t('userGroupsModule.admin','Documentation'), 'url'=>array('admin/documentation'), 'linkOptions'=> array('onclick' => 'js: return loadPage(this)'), 'visible' => Yii::app()->user->pbac('admin.read'), 'active' => isset($documentation));

$this->widget('zii.widgets.CMenu', array(
	'items'=>$userGroupsMenu,
	'htmlOptions'=>array('class'=>'userGroupsMenu'),
	'lastItemCssClass' => 'last',
));