<h3>Benvenuto a bordo!</h3>
<p>Probabilmente hai appena isntallato questo modulo e ti starai chiedendo "<b><i>e ora cosa devo fare?</i></b>.</p>
<p>userGroups esegue la maggior parte dell'installazione da solo, ma ci sono un paio di cose che dovrai fare per assicurarti che tutto funzioni come deve:<br/>
cambiare i link di login e logout nei tuoi menu.</p>

<p>Se questa &egrave; un applicazione nuova di Yii quei link punteranno verso questi due indirizzi: "<b>/site/login</b>" e "<b>/site/logout</b>".<br/>
Il login path in userGroups &egrave; estremamente semplice:"<b>/userGroups</b>".<br/>
Il percorso di logout invece &egrave;:"<b>/userGroups/user/logout</b>".<br/>
In realt&agrave; avresti potuto lasciare il percorso di logout cos&igrave; com'&egrave;, poich&egrave; l'azione di logout di default semplicemente
chiama il metodo di logout della classe WebUser, ma un giorno potresti dimenticarti che i link di logout nei utoi menu puntano ad un probabilmente
vecchio ed ora inutile controller e potresti cancellarlo per errore.<br/>
Pertanto cambialo ora, cos&igrave; non dovrai mai pi&ugrave; preoccupartene.</p>
<p>E questo &egrave; tutto, ora sei pronto ad andare avanti, pertanto dai un'occhiata al prossimo capitolo per imparare ad utilizzare le nuove ed
avanzate accessRules.</p>

<h3>NB: il tuo vecchio Controller &egrave; andato!</h3>
<p>Ma non molto lontano a dire il vero. Durante il processo di installazione il controller di base di userGroups &egrave; stato copiato all'interno
della directory components della tua applicazione, ed il tuo vecchio controller &egrave; stato rinominato <b>_old_Controller.php</b>.<br/>
Se avevi qualche settaggio particolare all'interno del tuo vecchio controller ricordati che dovrai copiarli all'interno del nuovo fornito da userGroups, altrimenti
qualcosa potrebbe non funzionare come dovrebbe nella tua applicazione.</p>