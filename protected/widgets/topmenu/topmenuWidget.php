<?php

class topmenuWidget extends CWidget {

        public $itemview='topmenu';

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
    	    $cssFile = CHtml::asset(dirname(__FILE__).DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'cssmenu.css');
    	    $cs->registerCssFile($cssFile);
        }



        public function run() {

            $cs = Yii::app()->getClientScript();
            Yii::app()->clientScript->registerCoreScript('jquery');
            $this->registerCoreScripts();

            $model = MainMenu::model()->findByPK(1);
            $menu=$model->getTreeViewArray(false,'id',1);            
            
        	$this->render($this->itemview,array(
        	'menu'=>$menu,
        	));


        }
}