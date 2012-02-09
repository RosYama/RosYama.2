<p>userGroups ti fornisce un estensione della classe di core AccessControlFilter per controllare i permessi degli utenti quando cercano di accedere ad una
specifica azione all'interno di un controller.</p>
<p>Utilizzando <b>userGroupsAccessControl</b> non perderai nessuna delle feature di base del vecchio filter, ma ne guadagnerai molte altre.</p>

<h3>Cosa devo cambiare nei miei controller?</h3>
<p>Se non sei interessato ad utilizzare <b>UserGroupsAccessControl</b> non dovrai cambiare assolutamente niente, il nostro nuovo filtro non fa alcun override n&egrave;
disabilita niente, ma perch&egrave; installare questo modulo se non intendi usare il nuovo filtro?</p>

<p>Diamo ora un'occhiata all'aspetto che di solito ha uno dei tuoi controller. Avevi una cosa del genere:</p>

<div class="code"><code>
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}
	
</code></div>

<p>Ora per utilizzare il nuovo filtro dovrai cambiare il tuo codice per far s&igrave; che assomigli a questo:</p>

<div class="code"><code>
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			<span class="highlight">'userGroupsAccessControl',</span> // perform access control for CRUD operations
		);
	}
	
</code></div>

<h3>E le mie vecchie accessRules? Cosa gli accadr&agrave;?</h3>
<p>La risposta &egrave; semplice: assolutamente nulla!<br/>
<b>UserGroupsAcessControl</b> cerca le sue regole esattamente dove le cercava accessControlFilter, pertanto le tue vecchie regole stanno ancora funzionando 
come prima</p>
 
<p>Ora vai al prossimo capitolo e controllare quali nuovi tipi di accessRules userGroups supporta.</p>