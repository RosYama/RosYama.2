<?php

class HolesController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/main';

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
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view', 'findSubject', 'findCity', 'map', 'ajaxMap'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	public function actionFindSubject()
	{
	
		$q = $_GET['term'];
       if (isset($q)) {
           $criteria = new CDbCriteria;           
           $criteria->params = array(':q' => trim($q).'%');
           $criteria->condition = 'name LIKE (:q)'; 
           $RfSubjects = RfSubjects::model()->findAll($criteria); 
 
           if (!empty($RfSubjects)) {
               $out = array();
               foreach ($RfSubjects as $p) {
                   $out[] = array(
                       // expression to give the string for the autoComplete drop-down
                       //'label' => preg_replace('/('.$q.')/i', "<strong>$1</strong>", $p->name_full),  
                       'label' =>  $p->name_full,  
                       'value' => $p->name_full,
                       'id' => $p->id, // return value from autocomplete
                   );
               }
               echo CJSON::encode($out);
               Yii::app()->end();
           }
       }
	}
	
	public function actionFindCity()
		{
		
			$q = $_GET['Holes']['ADR_CITY'];
		   if (isset($q)) {
			   $criteria = new CDbCriteria;           
			   $criteria->params = array(':q' => trim($q).'%');
			   if (isset($_GET['Holes']['ADR_SUBJECTRF']) && $_GET['Holes']['ADR_SUBJECTRF']) $criteria->condition = 'ADR_CITY LIKE (:q) AND ADR_SUBJECTRF='.$_GET['Holes']['ADR_SUBJECTRF']; 
			   else $criteria->condition = 'ADR_CITY LIKE (:q)'; 
			   $criteria->group='ADR_CITY';
			   $Holes = Holes::model()->findAll($criteria); 
	 
			   if (!empty($Holes)) {
				   $out = array();
				   foreach ($Holes as $p) {
					   $out[] = array(
						   // expression to give the string for the autoComplete drop-down
						   //'label' => preg_replace('/('.$q.')/i', "<strong>$1</strong>", $p->name_full),  
						   'label' =>  $p->ADR_CITY,    
						   'value' => $p->ADR_CITY,
					   );
				   }
				   echo CJSON::encode($out);
				   Yii::app()->end();
			   }
		   }
		}	

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$cs=Yii::app()->getClientScript();
        $cs->registerCssFile('/css/hole_view.css'); 
        
        
		$this->render('view',array(
			'hole'=>$this->loadModel($id),

		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Holes;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Holes']))
		{
			$model->attributes=$_POST['Holes'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->ID));
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

		if(isset($_POST['Holes']))
		{
			$model->attributes=$_POST['Holes'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->ID));
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
		$this->layout='//layouts/header_default';
	
		$model=new Holes('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_POST['Holes']))
			$model->attributes=$_POST['Holes'];
			if ($model->ADR_CITY=="Город") $model->ADR_CITY='';
			
		$this->render('index',array(
			'model'=>$model,
		));
	}
	
	public function actionMap()
	{
		//$this->layout='//layouts/header_default';
	
		$model=new Holes('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_POST['Holes']))
			$model->attributes=$_POST['Holes'];
			if ($model->ADR_CITY=="Город") $model->ADR_CITY='';
			
		$this->render('map',array(
			'model'=>$model,
		));
	}
	
	public function actionAjaxMap()
	{
		$min_latitude  = false;
		$max_latitude  = false;
		$min_longitude = false;
		$max_longitude = false;
		
		$criteria=new CDbCriteria;
		/// Фильтрация по масштабу позиции карты
		if (isset ($_GET['bottom'])) {
			$min_latitude=(float)$_GET['bottom'];
			$criteria->addCondition('LATITUDE > '.$min_latitude);
		}		
		if (isset ($_GET['left'])) {
			$min_longitude=(float)$_GET['left'];
			$criteria->addCondition('LONGITUDE > '.$min_longitude);		
			}
		if (isset ($_GET['right'])) {
			$max_longitude=(float)$_GET['right'];
			$criteria->addCondition('LONGITUDE < '.$max_longitude);		
			}
		if (isset ($_GET['top'])) {
			$max_latitude=(float)$_GET['top'];
			$criteria->addCondition('LATITUDE < '.$max_latitude);					
			}
		
		/*$arFilter = array(
			'>LATITUDE'  => (float)$_GET['bottom'],
			'>LONGITUDE' => (float)$_GET['left'],
			'<LONGITUDE' => (float)$_GET['right'],
			'<LATITUDE'  => (float)$_GET['top']
		);*/
		
		/// Фильтрация по состоянию ямы
		/*if(isset($_GET['state']) && sizeof($_GET['state']) && is_array($_GET['state']))
		{
			$criteria->addCondition('STATE = '.$_GET['state']);
		}
		
		/// Фильтрация по типу ямы
		if(isset ($_GET['type']) && sizeof($_GET['type']) && is_array($_GET['type']))
		{
			$arFilter['TYPE'] = $_GET['type'];
		}*/
		
		// определение, нужна ли премодерация
		/*$raw = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/index.php');
		preg_match('/(\'|\")PREMODERATION\1 => (\"|\')(Y|N|)\2/', $raw, $_match);
		if($_match[3] == 'Y')
		{
			$arFilter['PREMODERATED'] = 1;
		}*/
		
		/// Если не заданы параметры, то производится выборка всех записей ям
		/*if(!$_GET['bottom'] && !$_GET['top'] && !$_GET['bottom'] && !$_GET['right'])
		{
			$res = C1234Hole::GetList();
		}
		else
		{
			$res = C1234Hole::GetList
			(
				array(),
				$arFilter,
				array('nopicts' => true)
			);
		}*/
		
		$res = Holes::model()->findAll($criteria);	
		
		if(in_array(false, array($min_latitude, $max_latitude, $min_longitude, $max_longitude), true))
		{
			foreach($res as &$hole)
			{
				if($min_latitude === false || $min_latitude < $hole['LATITUDE'])
				{
					$min_latitude = $hole['LATITUDE'];
				}
				if($max_latitude === false || $max_latitude > $hole['LATITUDE'])
				{
					$max_latitude = $hole['LATITUDE'];
				}
				if($min_longitude === false || $min_longitude < $hole['LONGITUDE'])
				{
					$min_longitude = $hole['LONGITUDE'];
				}
				if($max_longitude === false || $max_longitude > $hole['LONGITUDE'])
				{
					$max_longitude = $hole['LONGITUDE'];
				}
			}
		}
		
		//echo $_GET['zoom'];
		
		// группировка объектов карты по квадратикам
		$delta_lat     = ($max_latitude  - $min_latitude)  / 6;
		$delta_lon     = ($max_longitude - $min_longitude) / 12;
		$_groupped_res = array();
		foreach($res as &$hole)
		{
			$lon = floor(($hole['LONGITUDE'] - $min_longitude) / $delta_lon);
			$lat = floor(($hole['LATITUDE']  - $min_latitude)  / $delta_lat);
			if (!isset($_groupped_res[$lon][$lat][$hole['STATE']])) $_groupped_res[$lon][$lat][$hole['STATE']]=0;
			$_groupped_res[$lon][$lat][$hole['STATE']]++;
			//echo $_groupped_res[$lon][$lat][$hole['STATE']];
		}
		
		/// Создание объектов карты
		// одиночные точки
		foreach($res as &$hole)
		{
			if(!isset($_REQUEST['skip_id']) || $_REQUEST['skip_id'] != $hole['ID'])
			{
				$lon = floor(($hole['LONGITUDE'] - $min_longitude) / $delta_lon);
				$lat = floor(($hole['LATITUDE']  - $min_latitude)  / $delta_lat);
				if(isset ($_groupped_res[$lon][$lat]) && array_sum($_groupped_res[$lon][$lat]) > 2)
				{
					continue;
				}
				?>
				var s = new YMaps.Style();
				s.iconStyle = new YMaps.IconStyle();
				s.iconStyle.href = "/images/st1234/<?= $hole->type->alias ?>_<?= $hole['STATE'] ?>.png";
				s.iconStyle.size = new YMaps.Point(28, 30);
				s.iconStyle.offset = new YMaps.Point(-14, -30);
				PlaceMarks[<?= $hole['ID'] ?>] = new YMaps.Placemark(new YMaps.GeoPoint(<?= $hole['LONGITUDE'] ?>, <?= $hole['LATITUDE'] ?>), { hasHint: false, hideIcon: false, hasBalloon: false, style: s });
				map.addOverlay(PlaceMarks[<?= $hole['ID'] ?>]);
				<? if(!isset($_REQUEST['noevents'])): ?>
					YMaps.Events.observe
					(
						PlaceMarks[<?= $hole['ID'] ?>],
						PlaceMarks[<?= $hole['ID'] ?>].Events.Click,
						function (obj)
						{
							window.location="/<?= $hole['ID'] ?>/";
						}
					)
				<? endif; ?>
				<?
			}
		}
		
		// группы точек
		?>
		var st = new YMaps.Template(
			"<div class=\"groupPlacemark\" style=\"$[metaDataProperty.groupstyle]\">\
				<span style=\"$[metaDataProperty.spanstyle]\">$[name|0]<\/span>\
				<div class=\"achtung\" style=\"$[metaDataProperty.achtungstyle]\"><div style=\"$[metaDataProperty.achtungstyle2]\"><img src=\"\/images\/st1234\/achtung.png\" style=\"$[metaDataProperty.groupstyle]\"><\/div><\/div>\
				<div class=\"prosecutor\" style=\"$[metaDataProperty.prosecutorstyle]\"><div style=\"$[metaDataProperty.prosecutorstyle2]\"><img src=\"\/images\/st1234\/prosecutor.png\" style=\"$[metaDataProperty.groupstyle]\"><\/div><\/div>\
				<div class=\"inprogress\" style=\"$[metaDataProperty.inprogressstyle]\"><div style=\"$[metaDataProperty.inprogressstyle2]\"><img src=\"\/images\/st1234\/inprogress.png\" style=\"$[metaDataProperty.groupstyle]\"><\/div><\/div>\
				<div class=\"gibddre\" style=\"$[metaDataProperty.gibddrestyle]\"><div style=\"$[metaDataProperty.gibddrestyle2]\"><img src=\"\/images\/st1234\/gibddre.png\" style=\"$[metaDataProperty.groupstyle]\"><\/div><\/div>\
				<div class=\"fresh\" style=\"$[metaDataProperty.freshstyle]\"><div style=\"$[metaDataProperty.freshstyle2]\"><img src=\"\/images\/st1234\/fresh.png\" style=\"$[metaDataProperty.groupstyle]\"><\/div><\/div>\
				<div class=\"fixed\" style=\"$[metaDataProperty.fixedstyle]\"><div style=\"$[metaDataProperty.fixedstyle2]\"><img src=\"\/images\/st1234\/fixed.png\" style=\"$[metaDataProperty.groupstyle]\"><\/div><\/div>\
			<\/div>"
		);
		var s = new YMaps.Style();
		s.iconStyle = new YMaps.IconStyle(st);
		s.iconStyle.href = "/images/st1234/achtung_circle.png";
		<?
		foreach($_groupped_res as $column_id => $column)
		{
			foreach($column as $row_id => &$row)
			{
				$_state_count = array
				(
					'achtung'    => 0,
					'prosecutor' => 0,
					'inprogress' => 0,
					'gibddre'    => 0,
					'fresh'      => 0,
					'fixed'      => 0
				);
				$text    = '';
				$counter = 0;
				foreach($row as $state_name => &$cell)
				{
					//$text    .= GetMessage('HOLE_STATE_'.$state_name).': '.$cell.'<br>';
					$text    .= '--'.': '.$cell.'<br>';
					$counter += $cell;
					$_state_count[$state_name] = $cell;
				}
				if($counter > 2)
				{
					$latitude  = $min_latitude  + $delta_lat * $row_id;
					$longitude = $min_longitude + $delta_lon * $column_id;
					$text      = 'Всего в окрестности метки: '.$counter.'<br>'.$text;
					$cell_id = $column_id.'_'.$row_id;
					
					// размер пимпы
					$iconsize = 0;
					if($counter >= 150)
					{
						$iconsize = 80;
					}
					else
					{
						$iconsize = 40 + $counter / 3;
					}
					// размер шрифта в метке
					$fontsize = 0;
					if($counter >= 150)
					{
						$fontsize = 18;
					}
					else
					{
						$fontsize = ceil(10 + $counter / 14);
					}
					
					// вывод js формирования метки
					?>
					s.iconStyle.offset = new YMaps.Point(-15 -<?= $latitude ?> + Math.round(Math.random() * 30), -35 -<?= $iconsize ?> + Math.round(Math.random() * 30));
					PlaceMarks['<?= $cell_id ?>'] = new YMaps.Placemark
					(
						new YMaps.GeoPoint(<?= $longitude?>, <?= $latitude ?>),
						{
							hasHint: false,
							hideIcon: true,
							hasBalloon: true,
							style: s,
							balloonOptions: {
								hasCloseButton: true,
								mapAutoPan: 0
							}
						}
					);
					PlaceMarks['<?= $cell_id ?>'].name        = '<?= $counter ?>';
					PlaceMarks['<?= $cell_id ?>'].description = '<?= $text ?>';
					<?
					$h  = 0; // общая высота метки
					$dh = 0; // прирост высоты метки
					echo "PlaceMarks['".$cell_id."'].metaDataProperty.groupstyle = 'width: ".$iconsize."px'; ";
					foreach($_state_count as $state => $state_count)
					{
						$dh = $state_count / $counter * $iconsize + 2;
						echo "PlaceMarks['".$cell_id."'].metaDataProperty.".$state."style = 'height: ".(int)$dh."px;'; ";
						echo "PlaceMarks['".$cell_id."'].metaDataProperty.".$state."style2 = 'margin-top: -".(int)$h."px;'; ";
						echo "PlaceMarks['".$cell_id."'].metaDataProperty.spanstyle = 'font-size: ".$fontsize."px'; ";
						$h += $dh;
					}
					echo "map.addOverlay(PlaceMarks['".$cell_id."']); ";
				}
			}
		}
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Holes('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Holes']))
			$model->attributes=$_GET['Holes'];

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
		$model=Holes::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='holes-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
