<?php

/**
 * This is the model class for table "{{hole_answer_results}}".
 *
 * The followings are the available columns in table '{{hole_answer_results}}':
 * @property integer $id
 * @property string $name
 * @property integer $published
 * @property integer $ordering
 *
 * The followings are the available model relations:
 */
class HoleAnswerResults extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return HoleAnswerResults the static model class
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
		return '{{hole_answer_results}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, published, ordering', 'required'),
			array('published, ordering', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, published, ordering', 'safe', 'on'=>'search'),
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
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Заголовок',
			'published' => 'Опубликовано',
			'ordering' => 'Порядок',
		);
	}
	
	public function getpublish(){
		if ($this->published) {$publtext='снять с публикации'; $pubimg='published.png';}
		else {$publtext='опубликовать';  $pubimg='unpublished.png';}
		return '<a class="publish" title="'.$publtext.'" href="'.Yii::app()->getController()->CreateUrl("publish", Array('id'=>$this->id)).'">
			<img src="/images/'.$pubimg.'" alt="'.$publtext.'"/>
			</a>';
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
		$criteria->compare('name',$this->name,true);
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