					Не исключена вероятность того, что на <a href="http://www.gosuslugi.ru/ru/chorg/index.php?ssid_4=4120&stab_4=4&rid=228&tid=2" target="_blank">сайте госуслуг</a> окажется немного полезной информации.
					
					<?php $form=$this->beginWidget('CActiveForm', array(
						'id'=>'request-form',
						'enableAjaxValidation'=>false,
						'action'=>Yii::app()->createUrl("holes/request", array("id"=>$hole->ID)),
						'htmlOptions'=>Array ('onsubmit'=>"document.getElementById('pdf_form').style.display='none';"),
					)); 
					$usermodel=Yii::app()->user->userModel;
					$model=new HoleRequestForm;
					$model->to=$gibdd ? $gibdd->post_dative.' '.$gibdd->fio_dative : '';
					$model->from=$usermodel->relProfile->request_from ? $usermodel->relProfile->request_from : $usermodel->last_name.' '.$usermodel->name.' '.$usermodel->second_name;
					$model->address=CHtml::encode($hole->ADDRESS);
					$model->signature=$usermodel->relProfile->request_signature ? $usermodel->relProfile->request_signature : $usermodel->last_name.' '.substr($usermodel->name, 0, 2).($usermodel->name ? '.' : '').' '.substr($usermodel->second_name, 0, 2).($usermodel->second_name ? '.' : '');
					$model->postaddress=$usermodel->relProfile->request_address ? $usermodel->relProfile->request_address : '';
					?>					
						<h2><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM') ?></h2>
						<table>
							<tr>
								<th colspan="2"><?php echo $form->checkBox($model,'sendToGibddru',array('style'=>'width:20px', 'onChange'=>'if ($(this).attr("checked")) { $(this).parents("form").find(".fileButtons").hide(); $(this).parents("form").find(".sendButtons").show();} else {$(this).parents("form").find(".fileButtons").show(); $(this).parents("form").find(".sendButtons").hide();}')); ?>
								<?php echo $form->labelEx($model,'sendToGibddru'); ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_SEND_TO_GIBDDRU_COMMENT') ?></span></th>
								
							</tr>
							<tr>
								<th><?php echo $form->labelEx($model,'to'); ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_TO_COMMENT') ?></span></th>
								<td><?php echo $form->textArea($model,'to',array('rows'=>3, 'cols'=>40)); ?></td>
							</tr>
							<tr>
								<th><?php echo $form->labelEx($model,'from'); ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_FROM_COMMENT') ?></span></th>
								<td><?php echo $form->textArea($model,'from',array('rows'=>3, 'cols'=>40)); ?></td>
							</tr>
							<tr>
								<th><?php echo $form->labelEx($model,'postaddress'); ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_POSTADDRESS_COMMENT') ?></span></th>
								<td><?php echo $form->textArea($model,'postaddress',array('rows'=>3, 'cols'=>40)); ?></td>
							</tr>
							<tr>
								<th><?php echo $form->labelEx($model,'address'); ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_ADDRESS_COMMENT') ?></span></th>
								<td><?php echo $form->textArea($model,'address',array('rows'=>3, 'cols'=>40)); ?></td>
							</tr>
							<?php if($hole->type->alias == 'light'): ?>
								<tr>
									<th><?php echo $form->labelEx($model,'comment'); ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_COMMENT_COMMENT') ?></span></th>
									<td><?php echo $form->textArea($model,'comment',array('rows'=>3, 'cols'=>40)); ?></td>
								</tr>
							<?php endif; ?>
							<?php if($hole->description_size || $hole->description_locality): ?>
							<tr>
								<th colspan="2"><?php echo $form->labelEx($model,'showDescriptions'); ?>&nbsp;&nbsp;&nbsp;<?php echo $form->checkBox($model,'showDescriptions',array('style'=>'width:20px')); ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_SHOW_DESCRIPTIONS_COMMENT') ?></span></th>
								
							</tr>
							<?php endif; ?>
							<tr>
								<th><?php echo $form->labelEx($model,'signature'); ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_SIGNATURE_COMMENT') ?></span></th>
								<td><?php echo $form->textField($model,'signature',array('class'=>'textInput')); ?></td>
							</tr>
							<tr>
								<th></th>
								<td>
									<div class="fileButtons" style="<?php if ($model->sendToGibddru) echo 'display:none;'?>">
									<?php echo CHtml::submitButton(Yii::t('holes_view', 'HOLE_REQUEST_FORM_SUBMIT'), Array('class'=>'submit', 'name'=>'HoleRequestForm[pdf]')); ?>
									<?php echo CHtml::submitButton(Yii::t('holes_view', 'HOLE_REQUEST_FORM_SUBMIT2'), Array('class'=>'submit', 'name'=>'HoleRequestForm[html]')); ?>
									</div>
									<div class="sendButtons" style="<?php if (!$model->sendToGibddru) echo 'display:none;'?>">
									<?php echo CHtml::submitButton(Yii::t('holes_view', 'HOLE_REQUEST_FORM_SUBMIT3'), Array('class'=>'submit', 'name'=>'HoleRequestForm[pdf]')); ?>
									</div>
								</td>
							</tr>
						</table>
					<div class="fileButtons" style="<?php if ($model->sendToGibddru) echo 'display:none;'?>">
						<?= Yii::t('holes_view', 'ST1234_INSTRUCTION') ?>
					</div>
	
					<?php $this->endWidget(); ?>
