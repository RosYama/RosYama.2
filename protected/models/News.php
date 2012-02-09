<?php

/**
 * This is the model class for table "ttv_content_news".
 *
 * The followings are the available columns in table 'ttv_content_news':
 * @property integer $id
 * @property string $date
 * @property string $title
 * @property string $introtext
 * @property string $fulltext
 * @property integer $published
 * @property integer $archive
 *
 * The followings are the available model relations:
 */
class News extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return News the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	*/
	public $image;
	public $noimage="noimage.jpg";
	public $PictureFolder="/images/news/";
	public $PictureSize=Array('width'=>123, 'height'=>71);

	public function savePicture()
	{
			$picture=CUploadedFile::getInstance($this,'image');
			if ($picture){
			$rndindex=rand(0, 100);
			$imgmax=$this->PictureSize;
            $imagename=$picture->getTempName();
			$image = Yii::app()->image->load($imagename);
			$image->resize($imgmax['width'], 2000)->rotate(0)->quality(90)->sharpen(20);
			$image->crop($imgmax['width'], $imgmax['height']);
			preg_match('!(.*?)\.(.*?)$!',$picture->getName(),$match);
			$imgname=$match[1];
			$imgext=$match[2];
			$savename=$_SERVER['DOCUMENT_ROOT'].$this->PictureFolder.$imgname.$rndindex.".".$imgext;
		   	$image->save($savename);
            $this->picture=$imgname.$rndindex.".".$imgext;
            }
			return true;
    }

    public function getImg()
	{
			if ($this->picture)	return  $this->PictureFolder.$this->picture;
			else return  $this->PictureFolder.$this->noimage;
    }

    public function getpublish(){
		if ($this->published) {$publtext='снять с публикации'; $pubimg='published.png';}
		else {$publtext='опубликовать';  $pubimg='unpublished.png';}
		return '<a class="publish" title="'.$publtext.'" href="/news/publish?id='.$this->id.'">
			<img src="/images/'.$pubimg.'" alt="'.$publtext.'"/>
			</a>';
	}

	public function tableName()
	{
		return '{{news}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date, title, introtext', 'required'),
			array('published, archive', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>255),
			array('picture', 'length', 'max'=>255),
			array('fulltext', 'length'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.

			array('id, date, title, introtext, fulltext, published, archive', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function getDateValue()
	{
		//CDateTimeParser::parse(time(), 'dd/MM/yyyy');
	  if (!$this->date) return date('d.m.Y');
  	  else return date('d.m.Y',$this->date);
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
			'date' => 'Дата',
			'title' => 'Заголовок',
			'introtext' => 'Анонс',
			'picture' => 'Изображение',
			'fulltext' => 'Текст новости',
			'published' => 'Опубликовано',
			'archive' => 'Архив',
			'not_special' => 'Спец. новость'
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
		$criteria->compare('date',$this->date,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('introtext',$this->introtext,true);
		$criteria->compare('fulltext',$this->fulltext,true);
		$criteria->compare('published',$this->published);
		$criteria->compare('archive',$this->archive);
		$criteria->order='date DESC';

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
			'pagination'=>array(
                                'pageSize'=> Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize']),
                        ),
            'sort'=>array(
			    'defaultOrder'=>'date',
			    )
		));
	}
}	?>