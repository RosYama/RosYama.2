<p>dalla versione 1.6 puoi personalizzare i messaggi email inviati dal modulo.</p>

<h3>Prepara la classe del tuo messaggio</h3>
<p>Tutto quello che devi fare per utilizzare questa nuova feature &egrave; creare la classe del tuo messaggio.<br/>
Come fare? Semplicemente implementa l'interfaccia UGMailMessage (situata dentro il file UGMail.php all'interno della directory components del modulo)
e questo &egrave; tutto.<br/>
I metodi che dovrai implementare si sipegano da soli e se hai qualche dubbio riguardo cosa fare con ciascuno di essi puoi dare un'occhiata alle
classe gi&agrave; implementate.</p>

<h3>E se volessi solo fare dei piccoli cambiamenti ai messaggi di default?</h3>
<p>Ancora pi&ugrave; semplice: semplicemente estendi la classe del messaggio che vuoi cambiare.</p>

<h3>Come posso fare che userGroups si accorga della mia nuova classe?</h3>
<p>Esattamente come per le Profile Extension tutto quello che devi fare &egrave; modificare la configurazione del modulo all'interno del
file di configurazione dell'applicazione:</p>

<div class="code"><code>
	'userGroups'=&gt;array(
		'accessCode'=&gt;'your access code, by now useless',
		<span class="highlight">'mailMessages' =&gt; array('invitation' =&gt; 'YourClassName'),</span>
	)

</code></div>

<p>Presta semplicemente attenzione alle seguenti regole:</p>

<ul>
	<li>userGroups deve essere in grado di creare un istanza della tua clase, pertanto se posizione la classe all'interno di una
	directory che non &egrave; di base importata da Yii, fallo!
	</li>
	<li>se posizioni il file della tua classe all'interno di una directory importata da Yii ricordati di chiamare il file della classe
	esattamente con lo stesso nome della classe.</li>
	<li>in questa versione di userGroups non stiamo supportando la dot notation per il nome della classe quando utilizzi messaggi custom.</li>
</ul>

<h3>Nell'esempio sopra all'interno dell'array si usa la chiave 'invitation'. Quali sono le chiavi che devo usare?</h3>
<p>Semplicissimo:</p>

<dl>
	<dt>invitation</dt>
		<dd>il messaggio che vuoi che venga spedito quando viene inviata una mail di invito</dd>
	<dt>pass_reset</dt>
		<dd>il messaggio che verr&agrave; inviato quando un utente richieder&agrave; il reset della password</dd>
	<dt>activation</dt>
		<dd>il messaggio che verr&agrave; ad un utente con le istruzioni per attivare il suo account</dd>
</dl>


<h3>Tutti quei metodi hanno una variabile $data nell'argomento. Cos'&egrave;?</h3>
<p>$data &egrave; semplicemente un array, pronto per essere utilizzato da Yii::t().<br/>
Tuttavia, se non ti interessa la localizzazione puoi comunque utilizzare i dati all'interno di questo array.<br/>
Qui vi &egrave; una lista delle informazioni contenute all'interno di quell'array:</p>

<dl>
	<dt>{email}</dt>
		<dd>contiene l'indirizzo email dell'utente</dd>
	<dt>{username}</dt>
		<dd>contiene il nome dell'utente</dd>
	<dt>{activation_code}</dt>
		<dd>contien l'activation code dell'utente. questo valore pu&ograve; essere nullo</dd>
	<dt>{link}</dt>
		<dd>il link alla pagina di attivazione utente della tua applicazione</dd>
	<dt>{full_link}</dt>
		<dd>esattamente come quello sopra, ma gi&agrave; contiene il nome utente ed il codice di attivazione come parametri get</dd>
	<dt>{website}</dt>
		<dd>il nome della tua applicazione</dd>
	<dt>{temporary_username}</dt>
		<dd>un booleano che indica se lo username &egrave; temporaneo o meno</dd>
</dl>

<h3>E se io volessi solo cambiare il testo delle mail?</h3>
<p>Dalla versione 1.8 il corpo dei messaggi email si trova all'interno di alcune views.<br/>
Queste view si trovano nella cartella views della tua applicazione all'interno della directory ugmail.</p>
<p>
	Il motivo per cui questi file non si trovano nella cartella di userGroups &egrave; per evitare che tue eventuali modifiche
	vengano sovrascritte quando aggiorni il modulo.
</p>