/* ---------------------------------------
      🌍 RCgestion Multilingual Engine
      ✔ Icons Supported
      ✔ RTL Support
      ✔ Auto Font Switching
-----------------------------------------*/

const translations = {

  /* ------------------- 🇫🇷 FRENCH ------------------- */
  fr: {
    font_family: "'Poppins', sans-serif",
    title: "RCgestion v 2025",
    user_mode: "Mode utilisateur",
    logout: "🚪 Déconnexion",

    menu_stock_group: '<i class="fa fa-boxes"></i> Gestion Stock',
    menu_ventes_group: '<i class="fa fa-cart-shopping"></i> Gestion des Ventes',
    menu_admin_group: '<i class="fa fa-gear"></i> Administration',

    menu_clients: '<i class="fa fa-users"></i> Clients / Fournisseurs',
    menu_products: '<i class="fa fa-box"></i> Produits',
    menu_categories: '<i class="fa fa-list"></i> Catégories',
    menu_stock: '<i class="fa fa-warehouse"></i> Situation Stock',
    menu_marge: '<i class="fa fa-chart-line"></i> Situation Marge',
    menu_bl: '<i class="fa fa-truck"></i> Bons de Livraison',
    menu_comptoir: '<i class="fa fa-store"></i> Vente Comptoir',
    menu_be: '<i class="fa fa-warehouse"></i> Bons d’Entrée',
    menu_facture: '<i class="fa fa-file-invoice"></i> Factures',
    menu_versement: '<i class="fa fa-money-bill"></i> Versement Libre',
    menu_credit: '<i class="fa fa-user"></i> Situation Clients/Four',
 menu_rest: '<i class="fa fa-user"></i> les rest des crédits',

    menu_company: '<i class="fa fa-building"></i> Entreprise',
    menu_settings: '<i class="fa fa-cog"></i> Paramètres',
    menu_users: '<i class="fa fa-user-shield"></i> Utilisateurs',

    card_credit_clients: "Crédits Clients",
    card_credit_four: "Crédits Fournisseurs",
    card_marge: "Marge Brute",
    card_marge_jour: "Marge du Jour",
    card_stock: "Valeur Stock",
    card_ca: "Chiffre d’Affaires",

    label_start: "Début",
    label_end: "Fin",
    btn_apply: "Appliquer",
    label_type: "Type",
    type_line: "Courbe",
    type_bar: "Barres",
    type_radar: "Radar",
     
    chart_title: "Marge, CA & Stock par Date",
    dashboard_title: "📊 Tableau de Bord"
  },

  /* ------------------- 🇬🇧 ENGLISH ------------------- */
  en: {
    font_family: "'Poppins', sans-serif",
    title: "RCgestion v 2025",
    user_mode: "User Mode",
    logout: "🚪 Logout",

    menu_stock_group: '<i class="fa fa-boxes"></i> Stock Management',
    menu_ventes_group: '<i class="fa fa-cart-shopping"></i> Sales Management',
    menu_admin_group: '<i class="fa fa-gear"></i> Administration',

    menu_clients: '<i class="fa fa-users"></i> Clients / Suppliers',
    menu_products: '<i class="fa fa-box"></i> Products',
    menu_categories: '<i class="fa fa-list"></i> Categories',
    menu_stock: '<i class="fa fa-warehouse"></i> Stock Status',
    menu_marge: '<i class="fa fa-chart-line"></i> Margin Status',
    menu_bl: '<i class="fa fa-truck"></i> Delivery Notes',
    menu_comptoir: '<i class="fa fa-store"></i> Counter Sales',
    menu_be: '<i class="fa fa-warehouse"></i> Receipt Notes',
    menu_facture: '<i class="fa fa-file-invoice"></i> Invoices',
    menu_versement: '<i class="fa fa-money-bill"></i> Free Payment',
    menu_credit: '<i class="fa fa-user"></i> Balance Clients/Suppliers',
    menu_company: '<i class="fa fa-building"></i> Company',
    menu_settings: '<i class="fa fa-cog"></i> Settings',
    menu_users: '<i class="fa fa-user-shield"></i> Users',
   menu_rest: '<i class="fa fa-user"></i> The rest of the credits',
    card_credit_clients: "Client Credits",
    card_credit_four: "Supplier Credits",
    card_marge: "Gross Margin",
    card_marge_jour: "Daily Margin",
    card_stock: "Stock Value",
    card_ca: "Turnover",

    label_start: "Start",
    label_end: "End",
    btn_apply: "Apply",
    label_type: "Type",
    type_line: "Line",
    type_bar: "Bars",
    type_radar: "Radar",

    chart_title: "Margin, Turnover & Stock by Date",
    dashboard_title: "📊 Dashboard"
  },

  /* ------------------- 🇩🇿🇦🇪 ARABIC ------------------- */
  ar: {
    font_family: "'Cairo', sans-serif",
    title: "نظام RCgestion 2025",
    user_mode: "وضع المستخدم",
    logout: "🚪 تسجيل الخروج",

    menu_stock_group: '<i class="fa fa-boxes"></i> إدارة المخزون',
    menu_ventes_group: '<i class="fa fa-cart-shopping"></i> إدارة المبيعات',
    menu_admin_group: '<i class="fa fa-gear"></i> الإدارة',

    menu_clients: '<i class="fa fa-users"></i> الزبائن / الموردون',
    menu_products: '<i class="fa fa-box"></i> المنتجات',
    menu_categories: '<i class="fa fa-list"></i> الفئات',
    menu_stock: '<i class="fa fa-warehouse"></i> وضعية المخزون',
    menu_marge: '<i class="fa fa-chart-line"></i> وضعية الهامش',
    menu_bl: '<i class="fa fa-truck"></i> سندات التسليم',
    menu_comptoir: '<i class="fa fa-store"></i> مبيعات المحل',
    menu_be: '<i class="fa fa-warehouse"></i> سندات الإدخال',
    menu_facture: '<i class="fa fa-file-invoice"></i> الفواتير',
    menu_versement: '<i class="fa fa-money-bill"></i> الدفع الحر',
    menu_credit: '<i class="fa fa-user"></i> وضعية الزبائن/الموردين',
    menu_company: '<i class="fa fa-building"></i> المؤسسة',
    menu_settings: '<i class="fa fa-cog"></i> الإعدادات',
    menu_users: '<i class="fa fa-user-shield"></i> المستخدمون',
   menu_rest: '<i class="fa fa-user"></i> باقي الرصيد',
    card_credit_clients: "ديون الزبائن",
    card_credit_four: "ديون الموردين",
    card_marge: "الهامش الإجمالي",
    card_marge_jour: "هامش اليوم",
    card_stock: "قيمة المخزون",
    card_ca: "رقم الأعمال",

    label_start: "البداية",
    label_end: "النهاية",
    btn_apply: "تطبيق",
    label_type: "النوع",
    type_line: "منحنى",
    type_bar: "أعمدة",
    type_radar: "رادار",

    chart_title: "الهامش · رقم الأعمال · المخزون حسب التاريخ",
    dashboard_title: "📊 لوحة التحكم"
  }

};


/* ======================================
        APPLY LANGUAGE + FONT + RTL
====================================== */
function applyLang(lang) {
  document.querySelectorAll("[data-lang]").forEach(el => {
    const key = el.getAttribute("data-lang");
    if (translations[lang] && translations[lang][key]) {
      el.innerHTML = translations[lang][key];
    }
  });

  /* Apply Font Family */
  document.body.style.fontFamily = translations[lang].font_family;
}


/* ======================================
           SWITCH LANGUAGE
====================================== */
function setLang(lang) {

  localStorage.setItem("lang", lang);
  applyLang(lang);

  if (lang === "ar") {
    document.body.classList.add("rtl");
    document.body.classList.add("arabic-font");
  } else {
    document.body.classList.remove("rtl");
    document.body.classList.remove("arabic-font");
  }
}


/* ======================================
          LOAD ON START
====================================== */
document.addEventListener("DOMContentLoaded", () => {
  let lang = localStorage.getItem("lang") || "fr";
  setLang(lang);
});
