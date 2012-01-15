<div id="addmess" style="display:none; color:#0C0"><p><b>Выберите место на карте и кликните по нему два раза, чтобы отметить расположение ямы.</b></p></div>
  <div class="head">
		<div class="container">
<div class="lCol">
												<a href="/" class="logo" title="На главную"><img src="/images/logo.png"  alt="РосЯма" /></a>
											</div>
						<div class="rCol">
				<script language="javascript">
<!--

function GetCheckNull()
{
	for(var i=0;i<4;i++)
	{
		document.getElementById("chn" + i).checked = 0;
	}
	for(var i=0;i<6;i++)
	{
		document.getElementById("ch" + i).checked = 0;
	}
}
-->
</script>
<h1>Карта дефектов</h1>

<?php $form=$this->beginWidget('CActiveForm',Array(
	'id'=>'map-form',
	'enableAjaxValidation'=>false,
)); ?>

<div class="filterCol filterStatus">
<p class="title">Показать дефекты со статусом</p>
<?php foreach ($model->allstatesMany as $alias=>$name) : ?>
	<label><span class="<?php echo $alias; ?>"><input id="chn0" name="Holes[STATE][]" type="checkbox"  value="<?php echo $alias; ?>" /></span><ins><?php echo $name; ?></ins></label>
<?php endforeach; ?>	
</div>
<div class="filterCol filterType">
<p class="title">Показать тип дефектов</p>
<?php /* echo $form->checkBoxList($model, 'type', CHtml::listData(HoleTypes::model()->findAll(Array('condition'=>'t.published=1', 'order'=>'t.ordering')), 'id', 'name'),
	Array(
	'template'=>'<div class="col1"><span>{input}</span><ins class="badroad">{label}</ins></div>',
	)
); */ ?>
<?php foreach ($types as $i=>$type) : ?>
<label class="col2"><span><input id="ch0" name="Holes[type][]" type="checkbox" value="<?php echo $type->id; ?>"   /></span><ins class="<?php echo $type->alias; ?>"><?php echo $type->name; ?></ins></label>
<?php /*
<label class="col2"><span><input id="ch1" name="TYPE[10]" type="checkbox"  value="hatch"   /></span><ins class="hatch">Люк</ins></label>
<!--<label class="col3"><span><input id="ch2" name="TYPE[1]" type="checkbox"  value="nomarking" /></span><ins class="nomarking">Нет разметки</ins></label>-->
<!--<label class="col4"><span><input id="ch3" name="TYPE[2]" type="checkbox"  value="light"  /></span><ins class="light">Светофор</ins></label> -->
<label class="col1"><span><input id="ch2" name="TYPE[3]" type="checkbox"  value="holeonroad"  /></span><ins class="holeonroad">Яма на дороге</ins></label>
<!--<label class="col2"><span><input id="ch5" name="TYPE[4]" type="checkbox"  value="crossing"  /></span><ins class="crossing">Ж.&nbsp;д. переезд</ins></label> -->
<label class="col3"><span><input id="ch3" name="TYPE[5]" type="checkbox"  value="rails"  /></span><ins class="rails">Рельсы</ins></label>
<label class="col4"><span><input id="ch4" name="TYPE[6]" type="checkbox"  value="policeman"  /></span><ins class="policeman">Полицейский</ins></label>
<label class="col1"><span><input id="ch5" name="TYPE[7]" type="checkbox"  value="holeinyard"  /></span><ins class="holeinyard">Яма во дворе</ins></label>
<!--<label class="col2"><span><input id="ch9" name="TYPE[8]" type="checkbox"  value="fence"  /></span><ins class="fence">Ограждение</ins></label>-->
<?php */ endforeach; ?>
<input id="MAPLAT" name="MAPLAT" type="hidden" value="" />
<input id="MAPZOOM" name="MAPZOOM" type="hidden" value="" />



</div>
<div class="submit"><input type="submit" name="button" id="button" value="Показать" /><input name="reset" value="Сбросить" onclick="GetCheckNull()" type="button" /></div>
<?php $this->endWidget(); ?>			</div>
		</div>
	</div>
	<div class="mainCols">
			
<div id="addmess" style="display:none; color:#0C0"><p><b>Выберите место на карте и кликните по нему два раза, чтобы отметить расположение ямы.</b></p></div>

<div class="bx-yandex-search-layout">
	<div class="bx-yandex-search-form">
		<form id="search_form_MAP_DzDvWLBsil" name="search_form_MAP_DzDvWLBsil" onsubmit="jsYandexSearch_MAP_DzDvWLBsil.searchByAddress(this.address.value); return false;">
			<p>Введите адрес места для быстрого поиска</p>
			<input type="text" id="address_inp" name="address" class="textInput" value="" style="width: 300px;" />
			<input type="submit" value="Искать" />
			<a style="display:none;" id="clear_result_link" href="#" onclick="clearSerchResults('MAP_DzDvWLBsil', JCBXYandexSearch_arSerachresults); document.getElementById('address_inp').value=''; return false;">Очистить</a>
		</form>
	</div>

	<div class="bx-yandex-search-results" id="results_MAP_DzDvWLBsil"></div>
		

<div class="bx-yandex-view-layout">
	<div class="bx-yandex-view-map">
<script type="text/javascript">

if (window.attachEvent) // IE
	window.attachEvent("onload", function(){init_MAP_DzDvWLBsil()});
else if (window.addEventListener) // Gecko / W3C
	window.addEventListener('load', function(){init_MAP_DzDvWLBsil()}, false);
else
	window.onload = function(){init_MAP_DzDvWLBsil()};

</script>
<?php
					$this->widget('application.extensions.ymapmultiplot.YMapMultiplot', array(
							'key'=>$this->mapkey,
						   'id' => 'BX_YMAP_MAP_DzDvWLBsil',//id of the <div> container created
						   'label' => 'Тест', //Title for bubble. Used if you are plotting multiple locations of same business
						   'address' =>  Array(), //Array of AR objects
						   'width'=>'100%',
						   'height'=>'600px',						   
						   //'notshow'=>true
					  ));
					?>
	</div>
</div>
<img src="/images/map_shadow.jpg" class="mapShadow" alt="" />

			</div>
	</div>
	</div>
