# Esercizio GLS Symfony

## Introduzione
Partendo dalla traccia riceevuta via mail ho sviluppato gli esercizi richiesti utilizzando il framework [Symfony](https://symfony.com)


### Scopo e motivazioni

Dal punto di vista tecnologico erano possibili più soluzioni PHP. Sono partito quindi dal valutare i 2 framework PHP più utilizzati, Laravel e Symfony.
Valutando le due soluzioni, entrambe non propriamente nella mia "confort zone" in quanto Magentista, la scelta è ricaduta su Symfony per 2 principali motivi:
1. A livello di impostazione di cartelle e file, Symfony mi permetteva di rispettare abbastanza fedelmente la struttura richiesta dalla traccia ( nomi file e struttra cartelle, richieste soprattutto nell'esercizio 1)
2. Tra i tool utilizzati lato GLS ho visto prestashop, ammetto di averlo visto poco e di non usarlo da almeno 2 anni, ma ricordo che già allora aveva iniziato una transizione del suo core verso il framework Symfony. Questo mi ha fatto pensare che la scelta di Symfony potesse essere più vicina a quello che GLS utilizza.

---- 
## Esercizi

### traccia esercizio 1

Immagina di dover sviluppare un sistema per gestire un catalogo di libri in una libreria
online. Ogni libro nel catalogo ha le seguenti informazioni:
1. Titolo del libro
2. Autore
3. Anno di pubblicazione
4. Genere
5. Prezzo

Devi implementare le seguenti funzionalità:
1. Classe Libro:
   - Crea una classe Libro che rappresenti un libro con le proprietà descritte sopra.
   - Implementa i metodi necessari per ottenere e impostare ogni proprietà.
   - Implementa un metodo calcolaSconto($percentualeSconto) che calcoli il prezzo
   scontato del libro dato uno sconto percentuale.
2. Classe CatalogoLibri:
   - Crea una classe CatalogoLibri che gestisca un elenco di libri.
   - Implementa un metodo aggiungiLibro($libro) che aggiunga un libro al catalogo.
   - Implementa un metodo rimuoviLibro($titolo) che rimuova un libro dal catalogo
   dato il titolo.
   - Implementa un metodo trovaLibroPerAutore($autore) che restituisca un array di
   libri scritti da un determinato autore. 
   - Implementa un metodo trovaLibriPerGenere($genere) che restituisca un array
   di libri di un determinato genere.
3. Script di Esempio:
   - Crea uno script che istanzi una classe CatalogoLibri, aggiunga alcuni libri al
   catalogo e mostri l&#39;elenco completo dei libri presenti.
   - Applica uno sconto del 10% a tutti i libri nel catalogo e visualizza l&#39;elenco
   aggiornato con i prezzi scontati.
   -Trova e visualizza tutti i libri scritti da un autore specifico e quelli di un certo genere.

### Impostazione, Classi e codice

Per entrambi gli esercizi è stata ovviamente sfruttata la struttura di Symfony, Come prima cosa tramite i comandi di CLI di Symfony ho creato una nuova entità "[Libro](src/Entity/Libro.php)" al cui interno ho definito gli attributi richiesti dalla traccia.
La chiave dell'entità è un id autoincrementale, mentre tutti gli altri attributi, prezzo escluso che è in formato float, sono stati definiti come stringa.
A questa classe si aggiunge la classe "[CatalogoLibri](src/GestioneLibri/Utils/CatalogoLibri.php)" utilizzata per gestire l'elenco dei libri. Per questo scopo starebbe stato meglio utilizzare un mix di classei tra il repository e questa classe. Per semplicità e tempistica (ammetto di non aver avuto molto tempo per legger l'intera documentazione) ho preferito utilizzare solo la classe e attenermi alla traccia.
Nella classe CatalogoLibri ho implementato seguenti i metodi:
* getLibri() -> restituisce tutti i libri presenti nel catalogo
* creaLibro($titolo, $autore,$genere,$anno,$prezzo) -> Crea una nuova istanza di Libro e la aggiunge al catalogo
* aggiungiLibro($libro) -> è la funzione che effettua effettvamente la persistenza del libro nel database (fatta per rispettare la traccia)
* cancellaLibroDaID($id) -> cancella un libro dal catalogo dato il suo id
* trovaLibriDaKeyword($keyword) -> questa funzione è un'astrazione delle funzioni richieste, e presenti nella classe, trovaLibroPerAutore e trovaLibriPerGenere. Questa funzione prende la parola da cercare e una keyword che definisce su quale parametro\colonna cercare la parola. La funzione restituisce un array di libri che rispettano la ricerca.
* trovaLibroPerAutore($autore) -> fatta per traccia, si appoggia alla funzione trovaLibriDaKeyword
* trovaLibriPerGenere($genere) -> fatta per traccia, si appoggia alla funzione trovaLibriDaKeyword
* rimuoviLibro($titolo) -> fatta per traccia, questa funzione è implementata in modo particolare in quanto ho preferito non tenere il titolo come chiave. Di fatto, guardando gli attributi salta subito all'occhio che un libro, se ha anche data di pubblicazione, implica che ci siano più libri con lo stesso titolo ma con anno di pubblicazione diverse. Per questo motivo la funzione prende tutti i libri con lo stesso titolo e elimina "ogni edizione".
* checkifBookExists($titolo, $autore, $genere, $anno, $prezzo) -> funzione di controllo per evitare di inserire libri duplicati
* printListaLibri($libri) -> questa funzione mi permette di "stampare" in modo quasi umano i dati da stampare in pagina. Anche in questo caso è corretto fare un appunto, sarebbe stato meglio usare per la stampa a video i twig di Symfony, ma per semplicità e tempistica, in quanto ammetto di non ricordare la sintassi, ho preferito fare in questo modo.

Al fine di poter interagire e visualizzare i dati, ho creato un controller esclusivamente per la visualizzazione e integrazione con l'entity Libro, il file è il "[LibriController](src/Controller/LibriController.php)". 
all'interno di questo file ho dichiarato alcune action con scopi diversi:
* /libri/lista -> visualizza la normale lista dei libri
* /libri/genere/{genere} -> visualizza la lista dei libri filtrata per genere
* /libri/autore/{autore} -> visualizza la lista dei libri filtrata per autore
* /libri/sconto/{percentuale} -> visualizza la lista dei libri applicando anche un dato aggiuntivo, lo sconto percentuale
* /libri/rimuovi/{id} -> rimuove un libro dal catalogo dato l'id ed effettua un redirect alla lista dei libri

Ultimo file relativo a questo esercizio è il file [InserisciLibriCommand](src/Command/InserisciLibriCommand.php). Questo è un comando di Symfony che mi permette di inserire libri nel catalogo tramite CLI. Questo comando è stato fatto per rispettare la traccia e per avere un modo di inserire libri in modo rapido.
lanciando infatti il comando " php bin/console inserisciLibri" verranno inseriti a database 25 libri di default. Questo comando usa la funzione checkifBookExists della classe [CatalogoLibri](src/GestioneLibri/Utils/CatalogoLibri.php) per evitare di inserire libri duplicati.

### Osservazioni e note
Come detto precedentemente, per motivi di tempo e di conoscenza, ho preferito non utilizzare i twig di Symfony per la stampa a video dei dati. Non ho inoltre utilizzato, ne in questo ne nel prossimo esercizio, i repositoriy ne le collection. questo per rispettare la traccia e per limiti di tempo.
come ultimo punto, ho dichiarato come traccia la funzione calcolaSconto all'interno dell'entità Libro come richiesto, ma onestamente, avrei preferito dichiararla nella classe CatalogoLibri in quanto tendenzialmente le entità dovrebbero essere il più possibile "pulite" e senza logica di business.

### traccia esercizio 2

ESERCIZIO 2
Immagina di dover implementare un sistema che restituisce i dati dell’andamento dei
prezzi dell’olio.
L’applicativo deve avere un metodo JSON-RPC chiamato GetOilPriceTrend che deve
accettare in ingresso questi due parametri:
 - startDateISO8601, the starting date of the period to retrieve data from, in ISO 8601 format (e.g. 2000-12-25)
 - endDateISO8601, the ending date of the period to retrieve data from, in ISO 8601 format (e.g. 2001-01-20)

La response deve essere una lista di prezzi dell’olio per ogni giorno incluso nel range
di date passate come parametri di ingresso.
Esempio di payload in input
{
&quot;id&quot;: 1,
&quot;jsonrpc&quot;: &quot;2.0&quot;,
&quot;method&quot;: &quot;GetOilPriceTrend&quot;,
&quot;params&quot;: {
&quot;startDateISO8601&quot;: &quot;2020-01-01&quot;,
&quot;endDateISO8601&quot;: &quot;2020-01-05&quot;
}
}
Esempio di payload in output
{
&quot;jsonrpc&quot;: &quot;2.0&quot;,
&quot;id&quot;: 1,
&quot;result&quot;: {
&quot;prices&quot;: [
{
&quot;dateISO8601&quot;: &quot;2020-01-01&quot;,
&quot;price&quot;: 12.3
},
{
&quot;dateISO8601&quot;: &quot;2020-01-02&quot;,
&quot;price&quot;: 13.4
},
{
&quot;dateISO8601&quot;: &quot;2020-01-03&quot;,

&quot;price&quot;: 14.5
},
{
&quot;dateISO8601&quot;: &quot;2020-01-04&quot;,
&quot;price&quot;: 16.7
},
{
&quot;dateISO8601&quot;: &quot;2020-01-05&quot;,
&quot;price&quot;: 18.9
}
]}
}
Troverai i dati storici dei prezzi dell’olio al seguente
url:https://pkgstore.datahub.io/core/oil-prices/brent-day_json/data/b26a150f66f90717c7e533ecc468baef/brent-day_json.json

### Impostazione, Classi e codice
Anche in questo caso usiamo la stessa struttura vista precedentemente. Partendo quindi dal creare una nuova entità chiamata [PrezziOlio](src/Entity/PrezziOlio.php) con i seguenti attributi:
* id -> chiave primaria,autoincrement
* data -> data sotto forma di stringa
* prezzo -> prezzo dell'olio in formato float

all'interno di questa classe sono presenti solo i getter e i setter.

Per la gestione dei dati ho creato una classe chiamata [OilHelper](src/PrezzoOlio/Utils/OilHelper.php).
Premessa su questa classe, fin da subito ho valutato 2 possibili alternative, pescare le informazioni direttamente dal file json, creando una cache o un database, oppure creare un database e popolarlo con i dati del file json.
Ho scelto di utilizzare il database anche in vista del fatto che abbiamo la necessità di interrogare ed effettuare delle logiche su questi dati, nel caso avessimo solo bisogno di mostrare il json in pagina avrei valutato la possibilità di tenere solo il file e la cache.
All'interno di questa classe ho dichiarato questi metodi:
* getStoricoOlio -> questa è la funzione che prende il json e crea la cache del file per poterlo usare direttamente ( ho una action nel controller che lo usa, come extra traccia)
* getJsonFileInfo -> questa è la funzione che effettivamente legge il file dall'url e restituisce un array di oggetti PrezziOlio
* getPrezziOlioList -> questa funzione restituisce tutti gli item dentro la tabella legata ai prezzi olio. Nel caso in cui non trovasse alcun elemento, lancia la funzione riempiTabellaPrezziOlio
* riempiTabellaPrezziOlio -> questa funzione prende il json e lo inserisce nella tabella PrezziOlio. La funzione va in update sui dati già presenti e in insert su quelli non presenti. L'import viene fatto in batch di 1000 elementi per volta.
* creaNuovoPrezzoOlio -> questa funzione inserisce un nuovo item nella tabella
* getOilPrices -> questa funzione restituisce un array di oggetti PrezziOlio compresi tra le date di inizio e fine passate come parametro
* printJson -> questa funzione stampa a video il contenuto del json (usata come extra traccia)
* printData -> questa funzione stampa a video la lista di prezzi passati (la lista è di item provenienti dal db)

Il controller, che è la parte più importante, è [OilController](src/Controller/OilController.php). Questo contiene le seguenti action:
* /oil/storico -> questa action stampa a video tutte le informazioni dei prezzi presenti a db
* /oil/storico-json -> questa action stampa a video il contenuto del json (usata come extra traccia)
* /oil/rpc -> questa è la action in POST che si occupa di gestire la richiesta json-rpc. La funzione prende i dati in ingresso, li valida e restituisce un json con i prezzi dell'olio compresi tra le date passate nel body della richiesta.

Anche in questo caso ho pensato di creare un comando symfony per poter popolare il db con i dati del json. Il comando è [ImportOilPriceCommand](src/Command/ImportOilPriceCommand.php) e si può lanciare con il comando `php bin/console importOilPrice`.
Di per se il comando non fa nulla di nuovo che già non venga fatto dalla funzione riempiTabellaPrezziOlio. La differenza è che prende il file json senza passare per la cache, e fa una insert in update. 
Lo scopo di questo comando è quello di poterlo eseguire tramite un cron sulla macchina, ad esempio "* * * * * php bin/console importOilPrice".
Così facendo si può schedulare ogni tot l'update delle informazioni del db.


### Impostazione, Classi e codice
Anche per questo caso non ho utilizzato twig, repository o collection. Ho utilizzato solo le classi entity, controller,command e una classe di supporto.
All'interno del db ho preferito tenere le date in formato stringa per semplicità, e anche per poterle stampare a schermo senza dover fare conversioni.

