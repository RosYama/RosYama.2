<?php

class userAreaMapWidget extends CWidget {

        public $itemview='default';

        public $data=array();

        public $options=array();

        public $htmlOptions=array();

        public $all=TRUE;

        public function init() {
            $this->registerCoreScripts();
            parent::init();
        }


        protected function registerCoreScripts() {
            $cs=Yii::app()->getClientScript();
            $cs->registerCoreScript('jquery');
			$cs->registerScriptFile('http://api-maps.yandex.ru/1.1/index.xml?key='.Yii::app()->params['YMapKey']);
	        $jsFile = CHtml::asset(dirname(__FILE__).DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'area_script.js');
    	    $cs->registerScriptFile($jsFile);
    	    $cs->registerCssFile('/css/holes_list.css');
        }



        public function run() {
            $this->registerCoreScripts();
                $this->render($this->itemview, $this->data);

        }
}
