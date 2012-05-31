<?php
class ArchiveHolesCron extends UGCronJob {
	/**
	 * return the cron description
	 */
	protected function getDescription()
	{
		return 'Архивирование старых ям';
	}
	
	/**
	 * return the cron table model name
	 */
	protected function getCronTable()
	{
		return 'UserGroupsCron';
	}
	
	/**
	 * return the cron name
	 */
	protected function getName()
	{
		return 'archivate-holes';
	}
	
	/**
	 * returns the number of days that have to pass before performing this cron job
	 */
	protected function getLapse()
	{
		return 1;
	}
	
	/**
	 * return the model
	 */
	protected function getModel()
	{
		return new Holes;
	}
	
	/**
	 * return the action method
	 */
	protected function getAction()
	{
		return self::UPDATE;
	}
	
	/**
	 * return the action criteria
	 */
	protected function getCriteria()
	{
		$filters=HoleArchiveFilters::model()->findAll();		
		$criteria = new CDbCriteria;
		if ($filters){			
			foreach ($filters as $filter){
				$condArr=Array();
				if ($filter->time_to) $condArr[]='DATE_STATUS < '.(time()-$filter->time_to);
				if ($filter->type_id) $condArr[]='TYPE_ID = '.$filter->type_id;
				if ($filter->status) $condArr[]='STATE = "'.$filter->status.'"';
				$condStr=implode(' AND ', $condArr);
				$criteria->addCondition($condStr,'OR');				
				}
		}
		else $criteria->compare('ID',0);
		$criteria->compare('archive',0);		
		return $criteria;
	}
	
	/**
	 * return the interested database fields
	 */
	protected function getColumns()
	{
		return array('archive' => 1); 
	}
}
?>