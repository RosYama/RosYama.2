<?php

class socialWidget extends CWidget {

        public $itemview='left';

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
                	
                	)
                );

        }
}
