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
To Activate your account please click on this link:<br/>
<?php echo $data['{full_link}']; ?>
<br/>or you can go to this address<br/>
{link}<br/>
and insert in the form the following data<br/>
username: <b><?php echo $data['{username}']; ?></b><br/>
activation code: <b><?php echo $data['{activation_code}']; ?></b>