@extends('layouts.admin')

@section('title', 'Pengaturan')

@push('styles')
<style>
    /* ========== DESIGN TOKENS ========== */
    :root {
        --settings-bg: #f8f5f0;
        --card-bg: #ffffff;
        --card-border: rgba(0,0,0,0.06);
        --text-primary: #1a1a2e;
        --text-secondary: #6b7280;
        --text-muted: #9ca3af;
        --accent: #e8723a;
        --accent-light: #fff0e8;
        --accent-glow: rgba(232, 114, 58, 0.3);
        --toggle-bg: #e5e7eb;
        --toggle-active: #e8723a;
        --section-divider: rgba(0,0,0,0.05);
        --chevron-color: #d1d5db;
        --input-bg: #f9fafb;
        --input-border: #e5e7eb;
        --save-btn: #22c55e;
        --save-btn-hover: #16a34a;
        --preview-light-bg: #fef7f0;
        --preview-light-card: #ffffff;
        --preview-dark-bg: #1e1e2e;
        --preview-dark-card: #2a2a3e;
        --profile-gradient-1: #e8723a;
        --profile-gradient-2: #f59e0b;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
        --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
        --shadow-lg: 0 8px 30px rgba(0,0,0,0.12);
        --radius-sm: 10px;
        --radius-md: 16px;
        --radius-lg: 24px;
        --radius-full: 9999px;
    }

    [data-theme="dark"] {
        --settings-bg: #121218;
        --card-bg: #1e1e2e;
        --card-border: rgba(255,255,255,0.06);
        --text-primary: #f1f1f4;
        --text-secondary: #a0a3b1;
        --text-muted: #6b6e7e;
        --accent: #e8723a;
        --accent-light: rgba(232,114,58,0.15);
        --toggle-bg: #2a2a3e;
        --section-divider: rgba(255,255,255,0.06);
        --chevron-color: #4a4a5e;
        --input-bg: #2a2a3e;
        --input-border: #3a3a4e;
        --preview-light-bg: #fef7f0;
        --preview-light-card: #ffffff;
        --preview-dark-bg: #1e1e2e;
        --preview-dark-card: #2a2a3e;
    }

    /* ========== SETTINGS CONTAINER ========== */
    .settings-container {
        max-width: 480px;
        margin: 0 auto;
        padding: 8px 16px 100px;
        background: var(--settings-bg);
        min-height: 100vh;
        transition: background-color 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    /* ========== HEADER ========== */
    .settings-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 0;
        margin-bottom: 4px;
    }

    .settings-header h1 {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.3px;
        transition: color 0.3s;
    }

    .settings-close-btn {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-full);
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        color: var(--text-secondary);
    }

    .settings-close-btn:hover {
        background: var(--accent-light);
        color: var(--accent);
    }

    /* ========== PROFILE CARD ========== */
    .profile-card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: var(--radius-lg);
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .profile-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--profile-gradient-1), var(--profile-gradient-2));
        opacity: 0;
        transition: opacity 0.3s;
    }

    .profile-card:hover::before {
        opacity: 1;
    }

    .profile-avatar {
        width: 56px;
        height: 56px;
        border-radius: var(--radius-md);
        background: linear-gradient(135deg, var(--profile-gradient-1), var(--profile-gradient-2));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        font-weight: 700;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(232, 114, 58, 0.3);
        position: relative;
        overflow: hidden;
    }

    .profile-avatar::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.15) 50%, transparent 70%);
        animation: shimmer 3s ease-in-out infinite;
    }

    @keyframes shimmer {
        0%, 100% { transform: translateX(-100%) rotate(45deg); }
        50% { transform: translateX(100%) rotate(45deg); }
    }

    .profile-info {
        flex: 1;
        min-width: 0;
    }

    .profile-name {
        font-size: 17px;
        font-weight: 700;
        color: var(--text-primary);
        transition: color 0.3s;
        margin-bottom: 2px;
    }

    .profile-email {
        font-size: 13px;
        color: var(--text-muted);
        transition: color 0.3s;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .profile-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: var(--radius-full);
        background: var(--accent-light);
        color: var(--accent);
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    /* ========== THEME SWITCHER ========== */
    .theme-switcher-card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: var(--radius-lg);
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .theme-previews {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 16px;
    }

    .theme-preview {
        border-radius: var(--radius-md);
        padding: 16px 12px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        border: 2px solid transparent;
    }

    .theme-preview.light-preview {
        background: var(--preview-light-bg);
    }

    .theme-preview.dark-preview {
        background: var(--preview-dark-bg);
    }

    .theme-preview.active {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px var(--accent-glow);
    }

    .theme-preview:hover:not(.active) {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .preview-mockup {
        margin-bottom: 12px;
    }

    .preview-line {
        height: 6px;
        border-radius: 3px;
        margin-bottom: 6px;
    }

    .light-preview .preview-line {
        background: rgba(0,0,0,0.08);
    }

    .light-preview .preview-line:first-child {
        width: 70%;
        background: var(--accent);
        opacity: 0.5;
    }

    .light-preview .preview-line:nth-child(2) {
        width: 100%;
    }

    .light-preview .preview-line:nth-child(3) {
        width: 50%;
    }

    .dark-preview .preview-line {
        background: rgba(255,255,255,0.1);
    }

    .dark-preview .preview-line:first-child {
        width: 70%;
        background: var(--accent);
        opacity: 0.5;
    }

    .dark-preview .preview-line:nth-child(2) {
        width: 100%;
    }

    .dark-preview .preview-line:nth-child(3) {
        width: 50%;
    }

    .preview-card-mini {
        width: 100%;
        height: 24px;
        border-radius: 6px;
        margin-bottom: 6px;
    }

    .light-preview .preview-card-mini {
        background: var(--preview-light-card);
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }

    .dark-preview .preview-card-mini {
        background: var(--preview-dark-card);
        box-shadow: 0 1px 4px rgba(0,0,0,0.3);
    }

    .theme-label {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        transition: color 0.3s;
    }

    .theme-label .theme-icon {
        font-size: 16px;
    }

    /* ========== SECTION TITLES ========== */
    .section-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1.2px;
        padding: 0 4px;
        margin-bottom: 10px;
        transition: color 0.3s;
    }

    /* ========== TOGGLE CARD ========== */
    .settings-card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: var(--radius-lg);
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .toggle-item {
        display: flex;
        align-items: center;
        padding: 16px 20px;
        gap: 14px;
        transition: background 0.2s;
    }

    .toggle-item:not(:last-child) {
        border-bottom: 1px solid var(--section-divider);
    }

    .toggle-item:hover {
        background: var(--accent-light);
    }

    .toggle-icon {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
        background: var(--accent-light);
    }

    .toggle-info {
        flex: 1;
        min-width: 0;
    }

    .toggle-title {
        font-size: 15px;
        font-weight: 500;
        color: var(--text-primary);
        transition: color 0.3s;
    }

    .toggle-desc {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 2px;
        transition: color 0.3s;
    }

    /* Custom Toggle Switch */
    .toggle-switch {
        position: relative;
        width: 50px;
        height: 28px;
        flex-shrink: 0;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
        position: absolute;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: var(--toggle-bg);
        border-radius: 14px;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .toggle-slider::before {
        content: '';
        position: absolute;
        height: 22px;
        width: 22px;
        left: 3px;
        bottom: 3px;
        background: white;
        border-radius: 50%;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 4px rgba(0,0,0,0.15);
    }

    .toggle-switch input:checked + .toggle-slider {
        background: var(--toggle-active);
        box-shadow: 0 0 12px var(--accent-glow);
    }

    .toggle-switch input:checked + .toggle-slider::before {
        transform: translateX(22px);
    }

    /* ========== MENU ITEMS (Account-style) ========== */
    .menu-item {
        display: flex;
        align-items: center;
        padding: 16px 20px;
        gap: 14px;
        cursor: pointer;
        transition: all 0.2s;
        user-select: none;
    }

    .menu-item:not(:last-child) {
        border-bottom: 1px solid var(--section-divider);
    }

    .menu-item:hover {
        background: var(--accent-light);
    }

    .menu-item:active {
        transform: scale(0.99);
    }

    .menu-icon {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
        background: var(--accent-light);
    }

    .menu-item-info {
        flex: 1;
        min-width: 0;
    }

    .menu-item-title {
        font-size: 15px;
        font-weight: 500;
        color: var(--text-primary);
        transition: color 0.3s;
    }

    .menu-item-subtitle {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 2px;
        transition: color 0.3s;
    }

    .menu-chevron {
        color: var(--chevron-color);
        transition: all 0.3s;
        flex-shrink: 0;
    }

    .menu-item.expanded .menu-chevron {
        transform: rotate(90deg);
        color: var(--accent);
    }

    /* ========== EXPANDABLE PANEL ========== */
    .expand-panel {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), padding 0.3s;
        background: var(--input-bg);
        border-top: 1px solid var(--section-divider);
    }

    .expand-panel.open {
        max-height: 600px;
        padding: 20px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 6px;
        transition: color 0.3s;
    }

    .form-input {
        width: 100%;
        padding: 10px 14px;
        background: var(--card-bg);
        border: 1.5px solid var(--input-border);
        border-radius: var(--radius-sm);
        font-size: 14px;
        color: var(--text-primary);
        transition: all 0.3s;
        outline: none;
        font-family: 'Inter', sans-serif;
    }

    .form-input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px var(--accent-glow);
    }

    .form-input::placeholder {
        color: var(--text-muted);
    }

    .form-hint {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 4px;
        transition: color 0.3s;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    textarea.form-input {
        resize: vertical;
        min-height: 70px;
    }

    /* ========== SAVE BUTTON ========== */
    .save-btn-container {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 16px;
        background: linear-gradient(to top, var(--settings-bg) 70%, transparent);
        z-index: 100;
        display: flex;
        justify-content: center;
        pointer-events: none;
    }

    .save-btn {
        max-width: 480px;
        width: 100%;
        padding: 16px 32px;
        background: linear-gradient(135deg, var(--save-btn), #10b981);
        color: white;
        font-size: 16px;
        font-weight: 700;
        border: none;
        border-radius: var(--radius-md);
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 16px rgba(34, 197, 94, 0.35);
        pointer-events: all;
        position: relative;
        overflow: hidden;
        letter-spacing: 0.3px;
    }

    .save-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
        transition: left 0.5s;
    }

    .save-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 24px rgba(34, 197, 94, 0.45);
    }

    .save-btn:hover::before {
        left: 100%;
    }

    .save-btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(34, 197, 94, 0.3);
    }

    /* ========== CIRCULAR REVEAL ANIMATION ========== */
    .theme-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 9999;
        pointer-events: none;
    }

    .theme-overlay.light-reveal {
        background: #f8f5f0;
    }

    .theme-overlay.dark-reveal {
        background: #121218;
    }

    @keyframes circularReveal {
        0% { clip-path: circle(0% at var(--cx) var(--cy)); }
        100% { clip-path: circle(150% at var(--cx) var(--cy)); }
    }

    .theme-overlay.animate {
        animation: circularReveal 0.7s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    /* ========== ENTRANCE ANIMATIONS ========== */
    @keyframes fadeSlideUp {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-in {
        animation: fadeSlideUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) both;
    }

    .delay-1 { animation-delay: 0.05s; }
    .delay-2 { animation-delay: 0.10s; }
    .delay-3 { animation-delay: 0.15s; }
    .delay-4 { animation-delay: 0.20s; }
    .delay-5 { animation-delay: 0.25s; }
    .delay-6 { animation-delay: 0.30s; }

    /* ========== TOAST / SUCCESS ========== */
    .toast-notification {
        position: fixed;
        top: 80px;
        left: 50%;
        transform: translateX(-50%) translateY(-20px);
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: var(--radius-md);
        padding: 14px 24px;
        box-shadow: var(--shadow-lg);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        font-weight: 500;
        color: var(--text-primary);
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .toast-notification.show {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }

    .toast-icon {
        font-size: 20px;
    }

    /* ========== RESPONSIVE ========== */
    @media (min-width: 768px) {
        .settings-container {
            padding: 16px 24px 100px;
        }

        .save-btn-container {
            left: 264px; /* account for sidebar */
        }
    }

    @media (min-width: 1072px) {
        .save-btn-container {
            left: 264px;
        }
    }
</style>
@endpush

@section('content')
<form action="{{ route('admin.settings.update') }}" method="POST" id="settingsForm">
    @csrf

    {{-- Hidden field for is_closed when unchecked --}}
    <input type="hidden" name="is_closed" value="0">
    <input type="hidden" name="delivery_fee_enabled" value="0">
    <input type="hidden" name="discount_enabled" value="0">

    <div class="settings-container">

        {{-- Toast Notification --}}
        @if(session('success'))
        <div class="toast-notification show" id="toast">
            <span class="toast-icon">✅</span>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        {{-- Header --}}
        <div class="settings-header animate-in">
            <h1>Settings</h1>
            <a href="{{ route('admin.dashboard') }}" class="settings-close-btn" title="Kembali ke Dashboard">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </a>
        </div>

        {{-- Profile Card --}}
        <div class="profile-card animate-in delay-1">
            <div class="profile-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="profile-info">
                <div class="profile-name">{{ auth()->user()->name }}</div>
                <div class="profile-email">{{ auth()->user()->email }}</div>
            </div>
            <span class="profile-badge">{{ ucfirst(auth()->user()->role) }}</span>
        </div>

        {{-- Theme Switcher --}}
        <div class="theme-switcher-card animate-in delay-2">
            <div class="theme-previews">
                <div class="theme-preview light-preview active" id="lightPreview" onclick="switchTheme('light', event)">
                    <div class="preview-mockup">
                        <div class="preview-line"></div>
                        <div class="preview-card-mini"></div>
                        <div class="preview-line"></div>
                        <div class="preview-line"></div>
                        <div class="preview-card-mini"></div>
                    </div>
                    <div class="theme-label">
                        <span class="theme-icon">☀️</span>
                        <span>Daylight</span>
                    </div>
                </div>
                <div class="theme-preview dark-preview" id="darkPreview" onclick="switchTheme('dark', event)">
                    <div class="preview-mockup">
                        <div class="preview-line"></div>
                        <div class="preview-card-mini"></div>
                        <div class="preview-line"></div>
                        <div class="preview-line"></div>
                        <div class="preview-card-mini"></div>
                    </div>
                    <div class="theme-label">
                        <span class="theme-icon">🌙</span>
                        <span>Midnight</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Preferences Section --}}
        <div class="section-label animate-in delay-3">PREFERENCES</div>
        <div class="settings-card animate-in delay-3">
            {{-- Notifikasi Order --}}
            <div class="toggle-item">
                <div class="toggle-icon">🔔</div>
                <div class="toggle-info">
                    <div class="toggle-title">Notifikasi Order</div>
                    <div class="toggle-desc">Terima notifikasi order baru</div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="notifToggle" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            {{-- Tutup Toko --}}
            <div class="toggle-item">
                <div class="toggle-icon">🔒</div>
                <div class="toggle-info">
                    <div class="toggle-title">Tutup Toko</div>
                    <div class="toggle-desc">Pesanan otomatis nonaktif</div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="is_closed" value="1" {{ ($settings['is_closed'] ?? 'false') == 'true' ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            {{-- Notifikasi WhatsApp --}}
            <div class="toggle-item">
                <div class="toggle-icon">💬</div>
                <div class="toggle-info">
                    <div class="toggle-title">Notifikasi WhatsApp</div>
                    <div class="toggle-desc">Kirim update status via WA</div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="waToggle" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            {{-- Biaya Ongkir --}}
            <div class="toggle-item">
                <div class="toggle-icon">🚚</div>
                <div class="toggle-info">
                    <div class="toggle-title">Biaya Ongkir (Delivery)</div>
                    <div class="toggle-desc">Aktifkan biaya ongkir untuk pengiriman</div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="delivery_fee_enabled" value="1" {{ ($settings['delivery_fee_enabled'] ?? 'true') == 'true' ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            {{-- Nominal Ongkir (appears inline) --}}
            <div class="menu-item" onclick="toggleExpand('ongkirPanel', this)">
                <div class="menu-icon">💰</div>
                <div class="menu-item-info">
                    <div class="menu-item-title">Nominal Ongkir</div>
                    <div class="menu-item-subtitle">Rp {{ number_format((int)($settings['delivery_fee_amount'] ?? 10000), 0, ',', '.') }}</div>
                </div>
                <svg class="menu-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </div>
            <div class="expand-panel" id="ongkirPanel">
                <div class="form-group">
                    <label class="form-label">Biaya Ongkir (Rp)</label>
                    <input type="number" name="delivery_fee_amount" value="{{ $settings['delivery_fee_amount'] ?? '10000' }}" min="0" class="form-input" placeholder="10000">
                    <div class="form-hint">Biaya ongkir yang dikenakan saat customer pilih pengiriman (delivery). Set 0 jika gratis ongkir.</div>
                </div>
            </div>

            {{-- Diskon Otomatis --}}
            <div class="toggle-item">
                <div class="toggle-icon">🏷️</div>
                <div class="toggle-info">
                    <div class="toggle-title">Diskon Otomatis (10% OFF)</div>
                    <div class="toggle-desc">Aktifkan diskon 10% untuk semua pesanan</div>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="discount_enabled" value="1" {{ ($settings['discount_enabled'] ?? 'true') == 'true' ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>

        {{-- Account Section --}}
        <div class="section-label animate-in delay-4">PENGATURAN TOKO</div>
        <div class="settings-card animate-in delay-4">

            {{-- Informasi Toko --}}
            <div class="menu-item" onclick="toggleExpand('infoPanel', this)">
                <div class="menu-icon">🏪</div>
                <div class="menu-item-info">
                    <div class="menu-item-title">Informasi Toko</div>
                    <div class="menu-item-subtitle">Nama, telepon, alamat</div>
                </div>
                <svg class="menu-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </div>
            <div class="expand-panel" id="infoPanel">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nama Toko</label>
                        <input type="text" name="store_name" value="{{ $settings['store_name'] ?? '' }}" class="form-input" placeholder="Mamitha Bakery">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" name="store_phone" value="{{ $settings['store_phone'] ?? '' }}" class="form-input" placeholder="08xx-xxxx-xxxx">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nomor WhatsApp</label>
                        <input type="text" name="store_whatsapp" value="{{ $settings['store_whatsapp'] ?? '' }}" class="form-input" placeholder="62812xxxx">
                        <div class="form-hint">Format: 62812xxxx (tanpa + dan spasi)</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="store_email" value="{{ $settings['store_email'] ?? '' }}" class="form-input" placeholder="info@mamitha.com">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat Toko</label>
                    <textarea name="store_address" rows="2" class="form-input" placeholder="Alamat lengkap toko">{{ $settings['store_address'] ?? '' }}</textarea>
                </div>
            </div>

            {{-- Jam Operasional --}}
            <div class="menu-item" onclick="toggleExpand('hoursPanel', this)">
                <div class="menu-icon">🕐</div>
                <div class="menu-item-info">
                    <div class="menu-item-title">Jam Operasional</div>
                    <div class="menu-item-subtitle">{{ $settings['open_time'] ?? '07:00' }} - {{ $settings['close_time'] ?? '20:00' }}</div>
                </div>
                <svg class="menu-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </div>
            <div class="expand-panel" id="hoursPanel">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Jam Buka</label>
                        <input type="time" name="open_time" value="{{ $settings['open_time'] ?? '07:00' }}" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jam Tutup</label>
                        <input type="time" name="close_time" value="{{ $settings['close_time'] ?? '20:00' }}" class="form-input">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Batas Order Harian</label>
                    <input type="number" name="daily_order_limit" value="{{ $settings['daily_order_limit'] ?? '0' }}" min="0" class="form-input" placeholder="0">
                    <div class="form-hint">0 = tidak ada batasan order harian</div>
                </div>
            </div>

            {{-- Google Maps --}}
            <div class="menu-item" onclick="toggleExpand('mapsPanel', this)">
                <div class="menu-icon">📍</div>
                <div class="menu-item-info">
                    <div class="menu-item-title">Google Maps</div>
                    <div class="menu-item-subtitle">API key dan koordinat toko</div>
                </div>
                <svg class="menu-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </div>
            <div class="expand-panel" id="mapsPanel">
                <div class="form-group">
                    <label class="form-label">API Key Google Maps</label>
                    <input type="text" name="google_maps_api_key" value="{{ $settings['google_maps_api_key'] ?? '' }}" class="form-input" placeholder="AIzaSy...">
                    <div class="form-hint">Dapatkan API key di <a href="https://console.cloud.google.com" target="_blank" style="color: var(--accent);">Google Cloud Console</a></div>
                </div>
                <div class="form-group">
                    <label class="form-label">Link Google Maps Toko (Mengisi Otomatis Koordinat)</label>
                    <input type="text" name="store_gmaps_link" value="{{ $settings['store_gmaps_link'] ?? '' }}" class="form-input" placeholder="https://maps.app.goo.gl/... atau https://google.com/maps/place/...">
                    <div class="form-hint">Jika diisi, Latitude dan Longitude di bawah akan terisi otomatis secara instan dari link Google Maps ini setelah disimpan.</div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="store_latitude" value="{{ $settings['store_latitude'] ?? '-7.7705163' }}" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="store_longitude" value="{{ $settings['store_longitude'] ?? '110.2474903' }}" class="form-input">
                    </div>
                </div>
            </div>

            {{-- Pengaturan Kurir --}}
            <div class="menu-item" onclick="toggleExpand('courierPanel', this)">
                <div class="menu-icon">🛵</div>
                <div class="menu-item-info">
                    <div class="menu-item-title">Pengaturan Kurir</div>
                    <div class="menu-item-subtitle">Nama dan nomor kontak kurir pelacak</div>
                </div>
                <svg class="menu-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </div>
            <div class="expand-panel" id="courierPanel">
                <div class="form-group">
                    <label class="form-label">Nama Kurir</label>
                    <input type="text" name="courier_name" value="{{ $settings['courier_name'] ?? 'Pak Budi (Mamitha Courier)' }}" class="form-input" placeholder="Pak Budi (Mamitha Courier)">
                </div>
                <div class="form-group">
                    <label class="form-label">Nomor Kontak Kurir (WhatsApp/Telp)</label>
                    <input type="text" name="courier_phone" value="{{ $settings['courier_phone'] ?? '6281234567890' }}" class="form-input" placeholder="62812xxxx">
                    <div class="form-hint">Format: 62812xxxx (tanpa + dan spasi) untuk tombol chat & panggilan</div>
                </div>
            </div>

            {{-- Tentang Toko --}}
            <div class="menu-item" onclick="toggleExpand('aboutPanel', this)">
                <div class="menu-icon">📝</div>
                <div class="menu-item-info">
                    <div class="menu-item-title">Tentang Toko</div>
                    <div class="menu-item-subtitle">Deskripsi toko Anda</div>
                </div>
                <svg class="menu-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </div>
            <div class="expand-panel" id="aboutPanel">
                <div class="form-group">
                    <label class="form-label">Tentang Toko</label>
                    <textarea name="about_text" rows="4" class="form-input" placeholder="Ceritakan tentang toko Anda...">{{ $settings['about_text'] ?? '' }}</textarea>
                </div>
            </div>
        </div>

        {{-- Save Button --}}
        <div class="save-btn-container">
            <button type="submit" class="save-btn animate-in delay-6" id="saveBtn">
                💾 Simpan Pengaturan
            </button>
        </div>

    </div>
</form>
@endsection

@push('scripts')
<script>
    // ========== THEME SWITCHER WITH CIRCULAR REVEAL ==========
    let currentTheme = localStorage.getItem('admin-theme') || 'light';

    // Apply saved theme on load
    if (currentTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        document.getElementById('darkPreview')?.classList.add('active');
        document.getElementById('lightPreview')?.classList.remove('active');
    }

    function switchTheme(theme, event) {
        if (theme === currentTheme) return;

        const cx = event.clientX;
        const cy = event.clientY;

        // Create overlay
        const overlay = document.createElement('div');
        overlay.className = 'theme-overlay';
        overlay.classList.add(theme === 'dark' ? 'dark-reveal' : 'light-reveal');
        overlay.style.setProperty('--cx', cx + 'px');
        overlay.style.setProperty('--cy', cy + 'px');
        document.body.appendChild(overlay);

        // Start animation
        requestAnimationFrame(() => {
            overlay.classList.add('animate');
        });

        // Apply theme midway through animation
        setTimeout(() => {
            document.documentElement.setAttribute('data-theme', theme);
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            currentTheme = theme;
            localStorage.setItem('admin-theme', theme);

            // Update preview active states
            document.getElementById('lightPreview').classList.toggle('active', theme === 'light');
            document.getElementById('darkPreview').classList.toggle('active', theme === 'dark');
        }, 250);

        // Remove overlay after animation
        setTimeout(() => {
            overlay.remove();
        }, 800);
    }

    // ========== EXPAND/COLLAPSE PANELS ==========
    function toggleExpand(panelId, menuItem) {
        const panel = document.getElementById(panelId);
        const isOpen = panel.classList.contains('open');

        // Close all panels first
        document.querySelectorAll('.expand-panel').forEach(p => p.classList.remove('open'));
        document.querySelectorAll('.menu-item').forEach(m => m.classList.remove('expanded'));

        // Open if it was closed
        if (!isOpen) {
            panel.classList.add('open');
            menuItem.classList.add('expanded');

            // Smooth scroll to panel
            setTimeout(() => {
                panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 100);
        }
    }

    // ========== TOAST AUTO-HIDE ==========
    const toast = document.getElementById('toast');
    if (toast) {
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 400);
        }, 3000);
    }

    // ========== SAVE BUTTON ANIMATION ==========
    document.getElementById('settingsForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('saveBtn');
        btn.textContent = '⏳ Menyimpan...';
        btn.style.opacity = '0.7';
        btn.style.pointerEvents = 'none';
    });
</script>
@endpush
