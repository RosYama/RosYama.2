<?php

/**
 * This is the model class for table "{{hole_types}}".
 *
 * The followings are the available columns in table '{{hole_types}}':
 * @property integer $id
 * @property string $alias
 * @property string $name
 * @property string $pdf_body
 * @property string $pdf_footer
 * @property integer $published
 * @property integer $ordering
 *
 * The followings are the available model relations:
 */
class HoleTypes extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return HoleTypes the static model class
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
		return '{{hole_types}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('alias, name, pdf_body, pdf_footer, published, ordering', 'required'),
			array('published, ordering', 'numerical', 'integerOnly'=>true),
			array('alias, name', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, alias, name, pdf_body, pdf_footer, published, ordering', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		'commands'=>array(self::HAS_MANY, 'HoleTypePdfListCommands', 'hole_type_id','order'=>'commands.ordering'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function getLastOrder()
		{
        $criteria = new CDbCriteria();
		$criteria->select='ordering';
		$criteria->limit=1;
		$criteria->order='ordering DESC';
		$lastorder=$this->find($criteria);
        if ($lastorder) return $lastorder->ordering;
        else return 0;
		}

	public function getSortOrder()
		{
                $output = '';
                $output.= '<span>';
		        if ($this->ordering>1) $output.= CHtml::link('<img src="/images/uparrow.png" width="16" height="16" border="0" alt="Вверх"/>', array('order', 'id'=>$this->id, 'dir'=>'up'), array('class'=>'ajaxupdate'));
		        else $output.='&nbsp;';
		        $output.= '</span>';
		        $output.= '<span>';
		        if ($this->ordering<$this->getLastOrder()) $output.= CHtml::link('<img src="/images/downarrow.png" width="16" height="16" border="0" alt="Вниз"/>', array('order', 'id'=>$this->id, 'dir'=>'down'), array('class'=>'ajaxupdate'));
		        else $output.='&nbsp;';
		        $output.= '</span>';
   		        $output.= "<span>$this->ordering</span>";
   		        $output.= CHtml::hiddenField('order',$this->ordering, array('class'=>'order_ordering'));
   		        $output.= CHtml::hiddenField('id',$this->id, array('class'=>'order_id'));
		        return $output;
		}

    public function getpublish(){
		if ($this->published) {$publtext='снять с публикации'; $pubimg='published.png';}
		else {$publtext='опубликовать';  $pubimg='unpublished.png';}
		return '<a class="publish" title="'.$publtext.'" href="'.Yii::app()->getController()->CreateUrl("publish", Array('id'=>$this->id)).'">
			<img src="/images/'.$pubimg.'" alt="'.$publtext.'"/>
			</a>';
	}	

	
	public function afterSave(){
		if (isset($_POST['HoleTypePdfListCommands'])) {
			foreach ($_POST['HoleTypePdfListCommands'] as $command){
			if(isset($command['id'])) $model=HoleTypePdfListCommands::model()->findByPk($command['id']);
			else $model=new HoleTypePdfListCommands;
			$model->attributes=$command;
			$model->hole_type_id=$this->id;
			$model->save();
			} 
		}
		
	}	
	
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'alias' => 'Alias',
			'name' => 'Название',
			'pdf_body' => 'Тело ПДФ документа',
			'pdf_footer' => 'Низ ПДФ документа',
			'published' => 'Опубликованно',
			'ordering' => 'Порядок',
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
		$criteria->compare('alias',$this->alias,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('pdf_body',$this->pdf_body,true);
		$criteria->compare('pdf_footer',$this->pdf_footer,true);
		$criteria->compare('published',$this->published);
		$criteria->compare('ordering',$this->ordering);
       	$criteria->order='ordering';

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
			'pagination'=>array(
                                'pageSize'=> Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize']),
                        ),
		));
	}
}