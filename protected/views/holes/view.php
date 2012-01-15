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
								<p><a href="/personal/edit.php?FIX_ID=<?= $hole['ID'] ?>" class="declarationBtn"><?=  Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_8') ?></a></p>
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
							<div class="cc" style="width:150px">
								<p><?=  Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_7') ?></p>
								<p><a href="/personal/edit.php?FIX_ID=<?= $hole['ID'] ?>" class="declarationBtn"><?=  Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_8') ?></a></p>
							</div>
							<div class="rc" style="width:145px;padding: 24px 0 24px 15px;">
								<p>Если вас не устраивает ответ ГИБДД, то можно</p>
								<p><a href="#" onclick="var c=document.getElementById('prosecutor_form2');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;">подать Заявление в Прокуратуру</a></p>
								<div class="pdf_form" id="prosecutor_form2"<?= isset($_GET['show_prosecutor_form2']) ? ' style="display: block;"' : '' ?>>
									<a href="#" onclick="var c=document.getElementById('prosecutor_form2');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;" class="close">&times;</a>
									<form action="?pdf" method="post" onsubmit="document.getElementById('prosecutor_form2').style.display='none';">
										<input type="hidden" name="form_type" value="prosecutor2">
										<?=  Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM2_PREFACE') ?>
										<table>
											<tr>
												<th><?=  Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM_TO') ?></th>
												<td><textarea rows="3" cols="40" id="prosecurtor_form_to" name="to"><?= $arResult['PROSECUTOR_FORM_TO'] ?></textarea></td>
											</tr>
											<tr>
												<th><?=  Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM_FROM') ?></th>
												<td><textarea rows="3" cols="40" id="presecutor_form_from" name="from"><?= CHtml::encode($arResult['AUTHOR']['LAST_NAME'].' '.$arResult['AUTHOR']['NAME'].' '.$arResult['AUTHOR']['SECOND_NAME']) ?></textarea></td>
											</tr>
											<tr>
												<th><?= Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM_POSTADDRESS') ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM_POSTADDRESS_COMMENT') ?></span></th>
												<td><textarea rows="3" cols="40" id="prosecutor_form_postaddress" name="postaddress"></textarea></td>
											</tr>
											<tr>
												<th><?= Yii::t('holes_view', 'HOLE_PRESECUTOR_FORM_ADDRESS') ?></th>
												<td><textarea rows="3" cols="40" id="prosecutor_form_address" name="address"><?= CHtml::encode($hole['ESS']) ?></textarea></td>
											</tr>
											<tr>
												<th><?= Yii::t('holes_view', 'HOLE_PRESECUTOR_FORM_GIBDD') ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_PRESECUTOR_FORM_GIBDD_COMMENT') ?></span></th>
												<td><textarea rows="3" cols="40" id="prosecutor_form_address" name="gibdd"><?= $arResult['PROSECUTOR_GIBDD'] ?></textarea></td>
											</tr>
											<tr>
												<th><?= Yii::t('holes_view', 'HOLE_PRESECUTOR_FORM_GIBDD_REPLY') ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_PRESECUTOR_FORM_GIBDD_COMMENT2') ?></span></th>
												<td><textarea rows="3" cols="40" id="prosecutor_form_gibdd_reply" name="gibdd_reply"></textarea></td>
											</tr>
											<tr>
												<th><?= Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM_APPLICATION_DATA') ?></th>
												<td><input type="text" id="prosecutor_form_application" name="application_data"></td>
											</tr>
											<tr>
												<th><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_SIGNATURE') ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_SIGNATURE_COMMENT') ?></span></th>
												<td><input type="text" class="textInput" id="pdf_form_signature" name="signature" value="<?= CHtml::encode($arResult['AUTHOR']['LAST_NAME'].' '.substr($arResult['AUTHOR']['NAME'], 0, 1).($arResult['AUTHOR']['NAME'] ? '.' : '').' '.substr($arResult['AUTHOR']['SECOND_NAME'], 0, 1).($arResult['AUTHOR']['SECOND_NAME'] ? '.' : '')) ?>"></td>
											</tr>
											<tr>
												<th></th>
												<td>
													<input type="submit" class="submit" value="<?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_SUBMIT') ?>">
													<input type="submit" name="html" class="submit" value="<?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_SUBMIT2') ?>">
												</td>
											</tr>
										</table>
										<strong><?= CHtml::encode($arResult['PROSECUTOR_DATA']['NAME']) ?></strong>
										<p><?= strip_tags($arResult['PROSECUTOR_DATA']['PREVIEW_TEXT'], '<br>') ?></p>
									</form>
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
							<a href="/personal/edit.php?FIX_ID=<?= $hole['ID'] ?>" class="declarationBtn"><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_8') ?></a>
						</div>
						<div class="rc" style="width:184px;padding: 24px 0 24px 15px;">
							<p><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_16') ?></p>
							<p><a href="#" onclick="var c=document.getElementById('prosecutor_form');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;" class="declarationBtn"><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_14') ?></a></p>
							<p><a href="/personal/edit.php?PROSECUTOR_ID=<?= $hole['ID'] ?>" class="declarationBtn">Жалоба в прокуратуру подана</a></p>
						</div>
						<div class="pdf_form" id="prosecutor_form"<?= isset($_GET['show_prosecutor_form']) ? ' style="display: block;"' : '' ?>>
							<a href="#" onclick="var c=document.getElementById('prosecutor_form');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;" class="close">&times;</a>
							<form action="?pdf" method="post" onsubmit="document.getElementById('prosecutor_form').style.display='none';">
								<input type="hidden" name="form_type" value="prosecutor">
								<?= Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM_PREFACE') ?>
								<table>
									<tr>
										<th><?= Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM_TO') ?></th>
										<td><textarea rows="3" cols="40" id="prosecurtor_form_to" name="to"><?= $arResult['PROSECUTOR_FORM_TO'] ?></textarea></td>
									</tr>
									<tr>
										<th><?= Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM_FROM') ?></th>
										<td><textarea rows="3" cols="40" id="presecutor_form_from" name="from"><?= CHtml::encode($arResult['AUTHOR']['LAST_NAME'].' '.$arResult['AUTHOR']['NAME'].' '.$arResult['AUTHOR']['SECOND_NAME']) ?></textarea></td>
									</tr>
									<tr>
										<th><?= Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM_POSTADDRESS') ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM_POSTADDRESS_COMMENT') ?></span></th>
										<td><textarea rows="3" cols="40" id="prosecutor_form_postaddress" name="postaddress"></textarea></td>
									</tr>
									<tr>
										<th><?= Yii::t('holes_view', 'HOLE_PRESECUTOR_FORM_ADDRESS') ?></th>
										<td><textarea rows="3" cols="40" id="prosecutor_form_address" name="address"><?= CHtml::encode($hole['ADDRESS']) ?></textarea></td>
									</tr>
									<tr>
										<th><?= Yii::t('holes_view', 'HOLE_PRESECUTOR_FORM_GIBDD') ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_PRESECUTOR_FORM_GIBDD_COMMENT') ?></span></th>
										<td><textarea rows="3" cols="40" id="prosecutor_form_address" name="gibdd"><?= $arResult['PROSECUTOR_GIBDD'] ?></textarea></td>
									</tr>
									<tr>
										<th><?= Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM_APPLICATION_DATA') ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_PROSECUTOR_FORM_APPLICATION_DATA_COMMENT') ?></span></th>
										<td><input type="text" id="prosecutor_form_application" name="application_data"></td>
									</tr>
									<tr>
										<th><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_SIGNATURE') ?><span class="comment"><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_SIGNATURE_COMMENT') ?></span></th>
										<td><input type="text" class="textInput" id="pdf_form_signature" name="signature" value="<?= CHtml::encode($arResult['AUTHOR']['LAST_NAME'].' '.substr($arResult['AUTHOR']['NAME'], 0, 1).($arResult['AUTHOR']['NAME'] ? '.' : '').' '.substr($arResult['AUTHOR']['SECOND_NAME'], 0, 1).($arResult['AUTHOR']['SECOND_NAME'] ? '.' : '')) ?>"></td>
									</tr>
									<tr>
										<th></th>
										<td>
											<input type="submit" class="submit" value="<?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_SUBMIT') ?>">
											<input type="submit" name="html" class="submit" value="<?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_SUBMIT2') ?>">
										</td>
									</tr>
								</table>
								<strong><?= CHtml::encode($arResult['PROSECUTOR_DATA']['NAME']) ?></strong>
								<p><?= strip_tags($arResult['PROSECUTOR_DATA']['PREVIEW_TEXT'], '<br>') ?></p>
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
							<a href="/personal/edit.php?FIX_ID=<?= $hole['ID'] ?>" class="declarationBtn"><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_8') ?></a>
						</div>
						<div class="cc">
							<a href="/personal/edit.php?REPROSECUTOR_ID=<?= $hole['ID'] ?>" class="declarationBtn">Аннулировать факт отправки заявления в прокуратуру</a>
						</div>
						<?
						break;
					}
					case 'fixed':
					default:
					{
						if($arResult['allow_cancel_fix'])
						{
							?>
							<a href="/personal/edit.php?REFIX_ID=<?= $hole['ID'] ?>"  class="declarationBtn"><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_13') ?></a>
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
					<form action="?pdf" method="post" onsubmit="document.getElementById('pdf_form').style.display='none';">
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
				<?/*<h2><?= Yii::t('holes_view', 'HOLE_ITWAS') ?></h2>*/?>
			<? endif; ?>
			<? foreach($hole['pictures']['medium']['fresh'] as $src): ?>
				<img src="<?= $src ?>">
			<? endforeach; ?>
		</div>
		<? if(sizeof($hole['pictures']['medium']['gibddreply'])): ?>
			<div class="after">
				<? if($hole['COMMENT_GIBDD_REPLY']): ?>
				<div class="comment">
					<?= $hole['COMMENT_GIBDD_REPLY'] ?>
				</div>
				<? endif; ?>
				<h2><?= Yii::t('holes_view', 'HOLE_GIBDDREPLY') ?></h2>
				<? foreach($hole['pictures']['medium']['gibddreply'] as $src): ?>
					<img src="<?= $src ?>">
				<? endforeach; ?>
			
			</div>
		<? endif; ?>
		<? if($hole['STATE'] == 'fixed'): ?>
			<div class="after">
				<? if(sizeof($hole['pictures']['medium']['fixed'])): ?>
					<h2><?= Yii::t('holes_view', 'HOLE_ITBECAME') ?></h2>
					<? foreach($hole['pictures']['medium']['fixed'] as $src): ?>
						<img src="<?= $src ?>">
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