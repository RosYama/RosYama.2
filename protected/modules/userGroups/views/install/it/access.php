<p>
	Assicurati di aver specificato l'accessCode nel file di configurazione.<br/>
	userGroups ha inoltre altri parametri di configurazione che puoi utilizzare.<br/>
	<b>Se vuoi utilizzare uno di questi parametri addizionali dovrai settarli prima di effettuare l'installazione.</b>
</p>
<dl class="userGroups">
	<dt>salt</dt>
		<dd>
			userGroups utilizza gi&agrave; un salt per criptare la password, corrispondente al nome utente pi&ugrave; il timestamp dell'ora di registrazione.<br/>
			Se vuoi puoi settare un salt adizionale, che verr&agrave; concatenato a quello gi&agrave; in uso dal sistema.
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
<p>Devi inoltre settare correttamente la propriet&agrave; <i>class</i> del component user.</p>
<div class="code">
	<code>
	'user'=&gt;array(
		// enable cookie-based authentication
		'allowAutoLogin'=&gt;true,
		<span class="highlight">'class'=&gt;'userGroups.components.WebUserGroups',</span>
	),
	</code>
</div>
<p>userGroups ha inoltre bisogno che tu setti in maniera corretta il parametro adminEmail all'interno del tuo file di configurazione:</p>
<div class="code">
	<code>
	'params'=&gt;array(
		<span class="highlight">'adminEmail'=&gt;'me@myadmin.com',</span>
	),
	</code>
</div>
<div class="form login">
<?php $form=$this->beginWidget('CActiveForm'); ?>
	<p>Inserisci l'accessCode per proseguire con l'installazione</p>

	<?php echo $form->passwordField($model,'accesscode'); ?>
	<?php echo $form->error($model,'accesscode'); ?>
	<?php echo CHtml::hiddenField('action', 'accesscode'); ?>
	<?php echo CHtml::submitButton('Prosegui'); ?>

<?php $this->endWidget(); ?>
</div><!-- form -->