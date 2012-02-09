<?php

/**
 * Файлик, генерирующий заявление в HTML вместо PDF.
 */

class html1234 extends pdf1234
{
	// конструктор
	public function __construct() { }
	
	// получение HTML
	public function gethtml($temp, $params, $image = null){
		$this->params = pdf1234::regexp($params);
		if(is_object($temp) || method_exists(__CLASS__,'text_'.$temp))
		{
			$this->temp = $temp;
		}
		else return false;
		$this->note = count($image);
		$this->template();
		if(is_array($image) && $this->temp != 'prosecutor' && $this->temp != 'prosecutor2')
		{
			foreach($image as $im_path)
			{
				if(!empty($im_path))
				{
					echo '<p><img src="'.$im_path.'"></p>';
				}
			}
		}
		$this->getsignature();
		echo '</body></html>';
	}
	
	// собственно шаблон
	protected function template()
	{
		if (!is_object($this->temp)) $arResult = call_user_func(array(__CLASS__, 'text_'.$this->temp));
		else $arResult=$this->getTypeTemplate();
		$header = $this->header();
		$footer = $this->footer();
		$name   = $this->name();
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
		<p><?= $arResult['body0'] ?> <?= $arResult['body1'] ?></p>
		<p><?= $arResult['footerUP0'] ?></p>
		<ol>
			<? foreach($arResult['count'] as $c): ?>
				<li><?= $c ?></li>
			<? endforeach; ?>
		</ol>
		<p><?= $footer[0] ?></p><?
		}
		$buf = ob_get_clean();
		echo $buf;
	}
	
	// добавление подписи
	protected function getsignature()
	{
		echo '<p>'.'<p>';
		if($this->temp == 'prosecutor' || $this->temp == 'prosecutor2')
		{
			$date = date('d.m.Y');
		}
		else
		{
			$date = $this->params['date2.day'].'.'.$this->params['date2.month'].'.'.$this->params['date2.year'];
		}
		echo '<div style="float: right;">'.$this->params['signature'].'</div><p>'.$this->signature().'</p>'.$date;
	}
}

?>