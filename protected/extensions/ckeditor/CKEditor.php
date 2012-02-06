<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CKEditor
 *
 * @author Ekstazi
 * v 0.2
 */
class CKEditor extends CInputWidget{
    const COLS = 40;
    const ROWS = 10;

    public $language='ru';
    private $allowedLanguages=array(
        'af','ar','bg','bn','bs','ca','cs','da','de','el','en','en-au','en-ca',
        'en-uk','eo','es','et','eu','fa','fi','fo','fr','fr-ca','gl','gu','he',
        'hi','hr','hu','is','it','ja','km','ko','lt','lv','mn','ms','nb','nl',
        'no','pl','pt','pt-br','ro','ru','sk','sl','sr','sr-latn','sv','th','tr',
        'uk','vi','zh','zh-cn'
    );

    private $options=array();
    private $assets;
//advanced - пользователь сам определяет тулбар
    private $allowedEditorTemplates=array('full','basic','advanced');
    private $editorTemplate='full';

    private $plugins=array();

    private $contentCSS = '/css/editor.css';


    private $width = '100%';
    private $height = '400px';

    private $fontFamilies=array(
        'Arial'=>'Arial, Helvetica, sans-serif',
        'Comic Sans MS'=>'Comic Sans MS, cursive',
        'Courier New'=>'Courier New, Courier, monospace',
        'Georgia'=>'Georgia, serif',
        'Lucida Sans Unicode'=>'Lucida Sans Unicode, Lucida Grande, sans-serif',
        'Tahoma'=>'Tahoma, Geneva, sans-serif',
        'Times New Roman'=>'Times New Roman, Times, serif',
        'Trebuchet MS'=>'Trebuchet MS, Helvetica, sans-serif',
        'Verdana'=>'Verdana, Geneva, sans-serif',
    );

    private $fontSizes = array(
        '8'=>'8px',
        '9'=>'9px',
        '10'=>'10px',
        '11'=>'11px',
        '12'=>'12px',
        '14'=>'14px',
        '16'=>'16px',
        '18'=>'18px',
        '20'=>'20px',
        '22'=>'22px',
        '24'=>'24px',
        '26'=>'26px',
        '28'=>'28px',
        '36'=>'36px',
        '48'=>'48px',
        '72'=>'72px'
    );

    private $toolbar=array();

    public $skin='kama';
    private $theme='default';


    public function  __construct($owner=null) {
        parent::__construct($owner);
        $this->setLanguage(Yii::app()->language);
    }

    public function setLanguage($value){
        $lang = (($p = strpos($value, '_')) !== false) ? str_replace('_', '-', $value) : $value;
        if (in_array($lang, $this->allowedLanguages)) {
            $this->language = $lang;
        }
          else {
             $suffix = empty($lang) ? 'en' : ($p !== false) ? strtolower(substr($lang, 0, $p)) : strtolower($lang);
             if (in_array($suffix, $this->allowedLanguages)) $this->language = $suffix;
          }
        if(isset($this->allowedLanguages['ru']))
            $this->language='ru';
    }

    public function getLanguage(){
        return $this->language;
    }

    public function setOptions($value){
        if (!is_array($value))
            throw new CException(Yii::t(__CLASS__, 'options must be an array'));

        $this->options=$value;
    }

    public function getOptions(){
        return $this->options;
    }

    public function setHeight($value)
    {
        if (!preg_match("/[\d]+[px|\%]/", $value))
            throw new CException(Yii::t(__CLASS__, 'height must be a string of digits terminated by "%" or "px"'));
            $this->height = $value;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function setWidth($value)
    {
        if (!preg_match("/[\d]+[px|\%]/", $value))
            throw new CException(Yii::t('ETinyMce', 'width must be a string of digits terminated by "%" or "px"'));
        $this->width = $value;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function setFontFamilies($value)
    {
        if (!is_array($value))
            throw new CException(Yii::t(__CLASS__, 'fontFamilies must be an array of strings'));
        $this->fontFamilies = $value;
    }

    public function getFontFamilies()
    {
        return $this->fontFamilies;
    }

    public function setFontSizes($value)
    {
        if (!is_array($value))
            throw new CException(Yii::t(__CLASS__, 'fontSizes must be an array of integers'));
        $this->fontSizes = $value;
    }

    public function getFontSizes()
    {
        return $this->fontSizes;
    }

    public function setEditorTemplate($value)
    {
        if (!in_array($value, $this->allowedEditorTemplates))
            throw new CException(Yii::t(__CLASS__, 'editorTemplate must be one of {temp}', array('{temp}'=>implode(',', $this->validEditorTemplates))));
        $this->editorTemplate = $value;
    }

    public function getEditorTemplate()
    {
        return $this->editorTemplate;
    }

    public function setPlugins($value)
    {
        if (!is_array($value))
            throw new CException(Yii::t(__CLASS__, 'plugins must be an array of strings'));
        $this->plugins = $value;
    }

    public function getPlugins()
    {
        return $this->plugins;
    }

    public function setContentCSS($value)
    {
        if (!is_string($value))
            throw new CException(Yii::t(__CLASS__, 'contentCSS must be an URL'));
        $this->contentCSS = $value;
    }

    public function getContentCSS()
    {
        return $this->contentCSS;
    }

    public function setToolbar($value){
        if(is_array($value)||is_string($value)){
            $this->toolbar=$value;
        }else throw new CException(Yii::t(__CLASS__, 'toolbar must be an array or string'));
    }

    public function getToolbar()
    {
        return $this->toolbar;
    }

    public function setSkin($value)
    {
        if (!is_string($value))
            throw new CException(Yii::t(__CLASS__, 'Skin must be a string'));
        $this->skin = $value;
    }

    public function getSkin()
    {
        return $this->skin;
    }

    public function setTheme($value)
    {
        if (!is_string($value))
            throw new CException(Yii::t(__CLASS__, 'Theme must be a string'));
        $this->theme = $value;
    }

    public function getTheme()
    {
        return $this->theme;
    }

    protected function makeOptions()
    {
        list($name,$id) = $this->resolveNameID();

        $options['language'] = $this->language;

    // to make the content look like if it were in your target page
        if ($this->contentCSS !== '') {
            $options['contentsCss'] = $this->contentCSS;
        }

        switch ($this->editorTemplate) {
            case 'full':
                $options['toolbar']='Full';
                break;
            case 'basic':
                $options['toolbar']='Basic';
                break;
            default:
                $options['toolbar']=$this->toolbar;
        }

        $fontFamilies='';
        foreach($this->fontFamilies as $k=>$v){
            $fontFamilies.=$k.'/'.$v.';';
        }
        $options['font_names']=$fontFamilies;

        $fontSizes='';
        foreach($this->fontSizes as $k=>$v){
            $fontSizes.=$k.'/'.$v.';';
        }
        $options['fontSize_sizes']=$fontSizes;

        $options['extraPlugins'] = implode(',', $this->plugins);

        $options['skin']=$this->skin;
        $options['theme']=$this->theme;


        $baseDir = dirname(__FILE__);
        $assets = Yii::app()->getAssetManager()->publish($baseDir.DIRECTORY_SEPARATOR.'assets');
        $options["filebrowserBrowseUrl"]="{$assets}/ckfinder/ckfinder.html";
        $options["filebrowserImageBrowseUrl"]="{$assets}/ckfinder/ckfinder.html?type=Images";
        $options["filebrowserFlashBrowseUrl"]="{$assets}/ckfinder/ckfinder.html?type=Flash";
        $options["filebrowserUploadUrl"]="{$assets}/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files";
        $options["filebrowserImageUploadUrl"]="{$assets}/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images";
        $options["filebrowserFlashUploadUrl"]="{$assets}/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash";

        $cs = Yii::app()->getClientScript();
        $cs->registerScriptFile($assets.'/ckeditor.js');
        $cs->registerScriptFile($assets.'/ckfinder/ckfinder.js');

        $this->assets=$assets;
        if (is_array($this->options)) {
            $options = array_merge($options, $this->options);
        }

        return CJavaScript::encode($options);
   }

    public function run(){
        parent::run();

        $assets=$this->assets;

        // here any option is overriden by user's options
              list($name, $id) = $this->resolveNameID();

        $options = $this->makeOptions();

        $cs = Yii::app()->getClientScript();

        $this->htmlOptions['id'] = $id;
        if (!array_key_exists('style', $this->htmlOptions)) {
            $this->htmlOptions['style'] = "width:{$this->width};height:{$this->height};";
        }
        if (!array_key_exists('cols', $this->htmlOptions)) {
            $this->htmlOptions['cols'] = self::COLS;
        }
        if (!array_key_exists('rows', $this->htmlOptions)) {
            $this->htmlOptions['rows'] = self::ROWS;
        }
        $js =<<<EOP
CKEDITOR.replace('{$name}',{$options});
EOP;
        $cs->registerScript('Yii.'.get_class($this).'#'.$id, $js, CClientScript::POS_LOAD);

        if($this->hasModel()) {
            $html = CHtml::activeTextArea($this->model, $this->attribute, $this->htmlOptions);
        }
	else {
            $html = CHtml::textArea($name, $this->value, $this->htmlOptions);
        }

        echo $html;

    }
}
?>
