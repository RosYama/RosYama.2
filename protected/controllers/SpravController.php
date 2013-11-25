<?php

class SpravController extends Controller
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
			'userGroupsAccessControl', // perform access control for CRUD operations
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
				'actions'=>array('index','view','fill_gibdd_reference', 'fill_prosecutor_reference','local', 'jsonGibddMap'),
				'users'=>array('*'),
			),		
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('add','update','delete', 'moderate', 'updateprosecutor'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('saveAllPolygions'),
				'groups'=>array('root'), 
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	// склонятор
	public function sklonyator($str)
	{		
		return Y::sklonyator($str);
	}	
	
	function file_get_html() {
		$dom = new simple_html_dom;
		$args = func_get_args();
		$dom->load(call_user_func_array('file_get_contents', $args), true);
		return $dom;
	}
	
	// get html dom form string
	function str_get_html($str, $lowercase=true) {
		$dom = new simple_html_dom;
		$dom->load($str, $lowercase);
		return $dom;
	}
	
	// dump html dom tree
	function dump_html_tree($node, $show_attr=true, $deep=0) {
		$lead = str_repeat('    ', $deep);
		echo $lead.$node->tag;
		if ($show_attr && count($node->attr)>0) {
			echo '(';
			foreach($node->attr as $k=>$v)
				echo "[$k]=>\"".$node->$k.'", ';
			echo ')';
		}
		echo "\n";
	
		foreach($node->nodes as $c)
			dump_html_tree($c, $show_attr, $deep+1);
	}
	
	// get dom form file (deprecated)
	function file_get_dom() {
		$dom = new simple_html_dom;
		$args = func_get_args();
		$dom->load(call_user_func_array('file_get_contents', $args), true);
		return $dom;
	}
	
	// get dom form string (deprecated)
	function str_get_dom($str, $lowercase=true) {
		$dom = new simple_html_dom;
		$dom->load($str, $lowercase);
		return $dom;
	}
	
	
	public function actionSaveAllPolygions()
	{
		if (isset($_POST['GibddAreaName'])){
			foreach ($_POST['GibddAreaName'] as $i=>$region){
				$points=$_POST['GibddAreaPoints'][$i];
				$subjId=RfSubjects::model()->SearchID($region);
				if ($subjId){
				$subj=RfSubjects::model()->findByPk($subjId);
				echo '<font color="green">Обновлено: '.$subj->name.'</font><br />';
					if ($subj && $subj->gibdd){
						foreach ($subj->gibdd->areas as $item) $item->delete();
						$areamodel=new GibddAreas;
						$areamodel->gibdd_id=$subj->gibdd->id;					

						if ($points && $areamodel->save()){
							foreach ($points as $ii=>$point){
								if ($point['lat'] && $point['lng']){
											$pointmodel=new GibddAreaPoints;
											$pointmodel->lat=$point['lat'];
											$pointmodel->lng=$point['lng'];
											$pointmodel->area_id=$areamodel->id;
											$pointmodel->point_num=$ii;
											$pointmodel->save(); 
								}
							}
						}
					}
				}
				else echo '<font color="red">Не найдено: '.$region.'</font><br />';
				
			}
		
		}
		
	}	
	
	public function actionFill_gibdd_reference()
	{
		set_time_limit(0);
		
		$bufer=GibddHeadsBuffer::model()->findAll();
		
		if (!$bufer){
			$gibds=GibddHeads::model()->findAll('is_regional=1');
			foreach ($gibds as $gibd){
				$model=new GibddHeadsBuffer;
				$model->attributes=$gibd->attributes;
				$model->id=$gibd->id;
				$model->save();
			}
		}
		
		$regions=RfSubjects::model()->findAll(Array('order'=>'region_num'));
		
		
		foreach($regions as $r)
		{			
			
			$html = $this->file_get_html('http://www.gibdd.ru/struct/reg/'.($r->region_num < 10 ? '0'.$r->region_num : $r->region_num).'/');
			
			$links=Array();
			
            foreach($html->find('div[class="news-item"]') as $div){
				$links[]=$div->find('a',0)->href;
			}			
            $html->clear();	

            foreach ($links as $i=>$link){
            	if ($i > 0) break; //пока тут останавливаем т.к. берем только региональные
            	
            	$attribs=Array();
            	
            	$html = $this->file_get_html('http://www.gibdd.ru'.$link);
            	$div=$html->find('div[id="gosserv"]',0);
            	
            	$attribs['gibdd_name']=$div->find('h3',0)->innertext;
            	$attribs['gibdd_kod']=str_replace('Код подразделения: ', '', $div->find('div[class="bord_left"]',0)->find('p',0)->innertext);
            	
            	
            	foreach ($div->find('p') as $p){
            		$b=$p->find('b',0);
            		if ($b){
            			if ($b->innertext=='Дежурная часть:') $attribs['tel_degurn']=trim(str_replace('<b>'.$b->innertext.'</b>', '', trim($p->innertext)));
            			if ($b->innertext=='Телефон доверия:') $attribs['tel_dover']=trim(str_replace('<b>'.$b->innertext.'</b>', '', trim($p->innertext)));
            		} 
            		$attribs['address']=$p->innertext;
            	}   	
            	$html->clear();	
            	
            	$attribs['gibbd_name_dative']=str_replace('Управление', 'Управления', $attribs['gibdd_name']);            	
            	$attribs['contacts'] = '';
            	$attribs['url']='http://'.($r->region_num < 10 ? '0'.$r->region_num : $r->region_num).'.gibdd.ru';
            	//$r['post_dative'] = trim($attribs['post'].'у '.$attribs['gibbd_name_dative']);
				//$r['fio_dative']  = $this->sklonyator($text[1][1]);
				
				//сохраняем							
				$model=GibddHeadsBuffer::model()->find('subject_id='.$r->id.' AND is_regional=1');
				//if (!$model) $model=new GibddHeads;
				$attribs['level']=1;
				if ($model){
					foreach ($attribs as $key=>$val){
						unset ($attribs['gibdd_kod']);
						unset ($attribs['gibbd_name_dative']);
						if (isset($model->$key) && $model->$key == $val) unset ($attribs[$key]);
					}
					if ($attribs){
						$curmodel=GibddHeads::model()->findByPk($model->id);
						if ($curmodel){
							$model->scenario='fill';
							$model->attributes=$attribs;	
							$model->level=1;
							$model->modified=time();
							if ($model->update()){
								$curmodel->scenario='fill';
								$curmodel->attributes=$attribs;	
								$curmodel->modified=time();
								$curmodel->update();
								echo 'Обновлено '.$curmodel->gibdd_name;
								print_r($attribs);
								echo '<br />';
							}
						}
					}
				}
				else {
					$model=new GibddHeads;
					$model->scenario='fill';
					$model->attributes=$attribs;
					$model->is_regional=1;
					$model->level=1;
					$model->moderated=1;
					$model->created=time();
					if ($model->save()){
						$bufer=new GibddHeadsBuffer;
						$bufer->attributes=$model->attributes;
						$bufer->id=$model->id;
						$bufer->save();
					}
				}						
            	
            }
	
			
		}
		
	}	

	public function actionFill_prosecutor_reference(){
		set_time_limit(0);
		
		$bufer=ProsecutorsBuffer::model()->findAll();
		
		if (!$bufer){
			$gibds=Prosecutors::model()->findAll();
			foreach ($gibds as $gibd){
				$model=new ProsecutorsBuffer;
				$model->attributes=$gibd->attributes;
				$model->id=$gibd->id;
				$model->save();
			}
		}
		
		$raw_html = file_get_contents('http://genproc.gov.ru/structure/subjects/');
		preg_match_all('`<select([\s\S]+)</select>`U', $raw_html, $_matches);
		preg_match_all('`<option value="([\d]+)"[\s\S]*>([\s\S]+)</option>`U', $_matches[0][0], $_matches, PREG_SET_ORDER);
		
		//$_matches = array ( 0 => array ( 0 => '', 1 => '110', 2 => 'Центральный федеральный округ', ), 1 => array ( 0 => '', 1 => '111', 2 => 'Северо-Западный федеральный округ', ), 2 => array ( 0 => '', 1 => '112', 2 => 'Южный федеральный округ', ), 3 => array ( 0 => '', 1 => '241', 2 => 'Северо-Кавказский федеральный округ', ), 4 => array ( 0 => '', 1 => '113', 2 => 'Приволжский федеральный округ', ), 5 => array ( 0 => '', 1 => '114', 2 => 'Уральский федеральный округ', ), 6 => array ( 0 => '', 1 => '115', 2 => 'Сибирский федеральный округ', ), 7 => array ( 0 => '', 1 => '116', 2 => 'Дальневосточный федеральный округ', ), 8 => array ( 0 => '', 1 => '242', 2 => 'Центральный аппарат', ), );
		
		foreach($_matches as &$set)
		{
			$raw_html = file_get_contents('http://genproc.gov.ru/structure/subjects/district-'.$set[1].'/');
			//echo 'http://genproc.gov.ru/structure/subjects/district-'.$set[1].'/<br />';
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
					//echo $subjects[1].'<br />';
					$subjectmodel = RfSubjects::model()->find("name_full LIKE '".trim($subjects[1])."'");
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
					
					if($r['subject_id']==0) continue;
					
					$model=ProsecutorsBuffer::model()->find('subject_id='.(int)$subject);
					
					
					if ($model){
						foreach ($r as $key=>$val){
							unset ($r['href']);
							unset ($r['subject_name']);
							unset ($r['id']);
							if (isset($model->$key) && trim ($model->$key) == trim($val)) unset ($r[$key]);
							
						}
						if ($r){
							$curmodel=Prosecutors::model()->findByPk($model->id);
							if ($curmodel){
								$model->attributes=$r;	
								if (!$model->subject_id) $model->subject_id=0;
								if ($model->update()){
									$curmodel->attributes=$r;	
									$curmodel->save();
									echo 'Обновлено '.$curmodel->gibdd_name;
									print_r($r);
									echo '<br />';
								}
								print_r($model->errors);
							}
						}
					}
					else {
						$model=new Prosecutors;
						$model->attributes=$r;
						if ($model->save()){
							$bufer=new ProsecutorsBuffer;
							$bufer->attributes=$model->attributes;
							$bufer->id=$model->id;
							$bufer->save();
						}
					}			

					
				}
			}
			//echo $set[1].' - ok<br>';
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
	
	public function actionLocal($id)
	{
	
		$cs=Yii::app()->getClientScript();
        $cs->registerCssFile('/css/hole_view.css'); 
        $cs->registerScriptFile('http://api-maps.yandex.ru/1.1/index.xml?key='.$this->mapkey);
       	$jsFile = CHtml::asset($this->viewPath.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'view_script.js');
        $cs->registerScriptFile($jsFile); 
		
		$this->render('view_local',array(
			'model'=>$this->loadGibddModel($id),
		));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model=RfSubjects::model()->with('gibdd')->findAll(Array('order'=>'t.region_num','together'=>true));
		$this->render('index',array(
			'model'=>$model,
		));
	}
	
	public function actionJsonGibddMap()
	{
		$model=GibddHeads::model()->findAll('lat > 0 AND lng > 0');
		$gibds=Array();
		foreach ($model as $item) {
			$areas=Array();
			if ($item->areas)
				foreach ($item->areas as $i=>$area){
					foreach ($area->points as $point)
						$areas[$i][]=Array('lat'=>$point->lat, 'lng'=>$point->lng);
				}		
			
			$descr=$this->renderPartial('_view_gibdd', array('data'=>$item), true);
			
			$gibds[]=Array('lat'=>$item->lat, 'lng'=>$item->lng, 'name'=>$item->name, 'id'=>$item->id, 'descr'=>$descr.($areas ? CHtml::link('Показать границу наблюдения', '#', Array('class'=>'show_gibdd_area', 'gibddid'=>$item->id)) : '' ),
			'areas'=>$areas, 
			);
		}
		echo $_GET['jsoncallback'].'({"gibdds": '.CJSON::encode($gibds).'})';
		
		Yii::app()->end();		
		
	}	
	
	public function actionAdd($subject_id)
	{
		$model=new GibddHeads;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		
		$cs=Yii::app()->getClientScript();
        $cs->registerCssFile('/css/add_form.css');
        $cs->registerScriptFile('http://api-maps.yandex.ru/1.1/index.xml?key='.$this->mapkey);
        $jsFile = CHtml::asset($this->viewPath.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'ymap.js');
        $cs->registerScriptFile($jsFile);     
		
		$subj=RfSubjects::model()->findByPk((int)$subject_id);
		if($subj) $model->subject_id=$subj->id;
		
		if(isset($_POST['GibddHeads']))
		{
			$model->attributes=$_POST['GibddHeads'];
			$model->author_id=Yii::app()->user->id;	
			$model->created=time();			
			
			if ($subj) $model->subject_id=$subj->id;
			else if ($model->str_subject){
				$subjct=RfSubjects::model()->SearchID(trim($model->str_subject));
				if($subjct) $model->subject_id=$subjct;
				else $model->subject_id=0;
			}
			
			
			if (Yii::app()->user->level > 50) $model->moderated=1;
			else $model->moderated=0;
			if ($model->level < 2) $model->level=2;
			if($model->save())
				$this->redirect(array('local','id'=>$model->id));
		}		

		$this->render('add',array(
			'model'=>$model,
			'subject'=>$subj,
		));
	}	
	
	public function actionUpdate($id)
	{
	
		$model=$this->loadGibddModel($id);
		
		if (Yii::app()->user->id!=$model->author_id && Yii::app()->user->level <= 50)
			throw new CHttpException(403,'Доступ запрещен.');
		
		if ($model->is_regional && Yii::app()->user->level <= 90)
			throw new CHttpException(403,'Доступ запрещен.');
		
		$cs=Yii::app()->getClientScript();
        $cs->registerCssFile('/css/add_form.css');
        $cs->registerScriptFile('http://api-maps.yandex.ru/1.1/index.xml?key='.$this->mapkey.';modules=regions');
        $jsFile = CHtml::asset($this->viewPath.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'ymap.js');
        $cs->registerScriptFile($jsFile);     

		if(isset($_POST['GibddHeads']))
		{
			$model->attributes=$_POST['GibddHeads'];			
			$model->modified=time();
			if ($model->str_subject){
				$subj=RfSubjects::model()->SearchID(trim($model->str_subject));
				if($subj) $model->subject_id=$subj;
				else $model->subject_id=0;
			}
			if ($model->level < 2 && !$model->is_regional) $model->level=2; 
			if($model->save())
				$this->redirect(array('local','id'=>$model->id));
		}		

		$this->render('update',array(
			'model'=>$model,			
		));
	}
	
public function actionUpdateprosecutor($id)
	{
	
		$model=$this->loadProsecutorModel($id);
		
		if (Yii::app()->user->level <= 90)
			throw new CHttpException(403,'Доступ запрещен.');
		
		$cs=Yii::app()->getClientScript();
        $cs->registerCssFile('/css/add_form.css');

		if(isset($_POST['Prosecutors']))
		{
			$model->attributes=$_POST['Prosecutors'];			
			if($model->save())
				$this->redirect(array('view','id'=>$model->subject_id));
		}		

		$this->render('update_prosecutor',array(
			'model'=>$model,			
		));
	}	
	
	public function actionModerate($id)
	{	
		$model=$this->loadGibddModel($id);
		if (!Yii::app()->user->isModer && $model->author_id!=Yii::app()->user->id)
				throw new CHttpException(403,'Доступ запрещен.');	
		$model->moderated=1;
		$model->update();
		if(!isset($_GET['ajax']))
			$this->redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function actionDelete($id)
	{
		$model=$this->loadGibddModel($id);
		
		if (!Yii::app()->user->isModer && $model->author_id!=Yii::app()->user->id)
				throw new CHttpException(403,'Доступ запрещен.');	
			
		$model->delete();

		if(!isset($_GET['ajax']))
				$this->redirect($_SERVER['HTTP_REFERER']);
	}
	

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=RfSubjects::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function loadGibddModel($id)
	{
		$model=GibddHeads::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function loadProsecutorModel($id)
	{
		$model=Prosecutors::model()->findByPk($id);
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
