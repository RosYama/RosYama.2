<?
$this->pageTitle=Yii::app()->name . ' :: Карточка дефекта';
?>
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
			<p><span class="date"><?php echo CHtml::encode(Y::dateFromTime($hole->DATE_CREATED)); ?></span><?php echo CHtml::link(CHtml::encode($hole->user->getParam('showFullname') ? $hole->user->Fullname : $hole->user->username), array('/profile/view', 'id'=>$hole->user->id),array('class'=>""));?></p>
			<p class="type type_<?= $hole->type->alias ?>"><?= $hole->type->name; ?><?php if ($hole->archive) echo ' (в архиве)';?></p>
			<p class="address"><?= CHtml::encode($hole->ADDRESS) ?></p>
			<p class="status">
				<span class="bull <?= $hole->STATE ?>">&bull;</span>
				<span class="state">
					<?= CHtml::encode($hole->StateName) ?>
					<? if($hole->STATE == 'prosecutor' && $hole->DATE_STATUS): ?>
						<?= CHtml::encode(Y::dateFromTime($hole->DATE_STATUS)).' '.Yii::t('holes_view', 'REQUEST_TO_PROSECUTOR_SENT') ?>
					<? elseif($hole->DATE_SENT): ?> 
						<?php if ($hole->requests_with_answers) echo (CHtml::encode(Y::dateFromTime($hole->requests_with_answers[0]->answers[0]->date))).'<br />'; ?>
						<?php if (count($hole->requests) == 1) : ?>
							<?= CHtml::encode(Y::dateFromTime($hole->DATE_SENT))?> отправлен запрос в ГИБДД
						<? else : ?>
							<?= CHtml::encode(Y::dateFromTime($hole->DATE_SENT))?> был отправлен первый запрос в ГИБДД 
						<? endif; ?>	
					<? endif; ?>
					<?php if (count($hole->requests) > 1) : ?>
						<br/><a href="#" onclick="$('#requests_gibdd_history').toggle('slow'); return false;">история запросов</a>
							<div id="requests_gibdd_history" style="display:none;">
							<ul>
							<?php foreach ($hole->requests as $request) : ?>
							<?php  if ($request->user) : ?>
								<li><?php echo date('d.m.Y',$request->date_sent);?> <?php echo $userlink=CHtml::link(CHtml::encode($request->user->getParam('showFullname') ? $request->user->Fullname : ($request->user->name ? $request->user->name : $request->user->username)), array('/profile/view', 'id'=>$request->user->id),array('class'=>""));?>  отправил запрос в <?php echo $request->type=="gibdd" ? "ГИБДД" : "прокуратуру";?>
								<?php if ($hole->STATE == 'fixed' && $fix=$hole->getFixByUser($request->user->id)) : ?> 
								<br /><?php echo date('d.m.Y',$fix->date_fix);?> <?php echo $userlink; ?> отметил факт исправления дефекта
								<?php endif; ?>
								</li>
							<?php endif; ?>
							<?php endforeach; ?>
							<li>==========</li>
							</ul>							
							</div>
					<?php endif;?>
					<? if($hole->STATE == 'fixed' && ($hole->fixeds || $hole->DATE_STATUS)): ?>
						<?= CHtml::encode(Y::dateFromTime($hole->fixeds ? $hole->fixeds[0]->date_fix : $hole->DATE_STATUS))?> отмечен факт исправления дефекта
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
			<? if(!Yii::app()->user->isGuest): ?>
				<? if(Yii::app()->user->IsAdmin) : ?>
					<p>
						<div class="errortext">
						Вы обладаете административными полномочиями
						<br/>
						</div>
					</p>
				<? endif; ?>
				
				<?php if(!$hole->PREMODERATED) : ?>
				<p>
						<div class="errortext">
						<?php echo  Yii::t('holes_view', 'PREMODRATION_WARNING');?>
						<br/>
						</div>
				</p>
				<? endif; ?>
				
				<?			
				switch($hole->STATE)
				{
					case 'fresh':
					{
						?>
						<? if($hole->IsUserHole || Yii::app()->user->IsAdmin): ?>
						<div class="edit">
							<?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_2'), array('update', 'id'=>$hole->ID), array('class'=>'profileBtn')); ?>
							<?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_10'), array('personalDelete', 'id'=>$hole->ID), array('class'=>'profileBtn', 'onclick'=>'return confirm("'.Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_11').'");')); ?>
						</div>
						<? endif; ?>
						<div class="progress">
							<div class="lc">
								<a href="#" onclick="var c=document.getElementById('pdf_form');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;" class="printDeclaration"><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_4') ?></a>
							</div>
							<div class="cc">
								<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_6'), array('sent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
								<? if($hole->IsUserHole || Yii::app()->user->IsAdmin): ?>
								<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_8a'), array('fix', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
								<? endif; ?>
							</div>
							<div class="rc">
								Также можно отправить:<br />
								<span><ins>&mdash;</ins>с&nbsp;официального сайта <a href="http://www.gibdd.ru/letter" target="_blank">ГИБДД&nbsp;МВД&nbsp;России</a></span>
							</div>
						</div>
						<?
						break;
					}
					case 'inprogress':
					{
						?>
						<? if($hole->request_gibdd): ?>
						<?php if ($hole->IsUserHole || Yii::app()->user->level > 40) : ?>
							<div class="cc" style="width:150px">
								<p><?=  Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_7') ?></p>
								<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_8'), array('fix', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
							</div>
						<?php endif; ?>	
							<div class="rc" style="width:145px;padding: 24px 0 24px 15px;">
								<p><a class="declarationBtn" href="#" onclick="var c=document.getElementById('pdf_form');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;"><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_15') ?></a></p>
								<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_12'), array('notsent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
								<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_GIBDD_REPLY_RECEIVED'), array('gibddreply', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
							</div>
						<? else : ?>	
						<!--<p>Заявление в ГИБДД уже было отправлено если Вы тоже отправили заявление по этому дефекту, отметте ниже</p>-->
							<div class="cc">
								<p><a href="#" onclick="var c=document.getElementById('pdf_form');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;" class="declarationBtn"><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_4') ?></a></p>
								<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_6'), array('sent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
							</div>
							<div class="rc">
								Также можно отправить:<br />
								<span><ins>&mdash;</ins>с&nbsp;официального сайта <a href="http://www.gibdd.ru/letter" target="_blank">ГИБДД&nbsp;МВД&nbsp;России</a></span>
							</div>						
						<? endif; ?>	
						<?
						break;
					}
					case 'gibddre':
					{
						?>
						<? if($hole->request_gibdd && $hole->request_gibdd->answers): ?>
							<div class="lc" style="width:150px">
								<p><?=  Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_7') ?></p>
								<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_8'), array('fix', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
							</div>
							<div class="cc"><?php echo CHtml::link('Ещё ответ из ГИБДД', array('gibddreply', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?>							
							</div>
							<div class="rc" style="width:145px;padding: 24px 0 24px 15px;">
								<p>Если вас не устраивает ответ ГИБДД, то можно</p>
								<p><a href="#" onclick="var c=document.getElementById('prosecutor_form2');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;">подать Заявление в Прокуратуру</a></p>
								<div class="pdf_form" id="prosecutor_form2"<?= isset($_GET['show_prosecutor_form2']) ? ' style="display: block;"' : '' ?>>								
								<?php $this->renderPartial('_form_prosecutor',Array('hole'=>$hole)); ?>	
								</div>
							</div>
						<? else : ?>							
							<div class="lc" style="width:150px">
							<?php if (!$hole->request_gibdd) : ?>
								<p>Вы тоже можете отправить свой запрос в ГИБДД по этому дефекту</p>
							<?php elseif(!$hole->request_gibdd->answers) : ?>
								<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_12'), array('notsent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
							<?php endif; ?>	
							</div>
							<div class="cc">
								<p><a href="#" onclick="var c=document.getElementById('pdf_form');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;" class="declarationBtn"><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_4') ?></a></p>
								<?php if (!$hole->request_gibdd) : ?>
								<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_6'), array('sent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
								<?php elseif(!$hole->request_gibdd->answers) : ?>									
									<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_GIBDD_REPLY_RECEIVED'), array('gibddreply', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
								<?php else : ?>
								<p><?php echo CHtml::link('Ещё ответ из ГИБДД', array('gibddreply', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
								<?php endif; ?>
							</div>
							<div class="rc">
								Также можно отправить <span><ins>&mdash;</ins>с&nbsp;официального сайта <a href="http://www.gibdd.ru/letter" target="_blank">ГИБДД&nbsp;МВД&nbsp;России</a></span>
							</div>						
						<? endif; ?>	
						<?
						break;
					}
					case 'achtung':
					{
						?>
						<? if($hole->request_gibdd): ?>
						<div class="cc" style="width:150px">
							<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_GIBDD_REPLY_RECEIVED'), array('gibddreply', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
							<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_12'), array('notsent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
							<?php if ($hole->IsUserHole || $hole->request_gibdd->answers || Yii::app()->user->level > 50) : ?>
							<p><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_7') ?></p>
							<?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_8'), array('fix', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?>
							<?php endif; ?>
						</div>
						<div class="rc" style="width:184px;padding: 24px 0 24px 15px;">
							<p><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_16') ?></p>
							<p><a href="#" onclick="var c=document.getElementById('prosecutor_form');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;" class="declarationBtn"><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_14') ?></a></p>
							<p><?php echo CHtml::link('Жалоба в прокуратуру подана', array('prosecutorsent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
						</div>
						<div class="pdf_form" id="prosecutor_form"<?= isset($_GET['show_prosecutor_form']) ? ' style="display: block;"' : '' ?>>
						<?php $this->renderPartial('_form_prosecutor_achtung',Array('hole'=>$hole)); ?>						
						</div>
						<? else : ?>						
							<div class="cc">
								<p><a href="#" onclick="var c=document.getElementById('pdf_form');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;" class="declarationBtn"><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_4') ?></a></p>
								<?php if (!$hole->request_gibdd) : ?>
								<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_6'), array('sent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
								<?php else : ?>
								<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_12'), array('notsent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
								<?php endif; ?>
							</div>
							<div class="rc">
								Также можно отправить <span><ins>&mdash;</ins>с&nbsp;официального сайта <a href="http://www.gibdd.ru/letter" target="_blank">ГИБДД&nbsp;МВД&nbsp;России</a></span>
							</div>	
						<? endif; ?>	
						<?
						break;
					}
					case 'prosecutor':
					{
						?>
						<? if(!$hole->request_gibdd): ?><?php endif; ?>
						<? if($hole->request_prosecutor): ?>
						<div class="lc" style="width:150px">
							<?php if($hole->request_gibdd && !$hole->request_gibdd->answers): ?>
									<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_GIBDD_REPLY_RECEIVED'), array('gibddreply', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>																		
								<?php elseif($hole->request_gibdd && $hole->request_gibdd->answers): ?>
									<p><?php echo CHtml::link('Ещё ответ из ГИБДД', array('gibddreply', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>									
								<?php endif; ?>	
						</div>
						<div class="cc">
							<p><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_7') ?></p>
							<?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_8'), array('fix', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?> 
						</div>
						<div class="cc">
							<?php echo CHtml::link('Аннулировать факт отправки заявления в прокуратуру', array('prosecutornotsent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?>
						</div>
						<? else : ?>
							<p>Для массовости отправьте свою жалобу в прокуратуру.</p>
							<div class="cc" style="width:150px">
								<?php if($hole->request_gibdd && !$hole->request_gibdd->answers): ?>
									<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_GIBDD_REPLY_RECEIVED'), array('gibddreply', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
									<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_12').' в ГИБДД', array('notsent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
									<p><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_7') ?></p>
									<?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_8'), array('fix', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?>
								<?php elseif($hole->request_gibdd && $hole->request_gibdd->answers): ?>
									<p><?php echo CHtml::link('Ещё ответ из ГИБДД', array('gibddreply', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>									
									<p><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_7') ?></p>
									<?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_8'), array('fix', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?>	
								<?php else: ?>	
								<p>Или отправьте еще одно заявление в ГИБДД:</p>
								<p><a href="#" onclick="var c=document.getElementById('pdf_form');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;" class="declarationBtn"><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_4') ?></a></p>
								<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_6'), array('sent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
								<?php endif; ?>	
							</div>
							
							<div class="rc" style="width:184px;padding: 24px 0 24px 15px;">
								<p><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_16') ?></p>
								<p><a href="#" onclick="var c=document.getElementById('prosecutor_form');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;" class="declarationBtn"><?= Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_14') ?></a></p>
								<p><?php echo CHtml::link('Жалоба в прокуратуру подана', array('prosecutorsent', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>
							</div>
							<div class="pdf_form" id="prosecutor_form"<?= isset($_GET['show_prosecutor_form']) ? ' style="display: block;"' : '' ?>>
							<?php $this->renderPartial('_form_prosecutor_achtung',Array('hole'=>$hole)); ?>													
							</div>
						<?php endif; ?>
						<?
						break;
					}
					case 'fixed':
					default:
					{
						if(!$hole->pictures_fixed)
						{
							?>
							<?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_TEXT_13'), array('defix', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?>
							<?
						}?>
												
						<? if($hole->request_gibdd && !$hole->request_gibdd->answers): ?>
							<p><?php echo CHtml::link(Yii::t('holes_view', 'HOLE_CART_ADMIN_GIBDD_REPLY_RECEIVED'), array('gibddreply', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>						
						<? elseif($hole->request_gibdd && $hole->request_gibdd->answers): ?>
							<p><?php echo CHtml::link('Ещё ответ из ГИБДД', array('gibddreply', 'id'=>$hole->ID),array('class'=>"declarationBtn")); ?></p>						
						<? endif; ?>
						
						<?php break;
					}
				}
				?>				
				<div class="pdf_form" id="pdf_form"<?= isset($_GET['show_pdf_form']) ? ' style="display: block;"' : '' ?>>
				<a href="#" onclick="var c=document.getElementById('pdf_form');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;" class="close">&times;</a>
				<?php /* echo CHtml::dropDownList('gibdd_id','',CHtml::listData($hole->territorialGibdd, 'id', 'gibdd_name' ),
									array(
									'prompt'=>'Выберете отдел ГИБДД',
									'ajax' => array(
									'type'=>'POST', //request type
									'url'=>$this->createUrl('requestForm', Array('type'=>'gibdd','hole'=>$hole->ID)), //url to call.
									'update'=>'#gibdd_form', //selector to update
									'data'=>'js:"id="+$(this).val()',
									'beforeSend'=>'js:function(){
														$("#gibdd_form").empty().addClass("loading");
													 }',
									'complete'=>'js:function(){
														$("#gibdd_form").removeClass("loading");
													 }',				 
									))); */ ?>
				<div id="gibdd_form"></div>
				<?php $this->renderPartial('_form_gibdd',Array('hole'=>$hole, 'gibdd'=>$hole->gibdd)); 
				?>
				</div>
			<?php else : ?>	
				<div class="progress">

								<p>		мешает эта яма? <?php  echo CHtml::link('авторизуйся и отправь заявление в гибдд', array('review','id'=>$hole->ID),array('class'=>"declarationBtn")); ?>.
								</p>


				</div>
			<?php endif; ?>
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

				<script type="text/javascript" src="http://yandex.st/share/share.js" charset="utf-8"></script>
				<div id="ya_share1"></div>
<script>
new Ya.share({
	element: 'ya_share1',
		elementStyle: {
			'type': 'none',
			'border': true,
			'quickServices': ['twitter', '|', 'vkontakte', 'facebook', '|', 'yaru', 'odnoklassniki', 'moimir']
		},
		serviceSpecific: {
			twitter: {
				title: 'Обнаружен дефект на дороге по адресу: <?= CHtml::encode($hole->ADDRESS) ?>'
			},
			facebook: {
				title: 'Обнаружен дефект на дороге по адресу: <?= CHtml::encode($hole->ADDRESS) ?>'
			},
			moimir: {
				title: 'Обнаружен дефект на дороге по адресу: <?= CHtml::encode($hole->ADDRESS) ?>'
			},
			odnoklassniki: {
				title: 'Обнаружен дефект на дороге по адресу: <?= CHtml::encode($hole->ADDRESS) ?>'
			}
		}
});
</script>

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
			<textarea onfocus="selectAll(this)" rows="3">[url=<?=Yii::app()->request->hostInfo?>/<?=Yii::app()->request->pathInfo?>]<?php if ($hole->pictures_fresh) : ?>[img]<?=Yii::app()->request->hostInfo.'/'.$hole->pictures_fresh[0]->medium?>[/img]<?php endif; ?>[/url][url=<?=Yii::app()->request->hostInfo?>/<?=Yii::app()->request->pathInfo?>] 
			РосЯма :: <?=CHtml::encode($hole['ADDRESS'])?>[/url]</textarea>
			
			
		</div>
</div>
<div class="rCol">
	<div class="b">
		<div class="before">
			<? if($hole->pictures_fixed): ?>
				<h2><?= Yii::t('holes_view', 'HOLE_ITWAS') ?></h2>
			<? endif; ?>
			<? foreach($hole->pictures_fresh as $i=>$picture): ?>
				<?php echo CHtml::link(CHtml::image($picture->medium), $picture->original, 
					Array('class'=>'holes_pict','rel'=>'hole', 'title'=>CHtml::encode($hole->ADDRESS))); ?>
			<? endforeach; ?>
		</div>
		<?php foreach($hole->requests_gibdd as $request): ?>
			<?php if($request->answers): ?>
				<?php foreach($request->answers as $answer) : ?>		
				<div class="after">
					<?php if($answer->comment): ?>
					<div class="comment">
						<?php echo $answer->comment ?>
					</div>
					<? endif; ?>
					<h2><?= Yii::t('holes_view', 'HOLE_GIBDDREPLY') ?> пользователю <?php echo $request->user->fullname;?>, от <?php echo date('d.m.Y',$answer->date);?>
					<?php if ($request->user_id==Yii::app()->user->id) : ?>
							<?php echo CHtml::link(CHtml::image('/images/update.png', 'Редактировать', Array('title'=>'Редактировать')), Array('gibddreply','id'=>$hole->ID,'answer'=>$answer->id), Array('class'=>'declarationBtn')); ?><br />
					<?php endif; ?>	
					</h2>				
					<?php if ($answer->files_other) : ?>
					<? foreach($answer->files_other as $file): ?>					
						<div class="answer_file <?php echo $file->divClass; ?>">
						<?php if ($request->user_id==Yii::app()->user->id) : ?>
							<?php echo CHtml::link(CHtml::image('/images/delete.png', 'Удалить файл', Array('title'=>'Удалить файл')), Array('delanswerfile','id'=>$file->id), Array('class'=>'delfileBtn')); ?>
						<?php endif; ?>											
						<?php echo CHtml::link(CHtml::image('/images/icon_'.$file->divClass.'.png', $file->file_name, Array('title'=>$file->file_name)), $answer->filesFolder.'/'.$file->file_name, Array('class'=>'declarationBtn')); ?>
						</div>
					<? endforeach; ?>	
					<div class="clear"></div>
					<br />
					<?php endif; ?>
					
					<? foreach($answer->files_img as $img): ?>
					<p>
						<?php if ($request->user_id==Yii::app()->user->id) : ?>
							<?php echo CHtml::link(CHtml::image('/images/delete.png', 'Удалить это изображение', Array('title'=>'Удалить это изображение')), Array('delanswerfile','id'=>$img->id), Array('class'=>'declarationBtn')); ?><br />
						<?php endif; ?>
						<?php echo CHtml::link(CHtml::image($answer->filesFolder.'/thumbs/'.$img->file_name), $answer->filesFolder.'/'.$img->file_name, 
							Array('class'=>'holes_pict','rel'=>'answer_'.$answer->id, 'title'=>'Ответ ГИБДД от '.date('d.m.Y',$answer->date))); ?>
					</p>		
					<? endforeach; ?>
				
				</div>
				<?php endforeach; ?>	
			<?php endif; ?>
		<?php endforeach; ?>
		<?php if($hole['STATE'] == 'fixed'): ?>
			<div class="after">
				<? if($hole->pictures_fixed): ?>
					<h2><?= Yii::t('holes_view', 'HOLE_ITBECAME') ?></h2>
					<? foreach($hole->pictures_fixed as $i=>$picture): ?>
					
					<?php if ($picture->user_id==Yii::app()->user->id || Yii::app()->user->level > 80 || $hole->IsUserHole) : ?>
							<?php echo CHtml::link(CHtml::image('/images/delete.png', 'Удалить это изображение', Array('title'=>'Удалить это изображение')), Array('delpicture','id'=>$picture->id), Array('class'=>'declarationBtn')); ?></br>
					<?php endif; ?>
					
						<?php echo CHtml::link(CHtml::image($picture->medium), $picture->original, 
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
		<br/>
		<?php  $this->widget('comments.widgets.ECommentsListWidget', array(
				'model' => $hole,
			));  ?>
	</div>
</div>
</div>
