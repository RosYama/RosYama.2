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
		$criteria = new CDbCriteria;
		$criteria->compare('archive',0);
		$criteria->addCondition('DATE_STATUS < '.(time()-60*60*24*30*12).' OR (STATE="fixed" AND DATE_STATUS < '.(time()-60*60*24*30*3).')');
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