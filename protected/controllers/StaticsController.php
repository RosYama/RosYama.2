<?php

class StaticsController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/header_blank';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('index'),
				'users'=>array('*'),
			),		
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}	
	
	public $arParams = Array('LIMIT'=>100500);
	
	/**
	*склонение дат, принимает дату в определенном формате, возвращает слово правильном склонении
	*@params int $num, char $format
	*@return string $end Yii::t('errors', 'GREENSIGHT_ERROR_UNSUPPORTED_IMAGE_TYPE')
	**/
	public function declination($num, $format)
	{
		$last = substr($num, strlen($num)-1);
		if((substr($num, strlen($num)-2) >= 11 && substr($num, strlen($num)-2) <= 19)
			|| $last == 0 
			|| ($last >= 5 && $last <= 9))
		{
			switch($format)
			{
				case 'Y': $end = Yii::t('statics', 'YEAR1'); break;
				case 'm': $end = Yii::t('statics', 'MONTH1'); break;
				case 'd': $end = Yii::t('statics', 'DAY1'); break;
				case 'H': $end = Yii::t('statics', 'HOUR1'); break;
				case 'i': $end = Yii::t('statics', 'MINUTE1'); break;
				case 's': $end = Yii::t('statics', 'SECOND1'); break;
			}
		}
		elseif($last >= 2 && $last <= 4)
		{
			switch($format)
			{
				case 'Y': $end = Yii::t('statics', 'YEAR2'); break;
				case 'm': $end = Yii::t('statics', 'MONTH2'); break;
				case 'd': $end = Yii::t('statics', 'DAY2'); break;
				case 'H': $end = Yii::t('statics', 'HOUR2'); break;
				case 'i': $end = Yii::t('statics', 'MINUTE2'); break;
				case 's': $end = Yii::t('statics', 'SECOND2'); break;
			}
		}
		elseif($last == 1)
		{
			switch($format)
			{
				case 'Y': $end = Yii::t('statics', 'YEAR3'); break;
				case 'm': $end = Yii::t('statics', 'MONTH3'); break;
				case 'd': $end = Yii::t('statics', 'DAY3'); break;
				case 'H': $end = Yii::t('statics', 'HOUR3'); break;
				case 'i': $end = Yii::t('statics', 'MINUTE3'); break;
				case 's': $end = Yii::t('statics', 'SECOND3'); break;
			}
		}
		return ' '.$end;
	}	
	
	public function actionIndex()
	{
		$limit_sql = !empty($this->arParams['LIMIT']) ? ' limit '.$this->arParams['LIMIT'] : '';
		
		//по городам
		$arResult['geography'][]=Holes::model()->findAll(Array('select'=>'count(*) as counts, ADR_CITY','condition'=>'ADR_CITY!="" and PREMODERATED=1','group'=>'trim(ADR_CITY)','order'=>'counts desc','limit'=>10));
		$arResult['geography'][]=Holes::model()->findAll(Array('select'=>'count(*) as counts, ADR_CITY','condition'=>'STATE="fixed" and ADR_CITY!="" and PREMODERATED=1','group'=>'trim(ADR_CITY)','order'=>'counts desc','limit'=>10));
		// по статусам
		$arResult['STATE'][]=Holes::model()->findAll(Array('select'=>'count(*) as counts, STATE as state_to_filter','condition'=>'PREMODERATED=1','group'=>'STATE_to_filter','order'=>'counts desc','limit'=>10));
		$arResult['STATE'][]=Holes::model()->findAll(Array('select'=>'avg(DATE_STATUS-DATE_SENT) as time','condition'=>'STATE="fixed" and ADR_CITY!="" and PREMODERATED=1','limit'=>10));
		
		// по пользователям
		$arResult['user'][]=Holes::model()->with('user')->findAll(Array('select'=>'count(*) as counts','condition'=>'PREMODERATED=1','group'=>'USER_ID','order'=>'counts desc','limit'=>10));
		$arResult['user'][]=Holes::model()->with('user')->findAll(Array('select'=>'count(*) as counts','condition'=>'STATE="fixed" and PREMODERATED=1','group'=>'USER_ID','order'=>'counts desc','limit'=>10));		
	
		
		$ru = array(
			'fresh'      => Yii::t('statics', 'STATE1'),
			'achtung'    => Yii::t('statics', 'STATE2'),
			'inprogress' => Yii::t('statics', 'STATE3'),
			'fixed'      => Yii::t('statics', 'STATE4'),
			'prosecutor' => Yii::t('statics', 'STATE5'),
			'gibddre'    => Yii::t('statics', 'STATE6')
		);
		foreach($arResult['STATE'][0] as $k=>$ar){
			$arResult['STATE'][0][$k]['STATE'] = strtr($ar['state_to_filter'], $ru);
		}
		
		
		$num = date('Y', $arResult['STATE'][1][0]['time'])-1970;
		$tmp = $num != 0 ? $num.$this->declination($num, 'Y').', ' : '';
		
		$num = gmdate('m', $arResult['STATE'][1][0]['time'])-1;
		$tmp .= $num != 0 ? $num.$this->declination($num, 'm').', ' : '';
		
		$num = gmdate('d', $arResult['STATE'][1][0]['time'])-1;
		$tmp .= $num != 0 ? $num.$this->declination($num, 'd').', ' : '';
		
		$num = gmdate('H', $arResult['STATE'][1][0]['time']);
		$tmp .= $num != 0 ? $num.$this->declination($num, 'H').', ' : '';
		
		$num = gmdate('i', $arResult['STATE'][1][0]['time']);
		$tmp .= $num != 0 ? $num.$this->declination($num, 'i').', ' : '';
		
		$num = gmdate('s', $arResult['STATE'][1][0]['time'])-1;
		$tmp .= $num != 0 ? $num.$this->declination($num, 's').', ' : '';
		
		$tmp = substr($tmp, 0, strlen($tmp) - 2);
		$arResult['STATE'][1][0]['time'] = $tmp;
		
		for($i = 0; $i < 2; $i++){
			foreach($arResult['user'][$i] as $k=>$v)
			{
				$arResult['user'][$i][$k]['user'] = CHtml::link(CHtml::encode((!empty($v->user->name) && !empty($v->user->last_name)) ? $v->user->name.' '.$v->user->last_name : $v->user->username), Array('profile/view','id'=>$v->user->id));
			}
		}
		$this->render('index',array(
			'arResult'=>$arResult,
		));
	}	

}
