<h3>Posso localizzare questo modulo?</h3>

<p>Certo che puoi!<br/>
userGroups &egrave; gi&agrave; stato localizzato in Italiano, ma se vuoi localizzarlo in un qualsiasi altro linguaggio abbiamo gi&agrave; preparato tutto
per renderti pi&ugrave; semplice il lavoro.</p>

<p>Se yiic funziona correttamente nella tua shell/terminale puoi andare da l&igrave; all'interno della directory di root della tua applicazione e digitare
questo comando:<br/>
<b>yiic message protected/modules/userGroups/messages/local.php</b></p>

<p>Prima di eseguire questo programma ricordati di cambiare il valore di languages all'interno del file <b>userGroups/messages/local.php</b></p>
<p>Questo comando si occuper&agrave; di creare tutti i file necessari per la localizzazione, a parte due cose:</p>

<dl>
<dt>viste di installaizone</dt>
	<dd>situate in <b>/userGroups/views/install</b></dd>
<dt>documentazione</dt>
	<dd>situata in <b>/userGroups/views/admin/documentation/</b></dd>
</dl>

<p>Dovrai tradurre questi file e metterli all'interno di una sotto directory col nome del codice della lingua.<br/>
Puoi facilmente avere un esempio di quello che abbiamo fatto con l'italiano.</p>

<h3>Un ultima cosa! Descrizioni delle Configurazioni e dei Controllers.</h3>
<p>Le descrizioni delle configurazioni si trovano all'interno del database, mentre quelle dei controller utilizzate negli strumenti di tools sono
costanti della classe.<br/>
Per questo motivo non abbiamo potuto utilizzare il metodo Yii::t() su di loro, ma siamo costretti ad utilizzarlo su di una variabile.<br/>
Per localizzare questi contenuti semplicemente copia i file <b>conf_description.php</b> e <b>cont_description.php</b> situati in
<b>/userGroups/messages/it/</b> all'interno della directory generata da yiic, sovrascrivili a quelli da esso creati e traducili.</p>