<?php
/**
 * passwordStrength class file.
 *
 * @author Nicola Puddu
 */

/**
 * CEmailValidator validates that the attribute value is a valid email address.
 *
 * @author Nicola Puddu
 * @package userGroups
 * @since 1.5
 */
class passwordStrength extends CValidator
{
	/**
	 * these constants rappresent the possible strenght of the password
	 * @var int
	 */
	const WEAK = 0;
	const MEDIUM = 1;
	const STRONG = 2;
	/**
	 * @var string the regular expression used to validate weak password.
	 */
	public $weak_pattern = '/^(?=.*[a-zA-Z0-9]).{5,}$/';
	/**
	 * @var string the regular expression used to validate medium password.
	 */
	public $medium_pattern = '/^(?=.*\d(?=.*\d))(?=.*[a-zA-Z](?=.*[a-zA-Z])).{5,}$/';
	/**
	 * @var string the regular expression used to validate strong password.
	 */
	public $strong_pattern = '/^(?=.*\d(?=.*\d))(?=.*[a-zA-Z](?=.*[a-zA-Z]))(?=.*[^a-zA-Z0-9]).{5,}$/';
	/**
	 * the user ID of the user that owns that password
	 * @var int
	 */
	public $user_id;
	
	public $pattern='/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/';
	

	/**
	 * Validates the attribute of the object.
	 * If there is any error, the error message is added to the object.
	 * @param CModel $object the object being validated
	 * @param string $attribute the attribute being validated
	 */
	protected function validateAttribute($object,$attribute)
	{
		
		// skip this check if it's happening on admin scenario and the password attribute is empty
		if ($object->scenario === 'admin' && $object->$attribute == NULL)
			return true;
		
		// save the user ID
		$this->user_id = (int)$object->id;
		// extract the strenght data
		extract($this->strengthData(UserGroupsConfiguration::findRule('password_strength')));
		
		// validate the password
		$value=$object->$attribute;
		if(!preg_match($pattern, $value))
		{
			$this->addError($object,$attribute,$message);
		}
	}

	/**
	 * Returns the JavaScript needed for performing client-side validation.
	 * @param CModel $object the data object being validated
	 * @param string $attribute the name of the attribute to be validated.
	 * @return string the client-side validation script.
	 * @see CActiveForm::enableClientValidation
	 */
	public function clientValidateAttribute($object,$attribute)
	{

		// save the user ID
		$this->user_id = (int)$object->id;
		// extract the strenght data
		extract($this->strengthData(UserGroupsConfiguration::findRule('password_strength')));
		/*
		$message=$this->message!==null ? $this->message : Yii::t('yii','{attribute} is not a valid email address.');
		$message=strtr($message, array(
			'{attribute}'=>$object->getAttributeLabel($attribute),
		));
		
		*/

		$condition="!value.match({$pattern})";

		return "
if(".$condition.") {
	messages.push(".CJSON::encode($message).");
}
";
	}
	
	/**
	 * returns the strenght matching pattern and error message.
	 * @param int $strength
	 * @return array
	 */
	private function strengthData($strength)
	{
		$array = array();
		switch ((int)$strength) {
			case self::WEAK:
				if ($this->user_id === UserGroupsUser::ROOT) {
					$array['pattern'] = $this->medium_pattern;
					$array['message'] = 'Рутовый пароль должен состоять из 2х цифр, 2х букв и быть не меньше 5ти символов';
				} else {
					$array['pattern'] = $this->weak_pattern;
					$array['message'] = 'Пароль должен быть больше 5ти символов';
				}
				break;
			case self::MEDIUM:
				$array['pattern'] = $this->medium_pattern;
				$array['message'] = 'Пароль должен состоять из 2х цифр, 2х букв и быть не меньше 5ти символов';
				break;
			case self::STRONG:
				$array['pattern'] = $this->strong_pattern;
				$array['message'] = 'Пароль должен состоять из 2х цифр, 2х букв и быть не меньше 5ти символов';
				break;
		}
		return $array;
	}
}
