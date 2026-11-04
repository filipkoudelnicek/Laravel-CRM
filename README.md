# Codencio CRM

Společný pracovní prostor pro malý vývojářský tým: klienti, projekty,
projektové poznámky, úkoly, komentáře, čas a období klientských služeb.

## Co aplikace umí

- Nástěnka s vlastními úkoly, termíny, týdenním časem a končícími službami.
- Klient se svými projekty, službami a fakturami.
- Projektové poznámky s autorem a datem; přístup podle členství v projektu.
- Úkoly, přiřazení kolegů, komentáře, zmínky a přílohy.
- Časovač uložený na serveru, viditelný napříč aplikací. Každý člověk má
  nejvýše jeden běžící časovač; opakované zastavení nepřepisuje čas.
- Ruční časové záznamy. Čas se ukládá v UTC, formuláře zobrazují pražský čas
  (nastavení `APP_DISPLAY_TIMEZONE`, výchozí `Europe/Prague`).
- Služby s obdobím od–do včetně posledního dne, cenou a datem úhrady.
  Prodloužení vytvoří nový záznam a ponechá předchozí období v historii.
- Faktury patří **klientovi**. Projekt je volitelný a musí patřit stejnému
  klientovi. Současná fakturace je interní evidence položek, částek a úhrady;
  neobsahuje kompletní vystavování českých účetních dokladů ani bankovní párování.
- Světlý a tmavý vzhled, mobilní navigace a ovládání klávesnicí.
- Kompaktní seznamy s jednou nabídkou akcí na řádek. Název otevírá detail,
  stav úkolu lze změnit přímo v seznamu. Pohledy a řazení zůstávají v URL.
- Detail klienta rozdělený na projekty, služby a faktury s kontaktem vedle obsahu.

## Technologie a požadavky

Laravel 12, PHP 8.3+, Blade, Bootstrap 5, vlastní CSS a JavaScript.
Node je potřeba pouze při sestavení souborů. Na hostingu nemusí běžet.
Databáze MySQL/MariaDB; pro místní náhled a testy lze použít SQLite.
Pro zvolenou databázi zapněte příslušný PDO ovladač a rozšíření vyžadovaná
Laravelem, včetně mbstring, DOM/XML, OpenSSL, cURL a Fileinfo.

## První instalace

```sh
composer install
cp .env.example .env
php artisan key:generate
npm ci
npm run production
```

V `.env` nastavte databázi, `APP_URL` a SMTP. Potom:

```sh
php artisan migrate
php artisan storage:link
php artisan crm:user --admin
php artisan serve
```

Příkaz `crm:user` se zeptá na jméno, e-mail a vlastní heslo. Nového kolegu
můžete přidat ve správě uživatelů a přiřadit k projektům. Veřejná registrace
je vypnutá. Příkaz bez `--admin` vytvoří člena týmu.

Pro ukázková data ve vývojovém prostředí (`APP_ENV=local`):

```sh
php artisan db:seed --class=DemoWorkspaceSeeder
```

Ukázkové účty jsou `dev1@example.test` a `dev2@example.test`, heslo
`Local-preview-2026!`. Seeder je určen výhradně pro místní náhled,
v produkci se odmítne spustit a do databáze s klienty další data nepřidá.
První účet je administrátor, druhý člen týmu.

## Aktualizace existující instalace

Zálohujte databázi a soubory. Zachovejte vlastní `.env`, `APP_KEY` a obsah
`storage` (klíč je potřeba také k dešifrování uložených přístupů).

```sh
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
```

Nové migrace přidávají projektové poznámky a volitelný projekt a datum úhrady
u služeb. Existující úhrady automaticky neodhadují: prázdné datum znamená
„úhrada neevidována“. Stávající klienti, projekty i faktury zůstávají zachované.
Starý časovač ukládal rozpracovaný začátek pouze do konkrétního prohlížeče;
před aktualizací jej zastavte a uložte ve staré verzi.

Sestavené soubory `public/css/workspace.css` a `public/js/workspace.js` jsou
součástí repozitáře, takže aktualizace pracovního rozhraní nevyžaduje Node na
hostingu. Po úpravě jejich zdrojů spusťte `npm ci && npm run production` na
vývojovém stroji nebo v CI a odešlete i nové sestavené soubory. Ostatní nově
generované soubory v `public` zůstávají ignorované. Složky `storage` a
`bootstrap/cache` musí být zapisovatelné.

## Sdílený hosting

- Veřejný adresář domény nastavte na `public`; konfigurace a `.env` zůstanou
  mimo veřejný adresář.
- Nastavte `APP_ENV=production`, `APP_DEBUG=false`, HTTPS a skutečné SMTP.
- Na serveru běží PHP a databáze. Aplikace nevyžaduje Docker, Redis,
  WebSocket server ani trvale běžící proces.
- Výchozí fronta `sync` zpracovává oznámení během požadavku. Přehled končících
  služeb se počítá při otevření aplikace; automatické e-mailové připomínky
  zatím nejsou implementované.
- Pro ukázková data používejte `DemoWorkspaceSeeder`; původní reset
  demonstrační databáze byl z plánovače odstraněný.

## Ověření

```sh
php artisan test
php artisan view:cache
npm run production
composer audit
```

Testy ověřují přístupová práva, klienta a projekt na faktuře, projektové
poznámky, časovače dvou lidí, ruční čas a prodlužování období služeb.

Původní vizuální základ je Soft UI Dashboard Laravel od Creative Tim a
UPDIVISION. Projekt zachovává původní licenční informace a používá jeho
bezplatné komponenty. Nové pracovní rozhraní má vlastní styly v
`resources/css/workspace.css`.
