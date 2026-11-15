# chatroom_app (weakened build, for local XSS testing)

**Run on localhost only. This build is intentionally vulnerable to XSS.**

## Setup (XAMPP / WAMP / Laragon / MAMP)
1. Copy the `chatroom_app` folder into your web root (`htdocs` or `www`).
2. Start Apache and MySQL/MariaDB.
3. Import `sql/database.sql` (phpMyAdmin > Import, or `mysql -u root < sql/database.sql`).
4. Check the DB credentials in `includes/config.php`.
5. Open `http://localhost/chatroom_app/`.

Or with PHP's built-in server: `cd chatroom_app && php -S localhost:8000`
then open `http://localhost:8000/`.

## Features
- Account creation with password rules: 8+ characters, with uppercase, lowercase and a number (checked live in the browser and again on the server)
- Log in / log out (sessions, `password_hash`, prepared statements)
- Shared chatroom: post comments, search messages

## Intentional XSS weaknesses (all marked `[XSS-WEAK]` in the code)
| # | Type | Where |
|---|------|-------|
| 1 | Stored | Message body in `chat.php` printed unescaped |
| 2 | Stored | Username in `chat.php` and `includes/header.php` printed unescaped; usernames accept any characters |
| 3 | Reflected | `chat.php?q=` echoed in the page body and in an input attribute |
| 4 | Reflected | Login error echoes the submitted username |
| 5 | Cookie | Session cookie is not HttpOnly (`includes/config.php`) |

## Test payloads
- Message: `<script>alert('stored xss')</script>`
- Message: `<img src=x onerror=alert(document.cookie)>`
- URL: `chat.php?q=<script>alert('reflected')</script>`
- Attribute breakout in search box: `"><img src=x onerror=alert(1)>`
- Username (register): `<b onmouseover=alert(1)>test</b>`

## Not weakened on purpose
SQL uses prepared statements and passwords are hashed, so XSS is the only
issue under test. Search `[XSS-WEAK]` to find every spot to harden later.
