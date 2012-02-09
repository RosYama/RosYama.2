<?php
$this->breadcrumbs=array(
	Yii::t('userGroupsModule.general','Documentation'),
);
?>
<div id="userGroups-container">
	<div class="userGroupsMenu-container">
		<?php $this->renderPartial('/admin/menu', array('mode' => 'profile', 'documentation' => true))?>
	</div>

	<?php
	$this->widget('zii.widgets.jui.CJuiAccordion', array(
	    'panels'=>array(
	        Yii::t('userGroupsModule.admin','First Steps') =>$this->renderPartial('documentation/first_steps', null, true),
	        Yii::t('userGroupsModule.admin','Setup the new accessControlFilter') =>$this->renderPartial('documentation/access_rules', null, true),
	        Yii::t('userGroupsModule.admin','Using the new Access Rules') =>$this->renderPartial('documentation/access_rules_2', null, true),
	        Yii::t('userGroupsModule.admin','Root Tools') =>$this->renderPartial('documentation/root_tools', null, true),
	        Yii::t('userGroupsModule.admin','Cron Jobs') =>$this->renderPartial('documentation/cron_jobs', null, true),
	        Yii::t('userGroupsModule.admin','Profile Extensions') =>$this->renderPartial('documentation/profile', null, true),
	        Yii::t('userGroupsModule.admin','Email Customization') =>$this->renderPartial('documentation/email', null, true),
	        Yii::t('userGroupsModule.admin','Localization') =>$this->renderPartial('documentation/localization', null, true),
	        Yii::t('userGroupsModule.admin','Is this thing safe?') =>$this->renderPartial('documentation/is_safe', null, true),
	        Yii::t('userGroupsModule.admin','What\'s new?') =>$this->renderPartial('documentation/new', null, true),
	        Yii::t('userGroupsModule.admin','What\'s coming next?') =>$this->renderPartial('documentation/next', null, true),
	        Yii::t('userGroupsModule.admin','License') =>$this->renderPartial('documentation/license', null, true),
	        Yii::t('userGroupsModule.admin','About the author') =>$this->renderPartial('documentation/about_me', null, true),
	        // panel 3 contains the content rendered by a partial view
	        //'panel 3'=>$this->renderPartial('_partial',null,true),
	    ),
	    // additional javascript options for the accordion plugin
	    'options'=>array(
	        'animated'=>'bounceslide',
	    	#'fillSpace'=>true,
	    	'autoHeight'=>false,
	    	'navigation'=>true,
	    ),
	    'htmlOptions'=>array(
	    	'id'=>'documentation-accordion',
	    ),
	));
	?>
	<script>
	$('#documentation-accordion').bind('accordionchange', function(event, ui) {
		if ($('html').scrollTop() > ui.newHeader.offset().top) {
			$('html, body').animate({
				scrollTop: ui.newHeader.offset().top
			}, 1000);
		}
	});
	</script>
</div>