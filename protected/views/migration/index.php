<div class="head">
		<div class="container">
			<div class="lCol">
												<a href="/" class="logo" title="На главную"><img src="/images/logo.png"  alt="РосЯма" /></a>
											</div>
			
<h1><?php echo $this->title; ?></h1>
</div></div>
<br clear="all">
	

	<div class="mainCols">
	<div class="lCol">

	</div>
	<div class="rCol">
	<?php if(Holes::getDbConnection()->getSchema()->getTable(Holes::tableName())===null) : ?>
	<?php echo CHtml::ajaxLink('Cоздать структуру данных > ',$this->createUrl('makedata'), array(                                    
                                    'update'=>'#makedata_result',
                                    'beforeSend' => 'function(){
                                        $("#makedata_result").addClass("loading");
                                    }',
                                    'complete'=>'function(){                                        
                                        $("#makedata_result").removeClass("loading");
                                    }'
                                )); ?>
    <div id="makedata_result"></div>                            
	<?php endif; ?>
	<?php if(BHoles::getDbConnection()->getSchema()->getTable(BHoles::tableName())) : ?>
	<?php echo CHtml::ajaxLink('Импорт пользователей из старой базы > ',$this->createUrl('importUsers'), array(                                    
                                    'update'=>'#importusers_result',
                                    'beforeSend' => 'function(){
                                        $("#importusers_result").addClass("loading");
                                    }',
                                    'complete'=>'function(){                                        
                                        $("#importusers_result").removeClass("loading");
                                    }'
                                )); ?>
    <div id="importusers_result"></div>   
    
    <?php echo CHtml::ajaxLink('Импорт ям из старой базы > ',$this->createUrl('importHoles'), array(                                    
                                    'update'=>'#importholes_result',
                                    'beforeSend' => 'function(){
                                        $("#importholes_result").addClass("loading");
                                    }',
                                    'complete'=>'function(){                                        
                                        $("#importholes_result").removeClass("loading");
                                    }'
                                )); ?>
    <div id="importholes_result"></div> 
	<?php endif; ?>
	<br /><br />
	<?php echo CHtml::link('Удалить скрипт миграции', Array('delthis'), Array('class'=>'declarationBtn')); ?><br />
	</div>	
	</div>		