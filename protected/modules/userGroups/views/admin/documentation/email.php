<p>since version 1.6 you can finally customize the email messages used by userGroups.</p>

<h3>Prepare your message class</h3>
<p>All you have to do to use this new feature is to create your email message class.<br/>
How can you do that? Just implement the UGMailMessage interface (located inside the UGMail.php file inside the module components directory)
and you are good to go.<br/>
The methods you'll have to implement are all self explanatory and if you have any doubt about what to do with each one of them you can take a look at
the already implemented classes.</p>

<h3>What if I just want to make small changes to the default messages?</h3>
<p>That's even easier: just extend the class of the mail message you want to change.</p>

<h3>How do i make userGroups aware of my new class?</h3>
<p>Just like Profile Extensions all you have to do is edit the module configuration located inside your application config file:</p>

<div class="code"><code>
	'userGroups'=&gt;array(
		'accessCode'=&gt;'your access code, by now useless',
		<span class="highlight">'mailMessages' =&gt; array('invitation' =&gt; 'YourClassName'),</span>
	)

</code></div>

<p>Just pay attention to the following rules:</p>

<ul>
	<li>userGroups must be able to create an instance of your class, so if you store your class file inside some directory that
	is not already imported by Yii, do it!
	</li>
	<li>if you put your class file inside a directory already imported by Yii remember to name the class file after the class name.</li>
	<li>in this version of userGroups we are not supporting dot notation for you class name when using customized mail messages.</li>
</ul>

<h3>In the above example the array key is 'invitation'. What keys do i have to use?</h3>
<p>Easy enough:</p>

<dl>
	<dt>invitation</dt>
		<dd>the message you want to use when sending invitation emails</dd>
	<dt>pass_reset</dt>
		<dd>the message that will be sent when a user request a pass reset</dd>
	<dt>activation</dt>
		<dd>the message that will be sent to a user with the instructions to activate his own account</dd>
</dl>


<h3>All those methods have a $data argument. What's that?</h3>
<p>$data is just an array, ready to be used by Yii::t().<br/>
Still, if you don't care about localization you can use the data inside that array.<br/>
Here's a list of the informations stored inside that array:</p>

<dl>
	<dt>{email}</dt>
		<dd>contains the user email address</dd>
	<dt>{username}</dt>
		<dd>contains the user username</dd>
	<dt>{activation_code}</dt>
		<dd>contains the user activation code. This value may be null</dd>
	<dt>{link}</dt>
		<dd>the link to your application user activation page</dd>
	<dt>{full_link}</dt>
		<dd>just like the one above, plus it's already filled with username and activation code as get parameters</dd>
	<dt>{website}</dt>
		<dd>your application name</dd>
	<dt>{temporary_username}</dt>
		<dd>a boolean value to determine if the username is temporary or not</dd>
</dl>

<h3>What if i just want to change the mail message and don't care about anything else?</h3>
<p>Since version 1.8 the mail messages body are stored inside some view files.<br/>
These view files are located inside your application view directory inside the ugmail folder.</p>
<p>The reason because those file are stored not inside the module folder is because in this way you won't have to
worry about it when updating userGroups.</p>
