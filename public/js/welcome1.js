document.addEventListener("DOMContentLoaded", function () {
    const modalBody = document.getElementById("complexModalBody");
    if (!modalBody) return;

    let lastLoadedType = null;
    let selectedComplexId = null;
    let selectedComplexName = null;

    function showLoading(message = "⏳ جاري جلب البيانات...") {
        modalBody.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-3 text-muted">${message}</p>
            </div>
        `;
    }

    function showError(message = "⚠ حدث خطأ أثناء جلب البيانات") {
        modalBody.innerHTML = `
            <div class="text-center py-4">
                <p class="text-danger fw-bold mb-0">${message}</p>
            </div>
        `;
    }

    function fetchComplexes(type) {
        lastLoadedType = type;
        showLoading();

        fetch(`/complexes/filter/${encodeURIComponent(type)}`, {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "Accept": "text/html"
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.text();
        })
        .then(html => {
            if (!html.trim()) {
                throw new Error("Réponse vide");
            }
            modalBody.innerHTML = html;
        })
        .catch(error => {
            console.error("Erreur chargement complexes :", error);
            showError("⚠ تعذر تحميل قائمة المركبات");
        });
    }

    function loginTemplate(complexId, complexName) {
        injectLoginCSS();

        return `
            <div class="text-center mb-4">
                <h4 class="title-2026">👇 اختر نوع الإنخراط للمتابعة</h4>
                <p class="text-muted">
                    المنشأة المختارة:
                    <strong style="color:#0077c8">${complexName || ""}</strong>
                </p>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-12 col-md-4 col-lg-3">
                    <div class="box-2026">
                        <div class="circle-icon person"><i class="fa-solid fa-user"></i></div>
                        <h5 class="fw-bold mt-2">الانخراط كممارس</h5>
                        <a class="btn-2026 person-btn" href="/person/register?complex=${complexId}">
                            التسجيل
                        </a>
                    </div>
                </div>

                <div class="col-12 col-md-4 col-lg-3">
                    <div class="box-2026">
                        <div class="circle-icon club"><i class="fa-solid fa-people-group"></i></div>
                        <h5 class="fw-bold mt-2">الانخراط كنادي رياضي</h5>
                        <a class="btn-2026 club-btn" href="/club/register?complex=${complexId}">
                            التسجيل
                        </a>
                    </div>
                </div>

                <div class="col-12 col-md-4 col-lg-3">
                    <div class="box-2026">
                        <div class="circle-icon company"><i class="fa-solid fa-building"></i></div>
                        <h5 class="fw-bold mt-2">الانخراط كشركة أو مؤسسة</h5>
                        <a class="btn-2026 company-btn" href="/entreprise/register?complex=${complexId}">
                            التسجيل
                        </a>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <button id="backToComplex" class="btn btn-secondary px-4">
                    ← رجوع إلى المركبات
                </button>
            </div>
        `;
    }

    document.addEventListener("click", function (e) {
        const openBtn = e.target.closest(".open-complex-modal");
        if (openBtn) {
            e.preventDefault();
            const type = openBtn.dataset.type;
            if (type) {
                fetchComplexes(type);
            }
            return;
        }

        const registerBtn = e.target.closest('[id^="btn-register-"]');
        if (registerBtn) {
            e.preventDefault();

            selectedComplexId = registerBtn.dataset.complexId;
            selectedComplexName = registerBtn.dataset.complexName || "—";

            modalBody.innerHTML = loginTemplate(selectedComplexId, selectedComplexName);
            return;
        }

        const backBtn = e.target.closest("#backToComplex");
        if (backBtn) {
            e.preventDefault();

            if (!lastLoadedType) {
                showError("⚠ لم يتم تحديد نوع المركب");
                return;
            }

            fetchComplexes(lastLoadedType);
        }
    });
});

function injectLoginCSS() {
    if (document.getElementById("loginTemplateStyles")) return;

    const style = document.createElement("style");
    style.id = "loginTemplateStyles";
    style.innerHTML = `
        .title-2026 {
            font-weight: 900;
            font-size: 1.8rem;
            text-align: center;
            margin-bottom: 15px;
        }

        .box-2026 {
            background: #ffffff;
            padding: 22px;
            border-radius: 18px;
            box-shadow: 0 12px 32px rgba(0,0,0,.08);
            text-align: center;
            transition: 0.3s ease;
        }

        .box-2026:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 45px rgba(0,0,0,.15);
        }

        .circle-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: auto;
            font-size: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            margin-bottom: 12px;
        }

        .circle-icon.person  { background: linear-gradient(135deg,#2563eb,#3b82f6); }
        .circle-icon.club    { background: linear-gradient(135deg,#059669,#10b981); }
        .circle-icon.company { background: linear-gradient(135deg,#d97706,#f59e0b); }

        .btn-2026 {
            display: block;
            width: 100%;
            border-radius: 14px;
            padding: 10px;
            color: #fff;
            font-weight: 700;
            text-decoration: none;
            transition: 0.25s ease;
            margin-top: 10px;
        }

        .person-btn  { background: linear-gradient(135deg,#2563eb,#3b82f6); }
        .club-btn    { background: linear-gradient(135deg,#059669,#10b981); }
        .company-btn { background: linear-gradient(135deg,#d97706,#f59e0b); }

        .person-btn:hover  { background: #1e3a8a; color:#fff; }
        .club-btn:hover    { background: #047857; color:#fff; }
        .company-btn:hover { background: #b45309; color:#fff; }

        #backToComplex {
            margin-top: 20px;
            font-weight: 700;
        }
    `;
    document.head.appendChild(style);
}