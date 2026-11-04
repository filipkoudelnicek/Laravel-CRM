# Pracovní prostor pro dva vývojáře

## Revize: kompaktní pracovní aplikace

Předchozí provedení ponechávalo staré seznamy a obalovalo obsah množstvím
panelů. Návrh se nově řídí pracovními tabulkami, nikoli dashboardovou šablonou.

- Jednotné seznamy: klienti, projekty, úkoly, služby, faktury, přístupy a lidé.
  Název vždy otevře detail; další operace jsou v jediné nabídce na konci řádku.
- Cílová výška běžného řádku 56–64 px. Akce ani dlouhý název ji nenatahují;
  úplný název je dostupný v odkazu a na detailu. Na dotykových displejích
  mají ovládací prvky alespoň 44 px.
- Nabídky akcí jsou mimo ořez tabulky, ovladatelné šipkami a Escape.
  Smazání je oddělené a potvrzuje se se jménem konkrétního záznamu.
- Světlá navigace s oddíly Práce / Klienti / Nastavení. Na mobilu zůstává
  viditelný název, stav a nabídka akcí; druhotné sloupce se schovají.
- Paleta: plátno #F7F8FA, plocha #FFFFFF, text #222936, sekundární #637083,
  oddělovač #E6E9EF, akce #4759C5. Barva stavu má vždy také textový popisek.
- Typografie: systémové Segoe UI; nadpis 26 px, běžný text 14 px,
  metadata 12–13 px. Číselné hodnoty mají tabulární šířku.
- Horní část stránky: název a jedna hlavní akce, pod nimi pohledy a filtry.
  Žádné přepínání hlavních detailů přes náhledový modal.
- Úkol lze přesunout do jiného stavu přímo ze seznamu, s plnou serverovou
  kontrolou oprávnění. Filtry a řazení zůstávají v URL.

Přezkoumání: automatický návrh skillu nabízel portfolio/HUD, které zde
neodpovídají práci s klienty a termíny. Použijí se zásady hustoty dat,
přístupných ovládacích prvků a konzistentní navigace. Návrh se ověří na
reálných tabulkách včetně dlouhých názvů, posledního řádku a šířky 390 px.

```text
Navigace | Sekce / kontext                         Vzhled  Oznámení
         | Název stránky                         + Nový záznam
         | Aktivní pohled  Další pohled
         | Hledat                Stav    Řazení
         | Název a kontext      Stav  Termín  Čas  Další akce
         | ---------------------------------------------------
         | Stránkování
```

Hlavní úloha: otevřít zakázku, domluvit další krok, zaznamenat čas a vědět,
které klientské služby bude potřeba prodloužit. Faktura patří klientovi;
projekt je pouze volitelné upřesnění.

## Ověření návrhu

- Dvacet řádků v seznamu: každý 62 px, otevření nabídky výšku nemění.
- Název přes 200 znaků se zkrátí, úplný je v odkazu a na detailu.
- Nabídka posledního řádku se vejde i do okna vysokého 600 px; šipky,
  Home/End a Escape fungují, po zrušení smazání se vrátí fokus.
- Filtry a řazení přežijí stránkování i rychlou změnu stavu.
- Seznamy a hlavní detaily ověřeny při šířkách 320, 390, 768, 1024 a 1440 px.
- Světlý/tmavý režim i chybějící externí fonty a ikony nemění základní
  ovládání nové navigace a seznamů. Nové ikony jsou lokální SVG.
- Ověřeny poznámky, časovače dvou uživatelů, ruční čas, faktura a nové
  období služby; 24 aplikačních testů / 129 kontrol.
