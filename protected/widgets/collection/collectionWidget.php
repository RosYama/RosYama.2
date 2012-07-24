<?php

class collectionWidget extends CWidget {

        public $itemview='default';

      
        public function init() {
            $this->registerCoreScripts();
            parent::init();
        }


        protected function registerCoreScripts() {
            $cs=Yii::app()->getClientScript();
            $cs->registerCoreScript('jquery');
        }



        public function run() {
        $model=Holes::model()->count(Array('condition'=>'PREMODERATED=1'));
        $all=$model;
        $model=Holes::model()->count(Array('condition'=>'PREMODERATED=1 AND STATE="inprogress"'));
        $ingibdd=$model;
        $model=Holes::model()->count(Array('condition'=>'PREMODERATED=1 AND STATE="fixed"'));
        $fixed=$model;
        //$model=Holes::model()->count(Array('condition'=>'archive=1'));
        //$archive=$model;  
            $this->registerCoreScripts();
                $this->render($this->itemview, Array(
                'all'=>Y::declOfNum($all, array('дефект', 'дефекта', 'дефектов')),
				'ingibdd'=>$ingibdd,
				'fixed'=>$fixed,
				//'archive'=>$archive,
                ));
        }
}
