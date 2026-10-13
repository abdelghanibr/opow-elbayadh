<!-- CSS commun de la carte (badge CR80). Le masquage écran et les règles d'impression sont propres à chaque vue. -->
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: "Tajawal", "Cairo", sans-serif;
        background: #eef2f0;
        direction: rtl;
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 100vh;
    }

    /* ===== Boutons (cachés à l'impression) ===== */
    .actions {
        display: flex;
        gap: 10px;
        margin-bottom: 22px;
        flex-wrap: wrap;
        justify-content: center;
        position: sticky;
        top: 10px;
        z-index: 5;
    }

    .actions a, .actions button {
        text-decoration: none;
        font-family: inherit;
        font-weight: 700;
        font-size: 14px;
        padding: 9px 20px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        color: #fff;
        background: #1b5e20;
        box-shadow: 0 3px 10px rgba(27, 94, 32, .25);
    }

    .actions .btn-outline {
        background: #fff;
        color: #1b5e20;
        border: 2px solid #1b5e20;
    }

    .note-box {
        margin: 16px auto 0;
        max-width: 520px;
        width: 100%;
        background: #fef3cd;
        border: 1px solid #f0d985;
        color: #7a5b00;
        border-radius: 12px;
        padding: 10px 16px;
        font-size: 13px;
        font-weight: 700;
    }
    .note-box li { margin: 3px 0; }

    /* ==================================================
       BADGE — format 95 × 55 mm
    ================================================== */
    .sheet {
        display: grid;
        grid-template-columns: repeat(1, 95mm);
        gap: 2mm;
        justify-content: center;
        margin-top: 12px;
    }

    .card-piece:not(:first-of-type) { display: none; }

    .bulk-page .card-piece:not(:first-of-type) { display: flex; }

    .card-piece {
        width: 95mm;
        height: 55mm;
        border-radius: 3.5mm;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 6px 18px rgba(0,0,0,.18);
        color: #0d3f12;
        display: flex;
        flex-direction: column;
    }

    .badge {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 1.2mm 1.8mm;
    }

    /* --- Bandeau supérieur --- */
    .badge-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1mm;
        background: linear-gradient(90deg, #1b5e20, #2e7d32);
        color: #fff;
        border-radius: 2mm;
        padding: 0.8mm 1.6mm;
        height: 8.2mm;
    }
    .badge-top .logo { height: 6mm; width: auto; object-fit: contain; }
    .badge-top .titles { text-align: center; flex: 1; line-height: 1.25; }
    .badge-top .titles .rep { font-size: 5.6px; font-weight: 800; display: block; }
    .badge-top .titles .org { font-size: 5px; font-weight: 600; opacity: .9; display: block; }
    .badge-top .season-chip {
        font-size: 5.6px; font-weight: 800;
        background: #fff; color: #1b5e20;
        border-radius: 1.6mm; padding: 0.6mm 1.8mm; white-space: nowrap;
    }

    /* --- Corps du badge --- */
    .badge-body {
        flex: 1;
        display: grid;
        grid-template-columns: 21mm 1fr 17mm;
        gap: 1.5mm;
        align-items: stretch;
        padding: 1mm 0;
    }

    /* 📷 Photo */
    .photo-wrap {
        border: 0.6mm solid #1b5e20;
        border-radius: 2mm;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f4faf5;
    }
    .photo-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .photo-wrap .no-photo {
        color: #8aa08d;
        font-size: 6px;
        font-weight: 700;
        text-align: center;
        padding: 2px;
    }

    /* ℹ️ Infos */
    .info { display: flex; flex-direction: column; justify-content: center; gap: 0.9mm; }
    .info .name {
        font-size: 9.5px; font-weight: 800; color: #10330f;
        background: #e6f4e8; border-radius: 1.4mm; padding: 0.4mm 1.5mm; text-align: center;
    }
    .info .row { display: flex; align-items: center; gap: 1.2mm; font-size: 6.4px; }
    .info .row .k { color: #6b7f6d; font-weight: 600; min-width: 14mm; }
    .info .row .v { font-weight: 800; color: #0d3f12; }
    .info .row .v.addr {
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 52mm;
    }
    .info .row .dayshours { font-size: 6px; line-height: 1.4; }

    /* 🔳 QR */
    .qr-col {
        display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.8mm;
        border: 0.4mm dashed #b8dfc0; border-radius: 2mm; padding: 0.8mm;
    }
    .qr-col svg { width: 15mm; height: 15mm; }
    .qr-col .n { font-size: 4.8px; font-weight: 800; color: #0d3f12; text-align: center; line-height: 1.35; }

    /* --- Pied du badge --- */
    .badge-foot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 4.9px;
        color: #0d3f12;
        border-top: 0.3mm solid #cfe7d2;
        padding-top: 0.7mm;
        height: 4.6mm;
    }
    .badge-foot b { font-weight: 800; }
    .badge-foot .st { font-weight: 800; }
    .badge-foot .st.sariya { color: #1b5e20; }
    .badge-foot .st.muntahia { color: #b45309; }
    .badge-foot .st.mawqofa { color: #b91c1c; }
</style>