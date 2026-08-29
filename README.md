# Monoverse

**Un framework open source per costruire community con al centro un canale IRC.**

[English version](README.en.md)

Monoverse è un framework open source per community nato attorno a un'idea semplice: **il canale IRC è il centro della community**.

Unisce la comunicazione in tempo reale di IRC a un moderno livello web dedicato alla community, integrando profili, contenuti brevi, discussioni, contenuti editoriali, media, widget e servizi esterni senza sostituire il protocollo aperto che ne costituisce il cuore.

Monoverse può adattarsi a diversi tipi di community, tra cui:

- community online tradizionali;
- community di sviluppatori;
- web radio;
- community crypto.

## IRC al centro

IRC non è una funzionalità di chat aggiunta a Monoverse.

È il punto di partenza.

L'interfaccia web estende la community attorno al canale: può mostrare presenza, attività e contenuti, offrendo allo stesso tempo spazi persistenti per tutto ciò che non appartiene naturalmente a una conversazione in tempo reale.

Anche i nomi **Ping** e **Pong**, utilizzati per i post e le relative risposte, derivano direttamente da IRC.

## Funzionalità della community

Monoverse include attualmente:

- **Ping & Pong** — contenuti brevi, conversazioni e interazioni della community;
- **Chanzine** — un'area editoriale con articoli, categorie e proposte degli utenti sottoposte a revisione editoriale;
- **Profili e presenza** — identità della community collegate all'attività IRC;
- **Media** — supporto per immagini, audio e video;
- **Widget** — blocchi configurabili che permettono a ogni community di costruire la propria identità;
- **Integrazioni per sviluppatori** — inclusi widget dedicati a GitHub;
- **Integrazioni per web radio** — inclusi Icecast e AzuraCast;
- **Integrazioni crypto** — progettate attorno a wallet self-custodial, senza conservare le chiavi private degli utenti;
- **Feed RSS** — per Chanzine e Ping, permettendo ai contenuti di uscire dal sito e tornare anche dentro IRC attraverso bot e altri strumenti;
- **Moderazione e strumenti per la community** — segnalazioni, moderazione, follow, contenuti salvati e notifiche;
- **Interfaccia multilingua** — con un'infrastruttura che permette traduzioni specifiche per ogni community.

## Community diverse, un unico framework

Monoverse non vuole imporre la stessa esperienza a ogni community.

Una community di sviluppatori può concentrarsi su repository, release e pull request.  
Una web radio può mettere al centro lo streaming live, gli ascoltatori e le richieste.  
Una community crypto può integrare funzionalità legate ai wallet.  
Una community tradizionale può semplicemente concentrarsi sulle persone, sulle conversazioni e sui propri contenuti.

Il framework fornisce una base comune; widget e configurazione permettono a ogni installazione di costruire la propria identità.

## Aperto per progettazione

Monoverse nasce attorno all'idea di un **Internet aperto**.

Invece di cercare di possedere ogni parte dell'identità digitale o dell'attività dell'utente, è progettato per integrarsi, quando possibile, con protocolli aperti e servizi esterni: IRC, OAuth, RSS, GitHub, Icecast, AzuraCast e wallet self-custodial.

Le informazioni sensibili non dovrebbero essere gestite da Monoverse quando Monoverse non ha bisogno di gestirle.

## Installazione

### Requisiti

Monoverse richiede:

- PHP 8.2 o successivo;
- PDO e PDO MySQL;
- JSON;
- mbstring;
- cURL;
- GD con supporto JPEG, PNG e WebP;
- FFmpeg;
- un database MySQL o MariaDB;
- la directory `storage/` scrivibile;
- Nginx o un altro web server in grado di instradare le richieste attraverso `index.php`.

### Nginx

La document root deve puntare alla directory principale dell'installazione di Monoverse, dove si trova `index.php`.

Una configurazione minima per il routing è:

```nginx
location / {
	try_files $uri $uri/ /index.php?$query_string;
}
```

Le richieste PHP devono inoltre essere inoltrate alla propria installazione PHP-FPM secondo la configurazione del server.

### Configurazione

Clona il repository nella directory servita dal web server:

```bash
git clone https://github.com/SimosNap/monoverse.git
cd monoverse
```

Assicurati che la directory `storage/` sia scrivibile dal web server.

Crea un database MySQL o MariaDB vuoto e apri quindi l'indirizzo di Monoverse nel browser.

Su una nuova installazione Monoverse avvierà automaticamente la procedura guidata.

L'installer guiderà attraverso:

1. verifica dei requisiti di sistema;
2. selezione della Community Edition;
3. configurazione del database;
4. configurazione OAuth;
5. creazione dell'account amministratore;
6. installazione finale.

Le credenziali locali del database e di OAuth vengono salvate in:

```text
config/database.php
config/oauth.php
```

Questi file sono esclusi da Git e non devono mai essere inseriti nel repository.

Il completamento dell'installazione viene registrato attraverso:

```text
storage/installed.lock
```

anch'esso escluso da Git.

## Licenza

Monoverse è software libero e open source distribuito secondo i termini della **GNU Affero General Public License v3.0 (AGPL-3.0)**.

Consulta [LICENSE](LICENSE) per il testo completo della licenza.

---

**Un canale. Delle persone. Una community.**
