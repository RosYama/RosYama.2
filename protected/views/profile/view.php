  <div class="head">
		<div class="container">
			<div class="lCol"><a href="/" class="logo" title="На главную"><img src="/images/logo.png"  alt="РосЯма" /></a>
			</div>
				<div class="rCol">
								
					<div id="head_user_info">				
					<div class="photo">
						<?php if($model->relProfile && $model->relProfile->avatar) echo CHtml::image($model->relProfile->avatar_folder.'/'.$model->relProfile->avatar); ?>
					</div>
					<div class="info">		
						<h1><?php echo $model->fullName; ?></h1>
						<div class="www">
							<a target="_blank" href="http://"></a>
						</div>
					</div>
					<div class="counter">
						<?php echo Y::declOfNum($model->holes_cnt, array('дефект', 'дефекта', 'дефектов')); ?> / <?php echo Y::declOfNum($model->holes_fixed_cnt, array('отремонтирован', 'отремонтировано', 'отремонтировано')); ?>						
					</div>	
				</div>		
			</div>
		</div>
	</div>
	<div class="mainCols">

	</div>		
	
