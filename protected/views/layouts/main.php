<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo CHtml::encode($this->pageTitle); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="language" content="ru" />
<meta name="copyright" content="rosyama" />
<meta name="robots" content="index, follow" />
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
			<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'О проекте', 'url'=>array('/site/page', 'view'=>'about')),
				array('label'=>'Карта', 'url'=>array('/holes/map')),
				array('label'=>'Нормативы', 'url'=>array('/site/page', 'view'=>'regulations')),
				array('label'=>'Статистика', 'url'=>array('/statics/index')),
				array('label'=>'FAQ', 'url'=>array('/site/page', 'view'=>'faq')),
				array('label'=>'Справочники', 'url'=>array('/sprav/index')),
				//array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest)
			),
			'htmlOptions'=>array('class'=>'menu'),
			'firstItemCssClass'=>'first',
			'activeCssClass'=>'selected',
		)); ?>
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
			</div>
			<div class="auth">
			<?php if(!Yii::app()->user->isGuest) : ?>
					<?php echo CHtml::link('<img src="/images/logout.png" alt="Выйти" />',Array('/site/logout'),Array('title'=>'Выйти')); ?>
					<div class="name">
						<p><?php echo CHtml::link(Yii::app()->user->fullname,Array('/holes/personal')); ?></p><span class="grad"></span>
					</div>
				<?php else: ?>
					<?php echo CHtml::link('Войти',Array('/holes/personal'),Array('title'=>'Войти', 'class'=>'profileBtn')); ?>
				<? endif; ?>
					<style type="text/css">
						.auth .name
						{
							width: 150px !important;
						}
						
					</style>
					
			</div>
		</div>
	</div>	
		<?php echo $content; ?>

	<div class="footer">
		<div class="container">
			<p class="autochmo"><a target="_blank" href="http://autochmo.ru/" title="Доска позора водителей &aring;вточмо">&aring;utochmo</a><br>Доска позора водителей</p>
			<p class="copy">&copy; <a href="http://navalny.ru/">Алексей Навальный</a>, 2011<br />
			<a href="mailto:rossyama@gmail.com">rossyama@gmail.com</a><br />
			<br />	
			<?php $this->widget('application.widgets.collection.collectionWidget'); ?>			
			<p class="friends">Чиним ямы <a href="http://ukryama.com/">в Украине</a>, <a href="http://belyama.by/">Беларуси</a> и <a href="http://kazyama.kz/">Казахстане</a></p>
		</div>
	</div>
	<script type="text/javascript">
	reformal_wdg_domain    = "rosyama";
	reformal_wdg_mode    = 0;
	reformal_wdg_title   = "rosyama";
	reformal_wdg_ltitle  = "Оставьте отзыв";
	reformal_wdg_lfont   = "";
	reformal_wdg_lsize   = "";
	reformal_wdg_color   = "#FFA000";
	reformal_wdg_bcolor  = "#516683";
	reformal_wdg_tcolor  = "#FFFFFF";
	reformal_wdg_align   = "left";
	reformal_wdg_charset = "utf-8";
	reformal_wdg_waction = 0;
	reformal_wdg_vcolor  = "#9FCE54";
	reformal_wdg_cmline  = "#E0E0E0";
	reformal_wdg_glcolor  = "#105895";
	reformal_wdg_tbcolor  = "#FFFFFF";
	
	reformal_wdg_bimage = "fb17bdca7e3a07420c91c07d5ef7e4f4.png";
	
	</script>
	
	<script type="text/javascript" language="JavaScript" src="http://reformal.ru/tab6.js?charset=utf-8"></script><noscript><a href="http://rosyama.reformal.ru">rosyama feedback </a> <a href="http://reformal.ru"><img src="http://reformal.ru/i/logo.gif" /></a></noscript>
	<script type="text/javascript">
	
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-21943923-3']);
	  _gaq.push(['_trackPageview']);
	
	  (function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	
	</script>
	<? if (!Yii::app()->user->isGuest && $flash=Yii::app()->user->getFlash('user')):?>
		<div id="addDiv">
			<div id="fon">
			</div>
			<div id="popupdiv">
			<?php echo ($flash); ?>			
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
	<?endif?>
	
	</body>
	</html>