<h3>Cambier&agrave; qualcosa nella struttura di accessRules?</h3>
<p>Nei tuoi vecchi controllers hai un metodo accessRules che utilizzi per imporre restrizioni all'accesso delle tue actions.</p>

<div class="code"><code>
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=&gt;array('index','view'),
				'users'=&gt;array('*'),
			),
			array('allow', // allow authenticated user to perform
				       // 'create' and 'update' actions
				'actions'=&gt;array('create','update'),
				'users'=&gt;array('@'),
			),
			array('allow', // allow admin user to perform 'admin'
				       // and 'delete' actions
				'actions'=&gt;array('admin','delete'),
				'users'=&gt;array('admin'),
			),
			array('deny',  // deny all users
				'users'=&gt;array('*'),
			),
		);
	}

</code></div>

<p>Con il nuovo filtro avrai nuovi set di regole che potrai usare  da soli o combinandoli sia con nuove che vecchie regole, senza dover imparare una nuova
struttura dati per scriverli.</p>
<p>Ora analizzeremo ogni singolo nuovo tipo di regola che potrai applicare e ti mostreremo un esempio per ciascuno di essi.</p>
<p>Tieni a mente che ciascuno dei risultati conseguiti dalle nuove regole potrebbe essere ottenuto usando la regola expression gi&agrave; fornita.<br/>
Tuttavia le regole che noi forniamo ti rende pi&ugrave; semplice scriverle e sopratutto saranno molto pi&ugrave; semplici da leggere.</p>

<h3>Ajax!</h3>
<p>Con questa regola potrai costringere gli utenti ad accedere ad una specifica action con o senza ajax.</p>

<div class="code"><code>
	<b>// allow all users to perform 'index' and 'view' actions
	// just when loading the page via ajax</b>
	array('allow',
		'actions'=&gt;array('index','view'),
		'users'=&gt;array('*'),
		<span class="highlight">'ajax'=&gt;true,</span>
	),

</code></div>
<p>Se vorrai costringere un utente invece a caricare una pagina senza ajax allora dovrai assegnarli come valore un <b>boolean false</b></p>

<h3>Grupppi, ovviamente!</h3>
<p>Questa regola ti permette di scegliere quali gruppi di utenti ha accesso ad una specifica action.<br/>
Questa regola si comporta esattamente come quella per gli utenti che gi&agrave; conosci:</p>

<div class="code"><code>
	<b>// permetti agli utenti che appartengono ai gruppi admin o core
	// od al gruppo con id 5 di eseguire 'update' e 'admin' actions</b>
	array('allow',
		'actions'=&gt;array('update','admin'),
		<span class="highlight">'groups'=&gt;array('admin', 'core', 5),</span>
	),

</code></div>
<p>Puoi anche utilizzare il <b>*</b> per indicare che garantisci accesso ad ogni gruppo.<br/>
Se stai usando questa regola i guest non avranno ovviamente accesso alla pagina.</p>

<h3>Livelli</h3>
<p>Ogni gruppo ha un livello assegnato, e cos&igrave; ogni utente eredita il livello del gruppo a cui appartiene.<br/>
Sarai in grado di stabilire il livello di ogni gruppo, ma di questo parleremo ulteriormente nella <b>sezione Strumenti di Root della documentazione</b></p>

<p>Questa &egrave; una delle regole pi&ugrave; che puoi applicare. Potrai dire al sistema che solo gli utenti con un livello superiore a 4
possono eseguire una specifica azione, o potrai decidere solo che quelli con un livello uguale a 5 o superiore a 50 possono vedere
la pagina, o se proprio vuoi potrai decidere che solo quelli che fanno rispettano tutte le regole sopra elencate possono accedere
a quel contenuto della tua applicazione.<br/>
Vediamo qualche esempio per rendere pi&ugrave; semplice il discorso:</p>

<div class="code"><code>
	<b>// permetti agli utenti con un livello inferiore a  10, o superiore a 20
	// o superiore o uguale a 35 di eseguire un update action</b>
	array('allow',
		'actions'=&gt;array('update'),
		<span class="highlight">'level'=&gt;array('&lt;10', '20' ,'&gt;=35'),</span>
	),

</code></div>
<div class="code"><code>
	<b>// permetti a tutti gli utenti con un livello inferiore a 40
	// E superiore a 35 di eseguire un update action</b>
	array('allow',
		'actions'=&gt;array('update'),
		<span class="highlight">'level'=&gt;array('&lt;40', '&gt;35', 'strict' =&gt; true),</span>
	),

</code></div>
<p>Penso che entrambi gli esempi si spieghino da soli. Puoi utilizzare qualunque operatore di paragone, ma ricordati di <b>non usare mai =</b> poich&egrave;
restituir&agrave; sempre true.</p>
<p>Se stai usando questa regola i guest non avranno accesso alla pagine, poich&egrave; non hanno un livello.</p>

<h3>P-cosa? PBAC! Permission Based Access Control.</h3>
<p>userGroups ti permette di gestire gruppi ed utenti, ma anche i loro permessi.<br/>
Per ogni controller potrai assegnare ad ogni utente e/o gruppo permessi di read, write ed admin.<br/>
Un utente in automatico eredita i permessi del suo gruppo, ma se vuoi potrai ulteriormente specificare ulteriori permessi per lui.<br/>
Parleremo ulteriormente dell'assegnazione dei permessi nel capito degli <b>Strumenti di Root</b> della documentazione, quindi se vuio pi&ugrave; informazioni
dai un'occhiata l&igrave;, ma al momento ci&ograve; che ti abbiamo detto &egrave; sufficiente a capire a fondo come usare questo tipo di regola.</p>
<p>Con PBAC sarai in grado di dare accesso ad una pagina ai soli utenti con permessi di write su quel controller, o permessi di admin su di un altro.</p>
<p>Rendiamo la cosa pi&ugrave; semplice. Hai nella tua applicazione due controller, uno che gestisce compagnie e l'altro gestisce dipendenti.<br/>
Solo gli utenti con permessi di admin su companies possono cancellarle, cos&igrave; scriverai questa regola:</p>

<div class="code"><code>
	<b>// permetti agli utenti con permessi di admin di cancellare compagnie</b>
	array('allow',
		'actions'=&gt;array('delete'),
		<span class="highlight">'pbac'=&gt;array('admin'),</span>
	),

</code></div>

<p>Facile, vero? Nel controller employee vuoi garantire accesso all'update action sia agli utenti con permessi di admin sul controller
employee che a quelli con permessi di admin sul controller companies.</p>

<div class="code"><code>
	<b>// permetti agli utenti con permessi di admin o su questo
	// controller o sul controller companies di accedere all'action update.</b>
	array('allow',
		'actions'=&gt;array('update'),
		<span class="highlight">'pbac'=&gt;array('admin', 'companies.admin'),</span>
	),

</code></div>

<p>Nella nostra applicazione abbiamo installato un modulo che gestisce le certificazioni degli impiegati, chiamato <b>certEmployee</b>.<br/>
Vogliamo che gli utenti con permessi di admin nel controller user di quel modulo abbiano accesso alla nostra admin action
sul controller employee, ma non vogliamo che possano accedere a quella pagina usando ajax.<br/>
Pertanto scriveremo la seguente regola nel nostro employee controller:</p>

<div class="code"><code>
	<b>// permetti agli utenti con permessi di admin nel controller user
	// che appartiene al modulo certEmployee di eseguire un update action,
	// ma solo se accede alla pagina senza usare ajax</b>
	array('allow',
		'actions'=&gt;array('update'),
		<span class="highlight">'pbac'=&gt;array('certEmployee.user.admin'),</span>
		<span class="highlight">'ajax'=&gt;false,</span>
	),

</code></div>

<p>Come puoi vedere PBAC usa una dot notation. Se non usi alcun punto PBAC controller&agrave; il permesso richiesto su quel controller, se utilizzi
un punto la parola prima di esso sar&agrave; il nome del controller, se utilizzi invece due punti la prima parola sar&agrave; il nome del modulo.<br/>
I Controller nell'applicazione di base sono considerati contenuti in un modulo chiamato Basic. Quindi se vuoi controllare i permessi di admin
nel site controller che si trova di default con una nuova applicazione di Yii dovrai scrivere "<b>Basic.site.admin</b>"</p>
<p>Ricordati che PBAC &egrave; <b>case sensitive</b>. Se hai dubbi sul come scrivere il nome di un controller controlal il suo web path,
fai un print del valore di <b>Yii::app()-&gt;getController()-&gt;id</b>, o guarda il suo nome nella pagina degli <b>Strumenti di Root</b>.</p>

<p>Se utilizzi PBAC nessun guest avr&agrave; accesso a quella pagina, poich&egrave; i guest non hanno alcun permesso.</p>

<h3>Come posso controllare le nuove informazioni degli utenti all'interno delle mie pagine?</h3>
<p>Certe volte vorrai garantire accesso ad una pagina a tutti gli utenti, ma solo alcuni di loro dovranno poter vedere un certo link al suo interno.<br/>
Questa &egrave; la sintassi che dovrai usare per controllare ciascuna delle nuove informazioni che l'utente sta tenendo memorizzate in sessione:</p>
<dl>
<dt>ID Utente</dt>
	<dd>Yii::app()-&gt;user-&gt;id</dd>
<dt>Nome Utente</dt>
	<dd>Yii::app()-&gt;user-&gt;name</dd>
<dt>ID Gruppo Utente</dt>
	<dd>Yii::app()-&gt;user-&gt;group</dd>
<dt>Nome Gruppo Utente</dt>
	<dd>Yii::app()-&gt;user-&gt;groupName</dd>
<dt>Livello del Gruppo dell'Utente</dt>
	<dd>Yii::app()-&gt;user-&gt;level</dd>
<dt>Email dell'Utente</dt>
	<dd>Yii::app()-&gt;user-&gt;email</dd>
<dt>Home dell'Utente</dt>
	<dd>Yii::app()-&gt;user-&gt;home</dd>
	<dd>Home? Non preoccuparti, parleremo delle Home nel capitolo degli Strumenti di Root</dd>
</dl>

<h3>E PBAC? Posso controllare i permessi?</h3>
<p>Certo che puoi, ma controllare i permessi sarebbe potuta essere un operazione troppo elaborata da performare, pertanto ti abbiamo
fornito un metodo speciale da invocare:</p>
<p><b>Yii::app()-&gt;user-&gt;pbac();</b></p>
<p>Il metodo user::pbac() pu&ograve; accettare fino a 3 argomenti, e solo il primo &egrave; obbligatorio.</p>
<dl>
<dt>Primo argomento: permission (mixed)</dt>
	<dd>
		Questo &egrave; il permesso che devi controllare. Puoi utilizzare sia una stringa che un array. Potrai utilizzare inoltre la stessa
		dot notation che hai utilizzato nelle tue regole.<br/>
		Se devi controllare solo un permesso puoi semplicemente inviare una stringa, ma se devi controllarne pi&ugrave; di uno allora
		dovrai inviare un array, nel quale ogni valore &egrave; un permesso da controllare (esattamente come nella regola).<br/>
		Ricordati che quando questo metodo viene utilizzato, se non specifichi il controller od il modulo il metodo utilizzer&agrave;
		quelli della pagina che l'utente sta al momento visionando.
	</dd>
<dt>Secondo argomento: configuration (string)</dt>
	<dd>
		userGroups ha diverse configurazioni, se vuoi controllare insieme il valore di una configurazione e di un permesso per decidere
		se l'utente pu&ograve; vedere una porzione della tua pagina puoi inserire il nome della configurazione all'interno di questo argomento.
	</dd>
<dt>Terzo argomento: operator ('AND' or 'OR')</dt>
	<dd>
		A volte vorrai garantire accesso ad un link se un utente ha un certo permesso O una certa configurazione &egrave; attiva.<br/>
		Di default il metodo user::pbac() presuppone che tu stia utilizzando un operatore AND, quindi se vuoi puoi passare la stringa OR
		a questo argomento per cambiare il tipo di paragone.
	</dd>
</dl>

<h3>C'&egrave; un nuovo utente in Citt&agrave;!</h3>
<p>Sai bene che in Yii puoi indicare qualunque utente utilizzando un <b>*</b>, i guest utilizzando un <b>?</b> e gli utenti registrati
utilizzando una <b>@</b>.</p>
<p>Abbiamo aggiunto una nuova tipologia di utente con questo modulo: <b>#</b><br/>
<b>#</b> identifica gli utenti che si trovano in recovery mode. Gli utenti sono in recovery mode quando stanno attivando il loro account,
o resettando la loro password. Gli utenti in recovery mode possono accedere solo alle pagine a cui possono accedere i guest o a quelle
che sono esplicitamente pensate per loro.<br/>
Nessun altro utente, neppure i guest, possono accedere alle pagine riservate agli utenti in recovery mode.</p>

<h3>Chi &egrave; che comanda? ROOT!</h3>
<p>Quando hai installato questo modulo hai creato al volo un nuovo utente, quello con il quale sei ora loggato. Quell'utente &egrave; noto come Root.<br/>
Root non pu&ograve; essere bannato, i suoi permessi non possono essere cambiati, nessuno tranne se stesso pu&ograve; cambiare il suo profilo ed
ha in automatico accesso ad ogni singola pagina nella tua applicazione, tranne quelle riservate agli utenti in recovery mode.</p>
<p>Non dovrai mai specificatamente dire ad una regola di dare accesso a ROOT, poich&egrave; ROOT far&agrave; sempre match con qualunque condizione
tu scriva.</p>