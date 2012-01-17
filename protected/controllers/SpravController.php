<?php

class SpravController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

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
				'actions'=>array('index','view','fill_gibdd_reference', 'fill_prosecutor_reference'),
				'users'=>array('*'),
			),		
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	// склонятор
	public function sklonyator($str)
	{
		$nanoyandex_reply = file_get_contents('http://export.yandex.ru/inflect.xml?name='.urlencode($str));
		$pos = strpos($nanoyandex_reply, '<inflection case="3">');
		if($pos === false)
		{
			return $str;
		}
		$nanoyandex_reply = substr($nanoyandex_reply, $pos);
		$nanoyandex_reply = substr($nanoyandex_reply, 21, strpos($nanoyandex_reply, '</inflection>') - 21); // 21 = strlen('<inflection case="3">')
		return trim($nanoyandex_reply, "\n\t ");
	}	
	
	public function actionFill_gibdd_reference()
	{
		set_time_limit(0);
		
		// 1) достать список регионов
		$text = file_get_contents('http://www.gibdd.ru/regions/');
		$text = substr($text, strpos($text, '<table cellpadding="0" cellspacing="0" width="730">'));
		$text = substr($text, 0, strpos($text, '</table>'));
		$text = explode('<tr>', $text);
		$i = 0;
		$_regions = array();
		foreach($text as &$item)
		{
			if($i > 2)
			{
				$item = explode('<td', $item);
				preg_match('/\<a[\s\S]*href=(\"|\')([\s\S]*)\1[\s\S]*\>([\s\S]*)\<\/a\>/U', $item[1], $_m);
				$region_id = substr($_m[2], 14);
				$_regions[$region_id] = array
				(
					'id'   => $region_id,
					'name' => $_m[3],
					'href' => $_m[2]
				);
			}
			$i++;
		}
		
		// 2) сопоставить всем регионам субъект РФ
		$myRegions=CHtml::listData(RfSubjects::model()->findAll(), 'id','name_full');
		foreach($_regions as &$r)
		{
			foreach($myRegions as $k => &$s)
			{
		
				
				if(strtolower($s) == strtolower($r['name']))
				{
					$r['subject_id']   = $k;
					$r['subject_name'] = $s;
					continue;
				}
				else
				{
					$name = explode(' ', $r['name']);
					$sname = explode(' ', strtolower($s));
					foreach($name as $part)
					{
						$part = strtolower($part);
						if
						(
							$part != ''
							&& $part != 'Республика' // на промышленном сервере strtolower на срабатывает на слове "республика"
							&& $part != 'республика'
							&& $part != 'край'
							&& $part != 'область'
							&& $part != 'автономная'
							&& $part != 'округ'
							&& $part != 'автономный'
						)
						{
							//if(stripos($s, $part) !== false)
							if(in_array($part, $sname))
							{
								$r['subject_id']   = $k;
								$r['subject_name'] = $s;
								continue;
							}
						}
					}
				}
			}
			foreach($_regions as $rr)
			{
				if($rr['id'] != $r['id'] && isset($rr['subject_id']) && $rr['subject_id'] == $r['subject_id'])
				{
					echo 'коллизия '.$r['name'].'-'.$rr['name'].'<br>';
					die();
				}
			}
			if(!$r['subject_id'])
			{
				echo 'нет ид '.$r['name'].'<br>';
				die();
			}
		}
		
		// 3) для каждого региона достать его главу и контакты		
		foreach($_regions as &$r)
		{
			if(!$r['subject_id'] || !$r['href'])
			{
				echo 'нет ссылки или ид субъекта '.$r['name'].'<br>';
				die();
			}
			
			
			$text = file_get_contents('http://www.gibdd.ru'.$r['href']);
			$text = substr($text, strpos($text, '<p class="bold" style="padding-bottom:15px;">'));
			$text = substr($text, 0, strpos($text, '</div>'));
			$text = explode('<p class="bold">', $text);
			
			$r['gibdd_name']=strip_tags(trim($text[0]));
			
			$text[0] = str_replace('УПРАВЛЕНИЕ', 'УПРАВЛЕНИЯ', strip_tags($text[0]));
			$text[1] = explode('</p>', $text[1]);
			$text[1][0] = str_replace(':', '', strip_tags($text[1][0]));
			$text[1][1] = str_replace(':', '', strip_tags($text[1][1]));
			
			$r['gibbd_name_dative']=$text[0];
			$r['post']     = trim($text[1][0]);
			$r['fio']      = trim($text[1][1]);
			$r['post_dative'] = trim($text[1][0].'у '.$text[0]);
			$r['fio_dative']  = $this->sklonyator($text[1][1]);
			$r['contacts'] = '';
			$contact_fiels=Array('','','address', 'tel_degurn', 'tel_dover','url');
			for($i = 2; $i < 6; $i++)
			{
				$r['contacts'] .= strip_tags(trim($text[$i])).'<br>';
				$text[$i] = explode('</p>', $text[$i]);
				$text[$i][0] = str_replace(':', '', strip_tags($text[$i][0]));
				$text[$i][1] = str_replace(':', '', strip_tags($text[$i][1]));
				$r[$contact_fiels[$i]]=trim($text[$i][1]);
				
			}
			$r['url']='http://'.$r['url'];
		}
		
		// 4) занести всё в базу
		foreach($_regions as &$r)
		{			
			$model=GibddHeads::model()->find('subject_id='.(int)$r['subject_id']);
			if (!$model) $model=new GibddHeads;		
			$model->attributes=$r;
			$model->save();
		}
		
	}	

	public function actionFill_prosecutor_reference(){
		set_time_limit(0);
		$raw_html = file_get_contents('http://genproc.gov.ru/structure/subjects/');
		preg_match_all('`<select([\s\S]+)</select>`U', $raw_html, $_matches);
		preg_match_all('`<option value="([\d]+)"[\s\S]*>([\s\S]+)</option>`U', $_matches[0][0], $_matches, PREG_SET_ORDER);
		
		//$_matches = array ( 0 => array ( 0 => '', 1 => '110', 2 => 'Центральный федеральный округ', ), 1 => array ( 0 => '', 1 => '111', 2 => 'Северо-Западный федеральный округ', ), 2 => array ( 0 => '', 1 => '112', 2 => 'Южный федеральный округ', ), 3 => array ( 0 => '', 1 => '241', 2 => 'Северо-Кавказский федеральный округ', ), 4 => array ( 0 => '', 1 => '113', 2 => 'Приволжский федеральный округ', ), 5 => array ( 0 => '', 1 => '114', 2 => 'Уральский федеральный округ', ), 6 => array ( 0 => '', 1 => '115', 2 => 'Сибирский федеральный округ', ), 7 => array ( 0 => '', 1 => '116', 2 => 'Дальневосточный федеральный округ', ), 8 => array ( 0 => '', 1 => '242', 2 => 'Центральный аппарат', ), );
		
		foreach($_matches as &$set)
		{
			$raw_html = file_get_contents('http://genproc.gov.ru/structure/subjects/district-'.$set[1].'/');
			if(!$raw_html)
			{
				echo $set[1].' - fail<br>';
				continue;
			}
			$raw_html = substr($raw_html, strpos($raw_html, '<dl class="institutions">'));
			$raw_html = explode('<div>', $raw_html);
			foreach($raw_html as &$office)
			{
				
				
				
				$office = explode('</a>', $office);
				if($office[1])
				{
					$office[0] = strip_tags($office[0]);
					$subjects = explode("\n", $office[0]);
					//print_r($subjects);
					if (isset($subjects[2])){
					$itemname=$subjects[2];
					$subjects[1]=preg_replace('/\(.*\)/i', '', $subjects[1]);
					$subjectmodel = RfSubjects::model()->find("name_full LIKE '%".trim($subjects[1])."%'");
					if ($subjectmodel) $subject=$subjectmodel->id;
					else $subject=$subject=RfSubjects::model()->SearchID($subjects[1]);
					}
					else {
						$itemname=$subjects[0];
						$subject=0;
						}
					
					$r['name'] = trim(str_replace("\n", ' ', str_replace("\t", ' ', $office[0])));
					$r['gibdd_name'] = trim(str_replace("\n", '', str_replace("\t", '', $itemname)));
					$r['preview_text'] = trim(str_replace("\t", ' ', strip_tags($office[1], '<br>')));
					$r['subject_id']=$subject;
					
					$model=Prosecutors::model()->find('subject_id='.(int)$subject);
					if (!$model) $model=new Prosecutors;		
					$model->attributes=$r;
					$model->save();					
				}
			}
			echo $set[1].' - ok<br>';
		}
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new News;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['News']))
		{
			$model->attributes=$_POST['News'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['News']))
		{
			$model->attributes=$_POST['News'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('News');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new News('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['News']))
			$model->attributes=$_GET['News'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=News::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='news-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
