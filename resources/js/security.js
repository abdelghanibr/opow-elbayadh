/* ======== security.js ======== */

/**
 * Vérifie si l’utilisateur est connecté
 * en appelant une API PHP qui retourne {loggedIn: true/false}
 */
async function checkAuth() {
  try {
    const res = await fetch("php/session_check.php", { credentials: "include" });
    const data = await res.json();

    if (!data.loggedIn) {
      // Si non connecté => rediriger vers login
      alert("⚠️ يجب تسجيل الدخول للوصول إلى هذه الصفحة.\n⚠️ Vous devez être connecté pour accéder à cette page.");
      window.location.href = "login";
    }
  } catch (err) {
    console.error("Erreur de vérification de session :", err);
    window.location.href = "login";
  }
}

/**
 * Surveille l’inactivité de l’utilisateur
 * Si inactif > X minutes => déconnexion auto
 *//*
function monitorInactivity(timeoutMinutes = 5000) {
  let timer;
  const resetTimer = () => {
    clearTimeout(timer);
    timer = setTimeout(() => {
      alert("⏳ انتهت جلستك بسبب عدم النشاط.\n⏳ Votre session a expiré pour cause d'inactivité.");
      window.location.href = "logout.php";
    }, timeoutMinutes * 60 * 1000);
  };

  // Événements qui réinitialisent le timer
  ["click", "mousemove", "keydown", "scroll"].forEach(evt =>
    document.addEventListener(evt, resetTimer)
  );

  resetTimer(); // démarrer
}
*/
/**
 * Vérifie la cohérence de la configuration (par ex: rôle utilisateur)
 */
function checkRole(allowedRoles = []) {
  fetch("php/session_check.php", { credentials: "include" })
    .then(res => res.json())
    .then(data => {
      if (!allowedRoles.includes(data.role)) {
        alert("🚫 ليس لديك صلاحيات كافية.\n🚫 Vous n'avez pas les permissions nécessaires.");
        window.location.href = "unauthorized";
      }
    });
}

/* Appels par défaut dans toutes les pages */
document.addEventListener("DOMContentLoaded", () => {
  checkAuth();          // ✅ vérifier login
 // monitorInactivity();  // ✅ surveiller inactivité
});
