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

Информационное сообщение сайта РосЯма<br/>
------------------------------------------<br/>
<?php echo $data['{username}']; ?>,<br/>
<br/>
Вы запросили ваши регистрационные данные.<br/>
<br/>
Ваша регистрационная информация:<br/>
<br/>
Login: <b><?php echo $data['{username}']; ?></b><br/>
<br/>
Вы авторизованы с помощью сервиса <b><?php echo $data['{external_auth_id}']; ?></b>. <br/> 
При авторизации с помощью сторонних сервисов пароль на сайте РосЯма не требуется.<br/>
<br/>
Сообщение сгенерировано автоматически.<br/>