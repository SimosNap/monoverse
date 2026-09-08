# Installation with Nginx and PHP-FPM

Recommended configuration for Monoverse on Debian with Nginx and PHP-FPM.

## Requirements

- PHP 8.2+
- Nginx
- PHP-FPM
- MariaDB/MySQL

Example:

```text
Domain: example.com
Linux user: example
Document root: /home/example/public_html
```

## Permissions

The website files should belong to the dedicated system user:

```bash
chown -R example:example /home/example/public_html
```

Do not use `777` permissions.

Monoverse must be able to write to:

```text
config/
storage/
```

## Dedicated PHP-FPM pool

Create:

```text
/etc/php/8.2/fpm/pool.d/example.conf
```

Contents:

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

Check the configuration:

```bash
php-fpm8.2 -t
```

Reload PHP-FPM:

```bash
systemctl reload php8.2-fpm
```

Verify that the socket exists:

```bash
ls -l /run/php/php8.2-fpm-example.sock
```

## Nginx configuration

Create:

```text
/etc/nginx/sites-available/example.com
```

Configuration:

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

Enable the site:

```bash
ln -s /etc/nginx/sites-available/example.com \
      /etc/nginx/sites-enabled/example.com
```

Check Nginx:

```bash
nginx -t
```

Reload Nginx:

```bash
systemctl reload nginx
```

## HTTPS

With Certbot:

```bash
certbot --nginx \
  -d example.com \
  -d www.example.com
```

## Database

MariaDB/MySQL example:

```sql
CREATE DATABASE monoverse
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

CREATE USER 'monoverse'@'localhost'
IDENTIFIED BY 'SECURE_PASSWORD';

GRANT ALL PRIVILEGES
ON monoverse.*
TO 'monoverse'@'localhost';

FLUSH PRIVILEGES;
```

## Installer

Open:

```text
https://example.com/
```

The installer will create:

```text
config/database.php
config/oauth.php
storage/installed.lock
```

## PHP-FPM verification

Nginx must use the dedicated pool socket:

```bash
grep -n "fastcgi_pass" \
  /etc/nginx/sites-enabled/example.com
```

It should return:

```text
fastcgi_pass unix:/run/php/php8.2-fpm-example.sock;
```

Not:

```text
fastcgi_pass unix:/run/php/php8.2-fpm.sock;
```

If the generic pool is used, PHP may run as `www-data` and may not have permission to write the installation files.

## Permission verification

```bash
ls -ld \
  /home/example/public_html/config \
  /home/example/public_html/storage
```

With the dedicated pool, both directories should belong to the website user:

```text
example example
```

## PHP sessions

A dedicated session directory is not required.

If the site is initially executed through the generic PHP-FPM pool and later switched to the dedicated pool, an old session owned by `www-data` may cause:

```text
session_start(): Permission denied
```

In that case, remove the old PHP session or start a new browser session.

## Installer log

If the final installation step fails:

```bash
cat storage/install-error.log
```

Also verify:

```bash
php-fpm8.2 -t
nginx -t
ls -l /run/php/php8.2-fpm-example.sock
```
