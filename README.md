# Monoverse

**An open-source framework for building communities around IRC.**

Monoverse is an open-source community framework designed around a simple idea: **the IRC channel is the center of the community**.

It combines real-time IRC communication with a modern web community layer, bringing together profiles, short-form posts, discussions, editorial content, media, widgets and external integrations without replacing the open protocol at its core.

Monoverse can be adapted to different kinds of communities, including:

- traditional online communities;
- developer communities;
- web radios;
- crypto communities.

## IRC at the center

IRC is not an additional chat feature embedded into Monoverse.

It is the starting point.

The web interface extends the community around the channel: it can expose presence, activity and content while providing persistent spaces for things that do not naturally belong to a real-time conversation.

Even the names **Ping** and **Pong**, used for posts and their replies, come directly from IRC.

## Community features

Monoverse currently includes:

- **Ping & Pong** — short-form community posts and conversations;
- **Chanzine** — an editorial area with articles, categories and user submissions subject to editorial review;
- **Profiles and presence** — community identities connected with IRC activity;
- **Media** — image, audio and video support;
- **Widgets** — configurable blocks that allow each community to build its own identity;
- **Developer integrations** — including GitHub-oriented widgets;
- **Web radio integrations** — including Icecast and AzuraCast;
- **Crypto integrations** — designed around self-custodial wallets rather than storing users' private keys;
- **RSS feeds** — for Chanzine and Ping, allowing content to flow outside the website and back into IRC through bots and other tools;
- **Moderation and community tools** — reporting, moderation, follows, saved content and notifications;
- **Multilingual interface** — with the infrastructure for community-specific translations.

## Different communities, one framework

Monoverse is not intended to impose the same experience on every community.

A developer community may focus on repositories, releases and pull requests.  
A web radio may put its live stream, listeners and requests at the center.  
A crypto community may expose wallet-related functionality.  
A traditional community may simply focus on its people, conversations and content.

The framework provides the common foundation; widgets and configuration give each installation its own identity.

## Open by design

Monoverse is built around the idea of an **Open Internet**.

Instead of trying to own every part of the user's digital identity or activity, it is designed to integrate with open protocols and external services whenever possible: IRC, OAuth, RSS, GitHub, Icecast, AzuraCast and self-custodial wallets.

Sensitive information should not be managed by Monoverse when it does not need to be.

## Installation

### Requirements

Monoverse requires:

- PHP 8.2 or newer;
- PDO and PDO MySQL;
- JSON;
- mbstring;
- cURL;
- GD with JPEG, PNG and WebP support;
- FFmpeg;
- a MySQL or MariaDB database;
- a writable `storage/` directory;
- Nginx or another web server capable of routing requests through `index.php`.

### Nginx

The document root must point to the root of the Monoverse installation, where `index.php` is located.

A minimal routing configuration looks like:

```nginx
location / {
	try_files $uri $uri/ /index.php?$query_string;
}
```

PHP requests must also be passed to your PHP-FPM installation according to your server configuration.

### Setup

Clone the repository into the directory served by your web server:

```bash
git clone https://github.com/SimosNap/monoverse.git
cd monoverse
```

Make sure the `storage/` directory is writable by the web server.

Create an empty MySQL or MariaDB database and then open the Monoverse URL in your browser.

On a fresh installation, Monoverse automatically starts the installation wizard.

The wizard will guide you through:

1. system requirements;
2. Community Edition selection;
3. database configuration;
4. OAuth configuration;
5. administrator account creation;
6. final installation.

Local database and OAuth credentials are written to:

```text
config/database.php
config/oauth.php
```

These files are excluded from Git and must never be committed.

The completed installation is marked by:

```text
storage/installed.lock
```

which is also excluded from Git.

## License

Monoverse is free and open-source software released under the **GNU Affero General Public License v3.0 (AGPL-3.0)**.

See [LICENSE](LICENSE) for the full license text.

---

**One channel. People. A community.**
