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
{username},<br/>
<br/>
Вы запросили ваши регистрационные данные.<br/>
<br/>
Ваша регистрационная информация:<br/>
<br/>
Login: <b><?php echo $data['{username}']; ?></b><br/>
Код активации: <b><?php echo $data['{activation_code}']; ?></b><br/>
<br/>
Для смены пароля перейдите по следующей ссылке:<br/>
<a href="<?php echo $data['{full_link}']; ?>"><?php echo $data['{full_link}']; ?></a><br/>
<br/>
Сообщение сгенерировано автоматически.<br/>