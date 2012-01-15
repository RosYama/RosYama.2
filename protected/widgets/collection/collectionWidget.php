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
        $model=Holes::model()->findAll('PREMODERATED=1');
        $all=count($model);
        $ingibdd=0;
        $fixed=0;
        foreach ($model as $item){
        	if ($item->STATE=='inprogress') $ingibdd++; 
        	if ($item->STATE=='fixed') $fixed++;
        }
            $this->registerCoreScripts();
                $this->render($this->itemview, Array(
                'all'=>Y::declOfNum($all, array('дефект', 'дефекта', 'дефектов')),
				'ingibdd'=>$ingibdd,
				'fixed'=>$fixed,
                ));
        }
}
