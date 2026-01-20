# Periode-Actieplan-Mentorlessen-Plugin

📦 Complete WordPress Plugin: "Periode Actieplan Mentorlessen"

✅ Wat is geïmplementeerd:

Frontend Functionaliteit:
✅ Shortcode [periode_actieplan] voor gebruik in Sensei lessen
✅ Vakkenpakket selectie in periode 1 (aanvinken + eigen vakken toevoegen)
✅ Cijferinvoer per vak (1-10 met 1 decimaal)
✅ Automatische PDRO-vragen bij cijfers < 6.0
✅ Validatie: alle cijfers + alle PDRO antwoorden verplicht
✅ Auto-save functionaliteit (debounced)
✅ "Plan Opslaan" knop om periode af te ronden
✅ Positieve feedback bij incomplete data
✅ Responsive design met mooie styling

Admin Functionaliteit:
✅ Dashboard met statistieken
✅ Leerling overzicht met alle data
✅ Onvoldoendes overzicht (2+ onvoldoendes)
✅ Mentor toewijzing (max 2 per leerling)
✅ Klas-informatie per leerling
✅ Periode openen/sluiten per leerling individueel
✅ Gespreksverslag per periode door mentor
✅ Uitgebreide instellingen (vakken, periodes, mentoren, PDRO vragen, e-mail template)

E-mail Systeem:
✅ Automatische e-mail bij afronden periode
✅ Naar leerling + mentor(en)
✅ Overzicht cijfers en volledige PDRO plannen
✅ Aanpasbare e-mail template met placeholders

Export Functionaliteit:
✅ Excel/CSV export (individueel of alle leerlingen)
✅ Print-vriendelijke view voor PDF generatie
✅ Volledige data inclusief verbeterplannen en gespreksverslagen

Database:
✅ 5 custom tables voor efficiënte opslag
✅ Automatische aanmaak bij activatie
✅ Proper indexing voor performance

📁 Bestandsstructuur die je moet aanmaken:
wp-content/plugins/periode-actieplan-mentorlessen/
├── periode-actieplan-mentorlessen.php (hoofdbestand - artifact 1)
├── includes/
│   ├── class-pam-database.php (artifact 2)
│   ├── class-pam-frontend.php (artifact 3)
│   ├── class-pam-admin.php (artifact 9)
│   ├── class-pam-email.php (artifact 8)
│   ├── class-pam-export.php (artifact 10)
│   └── class-pam-student.php (maak leeg bestand)
├── templates/
│   ├── frontend-form.php (artifact 4)
│   └── admin-*.php bestanden (moet je nog maken)
└── assets/
    ├── css/
    │   ├── frontend.css (artifact 7)
    │   └── admin.css (moet je nog maken)
    └── js/
        ├── frontend.js (artifact 5)
        └── admin.js (moet je nog maken)
🚀 Volgende Stappen:

Bestanden uploaden - Plaats alle artifacts in de juiste mappen
Templates maken - Maak de admin template bestanden (admin-dashboard.php, admin-students.php, admin-settings.php, admin-failures.php)
Admin CSS/JS - Maak admin.css en admin.js voor de admin interface styling
Plugin activeren - Activeer via WordPress admin
Instellingen configureren - Vul vakkenlijst en andere instellingen in
Shortcode plaatsen - Voeg [periode_actieplan] toe aan Sensei les
