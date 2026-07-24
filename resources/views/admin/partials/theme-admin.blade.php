@push('styles')
<style>
/* ===== Thème Clair 2026 - Light Mode Premium ===== */
:root {
    --bg-light: #f8fafc;
    --card-bg: #ffffff;
    --input-bg: #ffffff;
    --input-border: #e2e8f0;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --label-color: #3b82f6;
    --accent: #3b82f6;
    --accent-hover: #2563eb;
    --primary-gradient: linear-gradient(135deg, #3b82f6, #60a5fa);
    --shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    --border: 1px solid #e2e8f0;
}

body {
    background: var(--bg-light);
    color: var(--text-primary);
}

.card-modern {
    background: var(--card-bg);
    border: var(--border);
    box-shadow: var(--shadow);
    border-radius: 1rem;
}

.card-header {
    background: transparent !important;
    border-bottom: none;
    padding-bottom: 0;
}

.card-header h3 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.card-header p {
    color: var(--text-secondary);
    font-size: 1rem;
}

.form-label {
    color: var(--label-color);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.form-control-modern,
.form-select {
    background: var(--input-bg);
    border: 1px solid var(--input-border);
    color: var(--text-primary);
    border-radius: 0.75rem;
    padding: 0.85rem 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.form-control-modern:focus,
.form-select:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
    background: white;
}

.btn-glow {
    background: var(--primary-gradient);
    color: white;
    border: none;
    transition: all 0.4s ease;
}

.btn-glow:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
}

.image-circle {
    width: 170px;
    height: 170px;
    border-radius: 50%;
    background: #f1f5f9;
    border: 3px dashed #cbd5e1;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.4s ease;
    cursor: pointer;
    display:flex;
    align-items:center;
    justify-content:center;
}

.image-circle:hover {
    border-color: var(--accent);
    transform: scale(1.05);
}

.image-circle img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}
</style>
@endpush
