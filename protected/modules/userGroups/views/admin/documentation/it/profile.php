<p>La maggior parte delle volte avrai bisogno di aggiungere altri campi al tuo profilo, ma non abbiamo voluto che fossi costretto a mettere le mani
al codice dei modelli e dei controller del modulo per raggiungere il risultato.</p>

<p>Dalla versione 1.1 userGroups fornisce un modo semplice per estendere i tuoi profili utente: le <b>Profile Extension</b></p>


<h3>Primo passo: creare la tabella del database ed il modello</h3>
<p>Crea semplicemente una tabella nel database, come hai sempre fatto.<br/>
Ricordati semplicemente di inserire una colonna chiamata <b>ug_id</b>, fai s&igrave; che sia di tipo bigin con una lunghezza pari a 20, e che sia una chiave unica.</p>

<p>Subito dopo crea il modello, puoi farlo a mano od usando gii se preferisci.</p>

<h3>Aggiungere il metodo profileViews alla classe</h3>
<p>Per funzionare a dover userGroups ha bisogno che tu aggiunga un nuovo metodo al tuo modello: <b>profileView</b></p>

<div class="code"><code>
	/**
	 * returns an array that contains the views name to be loaded
	 * @return array
	 */
	public function profileViews()
	{
		return array(
			UserGroupsUser::VIEW =&gt; 'index',
			UserGroupsUser::EDIT =&gt; 'update',
		);
	}

</code></div>

<p>profileView verr&agrave; chiamato dal modulo userGroups ogni singola volta che dovr&agrave; mostrare la view di un profilo.<br/>
Facciamo l'esempio che un utente va nella pagina del suo profilo.<br/>
userGroups cercher&agrave; nella lista di modelli che estendono il profilo utente, cercando quelli che restituiscono un valore per la chiave
<b>userGroupsUser::VIEW</b>.<br/>
Se il modello restituisce un valore per quella chiave, userGroups caricher&agrave; la view con quel nome.</p>

<p>Per far s&igrave; che userGroups trovi il file della tua view dovrai posizionarlo in questo path:<br/>
<b>protected/views/TABLENAME/</b><br/>

Pertanto se hai creato un nuovo modello per la tabella <i>user_hobbies</i>, userGroups cercher&agrave; per il file della view nel path<br/>
<b>protected/views/user_hobbies/</b></p>

<h3>Creare views UserGroupsUser::VIEW</h3>
<p>Le pagine VIEW vengono caricate quando qualcuno carica un profilo<br/>
Nel tuo view file avrai la variabile <b>$model</b>, la quale conterr&agrave; il modello della tua Profile Extension.</p>

<h3>Creare view UserGroupsUser::UPDATE</h3>
<p>Nella tua update views avrai sempre il modello della tua Profile Extension all'interno della variabile <b>$model</b>.<br/>
Avrai inoltre una variabile nominata <b>$user_id</b>, la quale conterr&agrave; l'ID del profilo di cui si sta facendo l'editing.</p>
<p>Nella tua update view potrai utilizzare la validazione ajax e noi ti incoraggiamo a farlo, ma dovrai prestare particolare attenzione al bottone di invio.<br/>
Qui puoi vedere un esempio del bottone:</p>

<div class="code"><code>
	echo CHtml::ajaxSubmitButton(
		Yii::t('userGroupsModule.general','Update External Profile'), // prima riga
		Yii::app()-&gt;baseUrl . '/userGroups/user/update/id/'.$user_id, // seconda riga
		array('update' =&gt; '#userGroups-container'), // terza riga
		array('id' =&gt; 'submit-profile-'.$model-&gt;id.rand()) // quarta riga
	);

</code></div>

<p>Nella prima riga stiamo usando il metodo Yii::t() per definire il nome del botton. Non hai bisogno di fare altrettanto
se non ti interessa la localizzazione, pertanto puoi utilizzare una stringa.<br/>
Nella seconda riga settiamo il path del form. Assicurati di copiarlo cos&igrave; com'&egrave; almeno che tu non voglia
usare un tuo controller per gestire l'azione di salvataggio del form. In quel caso punta il form verso il path corretto.<br/>
Nella terza riga settiamo l'azione che il javascript eseguir&agrave; subito dopo l'esecuzione del form. Copiala esattamente
cos&igrave; com'&egrave;.<br/>
Nella quarta riga settiamo un nome casuale per l'id del bottone. Questa &egrave; una cosa importante da fare se non vuoi
avere problemi di overloading con ajax.</p>

<h3>Creare view UserGroupsUser::REGISTRATION</h3>
<p>Nei file delle view di registrazione dovrai semplicemente inserire i campi input del tuo form, e nient'altro.<br/>
I tuoi campi input saranno parte del form di registrazione e userGropus si occuper&agrave; della validazione e di salvare i dati sul database.<br/>
In queste view avrai il modello della tua Profile Extension all'interno della variabile <b>$model</b>.</p>

<h3>Quindi posso usare la normale validazione nel mio modello e tutto il resto?</h3>
<p>Certo che puoi, e potrai inoltre utilizzare tutte le altre feature dei modelli a cui sei abituato, come afterFind, beforeSave eccetera eccetera.<br/>
Al momento userGroups supporta le Profile Extensions solo per le azioni di view ed editing, non per la registrazione o il recovery.<br/>
Ad ogni modo ti incoraggiamo ad utilizzare regole basate sugli scenari per evitare futuri problemi.<br/>
Lo scenario utilizzato nelle view UserGroupsUser::UPDATE &egrave;: <b>updateProfile</b>.<br/>
Lo scenario utilizzato nelle view UserGroupsUser::REGISTRATION &egrave;: <b>registration</b>.</p>


<h3>Quindi, dopo aver settato sta roba cosa devo fare per far s&igrave; userGroups si accorga della mia Profile Extension?</h3>
<p>Questo, come sempre, &egrave; molto semplice: apri il file di configurazione della tua applicazione ed aggiungi il parametro
profilo al modulo userGroups.<br/>
Questo parametro deve contenere un array nel quale ciascun valore &egrave; il nome della classe del modello che stai includendo.</p>

<div class="code"><code>
	'userGroups'=&gt;array(
		'accessCode'=&gt;'your access code, by now useless',
		<span class="highlight">'profile' =&gt; array('UserHobbies'),</span>
	)

</code></div>

<h3>Le Profile Extension utilizzando il sistema di relazioni di CActiveRecord?</h3>
<p>Ovviamente s&igrave;, userGroups crea al momento relazioni di tipo HAS_ONE con i modelli che tu inserisci, pertanto ogni volta che
vorrai usare il modello UserGroupsUser tutti i dati delle tue Profile Extensions verranno caricati.<br/>
Potrai ottenerli usando questa sintassi:
<b>$model-&gt;rel[model class name]</b><br/>
Pertanto considerando ancora l'esempio del model UserHobbies, la sintassi sarebbe la seguente.
<b>$model-&gt;relUserHobbies</b>

<h3>Come posso caricare in sessione i dati delle Profile Extension?</h3>
<p>Semplicemente dovrai implementare un altro metodo all'interno del modello della tua Profile Extension:</p>

<div class="code"><code>
	/**
	 * returns an array that contains the names of the attributes that will
	 * be stored in session
	 * @return array
	 */
	public function profileSessionData()
	{
		return array(
			'attribute_name',
		);
	}

</code></div>

<p>Per caricare quei dati semplicemente usa la seguente riga di codice:<br/>
<strong>Yii::app()-&gt;user-&gt;profile('MODEL_CLASS_NAME', 'ATTRIBUTE_NAME');</strong><br/>
quindi nell'esempio seguito fin'ora scriveremo:<br/>
<strong>Yii::app()-&gt;user-&gt;profile('UserHobbies', 'hobby');</strong><br/>
</p>