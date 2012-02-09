<?php
/* 
 * EJuiAutoCompleteFkField class file
 *
 * @author Jeremy Dunn <jeremy.j.dunn@gmail.com>
 * @link http://www.yiiframework.com/
 * @version 1.1 - 21 March 2011
 */

/*
 * The EJuiAutoCompleteFkField extension renders a CJuiAutoComplete field plus supporting form fields for a FK field.
 * Typically it is used for a model with a foreign key field to a parent  table that has too many records
 * for a drop-down list to be practical.
 *
 * For example it could be used in a Contact table, with a foreign key PostCodeId to a PostCode table
 * with thousands of records, one for each city / postcode combination.  The user would type the city name in the AutoCompleter,
 * and the PostCodeId would be stored in the correct PostCodeId column; while the display attribute (e.g. City, Province) is shown
 * in the form.
 *
 * The extension renders the following form objects:
 * 1) the model field itself, which may optionally be hidden or visible
 * 2) a hidden field that holds the description field of the FK record, for redisplay if the user
 *    fails to choose a value from the autoCompleter
 * 3) the AutoComplete field itself, which also displays the existing value from the related record
 * 4) a 'delete' icon to clear fields 1-3 above
 * 5) javascript to tie everything together
 *
 * Typical usage:
 * 1) unzip the extension into ../extensions/
 *
 * 2) make sure config/main.php has:
 *    <pre>
 *      ...
 *      import=>array(
 *          ...
 *          'application.extensions.*',
 *          ...
 *      ),
 *    </pre>
 *
 * 3) ensure the relationship exists in the model:
 * in Contacts.php (example):
 * <pre>
 * ...
 * 'relations'=>array(
 *      'Postcode'=>array('self::BELONGS_TO, 'PostCodes', 'PostCodeId'),
 * ...
 * </pre>
 *
 * 4) in the related table, optionally create a pseudo-attribute for display purposes
 * in PostCodes.php (model) for example:
 * <pre>
 * ...
 * public function getPostCodeAndProvince() {
 *      return $this->PostCodeId . ', ' . $this->Province;
 * }
 * </pre>
 *
 * 5) in the _form.php for the main record (e.g. Contacts)
 * <pre>
 * ...
 * echo $form->labelEx($model, 'PostCodeId);
 * $this->widget('EJuiAutoCompleteFkField', array(
 *      'model'=>$model, //  e.g. the Contact model (from CJuiInputWidget)
 *      'attribute'=>'PostCodeId',  // the FK field (from CJuiInputWidget)
 *      'sourceUrl'=>'findPostCode', // name of the controller method to return the autoComplete data (see below)  (from CJuiAutoComplete)
 *      'showFKField'=>true, // defaults to false.  set 'true' to display the FK value in the form with 'readonly' attribute.
 *      'FKFieldSize=>15, // display size of the FK field.  only matters if not hidden.  defaults to 10
 *      'relName'=>'Postcode', // the relation name defined above
 *      'displayAttr'=>'PostCodeAndProvince',  // attribute or pseudo-attribute to display
 *      'autoCompleteLength'=>60, // length of the AutoComplete/display field, defaults to 50
 *      // any attributes of CJuiAutoComplete and jQuery JUI AutoComplete widget may also be defined.  read the code and docs for all options
 *      'options'=>array(
 *          'minLength'=>3, // number of characters that must be typed before autoCompleter returns a value, defaults to 2
 *      ),
 * ));
 * echo $form->error($model, 'PostCodeId');
 * ...
 * </pre>
 *
 * 6) in the Controller for the model, create a method to return the autoComplete data.
 *    NOTE: make sure to give users the correct permission to execute this method, according to your security scheme
 * 
 * in ContactsController.php (for example):
 * </pre>
 *   // data provider for EJuiAutoCompleteFkField for PostCodeId field
 *   public function actionFindPostCode() {
 *       $q = $_GET['term'];
 *       if (isset($q)) {
 *           $criteria = new CDbCriteria;
 *           $criteria->condition = '...', //condition to find your data, using q1 as the parameter field
 *           $criteria->order = '...'; // correct order-by field
 *           $criteria->limit = ...; // probably a good idea to limit the results
 *           $criteria->params = array(':q' => trim($q) . '%'); // with trailing wildcard only; probably a good idea for large volumes of data
 *           $PostCodes = PostCodes::model()->findAll($criteria);
 *
 *           if (!empty($PostCodes)) {
 *               $out = array();
 *               foreach ($PostCodes as $p) {
 *                   $out[] = array(
 *                       'label' => $p->PostCodeAndProvince,  // expression to give the string for the autoComplete drop-down
 *                       'value' => $p->PostCodeAndProvince, // probably the same expression as above
 *                       'id' => $p->PostCodeId, // return value from autocomplete
 *                   );
 *               }
 *               echo CJSON::encode($out);
 *               Yii::app()->end();
 *           }
 *       }
 *   }
 * </pre>
 *
 * 7) in the Controller loadModel() method, return the related record
 * in ContactsController.php (for example)
 * <pre>
 * public function loadModel() {
 *      ...
 *      if (isset($_GET['id']))
 *               $this->_model=Contacts::model()->with('Postcode')->findbyPk($_GET['id']);  // <====  NOTE 'with()'
 *      ...
 * }
 * </pre>
 */

Yii::import('zii.widgets.jui.CJuiAutoComplete');
class EJuiAutoCompleteFkField extends CJuiAutoComplete {

	/**
	 * @var boolean whether to show the FK field.
	 */
	public $showFKField = false;
	
	public $showDelImage = false;
	
	public $defaultVal='';
	
	public $cssClass;

	/**
	 * @var integer length of the FK field if visible
	 */
	public $FKFieldSize = 10;
	/**
	 * @var string the relation name to the FK table
	 */
	public $relName;

	/**
	 * @var string the attribute (or pseudo-attribute) to display from the FK table
	 */
	public $displayAttr;

	/**
	 * @var integer width of the AutoComplete field
	 */
	public $autoCompleteLength = 50;

	/**
	 * @var string the name of the FK field
	 */
	private $_fieldName;
        
	/**
	 * @var string the name of the hidden field to save the display value
	 */
	private $_saveName;
        
	/**
	 * @var string the name of the AutoComplete field
	 */
	private $_lookupName;

	/**
	 * @var string the initial display value
	 */
	private $_display;

    public function init() {
		parent::init(); // ensure assets are published
		
        $this->_fieldName = get_class($this->model).'_'.$this->attribute;  // match what is generated by Yii
        $this->_saveName = $this->attribute.'_save';
        $this->_lookupName = $this->attribute.'_lookup';

        $related = $this->model->{$this->relName}; // get the related record
        $this->_display=(!empty($this->model->{$this->attribute}) ? $related->{$this->displayAttr} : '');

        if (!isset($this->options['minLength']))
            $this->options['minLength'] = 2;

        if (!isset($this->options['maxHeight']))
            $this->options['maxHeight']='100';

        $this->htmlOptions['size'] = $this->autoCompleteLength;
        // fix problem with Chrome 10 validating maxLength for the auto-complete field
        $this->htmlOptions['maxLength'] = $this->autoCompleteLength;        
        $this->htmlOptions['id'] = $this->_lookupName;
        $this->htmlOptions['name'] = $this->_lookupName;
        if ($this->cssClass) $this->htmlOptions['class'] = $this->cssClass; 
        
        // setup javascript to do the work
        if (!$this->_display) $this->_display=$this->defaultVal;
        $this->options['create']="js:function(event, ui){\$(this).val('".$this->_display."');}";  // show initial display value
        // after user picks from list, save the ID in model/attr field, and Value in _save field for redisplay
        $this->options['select']="js:function(event, ui){\$('#".$this->_fieldName."').val(ui.item.id);\$('#".$this->_saveName."').val(ui.item.value);}";
        // when the autoComplete field loses focus, refresh the field with current value of _save
        // this is either the previous value if user didn't pick anything; or the new value if they did
        $this->htmlOptions['onblur']="$(this).val($('#".$this->_saveName."').val()); if ($(this).val()=='' || $(this).val()=='".$this->defaultVal."') $(this).addClass('".$this->cssClass."');";
        $this->htmlOptions['onclick']="if ($(this).val()=='".$this->defaultVal."') $(this).val(''); $(this).removeClass('".$this->cssClass."');";
    }

    public function run() {
        // first render the FK field.  This is the actual data field, populated by autocomplete.select()
        if ($this->showFKField) {
            echo CHtml::activeTextField($this->model, $this->attribute, array('size'=>$this->FKFieldSize,'readonly'=>'readonly'));
        } else {
            echo CHtml::activeHiddenField($this->model,$this->attribute);
        }
        
        // second, the hidden field used to refresh the display value
        echo CHtml::hiddenField($this->_saveName,$this->_display); 

        // third, the autoComplete field itself
        parent::run();

        // fouth, an image button to empty all three fields
        // first publish the delete.png from CGridView
        if ($this->showDelImage){
			$deleteImageURL=Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('zii.widgets.assets.gridview').'/delete.png');
			$label=Yii::t('app','Remove ').ucfirst($this->relName);  // TODO: how to translate relname ?
			echo CHtml::image($deleteImageURL, $label,
				array('title'=>$label,
					'name'=>'remove_'.$this->attribute,
					'style'=>'margin-left:6px;',
					'onclick'=>"$('#".$this->_fieldName."').val('');$('#".$this->_saveName."').val('');$('#".$this->_lookupName."').val('');",
				)
			);
        }
    }
}
?>
