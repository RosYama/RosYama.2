<p>userGroups gi&agrave; possiede dei cron jobs installati e funzionanti.<br/>
I Cron Jobs sono operazioni che vengono eseguite ad intervalli regolari.<br/>
I due cron jobs preinstallati si occupano di attivare gli utenti il cui periodo di ban sia scaduto e di rimuovere quelli che non sono stati attivati per pi&ugrave;
di sette giorni dall'invio della mail di attivazione al loro indirizzo di posta.</p>

<h3>Puoi personalizzarli!</h3>

<?php echo CHtml::image(Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('userGroups.img')) . "/doc06.png", 'documentation images', array('class' => 'doc-images')); ?>

<p>
Come puoi vedere puoi decidere quanti giorni devono passare tra un esecuzione e l'altra di ciascun cron.<br/>
Allo stato attuale i cron jobs verranno verificati ogni volta che un utente esegue il login nel sistema.<br/>
Se un tempo pari o superiore da quello indicato in Lapse sar&agrave; trascorso dall'ultima esecuzione, questo verr&agrave; eseguito di nuovo.
</p>

<h3>Puoi crearli!</h3>
<p>
Hai bisogno di altri cron jobs?<br/>
Puoi facilmente crearne quanti ne vuoi, perch&egrave; il sistema di cron job si userGroups &egrave; stato realizzato utilizzando design patterns e modularit&agrave;.<br/>
Cosa devi fare per crearne uno?<br/>
&Egrave; molto semplice, apri il file <b>UGCron.php</b> situato nella directory component di userGroups e dai un'occhiata ai due cron jobs gi&agrave; installati.<br/>
Il codice &egrave; commentato quasi riga per riga, per renderti pi&ugrave; semplice la comprensione dello stesso.<br/>
</p>

<p>Se utilizzi la stessa <b>cronTable</b> utilizzata dai due cron job esistenti, il tuo verr&agrave; automaticamente installato in userGroups.</p>

<h3>Quindi userGroups trova il mio cron job come per magia?</h3>
<p>Esatto!<br/>
Scherzi a parte, per far s&igrave; che userGroups si accorga del tuo cron job dovrai inserirlo all'interno di un suo file, ovunque tu voglia, assicurandoti che si trovi
in una directory gi&agrave; importata da Yii.<br/>
Puoi vedere quali directory sono gi&agrave; importate di default da Yii e settare quelle che vuoi all'interno del file di configurazione della tua
applicazione:
</p>

<div class="code"><code>
	'import'=&gt;array(
		'application.models.*',
		'application.components.*',
		<span class="highlight">'ext.myFolder.*',</span>
	),

</code></div>

<p>Per avere pi&ugrave; informazioni su come scrivere le stringhe per importare file, leggi la documentazione ufficiale di Yii.<br/>
Ora c'&egrave; solo una cosa che dovrai fare: dire a userGroups che il tuo cron job esiste.<br/>
Nel file di configurazione setta un parametro crons ed assegnagli un array. Ogni valore in questo array deve essere il nome della
clase di un tuo cron job:</p>

<div class="code"><code>
	'userGroups'=&gt;array(
		'accessCode'=&gt;'ciao',
		<span class="highlight">'crons'=&gt;array('myCronClassName'),</span>
	),

</code></div>

<p>E questo &egrave; tutto! Il tuo cron job verr&agrave; installato automaticamente all'interno di userGroups.<br/>
Se ti stanchi del tuo cron job dovrai semplicemente cancellare il suo nome dal parametro cron all'interno del file di setting dell'applicazione.
Per poi cancellarlo anche dal database premi il bottone che puoi vedere negli Strumenti di Root all'interno della sezione dei Cron Jobs.</p>

<h3>E se non volessi gestire i miei cron jobs all'interno di userGroups?</h3>
<p>Puoi creare una tua tabella sul database per tenere traccia dei tuoi cron jobs ed utilizzare le funzioni di base della classe UGCron.<br/>
Questa &egrave; la sintassi MySQL di base per creare la cron table:</p>

<div class="code"><code>
	CREATE TABLE your_table_name
	(
	id BIGINT(20) AUTO_INCREMENT,
	name VARCHAR(40),
	lapse INT(6),
	last_occurrence DATETIME,
	PRIMARY KEY (id)
	);

</code></div>

<p>Se vuoi eseguire i cron jobs all'interno di altre pagine dovrai utilizzare le seguenti righe di codice:</p>

<div class="code"><code>
	UGCron::init();
	UGCron::add(new UGCJGarbageCollection);
	UGCron::run();

</code></div>

<p>Ricordati di aggiungere ogni singolo Cron Job che vuoi usare prima di eseguire il metodo UGCron::run()</p>

<p>Se non ti interessa far eseguire i tuoi cron jobs in pagine diverse da quella di login, come di default fa userGroups, lo script di base del
modulo si occuper&agrave; di richiamare il tuo cron job, senza che tu debba modificare alcuna riga di codice.<br/>
Pertanto <b>non modificare mai UserGroupsIdentity</b> solo per aggiungere i tuoi cron jobs.</p>

<h3>Eseguire i cronjobs con crontab.</h3>
<p>Dalla versione 1.8 di userGroups puoi utilizzare i cronjob con crontab. In questo modo non dovrai pi&ugrave; dipendere da eventi random.</p>
<p>Per utilizzare questa feature semplicemente visita il seguente url utilizzando wget o qualunque altro sistema tu preferisca: /userGroups/admin/cron</p>
<p>Quel url pu&ograve; essere raggiunto solo da localhost per motivi di sicurezza e solo se il settaggio corrispondente &egrave; attivo.</p>