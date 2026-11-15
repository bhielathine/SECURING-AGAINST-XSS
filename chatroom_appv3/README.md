# chatroom_app_hardened — XSS-secured build

## What changed from the weakened build
Search for `[XSS-FIX]` in every file to find each secured spot.

| Fix # | Type | File(s) | Technique |
|-------|------|---------|-----------|
| #1 | Cookie protection | includes/config.php | `httponly => true`, `samesite => Lax` |
| #2 | CSP header | includes/config.php | `Content-Security-Policy` blocks inline scripts |
| #3 | Output escaping | header, register, login, chat | `h()` / `htmlspecialchars(ENT_QUOTES, UTF-8)` on every echo |
| #4 | Input allowlist | includes/functions.php, register.php | Username regex `^[A-Za-z0-9_]{3,30}$` |
| #5 | Strip at storage | chat.php | `strip_tags()` before INSERT; `h()` again at display |

## Setup (same as weakened build)
1. Copy folder to web root.
2. Import `sql/database.sql`.
3. Set credentials in `includes/config.php`.
4. Open `http://localhost/chatroom_app_hardened/`.

## Payloads that now fail
| Payload | Why it now fails |
|---------|-----------------|
| `<script>alert(1)</script>` as message | `strip_tags()` removes the tag at storage; `h()` encodes any residue at display |
| `<img src=x onerror=alert(document.cookie)>` as message | `strip_tags()` strips the img tag |
| `?q=<script>alert(1)</script>` in URL | `strip_tags()` on `$q`; `h($q)` in both echo points |
| `"><script>alert(1)</script>` in search box | `h($q)` encodes `"` → `&quot;` keeping the attribute closed |
| `<b onmouseover=alert(1)>x</b>` as username | regex allowlist rejects registration; `h()` on output |
| `document.cookie` theft via cookie | `httponly => true` makes cookie invisible to JS |
| Injected inline `<script>` tag | CSP `script-src 'self'` blocks inline script execution |
