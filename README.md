# Periode-Actieplan-Mentorlessen-Plugin

\# Periode Actieplan Mentorlessen - Installatie Handleiding



\## Bestandsstructuur



Maak de volgende mappenstructuur aan in je WordPress `/wp-content/plugins/` directory:



```

periode-actieplan-mentorlessen/

├── periode-actieplan-mentorlessen.php (hoofdbestand)

├── includes/

│   ├── class-pam-database.php

│   ├── class-pam-student.php

│   ├── class-pam-frontend.php

│   ├── class-pam-admin.php

│   ├── class-pam-email.php

│   └── class-pam-export.php

├── templates/

│   ├── frontend-form.php

│   ├── admin-settings.php

│   ├── admin-students.php

│   ├── admin-overview.php

│   └── admin-failures.php

├── assets/

│   ├── css/

│   │   ├── frontend.css

│   │   └── admin.css

│   └── js/

│       ├── frontend.js

│       └── admin.js

└── languages/

```



\## Installatie Stappen



\### 1. Bestand Upload

Upload alle bestanden naar de `/wp-content/plugins/periode-actieplan-mentorlessen/` map.



\### 2. Plugin Activeren

1\. Ga naar WordPress admin → Plugins

2\. Zoek "Periode Actieplan Mentorlessen"

3\. Klik op "Activeren"



De plugin maakt automatisch de benodigde database tabellen aan:

\- `wp\_pam\_student\_subjects` - vakkenpakketten

\- `wp\_pam\_grades` - cijfers per periode

\- `wp\_pam\_improvement\_plans` - PDRO actieplannen

\- `wp\_pam\_mentor\_notes` - gespreksverslagen

\- `wp\_pam\_student\_meta` - mentoren en klassen



\### 3. Basis Configuratie



Ga naar \*\*Mentorlessen → Instellingen\*\* in de WordPress admin:



\#### Vakken Instellen

1\. Vul de vakkenlijst in (één vak per regel):

```

Biologie

Wiskunde

Nederlands

Engels

Duits

Frans

Geschiedenis

Aardrijkskunde

Natuurkunde

Scheikunde

```



\#### Periodes Configureren

Standaard zijn er 4 periodes:

\- Periode 1 (actief)

\- Periode 2

\- Periode 3

\- Periode 4



Je kunt de namen aanpassen naar je eigen systeem.



\#### Mentor Rol Instellen

Selecteer welke WordPress rol toegang heeft als mentor (standaard: Teacher/Sensei Teacher rol).



\#### PDRO Vragen

De 4 standaard vragen zijn al ingesteld, maar kunnen aangepast worden:

1\. PLAN - Terugkijken

2\. PLAN - Doel stellen (SMART)

3\. DO - Concrete acties

4\. REVIEW - Evaluatie methode



\### 4. Sensei Integratie



\#### Cursus Aanmaken

1\. Ga naar \*\*Sensei LMS → Cursussen\*\*

2\. Maak een nieuwe cursus aan met naam "Mentor" (of gebruik bestaande)

3\. Maak een les aan binnen deze cursus



\#### Shortcode Toevoegen

In de les-inhoud, voeg de shortcode toe:

```

\[periode\_actieplan]

```



Dit toont het volledige formulier voor leerlingen.



\### 5. Mentoren Toewijzen



1\. Ga naar \*\*Mentorlessen → Leerlingen\*\*

2\. Selecteer een leerling

3\. Wijs 1 of 2 mentoren toe via de dropdown

4\. Vul optioneel de klas in (bijv. M3c, M4b)

5\. Klik op "Opslaan"



\## Gebruik voor Leerlingen



\### Eerste Periode

1\. Leerling logt in en gaat naar de Mentor les in Sensei

2\. Selecteert vakken die gevolgd worden (aanvinken)

3\. Eventueel eigen vakken toevoegen

4\. Cijfers invullen per vak (1-10, met 1 decimaal)

5\. Klik op "Cijfers opslaan"

6\. Voor vakken < 6.0 verschijnen PDRO vragen

7\. Vul alle vragen in

8\. Klik op "Plan Opslaan" om de periode af te ronden



\### Vervolgperiodes

1\. Vakken zijn al voorgeselecteerd op basis van periode 1

2\. Alleen cijfers invullen

3\. PDRO vragen verschijnen automatisch bij onvoldoendes

4\. Periode afronden met "Plan Opslaan"



\### Validatie

\- Alle vakken moeten een cijfer hebben (of "-" als er nog geen cijfer is)

\- Bij onvoldoendes moeten alle 4 PDRO vragen volledig ingevuld zijn

\- Voorbeeld teksten ("Bijv. ...") worden niet geaccepteerd

\- Bij incomplete data krijgt de leerling een positieve melding



\## Admin Functionaliteit



\### Dashboard Overzicht

\*\*Mentorlessen → Dashboard\*\*

\- Aantal leerlingen per periode

\- Leerlingen met 2+ onvoldoendes

\- Recente activiteit



\### Leerling Overzicht

\*\*Mentorlessen → Leerlingen\*\*

Functies:

\- Zoeken op naam

\- Filteren op klas

\- Filteren op mentor

\- Bekijk volledige data per leerling

\- Periode openen/sluiten per leerling

\- Gespreksverslag toevoegen per periode



\### Onvoldoendes Overzicht  

\*\*Mentorlessen → Onvoldoendes\*\*

\- Lijst met leerlingen met 2+ onvoldoendes

\- Per periode of actueel

\- Filter op mentor/klas

\- Klik door naar leerling details

\- Exporteer naar Excel/PDF



\### Instellingen

\*\*Mentorlessen → Instellingen\*\*

Tabbladen:

1\. \*\*Vakken\*\* - Beheer vakkenlijst

2\. \*\*Periodes\*\* - Namen en actieve periode instellen

3\. \*\*Mentoren\*\* - Rol configuratie

4\. \*\*E-mail\*\* - Template bewerken met placeholders:

&nbsp;  - `{naam}` - Naam mentor/leerling

&nbsp;  - `{leerling\_naam}` - Naam leerling

&nbsp;  - `{periode\_nummer}` - Periode nummer

&nbsp;  - `{cijfer\_overzicht}` - Tabel met cijfers

&nbsp;  - `{verbeterplan\_overzicht}` - Actieplannen

&nbsp;  - `{admin\_link}` - Link naar admin overzicht

5\. \*\*PDRO Vragen\*\* - Aanpassen van de 4 vragen



\## Export Functies



\### Excel Export

Exporteert alle data naar `.xlsx` bestand:

\- Leerling info

\- Cijfers per periode

\- Actieplannen

\- Gespreksverslagen



\### PDF Rapport

Genereert individueel rapport per leerling:

\- Overzicht alle periodes

\- Cijfers en gemiddeldes

\- Actieplannen bij onvoldoendes

\- Mentor notities



\### Print View

Print-vriendelijke weergave voor papieren archief.



\## E-mail Notificaties



\### Bij Afronden Periode

Automatische e-mail naar:

\- Leerling (ter bevestiging)

\- Mentor 1 (indien toegewezen)

\- Mentor 2 (indien toegewezen)



Bevat:

\- Overzicht alle cijfers

\- Volledige PDRO plannen bij onvoldoendes

\- Link naar admin voor volledig overzicht



\### Template Aanpassen

Via \*\*Mentorlessen → Instellingen → E-mail\*\* tab kan de template aangepast worden.



\## Periode Management



\### Periode Activeren

1\. Ga naar \*\*Mentorlessen → Instellingen → Periodes\*\*

2\. Klik op "Actief" bij gewenste periode

3\. Leerlingen kunnen nu data invoeren voor deze periode



\### Individueel Openen/Sluiten

Als mentor kun je per leerling een periode heropenen:

1\. Ga naar \*\*Mentorlessen → Leerlingen\*\*

2\. Selecteer leerling

3\. Bij gewenste periode klik op "Heropenen"

4\. Leerling kan nu aanpassingen maken

5\. Klik op "Sluiten" wanneer compleet



\## Troubleshooting



\### Leerling ziet formulier niet

\- Check of leerling is ingelogd

\- Verify shortcode `\[periode\_actieplan]` staat in Sensei les

\- Check of er een actieve periode is ingesteld



\### E-mails worden niet verzonden

\- Controleer WordPress SMTP instellingen

\- Test met plugin zoals "WP Mail SMTP"

\- Check spam folder



\### Data wordt niet opgeslagen

\- Check browser console voor JavaScript errors

\- Verify AJAX URL is correct (wp-admin/admin-ajax.php)

\- Check of database tabellen aangemaakt zijn



\### Periode kan niet afgesloten worden

\- Verify alle cijfers zijn ingevuld

\- Check of alle PDRO antwoorden compleet zijn

\- Kijk in browser console voor foutmeldingen



\## Technische Details



\### Database Structuur

Plugin gebruikt custom tables met prefix `wp\_pam\_`:

\- Efficiënter dan meta tables voor grote datasets

\- Foreign keys voor data integriteit

\- Indexed voor snelle queries



\### Beveiliging

\- Nonce verificatie op alle AJAX calls

\- Input sanitization op alle user data

\- Prepared statements voor database queries

\- Capability checks voor admin functies



\### Performance

\- Auto-save met debouncing (2-3 seconden)

\- Lazy loading van improvement plans

\- Gecachte queries waar mogelijk

\- Optimized database indexes



\## Support



Voor vragen of problemen, neem contact op met de ontwikkelaar of check de plugin documentatie op eco.isdigitaal.nl.



\## Changelog



\### Versie 1.0.0

\- Initiële release

\- Basis functionaliteit voor 4 periodes

\- PDRO verbeterplannen

\- Mentor toewijzing (max 2)

\- E-mail notificaties

\- Excel/PDF export

\- Admin overzichten

