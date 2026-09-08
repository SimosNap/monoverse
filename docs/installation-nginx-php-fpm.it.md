# Installazione con Nginx e PHP-FPM

Configurazione consigliata per Monoverse su Debian con Nginx e PHP-FPM.

## Requisiti

- PHP 8.2+
- Nginx
- PHP-FPM
- MariaDB/MySQL

Esempio:

```text
Dominio: example.com
Utente Linux: example
Document root: /home/example/public_html
```

## Permessi

I file del sito devono appartenere all'utente dedicato:

```bash
chown -R example:example /home/example/public_html
```

Non utilizzare permessi `777`.

Monoverse deve poter scrivere in:

```text
config/
storage/
```

## Pool PHP-FPM dedicata

Creare:

```text
/etc/php/8.2/fpm/pool.d/example.conf
```

Contenuto:

```ini
[example]

user = example
group = example

listen = /run/php/php8.2-fpm-example.sock

listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 8
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3

chdir = /
```

Verificare la configurazione:

```bash
php-fpm8.2 -t
```

Ricaricare PHP-FPM:

```bash
systemctl reload php8.2-fpm
```

Verificare che il socket esista:

```bash
ls -l /run/php/php8.2-fpm-example.sock
```

## Configurazione Nginx

Creare:

```text
/etc/nginx/sites-available/example.com
```

Configurazione:

```nginx
server {
    listen 80;
    listen [::]:80;

    server_name example.com www.example.com;

    root /home/example/public_html;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm-example.sock;
    }
}
```

Abilitare il sito:

```bash
ln -s /etc/nginx/sites-available/example.com \
      /etc/nginx/sites-enabled/example.com
```

Verificare Nginx:

```bash
nginx -t
```

Ricaricare:

```bash
systemctl reload nginx
```

## HTTPS

Con Certbot:

```bash
certbot --nginx \
  -d example.com \
  -d www.example.com
```

## Database

Esempio MariaDB/MySQL:

```sql
CREATE DATABASE monoverse
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

CREATE USER 'monoverse'@'localhost'
IDENTIFIED BY 'PASSWORD_SICURA';

GRANT ALL PRIVILEGES
ON monoverse.*
TO 'monoverse'@'localhost';

FLUSH PRIVILEGES;
```

## Installer

Aprire:

```text
https://example.com/
```

L'installer creerà:

```text
config/database.php
config/oauth.php
storage/installed.lock
```

## Verifica PHP-FPM

Nginx deve utilizzare il socket della pool dedicata:

```bash
grep -n "fastcgi_pass" \
  /etc/nginx/sites-enabled/example.com
```

Deve risultare:

```text
fastcgi_pass unix:/run/php/php8.2-fpm-example.sock;
```

Non:

```text
fastcgi_pass unix:/run/php/php8.2-fpm.sock;
```

Se viene utilizzata la pool generica, PHP potrebbe essere eseguito come `www-data` e non avere i permessi necessari per scrivere nei file dell'installazione.

## Verifica permessi

```bash
ls -ld \
  /home/example/public_html/config \
  /home/example/public_html/storage
```

Con la pool dedicata, entrambe le directory dovrebbero appartenere all'utente del sito:

```text
example example
```

## Sessioni PHP

Non è necessaria una directory sessioni dedicata.

Se il sito viene inizialmente eseguito con la pool PHP-FPM generica e successivamente viene spostato sulla pool dedicata, una vecchia sessione appartenente a `www-data` può causare:

```text
session_start(): Permission denied
```

In questo caso eliminare la vecchia sessione PHP oppure iniziare una nuova sessione browser.

## Log installer

Se l'installazione finale fallisce:

```bash
cat storage/install-error.log
```

Verificare inoltre:

```bash
php-fpm8.2 -t
nginx -t
ls -l /run/php/php8.2-fpm-example.sock
```
