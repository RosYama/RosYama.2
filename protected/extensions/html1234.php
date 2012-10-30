<?php

/**
 * Файлик, генерирующий заявление в HTML вместо PDF.
 */

class html1234 extends pdf1234
{
	// конструктор
	public function __construct() { }
	
	// получение HTML
	public function gethtml($temp, $params, $image = null, $printAllPictures=true, $textonly=false){
		$this->params = pdf1234::regexp($params);
		if(is_object($temp) || method_exists(__CLASS__,'text_'.$temp))
		{
			$this->temp = $temp;
		}
		elseif (count ($this->models) < 2) return false;
		$this->note = count($image);
		$this->template($textonly);
		if(is_array($image) && $this->temp != 'prosecutor' && $this->temp != 'prosecutor2')
		{
			if ($textonly) echo "Ссылки на фотографии:\n";
			foreach($image as $im_path)
			{
				if(!empty($im_path))
				{
					if (!$textonly) echo '<p><img src="'.$im_path.'"></p>';
					else echo $im_path."\n";
				}
			}
		}
		
		// Обработка и вывод картинок на многоям
		if ($this->models && $printAllPictures)
			foreach($this->models as $model){
				echo $textonly ? $model->ADDRESS."\n" : '<h3>'.$model->ADDRESS.'</h3>';
				foreach($model->pictures_fresh as $picture)
					{
						if (!$textonly) echo '<p><img src="'.$picture->original.'"></p>'; 
						else echo $picture->original."\n";
					}
			}
		
		$this->getsignature($textonly);
		if (!$textonly) echo '</body></html>';
	}
	
	// собственно шаблон
	protected function template($textonly=false)
	{
		if (count ($this->models) < 2){
			if (!is_object($this->temp)) $arResult = call_user_func(array(__CLASS__, 'text_'.$this->temp));
			else $arResult=$this->getTypeTemplate();
		}
		else $arResult=$this->text_manyholes($this->models);
		
		$header = $this->header();
		$footer = $this->footer();
		$name   = $this->name();
		if (!$textonly) {
		ob_start();
		{
?><!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf8">
		<title><?= $name ?></title>
	</head>
	<body style="padding: 30px;">
		<div style="margin-left: 50%;">
			<? foreach($header as $h): ?>
				<p><?= $h ?></p>
			<? endforeach; ?>
		</div>
		<h1 style="text-align: center;"><?= $name ?></h1>
		<p><?= $arResult['body0'] ?> <?php if(!isset($arResult['holes'])) echo $arResult['body1']; else { ?>
			<ul>
			<?php foreach ($arResult['holes'] as $str) : ?>
			<li><?php echo $str; ?></li>
			<?php endforeach; ?>
			</ul>
			<?php } ?>
		</p>
		<p><?= $arResult['footerUP0'] ?></p>
			<? foreach($arResult['count'] as $i=>$c): ?>
				<p style="padding-left:20px;"><?php echo $i; ?>.&nbsp;<?= $c ?></p>
			<? endforeach; ?>

		<p><?= $footer[0] ?></p><?
		}
		$buf = ob_get_clean();
		echo $buf;
		}
		else {
			 foreach($header as $h)
				echo $h."\n";
			echo $name."\n";	
			echo $arResult['body0']."\n";
			if(!isset($arResult['holes'])) echo $arResult['body1']."\n"; 
			else foreach ($arResult['holes'] as $str) 
				echo $str."\n";

		}
	}
	
	// добавление подписи
	protected function getsignature($textonly=false)
	{
		if (!$textonly) echo '<p>'.'<p>';
		if($this->temp == 'prosecutor' || $this->temp == 'prosecutor2')
		{
			$date = date('d.m.Y');
		}
		else
		{
			$date = $this->params['date2.day'].'.'.$this->params['date2.month'].'.'.$this->params['date2.year'];
		}
		if (!$textonly) echo '<div style="float: right;">'.$this->params['signature'].'</div><p>'.$this->signature().'</p>'.$date;
		else echo $this->params['signature']."\n".$this->signature()."\n".$date; 
	}
}

?>