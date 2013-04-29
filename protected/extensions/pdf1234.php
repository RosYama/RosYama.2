<?php
/**
 *Генерация PDF
 */

class pdf1234{
	public $pdf;
	public $params;
	public $temp;
	public $models=Array();
	public $requestForm;
	private $note;
	
	public function __construct(){		
		$this->pdf = new tFPDF();
	}
	
	/**
	 *Основная функция. Выводит сгенерированный PDF
	 *@param string $temp тип дефекта
	 *@param array $params массив параметров:
	 *  $params['chief']      наименование структурного подразделения ГИБДД
	 *  $params['fio']        Фамилия Имя Отчество заявителя
	 *  $params['address']    индекс и почтовый адрес заявителя для переписки
	 *  $params['date1.day'] $params['date1.month']  $params['date1.year']  самая первая дата (стоящая после слова "ЗАЯВЛЕНИЕ") в формате dd.mm.yyyy
	 *  $params['street']     укажите улицу и номер ближайшего дома или перекресток улиц, километр шоссе и т.п.
	 *  $params['date2.*']    когда было отправленно заявление (для шаблона заявления в прокуратуру)
	 *  $params['date3.*']    когда был получен ответ из ГИБДД (для шаблона заявления в прокуратуру)
	 *@param array $image массив с картинками (если есть)
	 */
	public function getpdf($temp, $params, $image = null, $printAllPictures=true){
		$this->params = pdf1234::regexp($params);
		if(is_object($temp) || method_exists(__CLASS__,'text_'.$temp)){
			$this->temp = $temp;
		}		
		elseif (!$this->models) return false;
		$this->note = count($image);
		
		$this->pdf->Open();
		$this->pdf->AddFont('Arial','','',true);
		$this->pdf->SetFont('Arial', '', 9.6);
		$this->pdf->SetTextColor('black');
		$this->pdf->AddPage();
		
		$this->template();
		
		// Обработка  и вывод картинок
		/*if(is_array($image) && $this->temp != 'prosecutor' && $this->temp != 'prosecutor2')
		{
			foreach($image as $im_path){
					if(!empty($im_path)){
						$this->pdf->Image($im_path, null, null, 180, 0,'jpg');
					}
			}
		}*/
		// Обработка и вывод картинок на многоям
		if ($this->models &&  $printAllPictures)
			foreach($this->models as $i=>$model){
				if ($i > 0) $this->pdf->AddPage();
				if (count($this->models) > 1) pdf1234::getpages(pdf1234::slashN($model->ADDRESS, 100), 5, 10);
				foreach($model->pictures_fresh as $picture)
					{
						$this->pdf->Image($_SERVER['DOCUMENT_ROOT'].$picture->original, null, null, 180, 0,'jpg');
					}
			}
		
		pdf1234::getsignature();
		
		$this->pdf->Output('Statement '.date('Y-m-d H:i:s').'.pdf', 'D');
	}
	
	/**
	 *Функция очищающая входной массив от символов переносо/разрывов/etc строк
	 *@param array &$mass
	 *@return $mass
	 */
	protected function regexp($mass){
		if(is_array($mass) == true){
			foreach($mass as $key=>$val){
				$mass[$key] = preg_replace("/\t|\n|\a|\e|\v|\r/", ' ',$val);
			}
			return $mass;
		}else return false;
	}

	/**
	 *Возвращает массиив строк для шапки PDF
	 *@return array
	 */
	protected function header(){
		switch($this->temp)
		{
			case 'prosecutor2':
			case 'prosecutor':
			{
				$x[0] = 'В прокуратуру ';
				break;
			}
			default:
			{
				$x[0] = preg_match('/^Начальнику/i',$this->params['chief'])==1 ? '' : 'Начальнику ';
				break;
			}
		}
		$x[0] .= $this->params['chief'];
		$x[1] = 'От '.$this->params['fio'];
		$x[2] = 'Адрес: '.$this->params['address'];
		return $x;
	}
	
	/**
	 *Название заявления в зависимости от типа
	 *@return string
	 */
	protected function name(){
		
		switch($this->temp)
		{
			case 'prosecutor':
			{
				$x = 'Жалоба на бездействие органов ГИБДД';
				break;
			}
			case 'prosecutor2':
			{
				$x = "Заявление о нарушении законодательства Российской Федерации о содержании и ремонте автомобильных дорог и безопасности дорожного движения";
				break;
			}
			default:
			{
				$x = 'Заявление';
				break;
			}
		}
		return $x;
	}
	
	/**
	 *Подвал текствовой части заявления
	 *@return string
	 */
	protected function footer()
	{
		$x=Array('');
		if($this->note!=0){
			$x[0] = 'Приложение: '.$this->note.' фотографи'.pdf1234::getEnd($this->note);
			
			if($this->temp == 'prosecutor' || $this->temp == 'prosecutor2')
			{
				if($this->params['date3.year'] > 1970)
				{
					$x[0] = 'Приложение: ответ из ГИБДД от '.$this->params['date3.day'].'.'.$this->params['date3.month'].'.'.$this->params['date3.year'];
				}
			}
			else
			{
				$x[0] = 'Приложение: '.$this->note.' фотографи'.pdf1234::getEnd($this->note);
			}
		}
		return $x;
	}

	/**
	 *Подпись
	 */
	protected function signature()
	{
		$x = 'Подпись: ';
		return $x;
	}
	
	// жалоба в прокуратуру
	protected function text_prosecutor(){
		$ar['body0'] = '    '.$this->params['date2.day'].'.'.$this->params['date2.month'].'.'.$this->params['date2.year'].' мною было направлено заявление в '.$this->params['gibdd'].' об устранении повреждений дорожного покрытия по адресу: '.$this->params['street'].'.';
		$ar['body1'] = 'По истечению 30-ти дневного срока, установленного Федеральным законом «О порядке рассмотрений обращений граждан РФ» я не получил мотивированного и обоснованного ответа по существу своего обращения. По истечении 10 дней - максимально допустимого срока, предусмотренного ГОСТ Р 50597-93 для устранения повреждений дорожного покрытия, повреждения, указанные мною, не были устранены.Таким образом, было нарушено мое право на получение своевременного и мотивированного ответа, а также право на безопасные условия движения по дорогам РФ, предусмотренное ФЗ «О безопасности дорожного движения».';
		$ar['footerUP0'] = '   В связи с изложенным, прошу: ';
		$ar['count'][1] = 'Обязать ГИБДД предоставить в мой адрес мотивированный и обоснованный ответ по существу обращения.';
		$ar['count'][2] = 'Обязать ГИБДД принять меры к устранению указанных мною повреждений дорожного покрытия.';
		return $ar;
	}
	
	// ещё одна жалоба в прокуратуру
	protected function text_prosecutor2(){
		$ar['body0'] = '    '.$this->params['date2.day'].'.'.$this->params['date2.month'].'.'.$this->params['date2.year'].' мною было направлено заявление в '.$this->params['gibdd'].' об устранении повреждений дорожного покрытия по адресу: '.$this->params['street'].'.';
		$ar['body1'] = '    '.$this->params['date3.day'].'.'.$this->params['date3.month'].'.'.$this->params['date3.year'].' я получил ответ из ГИБДД, в котором указано: '.$this->params['gibdd_reply'];
		$ar['footerUP0'] = '   В связи с изложенным, на основании ФЗ «О прокуратуре», прошу: ';
		$ar['count'][1] = 'Провести проверку по факту неисполнения указанного предписания ГИБДД и федерального законодательства РФ о содержании и ремонте автомобильных дорог и безопасности дорожного движения.';
		$ar['count'][2] = 'Обязать организацию (учреждение), ответственную за содержание дороги в исправном состоянии, исполнить предписание ГИБДД.';
		return $ar;
	}
	
	// заявление на многоям
	protected function text_manyholes($models){
		$ar['body0'] = '    '.$this->params['date1.day'].'.'.$this->params['date1.month'].'.'.$this->params['date1.year'].' мною было обнаружено несколько повреждений дорожного покрытия, размеры каждого из которых превышают нормативы, установленные ГОСТ Р 50597-93, и которые могут представлять серьёзную опасность для дорожного движения. Ниже список адресов, описаний и ссылок на фотографии обнаруженных мной повреждений. ';
		$ar['body1'] = '';
		$ar['holes']=Array();
		foreach ($models as $model){
		$ar['holes'][] = $model->type->name.', '.$model->ADDRESS.($model->COMMENT1 ? ', '.trim($model->COMMENT1) : '').($model->description_locality ? ', '.trim($model->description_locality) : '').($model->description_size ? ', '.trim($model->description_size) : '').', http://rosyama.ru'.CController::createUrl("/holes/view",Array('id'=>$model->ID))."";    
		}
		$ar['footerUP0'] = 'В соответствии с п.19 ч.1 ст.12 ФЗ «О полиции» на полицию возлагаются обязанность по осуществлению государственного контроля (надзора) за соблюдением правил, стандартов, технических норм и иных требований нормативных документов в области обеспечения безопасности дорожного движения. Максимальный срок, предусмотренный ГОСТ Р 50597-93 для исправления повреждений дорожного покрытия составляет 10 суток. В связи с изложенным, на основании п.11 Положения «О ГИБДД МВД РФ», ст. 12 ФЗ «О полиции», п.3 ч.1.ст. 28.1, ч.1 ст29.13 КоАП Российской Федерации прошу:';
		$ar['count'][1] = 'Возбудить дело об административном правонарушении по статье 12.34 КоАП РФ «Несоблюдение требований по обеспечению безопасности дорожного движения при ремонте и содержании дорог, железнодорожных переездов или других дорожных сооружений» в отношении дорожной службы, ответственной за содержание и ремонт указанного дорожного участка.';
		$ar['count'][2] = 'Выдать предписание о принятии мер по устранению указанных недостатков.';
		$ar['count'][3] = 'По существу моего заявления и о принятых мерах сообщить мне письменно.';
		return $ar;
	}	
	
	public function replaceDescr($matches) {
			$model=$this->models[0];
			if ($model->description_size && $this->requestForm && $this->requestForm->showDescriptions) return $model->description_size;
			else return $matches[1];
		}	

	//универсальный шаблон для типов ям
	protected function getTypeTemplate(){
		$type=$this->temp;
		$model=$this->models[0];
		$ar['body0'] = '    '.$this->params['date1.day'].'.'.$this->params['date1.month'].'.'.$this->params['date1.year'].' мною на территории дороги по адресу: '.$this->params['street'].($model->description_locality && $this->requestForm && $this->requestForm->showDescriptions ? ', '.$model->description_locality : '')." (долгота: $model->LONGITUDE, широта: $model->LATITUDE, ".Yii::app()->request->hostInfo.CController::createUrl('/holes/view', Array('id'=>$model->ID)).")";
			$regex = "#{descr}(.*?){/descr}#s";
			$type->pdf_body = preg_replace_callback(
				$regex,
				array('self', 'replaceDescr'),
				$type->pdf_body
			);
		$ar['body0'].=' '.$type->pdf_body;
		$ar['body1']='';
		$ar['footerUP0'] = $type->pdf_footer;
		foreach ($type->commands as $i=>$count){
		$ar['count'][$i+1] = $count->text;
		}
		return $ar;
	}
	
	/**
	 *Разбивает 1 большую строку на маленькие
	 *@param string $txt большая строка
	 *@param int $lenght размер маленьких строк
	 *@param string $encoding кодировка
	 *@return array $res массив маленьких строк
	 */
	protected function slashN($txt, $lenght, $encoding = 'utf8')
	{
		$lenght = (int) $lenght;
		$txt = strval($txt);
		$start = 0;
		
		$len = mb_strlen($txt, $encoding);
		while($len - $start > $lenght){
			$tmp = mb_substr($txt, $start, $lenght, $encoding);
			$tmp_len = mb_strrpos($tmp, ' ', $encoding);
			if($tmp_len == $lenght || $tmp_len == null)
			{
				$res[] = mb_substr($txt, $start, $lenght, $encoding);
				$start += $lenght;
			}
			else
			{
				$res[] = mb_substr($txt, $start, $tmp_len + 1, $encoding);
				$start += $tmp_len + 1;
			}
		}
		$res[] = mb_substr($txt, $start, $len, $encoding);	
		return $res;
	}
	
	/**
	 *Превращает массив строк в текстовый блок
	 *@param array $arr массив строк
	 *@param int $betwen_str отстум меджу строками
	 *@param int $x, $y отступы слева и сверху в PDF
	 */
	private function getpages($arr, $betwen_str, $x, $y = null)
	{
		foreach($arr as $var){
			if($y!=null){
				$this->pdf->SetXY($x, $y);
				$y +=$betwen_str;
			}else{
				$this->pdf->SetX($x);
			}
			$this->pdf->Write(5, $var);
			$this->pdf->Ln();
		}
	}
	
	/**
	 *Верстка основной текстовой области в PDF
	 */
	protected function template()
	{
		if (count($this->models) <= 1){
			if (!is_object($this->temp)) $arResult = call_user_func(array(__CLASS__, 'text_'.$this->temp));
			else $arResult=$this->getTypeTemplate();
		}
		else $arResult=$this->text_manyholes($this->models);

		$x = $this->header();
		$y = $this->footer();
		$str_len = 100;


		$this->pdf->SetXY(100,10);

		pdf1234::getpages(pdf1234::slashN($x[0], 50), 5, 100, 0);
		$this->pdf->Ln();
		
		$this->pdf->SetX(100);
		pdf1234::getpages(pdf1234::slashN($x[1], 50), 5, 100, 0);
		$this->pdf->Ln();

		$this->pdf->SetX(100);
		pdf1234::getpages(pdf1234::slashN($x[2], 50), 5, 100, 0);

		$this->pdf->SetXY(90, 75);
		//$this->pdf->Write(5, $this->name());
		pdf1234::getpages(pdf1234::slashN($this->name(), floor($str_len / 2.7)), 5, 80, 0);

		pdf1234::getpages(pdf1234::slashN($arResult['body0'], $str_len), 5, 20);
		if (!isset($arResult['holes']))
			pdf1234::getpages(pdf1234::slashN($arResult['body1'], $str_len), 5, 20);
		else 
			foreach ($arResult['holes'] as $str) pdf1234::getpages(pdf1234::slashN($str, $str_len), 5, 20);
		$this->pdf->Ln();
		pdf1234::getpages(pdf1234::slashN($arResult['footerUP0'], $str_len), 5, 20);
		$this->pdf->Ln();
		for($i=1;$i<=count($arResult['count']);$i++)
		{
			$this->pdf->SetX(30);
			$this->pdf->Write(5,$i.'.');
			$this->pdf->SetX(35);
			pdf1234::getpages(pdf1234::slashN($arResult['count'][$i], 85), 5, 35);
			$this->pdf->Ln();
		}
		$this->pdf->SetX(20);
		$this->pdf->AddPage();
		$this->pdf->Write(5,$y[0]);
		$this->pdf->Ln();
	}
	
	/**
	 *Верстка нижней части документа(подпись, дата)
	 */
	protected function getsignature()
	{
		$x = pdf1234::signature();
		$this->pdf->setY(265);
		if($this->temp == 'prosecutor' || $this->temp == 'prosecutor2')
		{
			$date = date('d.m.Y');
		}else
		{
			$date = $this->params['date2.day'].'.'.$this->params['date2.month'].'.'.$this->params['date2.year'];
		}
		$this->pdf->Write(5, $date.'  '.$this->params['signature']);
		$this->pdf->setY(270);
		$this->pdf->SetX(20);
		$this->pdf->SetX(20);
		$this->pdf->Write(5,$x);
		$this->pdf->Ln();
	}
	
	/**
	 *Склоняет слово "фотография" и возвращает окончание, в зависимости от впередистоящего числа
	 *@param int $p
	 *@return char $text
	 */
	private function getEnd($p)
	{
		$end2=substr($p, strlen($p)-1, 1);
		if (2 <= $end2 && $end2 <= 4)
		{
			$text= 'и';
		}
		elseif ($end2==1)
		{
			$text = 'я';
		}
		else{
			$text = 'й';
		}
		return $text;
	}

}
?>
