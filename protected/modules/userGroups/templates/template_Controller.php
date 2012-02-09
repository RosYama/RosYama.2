<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var mixed the default tooltip for every controller.
	 * if you give to this parameter a boolean false value instead of an array,
	 * the controller will not be displayed in the permission menagement view.
	 * for more information view the documentation in the userGroups module.
	 */
	public static $_permissionControl = array('read' => false, 'write' => false, 'admin' => false);
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
	/**
	 * The filter method for 'UserGroupsAccessControl' filter.
	 * This filter is a wrapper of {@link UserGroupsAccessControl}.
	 * To use this filter, you must override {@link accessRules} method.
	 * @param CFilterChain $filterChain the filter chain that the filter is on.
	 */
	public function filterUserGroupsAccessControl($filterChain)
	{
		Yii::import('userGroups.models.UserGroupsUser');
		Yii::import('userGroups.models.UserGroupsConfiguration');
		Yii::import('userGroups.components.UserGroupsAccessControl');
		$filter=new UserGroupsAccessControl;
		$filter->setRules($this->accessRules());
		$filter->filter($filterChain);
	}
	
}