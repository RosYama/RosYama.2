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

<?php echo $data['{username}']; ?>,<br/>
<br/>
Вы запросили ваши регистрационные данные.<br/>
Ваша регистрационная информация:<br/>
<br/>
Login: <b><?php echo $data['{username}']; ?></b><br/>
Код активации: <b><?php echo $data['{activation_code}']; ?></b><br/>
<br/>
Для смены пароля перейдите по следующей ссылке:<br/>
<a href="<?php echo $data['{full_link}']; ?>"><?php echo $data['{full_link}']; ?></a><br/>

<br/>
<hr/>
<p>Не хотите получать такие уведомления? Отключите соответствующую опцию в <?php echo CHtml::link('настройках вашего профиля.', 'http://'.$_SERVER['HTTP_HOST'].'/profile/update');?></p>
