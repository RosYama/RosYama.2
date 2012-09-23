<?php $this->beginContent('//layouts/main'); ?>
<div class="head">
		<div class="container">
			<div class="lCol">
					<a href="/" class="logo" title="На главную"><img src="/images/logo.png"  alt="Логотип" /></a>
			</div>

			<div class="rCol">
				<ul class="aboutProject">
					<li class="about1">Добавить факт и&nbsp;отправить заявление в&nbsp;местное ГИБДД <em></em></li>
					<li class="about2">Ждать 37&nbsp;календарных дней с&nbsp;момента регистрации вашего заявления</li>
					<li class="about3">Если дефект не&nbsp;отремонтировали, отправлять жалобу в&nbsp;прокуратуру</li>
				</ul>
			</div>
		</div>
	</div>
	

	<div class="mainCols">
	<?php echo $content; ?>
	</div>		
	
<?php $this->endContent(); ?>
