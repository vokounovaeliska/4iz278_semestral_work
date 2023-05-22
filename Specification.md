Zadání PHP aplikace - Vokounová Eliška (voke01)
-------------------------------------------------
--------------------------------------------------
Zadání
----------------------------
Aplikace by sloužila k evidenci rostlin.
Ke každé rostlině by bylo možné doplnit informace:
- o jejím jméně,
- latinském názvu,
- stáří (kdy byla zakoupena),
- jak častou zálivku potřebuje,
- zda má raději slunné nebo stinné místo,
- jakou teplotu má ráda,
- z jakého kontinentu pochází
- a zda má ráda rosení.

K rostlině by dále bylo možné vkládat i doplňující komentáře (popis). 
Každá rostlina patří právě jednomu uživateli. 
Jedna rostlina může spadat do žádné či více kategorií:
- "pro začátečníky", 
- "pro pokročilé",
- "vhodné k mazlíčkům",
- "pokojová rostlina"...).

Podle kategorií také bude fungovat filtrování (zobrazování jen seznamu rostlin, které spadají do dané kategorie).
V rámci aplikace se uživatel
- bude muset registrovat 
- a následně přihlásit, aby se mohl koukat na seznam svých rostlin. 

Do svého seznamu si může přidat novou rostlinu, editovat ji nebo ji smazat. 
V databázové tabulce rostlin tedy bude i pole, kdy byl záznam upraven.
Po přihlášení uživatel vidí i rostliny ostatních uživatelů a může jim dát like (nikoliv je editovat nebo mazat). Tím se rostlina přidá do uživvatelova nového seznamu "Oblíbené rostliny".

Celkem by tedy vznikly tabulky:
- user,
- plant,
- category,
- plant_category_map,
- plant_user_like_map.







Historie
---------------------------------------------------
Aplikace by sloužila k evidenci rostlin. Ke každé rostlině by bylo možné doplnit informace o jejím:
- jméně,
- stáří (kdy byla zakoupena),
- jak častou zálivku potřebuje,
- zda má raději slunné nebo stinné místo
- atd. 
K rostlině by dále bylo možné vkládat i doplňující komentáře týkající se jejího 
- stavu,
- kdy proběhla poslední zálivka atd.

V rámci aplikace se uživatel bude moci 
- registrovat 
- a následně přihlásit, aby se mohl kouknout na seznam svých rostlin. 
- Do seznamu může přidat novou rostlinu, editovat ji nebo ji smazat.
Co se týká databázových tabulek:
- tak by vznikla tabulka uživatelů, 
- rostlin 
- a kategorie rostliny, kdy každá rostlina vždy patří právě jednomu uživateli. 
 
Každá rostlina může patřit do jedné či více kategorií rostlin, například "pro začátečníky", "pro pokročilé", "vhodné k mazlíčkům", "pokojová rostlina" atd.
Celkem by tedy vznikly 4 tabulky: 
- user,
- plant,
- category,
- plant_category_map.

Připomínky
---------------------------------------------------
Tématicky je to fajn, ale bylo by vhodné trochu rozšířit dané funkce - v textu máte nějaké komentáře, ale v DB už nikde nejsou.
Navrhuji do zadání doplnit:
- uživatel má možnost spravovat seznam míst, kde rostliny pěstuje a zobrazovat rostliny dle daného umístění (alternativou by bylo prostě rozdělení rostlin do vlastních kategorií)
- ostatní uživatelé vidí rostliny ostatních uživatelů a mohou jim dát "like"

Prosím o potvrzení, zda vám to takhle dává smysl, nebo prosím o Vaše vlastní zpřesnění/doplnění zadání.