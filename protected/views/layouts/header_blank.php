<?php $this->beginContent('//layouts/main'); ?>
<div class="head">
		<div class="container">
			<div class="lCol">
												<a href="/" class="logo" title="На главную"><img src="/images/logo.png"  alt="Логотип" /></a>
											</div>
			
<h1><?php echo $this->title; ?></h1>
</div></div>
<br clear="all">
	

	<div class="mainCols">
		<?php /* if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif; */ ?>
	<?php echo $content; ?>
	</div>		
	
<?php $this->endContent(); ?>
