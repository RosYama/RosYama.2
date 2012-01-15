<div id="userGroups-container">
	<div class="info" style="display:none;">
		<p>update complete.</p>
		<?php echo CHtml::link(Yii::t('userGroupsModule.admin', 'click here to return to the admin page'), array('index')); ?>
	</div>
	<?php
	$this->widget('zii.widgets.jui.CJuiProgressBar', array(
		'value'=>0,
		// additional javascript options for the progress bar plugin
		'htmlOptions'=>array(
			'style'=>'height:20px;',
			'id'=>'UGprogressBar'
		),
	));
	?>
	<div id="update1.8">
		<ul>
			<li>create mail view directory</li>
			<li>create invitation mail view file</li>
			<li>create password reset mail view file</li>
			<li>create activation mail view file</li>
			<li>create new configuration option for cronjobs</li>
			<li>update userGroups version number</li>
		</ul>
	</div>
	<?php
	echo CHtml::ajaxButton(Yii::t('userGroupsModule.admin','update to version {version}', array('{version}' => UserGroupsInstallation::VERSION)), array('update/execute?v='.UserGroupsInstallation::VERSION), array('success' => 'js: function(data){$("#UGprogressBar").progressbar( "option", "value", 100 ); $("#updateButton").remove(); $(".info").show(); }'), array('id' => 'updateButton'));
	?>
</div>