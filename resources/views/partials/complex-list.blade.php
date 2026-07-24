
<div class="row g-3 g-md-4">
    @foreach($complexes as $complex)
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="complex-card">
                
                <!-- صورة المركب -->
                <div class="complex-image-container">
                    <img src="{{ asset($complex->image ?? 'images/default-complex.jpg') }}" 
                         alt="{{ $complex->nom }}"
                         class="complex-image"
                         loading="lazy">
                    <div class="complex-badge">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                </div>

                <!-- محتوى البطاقة -->
                <div class="complex-content">
                    <h5 class="complex-title" title="{{ $complex->nom }}">
                        {{ \Illuminate\Support\Str::limit($complex->nom, 200) }}
                    </h5>
                    
                    <div class="complex-meta">
                        <span class="complex-address" title="{{ $complex->adresse ?? 'العنوان غير متوفر' }}">
                            <i class="fas fa-location-dot me-1"></i>
                            {{ \Illuminate\Support\Str::limit($complex->adresse ?? 'العنوان غير متوفر', 30) }}
                        </span>
                    </div>

                    <!-- معلومات إضافية (اختياري) -->
                    <div class="complex-details">
                        @if($complex->capacity)
                            <span class="detail-item">
                                <i class="fas fa-users"></i>
                                {{ $complex->capacity }} شخص
                            </span>
                        @endif
                        
                        @if($complex->price)
                            <span class="detail-item">
                                <i class="fas fa-tag"></i>
                                {{ number_format($complex->price) }} دج
                            </span>
                        @endif
                    </div>

                    <!-- زر التسجيل -->
                    <button id="btn-register-{{ $complex->id }}"
                            class="btn-register"
                            data-complex-id="{{ $complex->id }}"
                            data-complex-name="{{ $complex->nom }}"
                            aria-label="تسجيل في {{ $complex->nom }}">
                        <i class="fas fa-user-plus me-2"></i>
                        تسجيل
                    </button>
                </div>
            </div>
        </div>
    @endforeach
</div>

<style>
    /* ===== CSS Variables ===== */
    :root {
        --complex-primary: #2563eb;
        --complex-primary-dark: #1d4ed8;
        --complex-secondary: #10b981;
        --complex-gray: #6b7280;
        --complex-light-gray: #f3f4f6;
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --card-shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        --border-radius: 16px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ===== Complex Card Container ===== */
    .complex-card {
        background: #ffffff;
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        height: 100%;
        display: flex;
        flex-direction: column;
        border: 1px solid rgba(0, 0, 0, 0.05);
        position: relative;
    }

    .complex-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--card-shadow-hover);
        border-color: var(--complex-primary);
    }

    /* ===== Image Container ===== */
    .complex-image-container {
        position: relative;
        height: 160px;
        overflow: hidden;
        background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
    }

    .complex-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .complex-card:hover .complex-image {
        transform: scale(1.05);
    }

    .complex-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: rgba(255, 255, 255, 0.95);
        color: var(--complex-primary);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        z-index: 2;
    }

    /* ===== Content Area ===== */
    .complex-content {
        padding: 1.25rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    /* ===== Title ===== */
    .complex-title {
        font-size: 1.1rem;
        font-weight: 900;
        color: #1f2937;
        margin-bottom: 0.75rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
      
        text-overflow: ellipsis;
        min-height: 2.8rem;
             white-space: normal;
    overflow: visible;
    text-overflow: unset;
    display: block;
    min-height: auto;
    }

    /* ===== Address ===== */
    .complex-address {
        font-size: 0.85rem;
        color: var(--complex-gray);
        margin-bottom: 1rem;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        min-height: 2.5rem;
    }

    .complex-address i {
        color: var(--complex-secondary);
        font-size: 0.8rem;
    }

    /* ===== Details ===== */
    .complex-details {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
    }

    .detail-item {
        font-size: 0.8rem;
        color: var(--complex-gray);
        background: var(--complex-light-gray);
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .detail-item i {
        font-size: 0.7rem;
    }

    /* ===== Register Button ===== */
    .btn-register {
        background: linear-gradient(135deg, var(--complex-primary), var(--complex-primary-dark));
        color: white;
        border: none;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: auto;
        text-decoration: none;
        width: 100%;
    }

    .btn-register:hover {
        background: linear-gradient(135deg, var(--complex-primary-dark), #1e40af);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
    }

    .btn-register:active {
        transform: translateY(0);
    }

    .btn-register i {
        font-size: 0.9rem;
    }

    /* ===== Empty State ===== */
    .complex-card:empty::before {
        content: 'لا توجد مركبات';
        display: flex;
        align-items: center;
        justify-content: center;
        height: 200px;
        color: var(--complex-gray);
        font-size: 0.9rem;
    }

    /* ===== Loading State ===== */
    .complex-card.loading {
        position: relative;
        overflow: hidden;
    }

    .complex-card.loading::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    /* ===== Responsive Design ===== */

    /* Tablets */
    @media (max-width: 992px) {
        .complex-image-container {
            height: 140px;
        }
        
        .complex-content {
            padding: 1rem;
        }
        
        .complex-title {
            font-size: 1rem;
        }
        
        .complex-address {
            font-size: 0.8rem;
        }
    }

    /* Mobile Large */
    @media (max-width: 768px) {
        .complex-card {
            margin-bottom: 0.5rem;
        }
        
        .complex-image-container {
            height: 130px;
        }
        
        .complex-badge {
            width: 32px;
            height: 32px;
            font-size: 0.8rem;
        }
        
        .btn-register {
            padding: 0.65rem 0.9rem;
            font-size: 0.9rem;
        }
        
        .detail-item {
            font-size: 0.75rem;
            padding: 0.2rem 0.6rem;
        }
    }

    /* Mobile Medium */
    @media (max-width: 576px) {
        .row.g-3 {
            margin: 0 -0.375rem;
        }
        
        .col-12 {
            padding: 0 0.375rem;
        }
        
        .complex-card {
            border-radius: 12px;
        }
        
        .complex-image-container {
            height: 120px;
        }
        
        .complex-content {
            padding: 0.875rem;
        }
        
        .complex-title {
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
            min-height: 2.4rem;
        }
        
        .complex-address {
            font-size: 0.75rem;
            margin-bottom: 0.75rem;
            min-height: 2rem;
        }
        
        .complex-details {
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .btn-register {
            padding: 0.6rem 0.8rem;
            font-size: 0.85rem;
            border-radius: 10px;
        }
        
        .btn-register i {
            font-size: 0.8rem;
        }
    }

    /* Mobile Small */
    @media (max-width: 375px) {
        .complex-image-container {
            height: 110px;
        }
        
        .complex-content {
            padding: 0.75rem;
        }
        
        .complex-title {
            font-size: 0.9rem;
            -webkit-line-clamp: 2;
            min-height: 2.2rem;
        }
        
        .complex-address {
            font-size: 0.7rem;
            -webkit-line-clamp: 2;
            min-height: 1.8rem;
        }
        
        .detail-item {
            font-size: 0.7rem;
            padding: 0.15rem 0.5rem;
        }
        
        .btn-register {
            padding: 0.55rem 0.7rem;
            font-size: 0.8rem;
        }
    }

    /* Portrait Mobile */
    @media (max-width: 320px) {
        .complex-image-container {
            height: 100px;
        }
        
        .complex-badge {
            width: 28px;
            height: 28px;
            font-size: 0.7rem;
        }
        
        .complex-title {
            font-size: 0.85rem;
        }
        
        .complex-address {
            font-size: 0.65rem;
        }
        
        .btn-register {
            font-size: 0.75rem;
        }
    }

    /* Landscape Mobile */
    @media (max-height: 500px) and (orientation: landscape) {
        .complex-card {
            flex-direction: row;
            height: auto;
        }
        
        .complex-image-container {
            width: 120px;
            height: 120px;
            flex-shrink: 0;
        }
        
        .complex-content {
            flex-grow: 1;
            padding: 1rem;
        }
        
        .complex-title {
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        
        .complex-details {
            margin-bottom: 0.75rem;
        }
        
        .btn-register {
            width: auto;
            margin-top: 0;
            align-self: flex-start;
        }
    }

    /* Dark Mode Support */
    @media (prefers-color-scheme: dark) {
        .complex-card {
            background: #1f2937;
            border-color: #374151;
        }
        
        .complex-title {
            color: #f9fafb;
        }
        
        .complex-address {
            color: #d1d5db;
        }
        
        .detail-item {
            background: #374151;
            color: #9ca3af;
        }
        
        .complex-image-container {
            background: linear-gradient(135deg, #111827, #1e3a8a);
        }
    }

    /* High Contrast Mode */
    @media (prefers-contrast: high) {
        .complex-card {
            border: 2px solid currentColor;
        }
        
        .btn-register {
            border: 2px solid currentColor;
        }
    }

    /* Reduce Motion */
    @media (prefers-reduced-motion: reduce) {
        .complex-card,
        .btn-register,
        .complex-image {
            transition: none;
        }
        
        .complex-card:hover {
            transform: none;
        }
        
        .btn-register:hover {
            transform: none;
        }
    }

    /* Touch Device Optimizations */
    @media (hover: none) and (pointer: coarse) {
        .complex-card:hover {
            transform: none;
            box-shadow: var(--card-shadow);
        }
        
        .btn-register:active {
            transform: scale(0.98);
        }
        
        /* زيادة مساحة اللمس */
        .btn-register {
            min-height: 44px;
        }
        
        .detail-item {
            min-height: 32px;
            min-width: 32px;
            align-items: center;
            justify-content: center;
        }
    }

    /* Print Styles */
    @media print {
        .complex-card {
            break-inside: avoid;
            box-shadow: none;
            border: 1px solid #ddd;
        }
        
        .btn-register {
            display: none;
        }
        
        .complex-badge {
            display: none;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // تحسين تجربة اللمس للأجهزة المحمولة
    const complexCards = document.querySelectorAll('.complex-card');
    
    complexCards.forEach(card => {
        // إضافة تأثير النقر للجوال
        card.addEventListener('touchstart', function() {
            this.classList.add('touch-active');
        }, { passive: true });
        
        card.addEventListener('touchend', function() {
            this.classList.remove('touch-active');
        }, { passive: true });
        
        // منع التمرير العرضي عند السحب على البطاقة
        let startX, startY;
        
        card.addEventListener('touchstart', function(e) {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
        }, { passive: true });
        
        card.addEventListener('touchmove', function(e) {
            if (!startX || !startY) return;
            
            const diffX = Math.abs(e.touches[0].clientX - startX);
            const diffY = Math.abs(e.touches[0].clientY - startY);
            
            // إذا كان السحب رأسيًا أكثر من أفقيًا، منع السلوك الافتراضي
            if (diffY > diffX && diffY > 10) {
                e.preventDefault();
            }
        }, { passive: false });
    });
    
    // تحميل الصور بتقنية Lazy Loading
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src || img.src;
                    img.classList.add('loaded');
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px 0px',
            threshold: 0.1
        });
        
        document.querySelectorAll('.complex-image').forEach(img => {
            if (!img.classList.contains('loaded')) {
                imageObserver.observe(img);
            }
        });
    }
    
    // تحسين أزرار التسجيل للشاشات الصغيرة
    const registerButtons = document.querySelectorAll('.btn-register');
    
    registerButtons.forEach(button => {
        // زيادة مساحة اللمس
        button.style.minHeight = '44px';
        button.style.minWidth = '44px';
        
        // تأثير النقر للجوال
        button.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.98)';
        }, { passive: true });
        
        button.addEventListener('touchend', function() {
            this.style.transform = '';
        }, { passive: true });
    });
    
    // تحسين عرض العناوين والعناوين للشاشات الصغيرة
    function adjustTextForMobile() {
        const isMobile = window.innerWidth <= 768;
        const titles = document.querySelectorAll('.complex-title');
        const addresses = document.querySelectorAll('.complex-address');
        
        if (isMobile) {
            titles.forEach(title => {
                title.style.fontSize = '0.95rem';
                title.style.lineHeight = '1.4';
            });
            
            addresses.forEach(address => {
                address.style.fontSize = '0.75rem';
                address.style.lineHeight = '1.4';
            });
        } else {
            titles.forEach(title => {
                title.style.fontSize = '';
                title.style.lineHeight = '';
            });
            
            addresses.forEach(address => {
                address.style.fontSize = '';
                address.style.lineHeight = '';
            });
        }
    }
    
    // استدعاء الدالة عند تحميل الصفحة وتغيير حجم النافذة
    window.addEventListener('load', adjustTextForMobile);
    window.addEventListener('resize', adjustTextForMobile);
    
    // إضافة مؤشر التحميل عند الضغط على زر التسجيل
    registerButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const originalHTML = this.innerHTML;
            const complexId = this.dataset.complexId;
            const complexName = this.dataset.complexName;
            
            // عرض مؤشر التحميل
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري التحميل...';
            this.disabled = true;
            
            // استعادة النص الأصلي بعد 2 ثانية (محاكاة)
            setTimeout(() => {
                this.innerHTML = originalHTML;
                this.disabled = false;
                
                // هنا يمكنك إضافة منطق التسجيل الفعلي
                console.log(`تسجيل في المركب: ${complexName} (ID: ${complexId})`);
            }, 2000);
            
            // منع السلوك الافتراضي إذا كان الرابط
            if (this.tagName === 'A') {
                e.preventDefault();
            }
        });
    });
});
</script>
