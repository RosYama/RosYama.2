<?php

/**
 * This is the model class for table "{{rf_subjects}}".
 *
 * The followings are the available columns in table '{{rf_subjects}}':
 * @property integer $id
 * @property string $name
 * @property string $name_full
 * @property string $name_full_genitive
 */
class RfSubjects extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return RfSubjects the static model class
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
		return '{{rf_subjects}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, name_full, name_full_genitive', 'required'),
			array('name, name_full, name_full_genitive', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, name_full, name_full_genitive', 'safe', 'on'=>'search'),
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
		'gibdd'=>array(self::HAS_ONE, 'GibddHeads', 'subject_id', 'condition'=>'is_regional=1'),//Yii::app()->user->isModer ? '':''
		'gibdd_local'=>array(self::HAS_MANY, 'GibddHeads', 'subject_id', 'condition'=>Yii::app()->user->isModer ? 'is_regional=0': (!Yii::app()->user->isGuest ? 'is_regional=0 AND (moderated=1 OR author_id='.Yii::app()->user->id.')':'is_regional=0 AND moderated=1')),
		'gibdd_local_not_moderated' => array(self::STAT, 'GibddHeads', 'subject_id', 'condition'=>'moderated=0'),
		'prosecutor'=>array(self::HAS_ONE, 'Prosecutors', 'subject_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'name_full' => 'Name Full',
			'name_full_genitive' => 'Name Full Genitive',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	 
	// нечувствительный к регистру поиск по массиву
	protected function gs_array_search($needle, $haystack)
	{
		foreach($haystack as $k => $v)
		{		
			if(mb_strtoupper($v,'UTF-8') == mb_strtoupper($needle,'UTF-8'))
			{
				return $k;
				
			}
		}
		return false;
	}	 
	
	public function SearchID($subject_name)
	{
		$subject_name = trim($subject_name, " \n\t");
		$_RF_SUBJECTS=CHtml::listData($this->findAll(), 'id','name');
		$result = $this->gs_array_search($subject_name, $_RF_SUBJECTS);
		if(!$result)
		{
			$subject_name = explode(' ', $subject_name);
			foreach($subject_name as $s)
			{
				$ls = mb_strtolower($s,'UTF-8');				
				if
				(
					$ls == 'республика'
					|| $ls == 'край'
					|| $ls == 'область'
					|| $ls == 'округ'
				)
				{
					continue;
				}
				$result = $this->gs_array_search($s, $_RF_SUBJECTS);
				if($result)
				{
					break;
				}
			}
		}
		return $result;
	}
	
	public function Address($address)
	{
		// достанем из адреса субъект РФ и город
		$_address = explode(',', $address);
		do
		{
			$subject_rf = '';
			$city       = '';
			if
			(
				$_address[0] == 'Россия'
				|| $_address[0] == 'Российская Федерация'
				|| $_address[0] == 'Russia'
				|| $_address[0] == 'Russian Federation')
			{
				$_address = array_slice($_address, 1);
			}
			$_address[0] = trim($_address[0]);
			// города - субъекты РФ
			if($_address[0] == 'Москва' || $_address[0] == 'Санкт-Петербург')
			{
				$subject_rf  = $this->SearchID($_address[0]);
				$city        = $_address[0];
				$_address[0] = '';
				$_address[1] = trim($_address[1]);
				// города-спутники
				if
				(
					$_address[1] == 'Зеленоград'
					|| strpos($_address[1], 'поселок') !== false
					|| strpos($_address[1], 'город')   !== false
					|| strpos($_address[1], 'деревня') !== false
					|| strpos($_address[1], 'село')    !== false
				)
				{
					$city        = $_address[1];
					$_address[1] = '';
					$address     = implode(', ', $_address);
					break;
				}
				$address = implode(', ', $_address);
				break;
			}
			// неизвестно что
			if(!isset($_address[1]) || !$_address[1])
			{
				$subject_rf = '';
				$city       = '';
				$address    = implode(', ', $_address);
				break;
			}
			$subject_rf = trim($_address[0]);
			$subject_rf = $this->SearchID($subject_rf);
			if(!$subject_rf)
			{
				// регион не определился
				$subject_rf = '';
				$city       = '';
				$address    = implode(', ', $_address);
				break;
			}
			$_address[0] = '';
			// район или город
			if(strpos($_address[1], 'район') !== false)
			{
				$_address[1] = '';
				// точка попала в город
				if($_address[2])
				{
					$city        = trim($_address[2]);
					$_address[2] = '';
					$address     = implode(', ', $_address);
				}
				// точка попала фиг знает куда
				else
				{
					$city       = '';
					$address    = implode(', ', $_address);
					break;
				}
			}
			else
			{
				$city        = trim($_address[1]);
				$_address[1] = '';
				$address     = implode(', ', $_address);
			}
		}
		while(false);
		$address = trim($address, ' ,');
		$address = str_replace('  ', ' ', $address);
		return array
		(
			'subject_rf' => $subject_rf,
			'city'       => $city,
			'address'    => $address
		);
	}
	
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('name_full',$this->name_full,true);
		$criteria->compare('name_full_genitive',$this->name_full_genitive,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}