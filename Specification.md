#Zadání PHP aplikace - Vokounová Eliška (voke01)
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
