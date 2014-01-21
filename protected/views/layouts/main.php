<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="language" content="ru" />
<meta name="copyright" content="rosyama" />
<meta name="robots" content="index, follow" />
<title><?php echo CHtml::encode($this->pageTitle); ?></title>
<link rel="icon" href="/favicon.ico" type="image/x-icon" />

<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/styles.css" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/template_styles.css" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
<!--[if lte IE 7]><link rel="stylesheet" href="/css/ie.css" type="text/css" /><![endif]-->


<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?22"></script>

<script type="text/javascript">VK.init({apiId: 2232074, onlyWidgets: true});</script>

</head>

<body>

<script type="text/javascript">
					$(document).ready(function(){
						if ($('.name  a').width()>$('.auth .name').width())
							{
								$('.grad').show()
							}
					})
				</script>

<div class="wrap">

<div class="navigation">
		<div class="container">
			<div class="auth">
			<?php if(!$this->user->isGuest) : ?>
					<?php echo CHtml::link('<img src="/images/logout.png" alt="Выйти" />',Array('/site/logout'),Array('title'=>'Выйти')); ?>
					<div class="name">
						<p><?php echo CHtml::link($this->user->fullname,Array('/holes/personal')); ?></p><span class="grad"></span>
					</div>
				<?php else: ?>
					<?php echo CHtml::link('Войти',Array('/holes/personal'),Array('title'=>'Войти', 'class'=>'profileBtn')); ?>
				<?php endif; ?>


			</div>
			 <?php $this->widget('application.widgets.topmenu.topmenuWidget');?>

			<!--
			<div class="search">
				<form action="/map">
			<input type="image" name="s" src="/images/search_btn.gif" class="btn" /><input type="text" class="textInput inactive" name="q"  value="Поиск по адресу" />
	<script type="text/javascript">
		$(document).ready(function(){
			var startSearchWidth=$('.search').width();
			var startSearchInputWidth=$('.search .textInput').width();
			var time=200;

							var searchWidth=230;
				var	searchInputWidth=searchWidth-30;

										searchInputWidth-=47;
				searchWidth-=47;
							if ($.browser.msie && $.browser.version == 9) {
					searchInputWidth+=5;
					searchWidth+=5;
					}
				$('.search .textInput').click(function(){
					if ($(this).val()=='Поиск по адресу')
					{
						$(this).val('').removeClass('inactive');
					}
					$('.search').animate({width:searchWidth},time);
					$('.search .textInput').animate({width:searchInputWidth},time);
				})
				$('.search .textInput').blur(function(){

					if ($(this).val()=='')
					{
						$(this).val('Поиск по адресу').addClass('inactive');
					}
					$('.search').animate({width:startSearchWidth},time);
					$('.search .textInput').animate({width:startSearchInputWidth},time);
				})
			})
	</script>
	</form>
			</div> -->

			<div class="donation_button"><a href="http://donate.fbk.info/rosyama/">Поддержать РосЯму</a></div>
		</div>
	</div>
		<?php echo $content; ?>
		<?php $this->renderPartial('application.views.layouts._donateForm'); ?>
	<div class="footer">

		<div class="container">
		<div class="left_footer">
			&copy; <a href="http://navalny.ru/" target="_blank">Алексей Навальный</a>, 2011-2012
			<br /><a href="mailto:rossyama@gmail.com">rossyama@gmail.com</a>
			<br />
			<br/>Разработано в <a href="http://pixelsmedia.ru" target="_blank">Pixelsmedia</a>
			<br/>Powered by <a href="http://www.yiiframework.com/" target="_blank">Yii Framework</a>
			<br/><a href="/api/" target="_blank">API для разработчиков</a>
		</div>
		<div class="center_footer">
			<?php if($this->beginCache('countHoles', array('duration'=>3600))): ?>
			<?php $this->widget('application.widgets.collection.collectionWidget'); ?>
			<?php $this->endCache(); ?>
            <?php endif; ?>

			<p class="friends">Чиним ямы на <i class="flag-UA"></i> <a href="http://ukryama.com/">Украине</a>, в <i class="flag-BY"></i> <a href="http://belyama.by/">Беларуси</a> и <i class="flag-KZ"></i> <a href="http://kazyama.kz/">Казахстане</a></p>
		</div>
		<div class="right_footer">
			<p class="autochmo"><a target="_blank" href="http://autochmo.ru/" title="Доска позора водителей &aring;вточмо">&aring;utochmo</a><br>Доска позора водителей</p>
			Разработка прототипа и дизайна - <a href="http://greensight.ru">Greensight</a>
		</div>

		</div>
	</div>

	<div class="projects_links"> 
		<ul>
			<li class="no_bg">Проекты <a href="http://fbk.info">Фонда борьбы с коррупцией</a>:</li>
			<li class="rospil"><a href="http://rospil.info">&nbsp;</a></li>
			<li class="rosvybory"><a href="http://rosvybory.org">&nbsp;</a></li>
			<li class="rosyama"><span>&nbsp;</span></li>
			<li class="rosjkh"><a href="http://roszkh.ru">&nbsp;</a></li>
			<li class="rosdmp"><a href="http://mashina.org">&nbsp;</a></li>
		</ul>
	</div>
	
	<script type="text/javascript">
                var reformalOptions = {
                        project_id: 43983,
                        project_host: "rosyama.reformal.ru",
                        force_new_window: false,
                        tab_alignment: "left",
                        tab_top: "316",
                        tab_image_url: "/images/reformal_tab_orange.png" 
                };
                (function() {
                        if ('https:' == document.location.protocol) return;
                        var script = document.createElement('script');
                        script.type = 'text/javascript';
                        script.src = 'http://media.reformal.ru/widgets/v1/reformal.js';
                        document.getElementsByTagName('head')[0].appendChild(script);
                })();
        </script>


	<script type="text/javascript">
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-38325056-1']);
	  _gaq.push(['_trackPageview']);

	  (function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

	</script>
	<?php if (!$this->user->isGuest && $this->user->hasFlash('user')):?>
		<div id="addDiv">
			<div id="fon">
			</div>
			<div id="popupdiv">
			<?php echo $this->user->getFlash('user'); ?>
				 <span class="filterBtn close">
					<i class="text">Продолжить</i>
				 </span>
			</div>
		</div>

		<script type="text/javascript">
		$(document).ready(function(){
			$('.close').click(function(){
				$('#popupdiv').fadeOut(400);
				$('#fon').fadeOut(600);
				$('#addDiv').fadeOut(800);
			})
		})
		</script>
	<?php endif; ?>

	</body>
	</html>
