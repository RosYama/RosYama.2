<?php
/**
 * available variables inside the $data array:
 * '{email}'=> user email address,
 * '{username}'=> username,
 * '{activation_code}'=> activation code if available,
 * '{link}'=> short link without get parameters,
 * '{full_link}'=> full link with get parameters,
 * '{website}'=> value of the appName parameter inside your configuration file
 * '{temporary_username}' => boolean: true if the username is temporary and can be changed
 *
 * usage example:
 * $data['{link}']
 */
?>
<p>Автоматически сгенерированное информационное сообщение сайта
<?php echo CHtml::link($data['{website}'], 'http://'.$_SERVER['HTTP_HOST'].'/holes/index');?></p>
<p>------------------------------------------</p>

Здравствуйте, <?php echo $data['{username}']; ?><br /><br />
Вы получили это сообщение, так как ваш адрес был использован при регистрации нового пользователя на сервере <?=Yii::app()->params['name']?>.<br /><br />
Ваш код для подтверждения регистрации: <?php echo $data['{activation_code}']; ?><br /><br />
Для подтверждения регистрации перейдите по следующей ссылке:<br />
<a href="<?php echo $data['{full_link}']; ?>&UserGroupsUser[active]=1"><?php echo $data['{full_link}']; ?>&UserGroupsUser[active]=1</a><br/>
<br/>Вы также можете ввести код для подтверждения регистрации на странице:<br/>
<a href="<?php echo $data['{link}']; ?>"><?php echo $data['{link}']; ?></a><br/><br/>

Внимание! Вы не сможете пользоваться Вашей учётной записью, пока не подтвердите свою регистрацию.<br/><br/>
