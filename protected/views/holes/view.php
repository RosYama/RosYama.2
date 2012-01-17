<?php
$this->widget('application.extensions.fancybox.EFancyBox', array(
    'target'=>'.holes_pict',
    'config'=>array(
			'attr'=>'hole',
		),
    )
);
?>
<div class="head">
		<div class="container">
		<div class="lCol">
					<a href="/" class="logo" title="На главную"><img src="/images/logo.png"  alt="РосЯма" /></a>
			</div>
			<div class="rCol">
	<div class="h">
		<div class="info">
			<p><span class="date"><?php echo CHtml::encode(Y::dateFromTime($hole->DATE_CREATED)); ?></span><?= CHtml::encode(strlen($hole->user->name.$hole->user->last_name) ? $hole->user->name.' '.$hole->user->last_name : $hole->user->username) ?></p>
			<p class="type type_<?= $hole->type->alias ?>"><?= $hole->type->name; ?></p>
			<p class="address"><?= CHtml::encode($hole->ADDRESS) ?></p>
			<p class="status">
				<span class="bull <?= $hole->STATE ?>">&bull;</span>
				<span class="state">
					<?= CHtml::encode($hole->StateName) ?>
					<? if($hole->STATE == 'prosecutor' && $hole->DATE_STATUS): ?>
						<?= CHtml::encode(Y::dateFromTime($hole->DATE_STATUS)).' '/*.GetMessage('REQUEST_TO_PROSECUTOR_SENT')*/ ?>
					<? elseif($hole->STATE != 'fixed' && $hole->DATE_SENT): ?>
						<?= CHtml::encode(Y::dateFromTime($hole->DATE_SENT))?> отправлен запрос в ГИБДД
					<? endif; ?>
					<? if($hole->STATE == 'fixed' && $hole->DATE_STATUS): ?>
						<?= CHtml::encode(Y::dateFromTime($hole->DATE_STATUS))?> отмечен факт исправления дефекта
					<? endif; ?>
				</span>
			</p>
			<div class="control">
			<div class="progress">
			<? if($hole->WAIT_DAYS): ?>
			<div class="lc">
				<div class="wait">
					<p>Ждать, когда отремонтируют</p>
					<p class="days"><?php echo Y::declOfNum($hole->WAIT_DAYS, array('день', 'дня', 'дней')); ?></p> 
				</div>
			</div>
			<? elseif($hole->PAST_DAYS): ?>
			<div class="lc">
				<div class="wait">
					<p>Просрочено</p>
					<p class="days"><?php echo Y::declOfNum($hole->PAST_DAYS, array('день', 'дня', 'дней')); ?></p>
				</div>
			</div>
			<? endif; ?>
			<? if($hole->IsUserHole || Yii::app()->user->IsAdmin): ?>
				<? if(Yii::app()->user->IsAdmin) : ?>
					<p>
						<font class="errortext">
						Вы обладаете административными полномочиями
						<br/>
						</font>
					</p>
				<? endif; ?>
				
				<?php if(!$hole->PREMODERATED) : ?>
				<p>
						<font class="errortext">
						<?php echo  Yii::t('holes_view', 'PREMODRATION_WARNING');?>
						<br/>
						</font>
				</p>
				<? endif; ?>
				
				<?			
				switch($hole->STATE)
				{
					case 'fresh':
					{
						?>
						<div class="edit">
							<?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_2'), array('update', 'id'=>$hole->ID)); ?>								
							<?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_10'), array('personalDelete', 'id'=>$hole->ID), array('onclick'=>'return confirm("'.Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_11').'");')); ?>							
						</div>
						<div class="progress">
							<div class="lc">
								<a href="#" onclick="var c=document.getElementById('pdf_form');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;" class="printDeclaration"><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_4') ?></a>
							</div>
							<div class="cc">
								<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_6'), array('sent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
								<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_8a'), array('fix', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
							</div>
							<div class="rc">
								Также можно отправить:<br />
								<span><ins>&mdash;</ins>с&nbsp;<a href="/about/112/">сайта 112.ru</a></span>
								<span><ins>&mdash;</ins>с&nbsp;официального сайта <a href="http://www.gibdd.ru/letter" target="_blank">ГИБДД&nbsp;МВД&nbsp;России</a></span>
							</div>
						</div>
						<?
						break;
					}
					case 'inprogress':
					{
						?>
							<div class="cc" style="width:150px">
								<p><?=  Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_7') ?></p>
								<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_8'), array('fix', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
							</div>
							<div class="rc" style="width:145px;padding: 24px 0 24px 15px;">
								<p><a class="declarationBtn" href="#" onclick="var c=document.getElementById('pdf_form');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;"><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_15') ?></a></p>
								<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_12'), array('notsent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
								<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_GIBDD_REPLY_RECEIVED'), array('gibddreply', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
							</div>
						<?
						break;
					}
					case 'gibddre':
					{
						?>
							<div class="lc" style="width:150px">
								<p><?=  Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_7') ?></p>
								<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_8'), array('fix', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
							</div>
							<div class="cc"><?php echo CHtml::link('Ещё ответ из ГИБДД', array('gibddreply', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></div>
							<div class="rc" style="width:145px;padding: 24px 0 24px 15px;">
								<p>Если вас не устраивает ответ ГИБДД, то можно</p>
								<p><a href="#" onclick="var c=document.getElementById('prosecutor_form2');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;">подать Заявление в Прокуратуру</a></p>
								<div class="pdf_form" id="prosecutor_form2"<?= isset($_GET['show_prosecutor_form2']) ? ' style="display: block;"' : '' ?>>
									<a href="#" onclick="var c=document.getElementById('prosecutor_form2');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;" class="close">&times;</a>
									<?php $form=$this->beginWidget('CActiveForm', array(
										'id'=>'request-form',
										'enableAjaxValidation'=>false,
										'action'=>Yii::app()->createUrl("holes/request", array("id"=>$hole->ID)),
										'htmlOptions'=>Array ('onsubmit'=>"document.getElementById('prosecutor_form2').style.display='none';"),
									)); 
									$model=new HoleRequestForm;
									$model->form_type='prosecutor2';
									$model->to=$hole->subject ? $hole->subject->name_full_genitive : '';
									$model->from=CHtml::encode($hole->user->last_name.' '.$hole->user->name.' '.$hole->user->second_name);
									$model->address=CHtml::encode($hole->ADDRESS);
									$model->signature=CHtml::encode($hole->user->last_name.' '.substr($hole->user->name, 0, 1).($hole->user->name ? '.' : '').' '.substr($hole->user->second_name, 0, 1).($hole->user->second_name ? '.' : ''));
									$model->gibdd=$hole->subject && $hole->subject->gibdd ? $hole->subject->gibdd->gibdd_name : '';
									$model->application_data=$hole->request_gibdd ? date('d.m.Y',$hole->request_gibdd->date_sent) : '';
									?>											
									
										<?php echo $form->hiddenField($model,'form_type'); ?>
										<?=  Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM2_PREFACE') ?>
										<table>
											<tr>
												<th><?=  Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM_TO') ?></th>
												<td><?php echo $form->textArea($model,'to',array('rows'=>3, 'cols'=>40)); ?></td>
											</tr>
											<tr>
												<th><?=  Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM_FROM') ?></th>
												<td><?php echo $form->textArea($model,'from',array('rows'=>3, 'cols'=>40)); ?></td>
											</tr>
											<tr>
												<th><?= Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM_POSTADDRESS') ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM_POSTADDRESS_COMMENT') ?></span></th>
												<td><?php echo $form->textArea($model,'postaddress',array('rows'=>3, 'cols'=>40)); ?></td>
											</tr>
											<tr>
												<th><?= Yii::t('holes_view', 'HOLE_PRESECUTOR_FORM_ADDRESS') ?></th>
												<td><?php echo $form->textArea($model,'address',array('rows'=>3, 'cols'=>40)); ?>
												<?php /*<textarea rows="3" cols="40" id="prosecutor_form_address" name="address"><?= CHtml::encode($hole['ESS']) ?></textarea> */ ?></td>
											</tr>
											<tr>
												<th><?= Yii::t('holes_view', 'HOLE_PRESECUTOR_FORM_GIBDD') ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_PRESECUTOR_FORM_GIBDD_COMMENT') ?></span></th>
												<td><?php echo $form->textArea($model,'gibdd',array('rows'=>3, 'cols'=>40)); ?><?//= $arResult['PROSECUTOR_GIBDD'] ?></td>
											</tr>
											<tr>
												<th><?= Yii::t('holes_view', 'HOLE_PRESECUTOR_FORM_GIBDD_REPLY') ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_PRESECUTOR_FORM_GIBDD_COMMENT2') ?></span></th>
												<td><?php echo $form->textArea($model,'gibdd_reply',array('rows'=>3, 'cols'=>40)); ?></td>
											</tr>
											<tr>
												<th><?= Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM_APPLICATION_DATA') ?></th>
												<td><?php echo $form->textField($model,'application_data',array('class'=>'textInput')); ?></td>
											</tr>
											<tr>
												<th><?php echo $form->labelEx($model,'signature'); ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_SIGNATURE_COMMENT') ?></span></th>
												<td><?php echo $form->textField($model,'signature',array('class'=>'textInput')); ?></td>
											</tr>
											<tr>
												<th></th>
												<td>
													<?php echo CHtml::submitButton(Yii::t('holes_view', 'HOLE_REQUEST_FORM_SUBMIT'), Array('class'=>'submit', 'name'=>'HoleRequestForm[pdf]')); ?>
													<?php echo CHtml::submitButton(Yii::t('holes_view', 'HOLE_REQUEST_FORM_SUBMIT2'), Array('class'=>'submit', 'name'=>'HoleRequestForm[html]')); ?>
												</td>
											</tr>
										</table>
										<?php if ($hole->subject && $hole->subject->prosecutor) : ?>
										<strong><?php echo CHtml::encode($hole->subject->prosecutor->name) ?></strong>
										<p><?php echo CHtml::encode(strip_tags($hole->subject->prosecutor->preview_text)) ?></p>
										<?php endif; ?>
									<?php $this->endWidget(); ?>
								</div>
							</div>
						<?
						break;
					}
					case 'achtung':
					{
						?>
						<div class="cc" style="width:150px">
							<p><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_7') ?></p>
							<?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_8'), array('fix', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?>
						</div>
						<div class="rc" style="width:184px;padding: 24px 0 24px 15px;">
							<p><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_16') ?></p>
							<p><a href="#" onclick="var c=document.getElementById('prosecutor_form');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;" class="declarationBtn"><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_14') ?></a></p>
							<p><?php echo CHtml::link('Жалоба в прокуратуру подана', array('prosecutorsent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
						</div>
						<div class="pdf_form" id="prosecutor_form"<?= isset($_GET['show_prosecutor_form']) ? ' style="display: block;"' : '' ?>>
							<a href="#" onclick="var c=document.getElementById('prosecutor_form');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;" class="close">&times;</a>
							<?php $form=$this->beginWidget('CActiveForm', array(
										'id'=>'request-form',
										'enableAjaxValidation'=>false,
										'action'=>Yii::app()->createUrl("holes/request", array("id"=>$hole->ID)),
										'htmlOptions'=>Array ('onsubmit'=>"document.getElementById('prosecutor_form2').style.display='none';"),
									)); 
									$model=new HoleRequestForm;
									$model->form_type='prosecutor';
									$model->to=$hole->subject ? $hole->subject->name_full_genitive : '';
									$model->from=CHtml::encode($hole->user->last_name.' '.$hole->user->name.' '.$hole->user->second_name);
									$model->address=CHtml::encode($hole->ADDRESS);
									$model->signature=CHtml::encode($hole->user->last_name.' '.substr($hole->user->name, 0, 1).($hole->user->name ? '.' : '').' '.substr($hole->user->second_name, 0, 1).($hole->user->second_name ? '.' : ''));
									$model->gibdd=$hole->subject && $hole->subject->gibdd ? $hole->subject->gibdd->gibdd_name : '';
									$model->application_data=$hole->request_gibdd ? date('d.m.Y',$hole->request_gibdd->date_sent) : '';
									?>					
									<?php echo $form->hiddenField($model,'form_type'); ?>							
								<?= Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM_PREFACE') ?>
								<table>
									<tr>
										<th><?=  Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM_TO') ?></th>
										<td><?php echo $form->textArea($model,'to',array('rows'=>3, 'cols'=>40)); ?></td>
									</tr>
									<tr>
										<th><?=  Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM_FROM') ?></th>
										<td><?php echo $form->textArea($model,'from',array('rows'=>3, 'cols'=>40)); ?></td>
									</tr>
									<tr>
											<th><?= Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM_POSTADDRESS') ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM_POSTADDRESS_COMMENT') ?></span></th>
											<td><?php echo $form->textArea($model,'postaddress',array('rows'=>3, 'cols'=>40)); ?></td>
									</tr>
									<tr>
											<th><?= Yii::t('holes_view', 'HOLE_PRESECUTOR_FORM_ADDRESS') ?></th>
											<td><?php echo $form->textArea($model,'address',array('rows'=>3, 'cols'=>40)); ?>
												<?php /*<textarea rows="3" cols="40" id="prosecutor_form_address" name="address"><?= CHtml::encode($hole['ESS']) ?></textarea> */ ?></td>
									</tr>
									<tr>
										<th><?= Yii::t('holes_view', 'HOLE_PRESECUTOR_FORM_GIBDD') ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_PRESECUTOR_FORM_GIBDD_COMMENT') ?></span></th>
										<td><?php echo $form->textArea($model,'gibdd',array('rows'=>3, 'cols'=>40)); ?><?//= $arResult['PROSECUTOR_GIBDD'] ?></td>
									</tr>
									<tr>
										<th><?= Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM_APPLICATION_DATA') ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM_APPLICATION_DATA_COMMENT') ?></span></th>
										<td><?php echo $form->textField($model,'application_data',array('class'=>'textInput')); ?></td>
									</tr>
									<tr>
										<th><?php echo $form->labelEx($model,'signature'); ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_SIGNATURE_COMMENT') ?></span></th>
										<td><?php echo $form->textField($model,'signature',array('class'=>'textInput')); ?></td>
									</tr>								
									<tr>
									<th></th>
										<td>
											<?php echo CHtml::submitButton(Yii::t('holes_view', 'HOLE_REQUEST_FORM_SUBMIT'), Array('class'=>'submit', 'name'=>'HoleRequestForm[pdf]')); ?>
											<?php echo CHtml::submitButton(Yii::t('holes_view', 'HOLE_REQUEST_FORM_SUBMIT2'), Array('class'=>'submit', 'name'=>'HoleRequestForm[html]')); ?>
										</td>
									</tr>
								</table>
								<?php if ($hole->subject && $hole->subject->prosecutor) : ?>
								<strong><?php echo CHtml::encode($hole->subject->prosecutor->name) ?></strong>
								<p><?php echo CHtml::encode(strip_tags($hole->subject->prosecutor->preview_text)) ?></p>
								<?php endif; ?>
								<?php $this->endWidget(); ?>
							</form>
						</div>
						<?
						break;
					}
					case 'prosecutor':
					{
						?>
						<div class="lc" style="width:150px">
							<p><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_7') ?></p>
							<?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_8'), array('fix', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?>
						</div>
						<div class="cc">
							<?php echo CHtml::link('Аннулировать факт отправки заявления в прокуратуру', array('prosecutornotsent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?>
						</div>
						<?
						break;
					}
					case 'fixed':
					default:
					{
						if(!sizeof($hole['pictures']['original']['fixed']))
						{
							?>
							<?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_13'), array('defix', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?>
							<?
						}
						break;
					}
				}
				?>
				<div class="pdf_form" id="pdf_form"<?= isset($_GET['show_pdf_form']) ? ' style="display: block;"' : '' ?>>
					<a href="#" onclick="var c=document.getElementById('pdf_form');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;" class="close">&times;</a>
					Не исключена вероятность того, что на <a href="http://www.gosuslugi.ru/ru/chorg/index.php?ssid_4=4120&stab_4=4&rid=228&tid=2" target="_blank">сайте госуслуг</a> окажется немного полезной информации.
					
					<?php $form=$this->beginWidget('CActiveForm', array(
						'id'=>'request-form',
						'enableAjaxValidation'=>false,
						'action'=>Yii::app()->createUrl("holes/request", array("id"=>$hole->ID)),
						'htmlOptions'=>Array ('onsubmit'=>"document.getElementById('pdf_form').style.display='none';"),
					)); 
					$model=new HoleRequestForm;
					$model->to=$hole->subject ? $hole->subject->gibdd->post_dative.' '.$hole->subject->gibdd->fio_dative : '';
					$model->from=CHtml::encode($hole->user->last_name.' '.$hole->user->name.' '.$hole->user->second_name);
					$model->address=CHtml::encode($hole->ADDRESS);
					$model->signature=CHtml::encode($hole->user->last_name.' '.substr($hole->user->name, 0, 1).($hole->user->name ? '.' : '').' '.substr($hole->user->second_name, 0, 1).($hole->user->second_name ? '.' : ''));
					?>					
						<h2><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM') ?></h2>
						<table>
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
							<? if($hole->type->alias == 'light'): ?>
								<tr>
									<th><?php echo $form->labelEx($model,'comment'); ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_COMMENT_COMMENT') ?></span></th>
									<td><?php echo $form->textArea($model,'comment',array('rows'=>3, 'cols'=>40)); ?></td>
								</tr>
							<? endif; ?>
							<tr>
								<th><?php echo $form->labelEx($model,'signature'); ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_SIGNATURE_COMMENT') ?></span></th>
								<td><?php echo $form->textField($model,'signature',array('class'=>'textInput')); ?></td>
							</tr>
							<tr>
								<th></th>
								<td>
									<?php echo CHtml::submitButton(Yii::t('holes_view', 'HOLE_REQUEST_FORM_SUBMIT'), Array('class'=>'submit', 'name'=>'HoleRequestForm[pdf]')); ?>
									<?php echo CHtml::submitButton(Yii::t('holes_view', 'HOLE_REQUEST_FORM_SUBMIT2'), Array('class'=>'submit', 'name'=>'HoleRequestForm[html]')); ?>
								</td>
							</tr>
						</table>
					<?php $this->endWidget(); ?>
					<?= Yii::t('holes_view', 'ST1234_INSTRUCTION') ?>
				</div>
			<? endif; ?>
			</div>
			</div>
		</div>
		<div class="social">
			<div class="like">
				<!-- Facebook like -->
				<div id="fb_like">
					<iframe src="http://www.facebook.com/plugins/like.php?href=<?=Yii::app()->request->hostInfo?>/<?=Yii::app()->request->pathInfo?>&amp;layout=button_count&amp;show_faces=false&amp;width=180&amp;action=recommend&amp;font&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:180px; height:21px;" allowTransparency="true"></iframe>
				</div>
				<!-- Vkontakte like -->
				<div id="vk_like"></div>
				<script type="text/javascript">VK.Widgets.Like("vk_like", {type: "button", verb: 1});</script>
			</div>
			<div class="share">
				<span>Поделиться</span>
				<a href="http://www.facebook.com/sharer.php?u=<?=Yii::app()->request->hostInfo?>/<?=Yii::app()->request->pathInfo?>" class="fb" target="_blank">Facebook</a>
				<a href="http://vkontakte.ru/share.php?url=<?=Yii::app()->request->hostInfo?>/<?=Yii::app()->request->pathInfo?>" class="vk" target="_blank">VK</a>
				<a href="http://twitter.com/share" class="twitter-share-button" data-text="Обнаружен дефект на дороге по адресу <?= CHtml::encode($hole->ADDRESS) ?>" data-count="none">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
			</div>
		</div>
	</div> 
	
</div>
<!-- CLOSE HEAD CONTAINER -->
</div>
<!-- CLOSE HEAD -->
</div>
<div class="mainCols" id="col">
	<div class="lCol">
		<script src="http://api-maps.yandex.ru/1.1/index.xml?key=<?php echo $this->mapkey; ?>" type="text/javascript"></script>
		<div id="ymapcontainer_big"><div align="right"><span class="close" onclick="document.getElementById('ymapcontainer_big').style.display='none';$('#col').css('marginBottom',0)">&times;</span></div><div id="ymapcontainer_big_map"></div></div>
		<?if($hole['LATITUDE'] && $hole['LONGITUDE']):?><div id="ymapcontainer" class="ymapcontainer"></div><?endif;?>
		<script type="text/javascript">
			var map_centery = <?= $hole['LATITUDE'] ?>;
			var map_centerx = <?= $hole['LONGITUDE'] ?>;
			var map = new YMaps.Map(YMaps.jQuery("#ymapcontainer")[0]);
			YMaps.Events.observe(map, map.Events.DblClick, function () { toggleMap(); } );
			map.enableScrollZoom();
			map.setCenter(new YMaps.GeoPoint(map_centerx, map_centery), 14);
			var s = new YMaps.Style();
			s.iconStyle = new YMaps.IconStyle();
			s.iconStyle.href = "/images/st1234/<?= $hole->type->alias;?>_<?= $hole['STATE'] ?>.png";
			s.iconStyle.size = new YMaps.Point(54, 61);
			s.iconStyle.offset = new YMaps.Point(-30, -61);
			var placemark = new YMaps.Placemark(new YMaps.GeoPoint(map_centerx, map_centery), { hideIcon: false, hasBalloon: false, style: s } );
			YMaps.Events.observe(placemark, placemark.Events.Click, function () { toggleMap(); } );
			map.addOverlay(placemark);
		</script>
		
		<div class="comment">
			<?= $hole['COMMENT1'] ?>
		</div>
		<div class="bbcode">
			<p><b>Ссылка на эту страницу:</b></p>
			<input onfocus="selectAll(this)" type="text" value='<a href="<?=Yii::app()->request->hostInfo?>/<?=Yii::app()->request->pathInfo?>">РосЯма :: <?= CHtml::encode($hole->ADDRESS) ?></a>'/>
			<p><b>BBcode для форума:</b></p>
			<textarea onfocus="selectAll(this)" rows="3">[url=<?=Yii::app()->request->hostInfo?>/<?=Yii::app()->request->pathInfo?>][img]<?=Yii::app()->request->hostInfo.'/'.$hole['pictures']['medium']['fresh'][0]?>[/img][/url][url=<?=Yii::app()->request->hostInfo?>/<?=Yii::app()->request->pathInfo?>] 
			РосЯма :: <?=CHtml::encode($hole['ADDRESS'])?>[/url]</textarea>
			
			
		</div>
</div>
<div class="rCol">
	<div class="b">
		<div class="before">
			<? if(sizeof($hole['pictures']['medium']['fixed'])): ?>
				<h2><?= Yii::t('holes_view', 'HOLE_ITWAS') ?></h2>
			<? endif; ?>
			<? foreach($hole['pictures']['medium']['fresh'] as $i=>$src): ?>
				<?php echo CHtml::link(CHtml::image($src), $hole['pictures']['original']['fresh'][$i], 
					Array('class'=>'holes_pict','rel'=>'hole', 'title'=>CHtml::encode($hole->ADDRESS))); ?>
			<? endforeach; ?>
		</div>
		<?php if($hole->request_gibdd && $hole->request_gibdd->answers): ?>
			<?php foreach($hole->request_gibdd->answers as $answer) : ?>		
			<div class="after">
				<?php if($answer->comment): ?>
				<div class="comment">
					<?php echo $answer->comment ?>
				</div>
				<? endif; ?>
				<h2><?= Yii::t('holes_view', 'HOLE_GIBDDREPLY') ?> от <?php echo date('d.m.Y',$answer->date);?></h2>
				<? foreach($answer->files_other as $file): ?>
					<?php echo CHtml::link($file->file_name, $answer->filesFolder.'/'.$file->file_name); ?><br />
				<? endforeach; ?>
				<?php if ($answer->files_other) : ?>
				<br />
				<?php endif; ?>
				<? foreach($answer->files_img as $img): ?>
					<?php echo CHtml::link(CHtml::image($answer->filesFolder.'/thumbs/'.$img->file_name), $answer->filesFolder.'/'.$img->file_name, 
						Array('class'=>'holes_pict','rel'=>'answer_'.$answer->id, 'title'=>'Ответ ГИБДД от '.date('d.m.Y',$answer->date))); ?>
				<? endforeach; ?>
			
			</div>
			<?php endforeach; ?>	
		<?php endif; ?>
		<?php if($hole['STATE'] == 'fixed'): ?>
			<div class="after">
				<? if(sizeof($hole['pictures']['medium']['fixed'])): ?>
					<h2><?= Yii::t('holes_view', 'HOLE_ITBECAME') ?></h2>
					<? foreach($hole['pictures']['medium']['fixed'] as $i=>$src): ?>
						<?php echo CHtml::link(CHtml::image($src), $hole['pictures']['original']['fixed'][$i], 
					Array('class'=>'holes_pict','rel'=>'hole_fixed', 'title'=>CHtml::encode($hole->ADDRESS).' - исправлено')); ?>
					<? endforeach; ?>
				<? endif; ?>
				<? if($hole['COMMENT2']): ?>
					<div class="comment">
						<?= $hole['COMMENT2'] ?>
					</div>
				<? endif; ?>
			</div>
		<? endif; ?>
	</div>
</div>