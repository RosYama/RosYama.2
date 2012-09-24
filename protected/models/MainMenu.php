<?php

/**
 * This is the model class for table "ttv_menu_top".
 *
 * The followings are the available columns in table 'ttv_menu_top':
 * @property integer $id
 * @property integer $lft
 * @property integer $rgt
 * @property integer $level
 * @property string $name
 * @property string $link
 *
 * The followings are the available model relations:
 */
class MainMenu extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return MainMenu the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{mainmenu}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('lft, rgt, level', 'required'),
			array('lft, rgt, level, type', 'numerical', 'integerOnly'=>true),
			array('name, controller, action, elementmodel, element', 'length', 'max'=>255),
			array('link', 'url','allowEmpty'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, lft, rgt, level, name, link', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function behaviors(){
		    return array(
		        'TreeBehavior' => array(
		            'class' => 'application.extensions.nestedset.TreeBehavior',
		            '_idCol' => 'id',
		            '_lftCol' => 'lft',
		            '_rgtCol' => 'rgt',
		            '_lvlCol' => 'level',
		        ),
		        'TreeViewTreebehavior' => array(
		            'class' => 'application.extensions.nestedset.TreeViewTreebehavior',
		        )
		    );
	}

	public function getNameExt()
	{
    // это нужно для наглядности дерева в контролах CDropdownList и CListBox
    $lvl=$this->level;
    $separator='';
    if ($lvl>1) $separator=' |_ ';
    if ($lvl>=1)$lvl-=1;


    return str_repeat('.....',$lvl).$separator.$this->name;
	}

	public function gettypestring()
	{
		if ($this->type==0) return "Стандартно";
		elseif ($this->type==1) return "Внешняя ссылка";
	}

	public function getcontrollerstring()
	{
		if ($this->type==0) return $this->controller;
		elseif ($this->type==1) return $this->link;
	}

	public function getactionstring()
	{
		if ($this->type==0) return $this->action;
		elseif ($this->type==1) return '';
	}

	public function getNameExtWithRoot()
	{
    // это нужно для наглядности дерева в контролах CDropdownList и CListBox
    $lvl=$this->level;
    return str_repeat('----',$lvl).' '.$this->name;
	}

	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'lft' => 'Lft',
			'rgt' => 'Rgt',
			'level' => 'Level',
			'name' => 'Заголовок',
			'link' => 'Сcылка',
			'controller' => 'Контроллер',
			'action' => 'Действие',
			'element' => 'Элемент',
			'nameExt' => 'Заголовок',
			'type' => 'Тип пункта меню',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('lft',$this->lft);
		$criteria->compare('rgt',$this->rgt);
		$criteria->compare('level',$this->level);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('link',$this->link,true);
		$criteria->order='lft';
        $criteria->condition="id > 1";
		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
			'pagination'=>array(
                                'pageSize'=> Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize']),
                        ),
		));
	}

	public function getControllerActions($items=null)
	{
		if( $items===null )
			$items = $this->getAllControllers();

		foreach( $items['controllers'] as $controllerName=>$controller )
		{
			$actions = array();
			$file = fopen($controller['path'], 'r');
			$lineNumber = 0;
			while( feof($file)===false )
			{
				++$lineNumber;
				$line = fgets($file);
				preg_match('/public[ \t]+function[ \t]+action([A-Z]{1}[a-zA-Z0-9]+)[ \t]*\(/', $line, $matches);
				if( $matches!==array() )
				{
					$name = $matches[1];
					$actions[ strtolower($name) ] = array(
						'name'=>$name,
						'line'=>$lineNumber
					);
				}
			}

			$items['controllers'][ $controllerName ]['actions'] = $actions;
		}

		foreach( $items['modules'] as $moduleName=>$module )
			$items['modules'][ $moduleName ] = $this->getControllerActions($module);

		return $items;
	}

	public function getContActions($controller)
	{

			$actions = array();
			
			$p = Yii::app()->createController($controller);
			if ($p && isset ($p[0]) && $actonArr=$p[0]->actions()){
				foreach ($actonArr as $key=>$val){
					if (isset($val['class']) && $val['class']=='CViewAction'){
						$actions[$key]= array(
								'name'=>$key,
								'line'=>0
							);
						}
				}
			}
			
			$fstchar=substr($controller, 0,1);
			$fstchar=strtoupper($fstchar);
			$controller=substr($controller, 1,strlen($controller));
			$controller=$fstchar.$controller;
			$controller=$_SERVER['DOCUMENT_ROOT'].'/protected/controllers/'.$controller.'Controller.php';
			$file = fopen($controller, 'r');
			$lineNumber = 0;			
			
			while( feof($file)===false )
			{
				++$lineNumber;
				$line = fgets($file);
				preg_match('/public[ \t]+function[ \t]+action([A-Z]{1}[a-zA-Z0-9]+)[\t]*\(/', $line, $matches);
				if( $matches!==array() )
				{
					$name = $matches[1];
					$actions[ strtolower($name) ] = array(
						'name'=>$name,
						'line'=>$lineNumber
					);
				}
			}

			$items = $actions;


		return $items;
	}

	public function getActionElements($controller, $action)
	{
           	$form=new CActiveForm;
			
			$p = Yii::app()->createController($controller);
			if ($p && isset ($p[0])) $contObj=$p[0];
			else $contObj=Array();
			if ($contObj && $actonArr=$contObj->actions()){
				if (isset($actonArr[$action]) && $actonArr[$action]['class']=='CViewAction'){
				$path=$contObj->viewPath.'/pages';
				$files=Array();
				if (is_dir($path))
					foreach (scandir($path) as $file){
						if (strpos($file, '.php')) {
							$name=str_replace('.php', '', $file);
							$files[$name]=$name;
							}							
					}
				if ($files) return $form->dropDownList($this, 'element', $files, array()).
				$form->hiddenField($this, 'elementmodel', Array('value'=>'CViewAction'));			

				}
			}
			
			$actions = array();
			$controller=$_SERVER['DOCUMENT_ROOT'].'/protected/controllers/'.$controller.'Controller.php';
			$file = file_get_contents($controller);

			preg_match('!.*action'.$action.'(.*)!im', $file, $matches);
			if (isset ($matches[1])) preg_match('!\/\*(.*)\*\/!ism', $matches[1], $matches);

			if (isset ($matches[1])){
				parse_str($matches[1]);
				//

				$criteria=new CDbCriteria;
				if ($condition) $criteria->condition=$condition;
				if ($order) $criteria->order=$order;
				
				$returmnodels=new $model;
				$returmnodels=$returmnodels->model()->findAll($criteria);
				$this->elementmodel=$model;
				if (isset ($selectitem3)) return $form->dropDownList($this, 'element', CHtml::listData( $returmnodels, $selectitem1, $selectitem2, $selectitem3), array('prompt'=>'Выбирите...') ).
				$form->hiddenField($this, 'elementmodel');
				else return $form->dropDownList($this, 'element', CHtml::listData( $returmnodels, $selectitem1, $selectitem2), array('prompt'=>'Выбирите...') ).
				$form->hiddenField($this, 'elementmodel');

			}
			else return '';



	}

	public function getAllControllers()
	{
		$basePath = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'protected';
		$items['controllers'] = $this->getControllersInPath($basePath.DIRECTORY_SEPARATOR.'controllers');
		$items['modules'] = $this->getControllersInModules($basePath);
		return $items;
	}

	protected function getControllersInPath($path)
	{
		$controllers = array();

		if( file_exists($path)===true )
		{
			$controllerDirectory = scandir($path);
			foreach( $controllerDirectory as $entry )
			{
				if( $entry{0}!=='.' )
				{
					$entryPath = $path.DIRECTORY_SEPARATOR.$entry;
					if( strpos(strtolower($entry), 'controller')!==false )
					{
						$name = substr($entry, 0, -14);
						$controllers[ strtolower($name) ] = array(
							'name'=>$name,
							'file'=>$entry,
							'path'=>$entryPath,
						);
					}

					if( is_dir($entryPath)===true )
						foreach( $this->getControllersInPath($entryPath) as $controllerName=>$controller )
							$controllers[ $controllerName ] = $controller;
				}
			}
		}

		return $controllers;
	}

	protected function getControllersInModules($path)
	{
		$items = array();

		$modulePath = $path.DIRECTORY_SEPARATOR.'modules';
		if( file_exists($modulePath)===true )
		{
			$moduleDirectory = scandir($modulePath);
			foreach( $moduleDirectory as $entry )
			{
				if( substr($entry, 0, 1)!=='.' && $entry!=='rights' )
				{
					$subModulePath = $modulePath.DIRECTORY_SEPARATOR.$entry;
					if( file_exists($subModulePath)===true )
					{
						$items[ $entry ]['controllers'] = $this->getControllersInPath($subModulePath.DIRECTORY_SEPARATOR.'controllers');
						$items[ $entry ]['modules'] = $this->getControllersInModules($subModulePath);
					}
				}
			}
		}

		return $items;
	}
}	?>