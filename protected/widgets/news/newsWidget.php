<?php

class newsWidget extends CWidget {

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
        }



        public function run() {
            $this->registerCoreScripts();
                $this->render($this->itemview, Array(
                	'model'=>News::model()->findAll(array ('order'=>"date desc",'condition'=>"published=1 AND archive=0", 'limit'=>1) ) , 
                	)
                );

        }
}
