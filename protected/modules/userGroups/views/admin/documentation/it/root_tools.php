<h3>Questo &egrave; il tuo Palantir!</h3>
<p>Negli Strumenti di Root hai accesso a tutto quello che ti serve:<br/>
qui puoi creare utenti, gruppi, cambiare alcuni dei loro dati, aggiornare configurazioni e cron jobs.
</p>

<h3>Utenti e Gruppi</h3>
<p>
Questi sono i due pannelli che userai di pi&ugrave;.<br/>
Qui, chiunque abbia accesso a questa pagina, pu&ograve; vedere ogni gruppo o utente il cui livello sia sotto il proprio.<br/>
Root pu&ograve; ovviamente vedere ogni utente e gruppo esistente, tranne se stesso.<br/>
Puoi facilmente creare utenti e gruppi e cambiare la maggior parte dei loro dati.</p>

<p>Questa &egrave; l'unica pagina dove sia possibile gestire i gruppi. Potrai cambiare il loro livello ed anche cancellarli.<br/>
Questa &egrave; anche l'unica pagina nella quale potrai effettivamente cancellare un utente dal sistema.<br/>
Stai sempre attento quando cancelli un gruppo, poich&egrave; ogni utente che ne fa parte verr&agrave; cancellato con esso.<br/>
Ora diamo un'occhiata alle configurazioni dei permessi.</p>

<h3>Configurazioni dei Permessi</h3>
<p>Se clicchi su un utente/gruppo o provi a crearne uno vedrai una lista dei controller presenti nella tua applicazione, ed una checkbox per read, write e admin.<br/>
Di fianco a quella checkbox vedrai una piccola icona. Se tieni il mouse fermo su di essa vedrai una breve descrizione dei privilegi che quel permesso garantisce.</p>
<?php echo CHtml::image(Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('userGroups.img')) . "/doc01.png", 'documentation images', array('class' => 'doc-images')); ?>
<p>Ora che hai installato il modulo vedrai che alcune di quelle icone sono rosse, mentre altre blu. Le icone rosse indicano che nessuna descrizione &egrave; stata
fornita e questo &egrave; un qualcosa che vogliamo risolvere immediatamente!</p>
<p>Vai all'interno del file del tuo controller e aggiungi le seguenti righe di codice:</p>

<div class="code"><code> 
	<span class="highlight">static $_permissionControl = array(</span>
		<span class="highlight">'write' =&gt; 'with this permission you can create new whatever',</span>
		<span class="highlight">'label' =&gt; 'Better Label');</span>
		
</code></div>

<p>Come puoi facilmente intuire ogni chiave fa riferimento ad un permesso, ed il suo valore &egrave; la sua descrizione.<br/>
Se ometti un permesso nessuna checkbox apparir&agrave; in sua corrispondenza negli Strumenti di Root, e questo &egrave; un qualcosa che farai molto spesso:
non avrai sempre bisogno di tre tipi di permessi per ogni controller.<br/>
Label indica il nome che vuoi che venga visualizzato negli Strumenti di Root per quel controller.<br/>
Userai questa feature spesso per rendere la gestione dei permessi ancor pi&ugrave; user friendly.</p>

<?php echo CHtml::image(Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('userGroups.img')) . "/doc02.png", 'documentation images', array('class' => 'doc-images')); ?>

<p>Se non vuoi ce il controller appaia proprio all'interno degli Strumenti di Root, cambia il valore di <b>$_permissionControl</b> in un boolean false.</p>

<div class="code"><code> 
	<span class="highlight">static $_permissionControl = false;</span>
	
</code></div>

<p>Come vi abbiamo gi&agrave; detto nei precedenti capitoli, gli utenti ereditano i permessi dei gruppi a cui appartengono.<br/>
Se apri i dettagli di un utente vedrai alcuni checkmark verdi di fianco ai permssi che il suo gruppo possiede.</p>

<?php echo CHtml::image(Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('userGroups.img')) . "/doc03.png", 'documentation images', array('class' => 'doc-images')); ?>

<p>Non avrai bisogno di dare quel permesso di nuovo all'utente, perch&egrave; lo &agrave; sempre fintanto che li ha il suo gruppo, ma puoi aggiungere altri permessi
a quell'utente, poich&egrave; questi verranno combinati con quelli che il suo gruppo gi&agrave; gli concede.</p>

<h3>Home?</h3>
<p>Ogni gruppo pu&ograve; avere una home. La Home &egrave; quella pagina in cui viene reindirizzato l'utente dopo il login.<br/>
Anche gli utenti possono avere una home. Se un utente ha una home dichiarata verr&agrave; reindirizzato a questa piuttosto che a quella del suo gruppo.<br/>
Come puoi vedere nel menu a tendina il controller verr&agrave; mostrato con la label che gli hai assegnato in <b>$_permissionControl</b></p>

<p>Se vuoi assegnare una label ad una Home, ma non hai bisogno di settare permessi per quel controller, potrai inserire solo il valore per label in <b>$_permissionControl</b></p>

<div class="code"><code> 
	<span class="highlight">static $_permissionControl = array('label' =&gt; 'Better Label');</span>
	
</code></div>

<?php echo CHtml::image(Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('userGroups.img')) . "/doc04.png", 'documentation images', array('class' => 'doc-images')); ?>

<p>Come puoi vedere in questo modo nel menu a tendina apparir&agrave; la label da te scelta, ma nessun permesso verr&agrave; mostrato nella tabella precedente.</p>

<h3>Molti modi per creare un utente!</h3>
<p>Qui potrai creare un utente scegliendo sia il nome che la password, o solo il nome, o solo la password, o nessuno dei due.<br/>
Se non crei un utente completo, quell'utente verr&agrave; impostato in uno stato di <b>waiting activation</b> ed un email gli verr&agrave; inviata,
utilizzando l'indirizzo email che hai inserito nella configurazione dell'applicazione.</p>

<?php echo CHtml::image(Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('userGroups.img')) . "/doc05.png", 'documentation images', array('class' => 'doc-images')); ?>

<p>In questa email l'utente trover&agrave; i parametri per attivare il suo account, utilizzando il gi&agrave; menzionato recovery mode.<br/>
Quando attiva il suo account verr&agrave; richiesto all'utente di inserire i dati mancanti e di cambiare il suo nome utente temporaneo se nessuno &egrave;
stato scelto per lui.</p>

<h3>Configurazioni</h3>
<p>userGroups ha molte configurazioni che potrai usare a piacimento. Il sistema si installa con i settaggi pi&ugrave; comuni.<br/>
Non ti spiegheremo cosa fa ogni singola configurazione, poich&egrave; ci sono gi&agrave; descrizioni esaustive per ciascuna di esse negli Strumenti di Root.<br/>
userGroups has several configurations that you can use at will. The system istalled himself with the most common settings.<br/>
Sentiti libero di sperimentare e di scegliere qualunque configurazione tu preferisca per la tua applicazione.</p>