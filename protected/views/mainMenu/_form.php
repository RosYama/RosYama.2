<div class="form">

<?php
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'menu-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Поля отмеченные <span class="required">*</span> являются обязательными.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model, 'name');?>
		<?php echo $form->error($model,'name'); ?>
	</div>
		<hr/>
    <div class="row">
			<?php echo $form->labelEx($model,'type'); ?>
			<br/>

				<ul class="radiogroup">

				<?php echo $form->RadioButtonList($model,'type',
								Array('0'=>'Стандартно','1'=>'Внешняя ссылка'),
								array(
								'encode'=>false,
								'checked'=>false,
								'onchange'=>"
								switch ($(this).val()) {
								  case '0':
									$('#standart_type').show();
									$('#link_type').hide();
									break
								  case '1':
									$('#standart_type').hide();
									$('#link_type').show();
									break
								}

								",
								'template'=>'<li>{input}{label}</li>', 'separator'=>''
								)
								); ?>


				</ul>
				<div class="clear"></div>
		</div>
		<hr/>
		<?php echo $form->error($model,'type'); ?>
<div id="standart_type" <?php if ($model->type!=0) echo 'style="display:none;"'?> >
    <?php $items=$model->getAllControllers();  ?>

	<div class="row">
		<?php echo $form->labelEx($model,'controller'); ?>
		<?php echo $form->hiddenField($model, 'controller');?>
		<?php echo $form->error($model,'controller'); ?>
	</div>



   <?php echo CHtml::dropDownList('controller',$model->controller,CHtml::listData( $items['controllers'], 'name', 'name' ),
				array(
				'prompt'=>'Нет ссылки',
				'ajax' => array(
				'type'=>'POST', //request type
				'url'=>CController::createUrl('mainMenu/dynamicActions'), //url to call.
				'update'=>'#MainMenu_action', //selector to update
				//'data'=>'js:javascript statement'
				'beforeSend' => 'function(){
								$("#MainMenu_action").empty();
								$("#dynelements").empty();
						}',
				'complete' => 'function(){
								$("#MainMenu_controller").val($("#controller option:selected").val());
								$("#MainMenu_action").change();
						}',
				))); ?>

		<?php
 $actions=Array();
	if ($model->controller){
	$actions=CHtml::listData(MainMenu::model()->getContActions($model->controller),'name','name');
	}

	?>
		<?php echo $form->dropDownList($model, 'action', $actions, array(
		'ajax' => array(
				'type'=>'POST', //request type
				'url'=>CController::createUrl('mainMenu/dynamicElements'), //url to call.
				'update'=>'#dynelements', //selector to update
				//'data'=>'js:javascript statement'
				'complete' => 'function(){
						//$("#dynelements").empty();
						}',
				)));

				?>
	<div class="row" id="dynelements">
		<?php echo $form->labelEx($model,'element'); ?>

		<?php if ($model->controller && $model->action) echo $model->getActionElements($model->controller, $model->action);	?>

		<?php echo $form->error($model,'element'); ?>
	</div>
</div>

<div id="link_type" <?php if ($model->type!=1) echo 'style="display:none;"'?>>
	<div class="row">
		<?php echo $form->labelEx($model,'link'); ?>
		<?php echo $form->textField($model, 'link', Array('size'=>'60'));?>
		<?php echo $form->error($model,'link'); ?>
	</div>
</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->