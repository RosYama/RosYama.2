<p>
	Make sure you specified an access code in your configuration file.<br/>
	userGroups also has other configuration parameters that you may use.<br/>
	<b>If you want to use any of these additional parameters you have to set them up before running this installation.</b>
</p>
<dl class="userGroups">
	<dt>salt</dt>
		<dd>
			userGroups already uses a salt string for the password which is the username plus the timestamp of the user registration time.<br/>
			If you want to, you can set up an additional salt that is added to the one already in use by the system.
		</dd>
</dl>
<div class="code">
	<code>
	'modules'=&gt;array(
		'userGroups'=&gt;array(
			<span class="highlight">'accessCode'=&gt;'Type your access code here',</span>
			<span class="highlight">'salt'=&gt;'Type your salt here',</span>
		),
	),
	</code>
</div>
<p>You also have to correctly set the class property in the user component.</p>
<div class="code">
	<code>
	'user'=&gt;array(
		// enable cookie-based authentication
		'allowAutoLogin'=&gt;true,
		<span class="highlight">'class'=&gt;'userGroups.components.WebUserGroups',</span>
	),
	</code>
</div>
<p>userGroups also needs that you properly set and adminEmail parameter inside your application configuration file:</p>
<div class="code">
	<code>
	'params'=&gt;array(
		<span class="highlight">'adminEmail'=&gt;'me@myadmin.com',</span>
	),
	</code>
</div>
<div class="form login">
<?php $form=$this->beginWidget('CActiveForm'); ?>
	<p>Please enter access code to proceed with the installation </p>

	<?php echo $form->passwordField($model,'accesscode'); ?>
	<?php echo $form->error($model,'accesscode'); ?>
	<?php echo CHtml::hiddenField('action', 'accesscode'); ?>
	<?php echo CHtml::submitButton('Enter'); ?>

<?php $this->endWidget(); ?>
</div><!-- form -->