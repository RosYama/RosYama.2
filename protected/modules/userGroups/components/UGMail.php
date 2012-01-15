<?php
/**
 * this file contains the UGMail class and all the UGMailMessage classes
 * @author Nicola Puddu
 * @package userGroups
 * @since 1.6
 */

/**
 * This class takes care of collecting the mail message info
 * and send it to the desired user
 * @author Nicola Puddu
 */
class UGMail {

	/**
	 * these constants define the three kind of mail messages
	 * @var string
	 */
	const ACTIVATION = 'activation';
	const PASS_RESET = 'pass_reset';
	const INVITATION = 'invitation';

	/**
	 * @var string the admin mail in the application settings
	 */
	protected $_from;
	/**
	 * @var string the user email
	 */
	protected $_to;
	/**
	 * @var array the data used by Yii::t() to complete the messages
	 */
	protected $_data;
	/**
	 * @var string the email headers
	 */
	protected $_header;
	/**
	 * @var string the email subject
	 */
	protected $_subject;
	/**
	 * @var string the email message
	 */
	protected $_body;
	/**
	 * @var string the flash message displayed in case of no errors
	 */
	protected $_sent;
	/**
	 * @var string the flash message displayed in case of errors
	 */
	protected $_error;
	/**
	 * @var boolean true if the swift-mail component is installed
	 */
	protected $_swiftmail;
	/**
	 * @var YiiMail contains the yii-mail object
	 */
	protected $_message;


	/**
	 * load the email data inside the object
	 * @param UserGroupsUser $model the user will receive the email
	 * @param string $message rappresent what kind of mail message the user will receive. refers to the three constants
	 */
	public function __construct(UserGroupsUser $model, $message)
	{
		$this->_from = Yii::app()->params->adminEmail;
		// check if yii-mail, the swift-mail component, is installed
		$this->_swiftmail = Yii::app()->getComponent('mail') instanceof YiiMail;
		// extract user data
		$this->extractUserData($model);
		// extract the mail classes defined
		$mailMessages = Yii::app()->controller->module->mailMessages;
		$mailMessage = isset($mailMessages[$message]) ? new $mailMessages[$message] : $this->defaultMailMessage($message);
		// populate the mail attributes
		if ($this->_swiftmail) {
			$this->_message = new YiiMailMessage;
			$this->_message->setBody($mailMessage->mailBody($this->_data), 'text/html');
			$this->_message->subject = $mailMessage->mailSubject($this->_data);
			$this->_message->addTo($model->email, $model->username);
			$this->_message->from = array($this->_from => Yii::app()->name . ' swift-mail');
		} else {
			$this->_header = $mailMessage->mailHeader($this->_from);
			$this->_subject = $mailMessage->mailSubject($this->_data);
			$this->_body = $mailMessage->mailBody($this->_data);
		}
		// pupulate the flash messages
		$this->_sent = $mailMessage->mailSuccess($this->_data);
		$this->_error = $mailMessage->mailError($this->_data);
	}

	/**
	 * send the email message and set the flash messages
	 */
	public function send()
	{
		$sentmail = $this->_swiftmail ? Yii::app()->mail->send($this->_message) : mail($this->_to, $this->_subject, $this->_body, $this->_header);

		if ($sentmail)
			Yii::app()->user->setFlash('mail', $this->_sent);
		else
			Yii::app()->user->setFlash('mail', $this->_error);
	}

	/**
	 * populate the $_data attribute with the user informations
	 * @param UserGroupsUser $model the user that is goint to receive the email
	 */
	protected function extractUserData(UserGroupsUser $model)
	{
		$this->_to = "{$model->username} <{$model->email}>";
		$link = 'http://'.$_SERVER['HTTP_HOST'].Yii::app()->homeUrl.'userGroups/user/activate';
		$full_link = $link.'?UserGroupsUser[username]='.$model->username.'&UserGroupsUser[activation_code]='.$model->activation_code;
		$this->_data = array(
			'{email}'=>$model->email,
			'{username}'=>$model->username,
			'{activation_code}'=>$model->activation_code,
			'{link}'=>$link,
			'{full_link}'=>$full_link,
			'{website}'=>Yii::app()->name,
			'{temporary_username}' => substr($model->username, 0, 1) === '_',
		);
	}

	/**
	 * return the default UGMailMessage object
	 * @param string $message the kind of mail message desired
	 * @return UGMailMessage
	 */
	protected function defaultMailMessage($message)
	{
		switch ($message) {
			case self::ACTIVATION:
				return new UGMailActivation;
				break;
			case self::PASS_RESET:
				return new UGMailPassReset;
				break;
			case self::INVITATION:
				return new UGMailInvitation;
				break;
		}
	}


}

/**
 * the interface that every mail message has to implement
 * @author Nicola Puddu
 * @package userGroups
 */
interface UGMailMessage {

	/**
	 * @param string $admin_mail the application adminMail parameter
	 * @return string the email headers
	 */
	public function mailHeader($admin_mail);
	/**
	 * @param array $data the data array that can be used by Yii::t()
	 * @return string the email subject
	 */
	public function mailSubject($data);
	/**
	 * @param array $data the data array that can be used by Yii::t()
	 * @return string the email body
	 */
	public function mailBody($data);
	/**
	 * @param array $data the data array that can be used by Yii::t()
	 * @return string the flash message to use in case of no errors
	 */
	public function mailSuccess($data);
	/**
	 * @param array $data the data array that can be used by Yii::t()
	 * @return string the flash message to use in case of errors
	 */
	public function mailError($data);
}

/**
 * the mail message that will be sent to user that have to be activated
 * @author Nicola Puddu
 * @package userGroups
 */
class UGMailActivation implements UGMailMessage {

	/**
	 * @see UGMailMessage::mailHeader()
	 */
	public function mailHeader($admin_mail)
	{
		$headers = 'MIME-Version: 1.0' . "\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
		$headers .= 'From: '.Yii::app()->name.' <'.$admin_mail.'>';
		return $headers;
	}

	/**
	 * @see UGMailMessage::mailSubject()
	 */
	public function mailSubject($data)
	{
		return Yii::t('userGroupsModule.mail', 'account activation');
	}

	/**
	 * @see UGMailMessage::mailBody()
	 */
	public function mailBody($data)
	{

		return Yii::app()->controller->renderPartial('//ugmail/activation', array(
			'data' => $data
			), true);
	}

	/**
	 * @see UGMailMessage::mailSuccess()
	 */
	public function mailSuccess($data)
	{
		return Yii::t('userGroupsModule.general','The activation email was successfully sent to {email}', $data);
	}

	/**
	 * @see UGMailMessage::mailError()
	 */
	public function mailError($data)
	{
		return Yii::t('userGroupsModule.general','Impossible to send email to the address {email}', $data);
	}
}

/**
 * the mail message that will be sent to user that requested a password reset
 * @author Nicola Puddu
 * @package userGroups
 */
class UGMailPassReset implements UGMailMessage {

	/**
	 * @see UGMailMessage::mailHeader()
	 */
	public function mailHeader($admin_mail)
	{
		$headers = 'MIME-Version: 1.0' . "\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
		$headers .= 'From: '.Yii::app()->name.' <'.$admin_mail.'>';
		return $headers;
	}

	/**
	 * @see UGMailMessage::mailSubject()
	 */
	public function mailSubject($data)
	{
		return Yii::t('userGroupsModule.mail', 'password reset request');
	}

	/**
	 * @see UGMailMessage::mailBody()
	 */
	public function mailBody($data)
	{

		return Yii::app()->controller->renderPartial('//ugmail/passreset', array(
			'data' => $data
			), true);
	}

	/**
	 * @see UGMailMessage::mailSuccess()
	 */
	public function mailSuccess($data)
	{
		return Yii::t('userGroupsModule.general','An email containing the instructions to reset your password has been sent to your email address: {email}', $data);
	}

	/**
	 * @see UGMailMessage::mailError()
	 */
	public function mailError($data)
	{
		return Yii::t('userGroupsModule.general','Impossible to send email to the address {email}', $data);
	}
}

/**
 * the mail message that will be sent to user that have been invited to join the application
 * @author Nicola Puddu
 * @package userGroups
 */
class UGMailInvitation implements UGMailMessage {

	/**
	 * @see UGMailMessage::mailHeader()
	 */
	public function mailHeader($admin_mail)
	{
		$headers = 'MIME-Version: 1.0' . "\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
		$headers .= 'From: '.Yii::app()->name.' <'.$admin_mail.'>';
		return $headers;
	}

	/**
	 * @see UGMailMessage::mailSubject()
	 */
	public function mailSubject($data)
	{
		return Yii::t('userGroupsModule.mail', 'invitation to {website}', $data);
	}

	/**
	 * @see UGMailMessage::mailBody()
	 */
	public function mailBody($data)
	{
		return Yii::app()->controller->renderPartial('//ugmail/invitation', array(
			'data' => $data
			), true);
	}

	/**
	 * @see UGMailMessage::mailSuccess()
	 */
	public function mailSuccess($data)
	{
		return Yii::t('userGroupsModule.general','An invitation email was sent to the address {email}', $data);
	}

	/**
	 * @see UGMailMessage::mailError()
	 */
	public function mailError($data)
	{
		return Yii::t('userGroupsModule.general','Impossible to send email to the address {email}', $data);
	}
}