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
			'userGroupsAccessControl',
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
				'actions'=>array('index', 'periods'),
				'users'=>array('*'),
			),		
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('notSentEmails'),
				'groups'=>array('root',), 
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
		$arResult['user'][]=Holes::model()->with('user')->findAll(Array('select'=>'count(*) as counts','condition'=>'PREMODERATED=1 AND deleted=0','group'=>'USER_ID','order'=>'counts desc','limit'=>10));
		$arResult['user'][]=Holes::model()->with('user')->findAll(Array('select'=>'count(*) as counts','condition'=>'STATE="fixed" and PREMODERATED=1 AND deleted=0','group'=>'USER_ID','order'=>'counts desc','limit'=>10));		

		if (Yii::app()->user->level >= 90) {
			$arResult['moders']=Holes::model()->with('moder')->findAll(Array('select'=>'count(*) as counts','condition'=>'PREMODERATED=1 AND deleted=0 AND moder.id > 0','group'=>'premoderator_id', 'together'=>true,'order'=>'counts desc','limit'=>10));	

				foreach($arResult['moders'] as $k=>$v)
				{
					$arResult['moders'][$k]['moder'] = CHtml::link(CHtml::encode((!empty($v->moder->name) && !empty($v->moder->last_name)) ? $v->moder->name.' '.$v->moder->last_name : $v->moder->username), Array('profile/view','id'=>$v->moder->id));
				}
		}
	
		
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
	
	public function actionPeriods()
	{
		$result=Array();
		$firstDate=CDateTimeParser::parse('01.01.'.(date('Y')-1),'dd.MM.yyyy');		
		
		$result = Yii::app()->cache->get('period_stat');					
		
		if (!$result){
			$holes=Holes::model()->findAll(Array('select'=>'t.DATE_CREATED, t.DATE_SENT, t.DATE_STATUS, t.STATE', 'condition'=>'t.DATE_CREATED >='.$firstDate));
			foreach ($holes as $hole){
				if (!isset($result[date('Ym', $hole->DATE_CREATED)]['created'])) $result[date('Ym', $hole->DATE_CREATED)]['created']=0;
				else $result[date('Ym', $hole->DATE_CREATED)]['created']++;
				if ($hole->DATE_SENT){
					if (!isset($result[date('Ym', $hole->DATE_SENT)]['sent'])) $result[date('Ym', $hole->DATE_SENT)]['sent']=0;
					else $result[date('Ym', $hole->DATE_SENT)]['sent']++;
				}
				if ($hole->STATE=='fixed' && $hole->DATE_STATUS){
					if (!isset($result[date('Ym', $hole->DATE_STATUS)]['fixed'])) $result[date('Ym', $hole->DATE_STATUS)]['fixed']=0;
					else $result[date('Ym', $hole->DATE_STATUS)]['fixed']++;
				}
			
			}
			
			$users=UserGroupsUser::model()->findAll(Array('select'=>'t.creation_date, t.id as notUseAfrefind', 'condition'=>'t.creation_date >= "'.(date('Y')-1).'-01-01"'));
			
			foreach ($users as $user){
				$time=CDateTimeParser::parse($user->creation_date,'yyyy-MM-dd HH:mm:ss');		
				if (!isset($result[date('Ym', $time)]['users'])) $result[date('Ym', $time)]['users']=0;
				else $result[date('Ym', $time)]['users']++;
			}
			
			Yii::app()->cache->set('period_stat',$result,3600*24);
		}
		
		
		
		$this->render('periods',array(
			'result'=>$result,
			'firstDate'=>$firstDate,
		));
	}
	
	public function actionNotSentEmails($dateStart=null)
	{
		ini_set('memory_limit', '1024M');
		set_time_limit(0);
		$time=$dateStart ? CDateTimeParser::parse($dateStart,'yyyy-MM-dd') : null;		

		$users=UserGroupsUser::model()->findAll(Array('select'=>'t.email', 'join'=>'INNER JOIN {{holes}} holes ON (t.id=holes.USER_ID)', 'condition'=>'holes.STATE="fresh"'.($time ? ' AND t.DATE_CREATED >= '.$time : ''), 'group'=>'t.email'));
		
		foreach ($users as $user) echo $user->email.'<br />';
	}

}
