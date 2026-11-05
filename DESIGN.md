# Codencio: pracovní prostor pro dva vývojáře

Uživatel upřesnil, že chce především modernější design a lepší uspořádání.
Zachovají se klienti, projekty, úkoly, komentáře, čas, faktury a období služeb.

## Směr

Inspirace: [Linear](https://linear.app/docs/custom-views) pro hierarchii seznamů,
[Ora](https://ora.pm/features) pro vztah zadání, diskuze a času. Vlastní kód,
žádné napojení na jejich služby.

Předchozí návrh opakoval nadpisy, používal příliš malý text a stejně výrazné
karty pro různé informace. Nově bude hlavní práce na klidné bílé ploše,
vedlejší kontext v užším sloupci. Projektové karty jsou vyhrazené zakázkám.

## Tokeny a uspořádání

- Místní Manrope se SIL Open Font License. Nadpis 28–30 px, oddíl 16 px,
  běžný text 14 px, metadata 12 px; zarovnání vlevo.
- Plocha #FFFFFF, vedlejší plocha #F6F6F9, text #252631, metadata #6C6D78,
  oddělovač #E9E9EF, akce #6156CE.
- Tmavé plátno #17181C, plocha #202127, text #EEEEF3, čára #32333D.
  Editor a jeho dialogy sdílejí barvy aplikace.
- Navigace 232 px, mezery 8/12/16/24/32 px. Ovládání zaoblené 6 px,
  projektová karta 10 px. Stín pouze u plovoucích nabídek.

```text
Navigace   | Projekty / kontext                        Vzhled Oznámení
           | Název stránky                              Hlavní akce
Moje práce | Pohledy                                  Hledání / filtry
Projekty   |
Úkoly      | Hlavní práce                         Vedlejší informace
           | zadání / úkoly / komentáře           termín / lidé / čas
Klienti    |
Služby     | Portfolio: konkrétní zakázka, klient, stav, postup a tým.
Faktury    |
Přístupy   |
```

- Moje práce: úkoly před statistikami; služby a poslední domluvy vedle.
- Projekty: přehled zakázek s klientem, skutečným postupem a týmem.
- Úkoly: střídmý seznam s blízkými metadaty. Pokročilé filtry na vyžádání.
- Detail úkolu: zadání a diskuze vlevo, vlastnosti a historie času vpravo.
  Časovač je hlavní akce; informace se neopakují v několika kartách.
- Formuláře: obsah vlevo, nastavení vpravo, čitelná šířka a jeden závěr.
  Výběr kolegů pomocí zaškrtávacích polí bez Ctrl/Cmd.

Přezkoumání proti zadání: automatické doporučení nabízelo marketingovou
landing page a přehnanou typografii. To pro každodenní CRM neodpovídá zadání.
Použije se aplikační plocha s obsahem před dekorací a bez nových funkcí.

## Ověření

Skutečná data i dlouhé názvy při 1440/1024/768/390/320 px. Mobilní navigace,
nabídky posledního řádku, filtry, skutečný CKEditor, oba motivy, poznámky,
časovače dvou uživatelů a faktury. Prázdné stavy nabídnou další krok.

Ověřeno v hotové verzi: 24 Laravel testů / 129 kontrol; hlavní stránky a
formuláře při 320, 390, 768, 1024 a 1440 px bez vodorovného přetékání.
Skutečný CKEditor 39.0.1 pro komentáře, odpovědi a úpravy v obou motivech.
Dále filtry s návratem zpět, nabídky klávesnicí, dlouhé názvy, dva kolegové
na úkolu, zachování zrušeného výběru po chybě a kurzor v otevřeném editoru.
Pracovní scénáře pokryly také poznámky, časovače dvou lidí, ruční čas, faktury
a navazující období služby. Finální snímky byly prohlédnuty na desktopu i mobilu.

## Obnovená nástěnka a podbarvené sekce

Uživatel chce zpět původní nástěnku a výraznější rozdělení sekcí podkladem,
včetně oddělení filtrů a tabulek ve všech seznamových výpisech. Nástěnka
se vrátí na /dashboard: souhrny, měsíční příjmy, stavy úkolů, faktury a služby.
Moje práce zůstává samostatně na /my-work. Finanční část počítá vždy jednu
z viditelných měn faktur; cenové součty služeb zůstávají oddělené po měnách.

Paleta sekcí: neutrální #F6F6F9 pro filtry, jemná fialová #F2F0FB pro práci,
zelená #EEF6F1 pro služby a teplá #FAF5E9 pro domluvy. V tmavém režimu
mají vlastní tmavé protějšky; text a ovládání zachovávají kontrast. Písmo,
rozměry ovládání a akce zůstávají ze současného návrhu.

Přezkoumání: podbarvení patří celému funkčnímu bloku, ne každému prvku.
Filtry mají jednotný podklad a tabulka vlastní bílou/tmavou plochu. Nové
panely bez dekorativních stínů obnovují požadovanou hierarchii a zachovávají
kompaktní řádky i plovoucí nabídky akcí.

Ověření této úpravy: 27 Laravel testů / 172 kontrol, sestavení produkčních
souborů a kompilace Blade. Prohlížeč ověřil všech sedm seznamů v obou
motivech, filtry nástěnky, přechod na neuhrazené faktury a nabídky akcí bez
změny výšky řádku. Nástěnka, Moje práce, seznamy a hlavní detaily nemají
vodorovné přetékání při 320, 390, 768 a 1024 px. Vizuální kontrola zahrnuje
desktop i mobil.
