<div id="cssmenu">
<?
$this->widget('zii.widgets.CMenu',array(
		'encodeLabel'=>true,
    	'items'=> $menu,
    	'activateParents'=>true,
    	//'submenuHtmlOptions'=>Array(),
    	)
); ?>
</div>
