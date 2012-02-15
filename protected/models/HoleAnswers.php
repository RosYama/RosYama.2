<?php

/**
 * This is the model class for table "{{hole_answers}}".
 *
 * The followings are the available columns in table '{{hole_answers}}':
 * @property integer $id
 * @property integer $request_id
 * @property integer $date
 * @property string $comment
 */
class HoleAnswers extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return HoleAnswers the static model class
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
		return '{{hole_answers}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('request_id, date', 'required'),
			array('request_id, date', 'numerical', 'integerOnly'=>true),
			array('comment', 'length'),
			array('uppload_files, results', 'safe'),
			array('uppload_files', 'required', 'on'=>'insert', 'message' => 'Необходимо загрузить ответ ГИБДД'),
			array('uppload_files', 'file', 'types'=>'jpg, png, gif, txt, pdf','maxFiles'=>10),			
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, request_id, date, comment', 'safe', 'on'=>'search'),
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
			'files'=>array(self::HAS_MANY, 'HoleAnswerFiles', 'answer_id'),
			'files_img'=>array(self::HAS_MANY, 'HoleAnswerFiles', 'answer_id', 'condition'=>'file_type="image"'),
			'files_other'=>array(self::HAS_MANY, 'HoleAnswerFiles', 'answer_id', 'condition'=>'file_type!="image"'),
			'request'=>array(self::BELONGS_TO, 'HoleRequests', 'request_id'),
			'results'=>array(self::MANY_MANY, 'HoleAnswerResults',
               '{{hole_answer_results_xref}}(answer_id, result_id)'),
		);
	}
	
	public function behaviors(){
          return array( 'CAdvancedArBehavior' => array(
            'class' => 'application.extensions.CAdvancedArBehavior'));
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'request_id' => 'Request',
			'date' => 'Date',
			'comment' => 'Комментарии (по желанию)',
			'uppload_files'=>'Необходимо добавить отсканированный ответ из ГИБДД',
			'results'=>'Фактический результат запроса'
		);
	}
	
	public function getFilesFolder(){
		return '/upload/st1234/answers/'.$this->request->hole->ID.'/'.$this->request->type;
	}	
	
	public function getuppload_files(){
		return CUploadedFile::getInstancesByName('');
	}
	
	public function afterSave()
	{			
		parent::afterSave();
		$files=$this->uppload_files;
		if($files){
		$dir=$_SERVER['DOCUMENT_ROOT'].$this->filesFolder;
		if (!is_dir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/answers/'))
			mkdir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/answers/');
		if (!is_dir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/answers/'.$this->request->hole->ID))
			mkdir($_SERVER['DOCUMENT_ROOT'].'/upload/st1234/answers/'.$this->request->hole->ID);
		if (!is_dir($dir))
			mkdir($dir);
		if (!is_dir($dir.'/thumbs'))
			mkdir($dir.'/thumbs');	
			
			foreach ($files as $file){
			if(!$file->hasError){
				$model=new HoleAnswerFiles;
				$model->answer_id=$this->id;
				$model->file_name=rand().'.'.$file->extensionName;
				$filetypeArr=explode('/', $file->type);
				if ($filetypeArr[0]=='image') $filetype='image';
				else $filetype=$file->type;
				$model->file_type=$filetype;
				if ($model->save()){
					$file->saveAs($dir.'/'.$model->file_name);
					if ($model->file_type=='image'){						
						$image = Yii::app()->image->load($dir.'/'.$model->file_name);
						$image->resize(600, 450)->rotate(0)->quality(90)->sharpen(20);
						//$image->crop($imgmax['width'], $imgmax['height']);
						$savename=$dir.'/thumbs/'.$model->file_name;
						$image->save($savename);
						}
					}
				}
			}
		}
	}	
	
	public function beforeDelete(){
		parent::beforeDelete();
		$this->results=Array();
		$this->update();
		foreach ($this->files as $file) $file->delete();
		return true;
	}
	
	
	public function afterDelete(){
		parent::afterDelete();
		$requests=CHtml::listData( $this->request->hole->requests_gibdd, 'id', 'id' );
		if (!count ($this->findAll('request_id IN ('.implode(',',$requests).')'))){						
			$this->request->hole->STATE='inprogress';				
			$this->request->hole->update();
			}
		return true;	
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
		$criteria->compare('request_id',$this->request_id);
		$criteria->compare('date',$this->date);
		$criteria->compare('comment',$this->comment,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}