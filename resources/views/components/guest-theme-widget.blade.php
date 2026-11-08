{{-- Guest User Theme & Display Customizer Widget (Draggable FAB + Settings Modal) --}}
@if(!auth()->check() || in_array(auth()->user()->role, ['student', 'admin']))
<div id="guestThemeWidgetContainer">
    <!-- Draggable Floating Action Button Trigger -->
    <div id="guestThemeTriggerWrap" class="guest-fab-wrap" style="bottom: 28px; right: 28px;" title="Display Settings (Click to open, Drag to move)">
        <button type="button" id="guestThemeFab" class="guest-theme-fab" aria-label="Open Display Settings">
            <span class="guest-fab-pulse"></span>
            <div class="guest-fab-icon-inner">
                <!-- Palette / Sliders SVG Icon -->
                <svg class="guest-fab-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/>
                    <circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/>
                    <circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/>
                    <circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/>
                    <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.563-2.512 5.563-5.563C22 6.5 17.5 2 12 2z"/>
                </svg>
            </div>
            <span class="guest-fab-drag-hint">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor">
                    <circle cx="5" cy="5" r="2.5"/><circle cx="12" cy="5" r="2.5"/><circle cx="19" cy="5" r="2.5"/>
                    <circle cx="5" cy="12" r="2.5"/><circle cx="12" cy="12" r="2.5"/><circle cx="19" cy="12" r="2.5"/>
                    <circle cx="5" cy="19" r="2.5"/><circle cx="12" cy="19" r="2.5"/><circle cx="19" cy="19" r="2.5"/>
                </svg>
            </span>
        </button>
    </div>

    <!-- Theme Settings Modal Overlay -->
    <div id="guestThemeModalOverlay" class="guest-modal-backdrop" onclick="handleThemeModalBackdrop(event)">
        <div class="guest-theme-dialog" role="dialog" aria-modal="true" aria-labelledby="themeDialogTitle">
            <!-- Modal Header -->
            <div class="guest-dialog-header">
                <div class="guest-dialog-title-group">
                    <div class="guest-dialog-badge">
                        <span class="pulse-dot"></span>
                        {{ auth()->check() ? (auth()->user()->role === 'admin' ? 'ADMIN DISPLAY' : 'STUDENT DISPLAY') : 'GUEST DISPLAY' }}
                    </div>
                    <h3 id="themeDialogTitle" class="guest-dialog-title">Appearance & Reading Settings</h3>
                    <p class="guest-dialog-subtitle">Personalize reading colors, typography, and contrast.</p>
                </div>
                <button type="button" class="guest-dialog-close" onclick="closeGuestThemeModal()" aria-label="Close dialog">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <div class="guest-dialog-body">
                <!-- SECTION 1: THEME MODES -->
                <div class="guest-setting-section">
                    <div class="guest-section-header">
                        <span class="guest-section-title">Theme Mode</span>
                    </div>

                    <div class="guest-theme-grid">
                        <!-- Clean Light (Default) -->
                        <button type="button" class="guest-theme-card active" data-mode="light" onclick="selectThemeMode('light')">
                            <span class="theme-swatch swatch-light"></span>
                            <span class="theme-card-name">Clean Light (Default)</span>
                            <span class="theme-active-check">✓</span>
                        </button>

                        <!-- Warm Sepia -->
                        <button type="button" class="guest-theme-card" data-mode="sepia" onclick="selectThemeMode('sepia')">
                            <span class="theme-swatch swatch-sepia"></span>
                            <span class="theme-card-name">Warm Sepia</span>
                            <span class="theme-active-check">✓</span>
                        </button>

                        <!-- Custom Colors Mode -->
                        <button type="button" class="guest-theme-card" data-mode="custom" onclick="selectThemeMode('custom')">
                            <span class="theme-swatch swatch-custom"></span>
                            <span class="theme-card-name">Custom Canvas</span>
                            <span class="theme-active-check">✓</span>
                        </button>
                    </div>
                </div>

                <!-- SECTION 2: CUSTOM COLOR PICKER (SHOWN ONLY ON CUSTOM CANVAS) -->
                <div id="customColorBox" class="guest-setting-section custom-color-panel" style="display: none;">
                    <div class="guest-section-header">
                        <span class="guest-section-title">Custom Palette Tuning</span>
                    </div>

                    <div class="custom-pickers-row">
                        <!-- Background Color -->
                        <div class="color-picker-item">
                            <label for="customBgInput" class="color-picker-label">
                                <span>Background</span>
                                <span id="customBgHex" class="hex-badge">#0b1119</span>
                            </label>
                            <div class="color-input-wrap">
                                <input type="color" id="customBgInput" value="#0b1119" oninput="onCustomBgChange(this.value)" class="native-color-picker">
                                <input type="text" id="customBgText" value="#0b1119" maxlength="7" onchange="onCustomBgTextChange(this.value)" class="hex-text-input">
                            </div>
                        </div>

                        <!-- Text Color -->
                        <div class="color-picker-item">
                            <label for="customTextInput" class="color-picker-label">
                                <span>Text Color</span>
                                <span id="customTextHex" class="hex-badge">#ffffff</span>
                            </label>
                            <div class="color-input-wrap">
                                <input type="color" id="customTextInput" value="#ffffff" oninput="onCustomTextChange(this.value)" class="native-color-picker">
                                <input type="text" id="customTextText" value="#ffffff" maxlength="7" onchange="onCustomTextTextChange(this.value)" class="hex-text-input">
                            </div>
                        </div>
                    </div>

                    <!-- Live Contrast & Readability Indicator -->
                    <div class="contrast-feedback-card" id="contrastIndicator">
                        <div class="contrast-header">
                            <div class="contrast-status-badge" id="contrastBadge">
                                <span class="contrast-dot"></span>
                                <span id="contrastText">Optimal Readability</span>
                            </div>
                            <button type="button" class="btn-auto-contrast" onclick="autoFixContrast()">
                                Auto-Tune Contrast
                            </button>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: ACCENT COLOR SELECTION -->
                <div class="guest-setting-section">
                    <div class="guest-section-header">
                        <span class="guest-section-title">Accent Color</span>
                    </div>

                    <div class="accent-swatches-grid">
                        <button type="button" class="accent-swatch-btn" data-accent="#00f5d4" style="--swatch-color: #00f5d4;" onclick="selectAccentColor('#00f5d4')" title="Cyber Cyan">
                            <span class="swatch-circle"></span>
                            <span class="swatch-label">Cyan</span>
                        </button>
                        <button type="button" class="accent-swatch-btn" data-accent="#10b981" style="--swatch-color: #10b981;" onclick="selectAccentColor('#10b981')" title="Emerald Green">
                            <span class="swatch-circle"></span>
                            <span class="swatch-label">Emerald</span>
                        </button>
                        <button type="button" class="accent-swatch-btn" data-accent="#38bdf8" style="--swatch-color: #38bdf8;" onclick="selectAccentColor('#38bdf8')" title="Sky Blue">
                            <span class="swatch-circle"></span>
                            <span class="swatch-label">Sky</span>
                        </button>
                        <button type="button" class="accent-swatch-btn" data-accent="#a855f7" style="--swatch-color: #a855f7;" onclick="selectAccentColor('#a855f7')" title="Purple Violet">
                            <span class="swatch-circle"></span>
                            <span class="swatch-label">Violet</span>
                        </button>
                        <button type="button" class="accent-swatch-btn" data-accent="#f43f5e" style="--swatch-color: #f43f5e;" onclick="selectAccentColor('#f43f5e')" title="Rose Coral">
                            <span class="swatch-circle"></span>
                            <span class="swatch-label">Coral</span>
                        </button>
                        <button type="button" class="accent-swatch-btn" data-accent="#f59e0b" style="--swatch-color: #f59e0b;" onclick="selectAccentColor('#f59e0b')" title="Golden Amber">
                            <span class="swatch-circle"></span>
                            <span class="swatch-label">Amber</span>
                        </button>
                    </div>
                </div>

                <!-- SECTION 4: READING COMFORT (FONT SCALING) -->
                <div class="guest-setting-section">
                    <div class="guest-section-header">
                        <span class="guest-section-title">Text Size</span>
                    </div>

                    <div class="font-scale-options">
                        <button type="button" class="font-scale-btn" data-scale="0.94rem" onclick="setFontScale('0.94rem')">
                            Compact
                        </button>
                        <button type="button" class="font-scale-btn active" data-scale="1rem" onclick="setFontScale('1rem')">
                            Standard
                        </button>
                        <button type="button" class="font-scale-btn" data-scale="1.08rem" onclick="setFontScale('1.08rem')">
                            Comfortable
                        </button>
                        <button type="button" class="font-scale-btn" data-scale="1.18rem" onclick="setFontScale('1.18rem')">
                            Large
                        </button>
                    </div>
                </div>

                <!-- DRAGGABLE ICON HELPER & POSITION RESET -->
                <div class="drag-hint-box">
                    <span class="drag-hint-text">
                        Tip: Drag the floating button to move it anywhere.
                    </span>
                    <button type="button" class="btn-reset-pos" onclick="resetWidgetPosition()">
                        Reset Position
                    </button>
                </div>
            </div>

            <!-- Modal Footer Actions -->
            <div class="guest-dialog-footer">
                <button type="button" class="btn-footer-reset" onclick="resetAllDisplayDefaults()">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                        <path d="M3 3v5h5"/>
                    </svg>
                    Restore Defaults
                </button>
                <button type="button" class="btn-footer-done" onclick="closeGuestThemeModal()">
                    Done
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* ===== FLOATING DRAGGABLE ACTION BUTTON (FAB) ===== */
    .guest-fab-wrap {
        position: fixed;
        z-index: 9999;
        cursor: grab;
        user-select: none;
        -webkit-user-select: none;
        touch-action: none;
        display: block;
    }

    .guest-fab-wrap.is-dragging {
        cursor: grabbing !important;
        transition: none !important;
    }

    .guest-theme-fab {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(14, 23, 34, 0.94), rgba(20, 32, 48, 0.96));
        border: 2px solid var(--guest-accent, #00f5d4);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.45), 0 0 18px var(--guest-accent-glow, rgba(0, 245, 212, 0.4));
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: inherit;
        position: relative;
        outline: none;
        padding: 0;
        transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.22s ease, border-color 0.22s ease;
    }

    .guest-fab-wrap:not(.is-dragging):hover .guest-theme-fab {
        transform: scale(1.08);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.55), 0 0 26px var(--guest-accent-glow, rgba(0, 245, 212, 0.6));
    }

    .guest-fab-pulse {
        position: absolute;
        inset: -4px;
        border-radius: 50%;
        border: 1.5px solid var(--guest-accent, #00f5d4);
        opacity: 0.6;
        animation: guestFabPulse 2.8s infinite;
        pointer-events: none;
    }

    @keyframes guestFabPulse {
        0% { transform: scale(0.96); opacity: 0.8; }
        50% { transform: scale(1.15); opacity: 0; }
        100% { transform: scale(0.96); opacity: 0; }
    }

    .guest-fab-icon-inner {
        width: 26px;
        height: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--guest-accent, #00f5d4);
        transition: transform 0.3s ease, color 0.25s ease;
    }

    .guest-fab-wrap:not(.is-dragging):hover .guest-fab-icon-inner {
        transform: rotate(20deg);
    }

    .guest-fab-icon {
        width: 24px;
        height: 24px;
    }

    .guest-fab-drag-hint {
        position: absolute;
        bottom: -3px;
        right: -3px;
        width: 17px;
        height: 17px;
        border-radius: 50%;
        background: var(--guest-accent, #00f5d4);
        color: #042f2e;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        pointer-events: none;
    }

    /* ===== MODAL BACKDROP & DIALOG ===== */
    .guest-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 10000;
        background: rgba(4, 8, 14, 0.78);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1.25rem;
        opacity: 0;
        transition: opacity 0.25s ease;
    }

    .guest-modal-backdrop.is-active {
        display: flex;
        opacity: 1;
    }

    .guest-theme-dialog {
        width: 100%;
        max-width: 580px;
        max-height: 88vh;
        background: var(--guest-bg-surface, #0f1926);
        border: 1px solid var(--guest-border, rgba(255, 255, 255, 0.1));
        border-radius: 20px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.55);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        animation: guestDialogPop 0.24s cubic-bezier(0.16, 1, 0.3, 1);
        color: var(--guest-text-main, #f8fafc);
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    }

    @keyframes guestDialogPop {
        from { transform: scale(0.96) translateY(8px); opacity: 0; }
        to { transform: scale(1) translateY(0); opacity: 1; }
    }

    /* Dialog Header */
    .guest-dialog-header {
        padding: 1.2rem 1.4rem 1rem;
        border-bottom: 1px solid var(--guest-border, rgba(255, 255, 255, 0.08));
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        background: rgba(255, 255, 255, 0.015);
    }

    .guest-dialog-title-group {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .guest-dialog-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--guest-accent, #00f5d4);
        margin-bottom: 1px;
    }

    .guest-dialog-badge .pulse-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--guest-accent, #00f5d4);
    }

    .guest-dialog-title {
        font-size: 1.15rem;
        font-weight: 700;
        letter-spacing: -0.015em;
        color: var(--guest-text-headings, #ffffff);
        margin: 0;
        line-height: 1.25;
    }

    .guest-dialog-subtitle {
        font-size: 0.76rem;
        color: var(--guest-text-muted, #94a3b8);
        margin: 0;
    }

    .guest-dialog-close {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--guest-border, rgba(255, 255, 255, 0.1));
        color: var(--guest-text-muted, #94a3b8);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        outline: none;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .guest-dialog-close:hover {
        background: rgba(239, 68, 68, 0.15);
        border-color: #ef4444;
        color: #ffffff;
    }

    /* Dialog Body */
    .guest-dialog-body {
        padding: 1.2rem 1.4rem;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 1.15rem;
        overscroll-behavior: contain;
    }

    .guest-dialog-body::-webkit-scrollbar {
        width: 5px;
    }
    .guest-dialog-body::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.12);
        border-radius: 999px;
    }

    /* Section Styles */
    .guest-setting-section {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .guest-section-header {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .guest-section-icon {
        font-size: 1rem;
        line-height: 1;
    }

    .guest-section-titles {
        display: flex;
        flex-direction: column;
        gap: 1px;
    }

    .guest-section-title {
        font-size: 0.86rem;
        font-weight: 700;
        color: var(--guest-text-headings, #ffffff);
        margin: 0;
    }

    .guest-section-desc {
        font-size: 0.72rem;
        color: var(--guest-text-muted, #94a3b8);
    }

    /* Theme Mode Cards Grid */
    .guest-theme-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }

    @media (max-width: 580px) {
        .guest-theme-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .guest-theme-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--guest-border, rgba(255, 255, 255, 0.08));
        border-radius: 10px;
        padding: 9px 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        text-align: left;
        outline: none;
        transition: all 0.16s ease;
        position: relative;
    }

    .guest-theme-card:hover {
        background: rgba(255, 255, 255, 0.06);
        border-color: rgba(255, 255, 255, 0.2);
    }

    .guest-theme-card.active {
        background: rgba(255, 255, 255, 0.08);
        border-color: var(--guest-accent, #00f5d4);
        box-shadow: 0 0 0 1px var(--guest-accent, #00f5d4);
    }

    .theme-swatch {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        flex-shrink: 0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    .swatch-light {
        background: #f8fafc;
        border: 1.5px solid #94a3b8;
    }
    .swatch-sepia {
        background: #fbf7ee;
        border: 1.5px solid #d97706;
    }
    .swatch-custom {
        background: conic-gradient(red, yellow, lime, aqua, blue, magenta, red);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .theme-card-name {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--guest-text-headings, #ffffff);
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .theme-active-check {
        font-size: 0.8rem;
        color: var(--guest-accent, #00f5d4);
        font-weight: 800;
        display: none;
    }

    .guest-theme-card.active .theme-active-check {
        display: inline-block;
    }

    /* Custom Color Controls Panel (Hidden by default, shown on Custom Canvas) */
    .custom-color-panel {
        display: none;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--guest-border, rgba(255, 255, 255, 0.09));
        border-radius: 14px;
        padding: 1rem;
        flex-direction: column;
        gap: 0.85rem;
        animation: guestFadeIn 0.2s ease;
    }

    @keyframes guestFadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .custom-pickers-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.85rem;
    }

    @media (max-width: 520px) {
        .custom-pickers-row {
            grid-template-columns: 1fr;
        }
    }

    .color-picker-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .color-picker-label {
        font-size: 0.74rem;
        font-weight: 600;
        color: var(--guest-text-headings, #ffffff);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .hex-badge {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.7rem;
        color: var(--guest-accent, #00f5d4);
        background: rgba(255, 255, 255, 0.06);
        padding: 1px 5px;
        border-radius: 4px;
    }

    .color-input-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid var(--guest-border, rgba(255, 255, 255, 0.12));
        border-radius: 8px;
        padding: 4px 7px;
    }

    .native-color-picker {
        width: 28px;
        height: 28px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        background: none;
        padding: 0;
    }
    .native-color-picker::-webkit-color-swatch-wrapper {
        padding: 0;
    }
    .native-color-picker::-webkit-color-swatch {
        border-radius: 6px;
        border: 1px solid rgba(255, 255, 255, 0.25);
    }

    .color-swatch-display {
        display: none;
    }

    .hex-text-input {
        flex: 1;
        background: none;
        border: none;
        outline: none;
        color: var(--guest-text-headings, #ffffff);
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Live Contrast Box */
    .contrast-feedback-card {
        margin-top: 0.5rem;
        background: rgba(0, 0, 0, 0.18);
        border: 1px solid var(--guest-border, rgba(255, 255, 255, 0.08));
        border-radius: 10px;
        padding: 0.75rem;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .contrast-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        flex-wrap: wrap;
    }

    .contrast-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 9999px;
        background: rgba(16, 185, 129, 0.12);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.25);
    }

    .contrast-status-badge.warning {
        background: rgba(245, 158, 11, 0.12);
        color: #f59e0b;
        border-color: rgba(245, 158, 11, 0.25);
    }

    .contrast-status-badge.danger {
        background: rgba(239, 68, 68, 0.12);
        color: #ef4444;
        border-color: rgba(239, 68, 68, 0.25);
    }

    .contrast-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: currentColor;
    }

    .btn-auto-contrast {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid var(--guest-border, rgba(255, 255, 255, 0.12));
        color: var(--guest-text-headings, #ffffff);
        border-radius: 9999px;
        padding: 3px 8px;
        font-size: 0.68rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-auto-contrast:hover {
        background: var(--guest-accent, #00f5d4);
        color: #042f2e;
        border-color: var(--guest-accent, #00f5d4);
    }

    .contrast-mini-preview {
        padding: 0.65rem 0.8rem;
        border-radius: 6px;
        background: var(--guest-bg-base, #0b1119);
        color: var(--guest-text-main, #ffffff);
        display: flex;
        flex-direction: column;
        gap: 2px;
        border: 1px solid var(--guest-border, rgba(255, 255, 255, 0.08));
        transition: all 0.2s ease;
    }

    .mini-preview-title {
        font-size: 0.76rem;
        font-weight: 700;
        color: var(--guest-text-headings, #ffffff);
    }

    .mini-preview-snippet {
        font-size: 0.68rem;
        color: var(--guest-text-muted, #94a3b8);
        line-height: 1.3;
    }

    /* Accent Swatches */
    .accent-swatches-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 6px;
    }

    @media (max-width: 480px) {
        .accent-swatches-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    .accent-swatch-btn {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--guest-border, rgba(255, 255, 255, 0.09));
        border-radius: 10px;
        padding: 6px 4px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        outline: none;
        transition: all 0.18s ease;
    }

    .accent-swatch-btn:hover {
        background: rgba(255, 255, 255, 0.07);
        border-color: var(--swatch-color);
    }

    .accent-swatch-btn.active {
        border-color: var(--swatch-color);
        background: rgba(255, 255, 255, 0.09);
        box-shadow: 0 0 0 1px var(--swatch-color);
    }

    .swatch-circle {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: var(--swatch-color);
    }

    .swatch-label {
        font-size: 0.64rem;
        font-weight: 600;
        color: var(--guest-text-headings, #ffffff);
    }

    /* Font Scale Options */
    .font-scale-options {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 6px;
    }

    @media (max-width: 480px) {
        .font-scale-options {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .font-scale-btn {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--guest-border, rgba(255, 255, 255, 0.08));
        border-radius: 8px;
        padding: 7px 4px;
        text-align: center;
        color: var(--guest-text-headings, #ffffff);
        font-size: 0.74rem;
        font-weight: 600;
        cursor: pointer;
        outline: none;
        transition: all 0.16s ease;
    }

    .font-scale-btn:hover {
        background: rgba(255, 255, 255, 0.06);
        border-color: rgba(255, 255, 255, 0.2);
    }

    .font-scale-btn.active {
        background: rgba(255, 255, 255, 0.08);
        border-color: var(--guest-accent, #00f5d4);
        color: var(--guest-accent, #00f5d4);
        box-shadow: 0 0 0 1px var(--guest-accent, #00f5d4);
    }

    /* Drag Hint Box (Minimal) */
    .drag-hint-box {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--guest-border, rgba(255, 255, 255, 0.06));
        border-radius: 10px;
        padding: 0.6rem 0.9rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .drag-hint-text {
        font-size: 0.72rem;
        color: var(--guest-text-muted, #94a3b8);
        line-height: 1.35;
    }

    .btn-reset-pos {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--guest-border, rgba(255, 255, 255, 0.1));
        color: var(--guest-text-headings, #ffffff);
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 0.68rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-reset-pos:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: var(--guest-accent, #00f5d4);
    }

    /* Dialog Footer */
    .guest-dialog-footer {
        padding: 0.85rem 1.4rem;
        border-top: 1px solid var(--guest-border, rgba(255, 255, 255, 0.08));
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        background: rgba(255, 255, 255, 0.015);
    }

    .btn-footer-reset {
        background: none;
        border: none;
        color: var(--guest-text-muted, #94a3b8);
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 8px;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .btn-footer-reset:hover {
        color: #ef4444;
        background: rgba(239, 68, 68, 0.08);
    }

    .btn-footer-done {
        background: var(--guest-accent, #00f5d4);
        color: var(--guest-accent-text, #042f2e);
        border: none;
        padding: 7px 20px;
        border-radius: 9999px;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 2px 10px var(--guest-accent-glow, rgba(0, 245, 212, 0.25));
        transition: all 0.2s ease;
    }

    .btn-footer-done:hover {
        transform: translateY(-1px);
        filter: brightness(1.08);
        box-shadow: 0 4px 14px var(--guest-accent-glow, rgba(0, 245, 212, 0.4));
    }
</style>

<script>
    (function() {
        const STORAGE_KEY = 'capstone_guest_display_theme';
        const POS_STORAGE_KEY = 'capstone_guest_widget_coords';

        // Default Theme Configuration (Clean Light Academic White)
        const defaults = {
            mode: 'light',
            accent: '#0c4233',
            customBg: '#ffffff',
            customText: '#1e293b',
            fontSize: '1rem'
        };

        // In-memory active settings
        let currentSettings = { ...defaults };

        // ── Drag & Drop Physics for the Floating Button ──
        const wrap = document.getElementById('guestThemeTriggerWrap');
        const fab = document.getElementById('guestThemeFab');
        let isDragging = false;
        let startX = 0, startY = 0;
        let initialLeft = 0, initialTop = 0;
        let moved = false;

        function loadSavedPosition() {
            try {
                const posRaw = localStorage.getItem(POS_STORAGE_KEY);
                if (posRaw && wrap) {
                    const pos = JSON.parse(posRaw);
                    const winW = window.innerWidth;
                    const winH = window.innerHeight;
                    
                    // Clamp to make sure it's always on-screen
                    const clampedX = Math.max(10, Math.min(winW - 65, pos.x));
                    const clampedY = Math.max(75, Math.min(winH - 65, pos.y));
                    
                    wrap.style.left = clampedX + 'px';
                    wrap.style.top = clampedY + 'px';
                    wrap.style.right = 'auto';
                    wrap.style.bottom = 'auto';
                }
            } catch (e) {
                console.error(e);
            }
        }

        window.resetWidgetPosition = function() {
            localStorage.removeItem(POS_STORAGE_KEY);
            if (wrap) {
                wrap.style.left = 'auto';
                wrap.style.top = 'auto';
                wrap.style.right = '28px';
                wrap.style.bottom = '28px';
            }
        };

        function onDragStart(clientX, clientY) {
            isDragging = true;
            moved = false;
            startX = clientX;
            startY = clientY;

            const rect = wrap.getBoundingClientRect();
            initialLeft = rect.left;
            initialTop = rect.top;

            wrap.classList.add('is-dragging');
        }

        function onDragMove(clientX, clientY) {
            if (!isDragging) return;
            const dx = clientX - startX;
            const dy = clientY - startY;

            if (Math.hypot(dx, dy) > 4) {
                moved = true;
            }

            const winW = window.innerWidth;
            const winH = window.innerHeight;

            let newLeft = initialLeft + dx;
            let newTop = initialTop + dy;

            // Constrain within viewport (keeps below top nav)
            newLeft = Math.max(10, Math.min(winW - 65, newLeft));
            newTop = Math.max(75, Math.min(winH - 65, newTop));

            wrap.style.left = newLeft + 'px';
            wrap.style.top = newTop + 'px';
            wrap.style.right = 'auto';
            wrap.style.bottom = 'auto';
        }

        function onDragEnd() {
            if (!isDragging) return;
            isDragging = false;
            wrap.classList.remove('is-dragging');

            if (moved) {
                const rect = wrap.getBoundingClientRect();
                localStorage.setItem(POS_STORAGE_KEY, JSON.stringify({
                    x: Math.round(rect.left),
                    y: Math.round(rect.top)
                }));
            } else {
                // Was a click!
                openGuestThemeModal();
            }
        }

        // Mouse listeners
        if (wrap) {
            wrap.addEventListener('mousedown', function(e) {
                if (e.button !== 0) return; // Only left click
                e.preventDefault();
                onDragStart(e.clientX, e.clientY);

                function onMouseMove(moveEvent) {
                    onDragMove(moveEvent.clientX, moveEvent.clientY);
                }

                function onMouseUp() {
                    document.removeEventListener('mousemove', onMouseMove);
                    document.removeEventListener('mouseup', onMouseUp);
                    onDragEnd();
                }

                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
            });

            // Touch listeners for mobile & tablet
            wrap.addEventListener('touchstart', function(e) {
                if (e.touches.length !== 1) return;
                const touch = e.touches[0];
                onDragStart(touch.clientX, touch.clientY);
            }, { passive: false });

            wrap.addEventListener('touchmove', function(e) {
                if (!isDragging || e.touches.length !== 1) return;
                e.preventDefault();
                const touch = e.touches[0];
                onDragMove(touch.clientX, touch.clientY);
            }, { passive: false });

            wrap.addEventListener('touchend', function() {
                onDragEnd();
            });
        }

        // ── Modal Handling ──
        window.openGuestThemeModal = function() {
            const modal = document.getElementById('guestThemeModalOverlay');
            if (modal) {
                modal.classList.add('is-active');
                syncUIFromSettings();
            }
        };

        window.closeGuestThemeModal = function() {
            const modal = document.getElementById('guestThemeModalOverlay');
            if (modal) {
                modal.classList.remove('is-active');
            }
        };

        window.handleThemeModalBackdrop = function(e) {
            if (e.target.id === 'guestThemeModalOverlay') {
                closeGuestThemeModal();
            }
        };

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeGuestThemeModal();
            }
        });

        // ── Theme Application Engine ──
        function saveSettings() {
            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(currentSettings));
            } catch (e) {
                console.error(e);
            }
        }

        function loadSettings() {
            try {
                const raw = localStorage.getItem(STORAGE_KEY);
                if (raw) {
                    const parsed = JSON.parse(raw);
                    currentSettings = { ...defaults, ...parsed };
                    if (currentSettings.mode === 'dark' || currentSettings.mode === 'oled') {
                        currentSettings.mode = 'light';
                    }
                }
            } catch (e) {
                console.error(e);
            }
        }

        function hexToRgb(hex) {
            hex = hex.replace('#', '');
            if (hex.length === 3) hex = hex.split('').map(c => c + c).join('');
            const num = parseInt(hex, 16);
            return {
                r: (num >> 16) & 255,
                g: (num >> 8) & 255,
                b: num & 255
            };
        }

        function getLuminance(r, g, b) {
            const a = [r, g, b].map(v => {
                v /= 255;
                return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
            });
            return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
        }

        function calculateContrastRatio(hex1, hex2) {
            const rgb1 = hexToRgb(hex1);
            const rgb2 = hexToRgb(hex2);
            const l1 = getLuminance(rgb1.r, rgb1.g, rgb1.b);
            const l2 = getLuminance(rgb2.r, rgb2.g, rgb2.b);
            const brightest = Math.max(l1, l2);
            const darkest = Math.min(l1, l2);
            return (brightest + 0.05) / (darkest + 0.05);
        }

        function applyTheme() {
            const doc = document.documentElement;
            doc.setAttribute('data-guest-theme', currentSettings.mode);
            doc.setAttribute('data-student-theme', currentSettings.mode);
            doc.setAttribute('data-admin-theme', currentSettings.mode);

            // Apply Accent
            if (currentSettings.accent) {
                doc.style.setProperty('--guest-accent', currentSettings.accent);
                try {
                    const rgb = hexToRgb(currentSettings.accent);
                    doc.style.setProperty('--guest-accent-glow', `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.35)`);
                    doc.style.setProperty('--guest-accent-rgb', `${rgb.r}, ${rgb.g}, ${rgb.b}`);
                    const lum = getLuminance(rgb.r, rgb.g, rgb.b);
                    const accentText = lum < 0.45 ? '#ffffff' : '#042f2e';
                    doc.style.setProperty('--guest-accent-text', accentText);
                } catch(e) {}
            }

            // Apply Font scale
            if (currentSettings.fontSize) {
                doc.style.setProperty('--guest-font-scale', currentSettings.fontSize);
                doc.style.fontSize = currentSettings.fontSize;
                const multiplierMap = {
                    '0.94rem': '0.94',
                    '1rem': '1.0',
                    '1.08rem': '1.08',
                    '1.18rem': '1.20'
                };
                doc.style.setProperty('--guest-font-multiplier', multiplierMap[currentSettings.fontSize] || '1.0');
            }

            // Apply Custom Canvas Overrides if in custom mode
            if (currentSettings.mode === 'custom') {
                const bg = currentSettings.customBg || '#0b1119';
                const text = currentSettings.customText || '#f8fafc';
                const bgRgb = hexToRgb(bg);
                const isBgDark = getLuminance(bgRgb.r, bgRgb.g, bgRgb.b) < 0.35;

                doc.style.setProperty('--guest-bg-base', bg);
                doc.style.setProperty('--guest-page-gradient', 'none');
                doc.style.setProperty('--guest-text-main', text);
                doc.style.setProperty('--guest-text-headings', text);
                
                // Muted text adjusted based on luminance
                const textRgb = hexToRgb(text);
                doc.style.setProperty('--guest-text-muted', `rgba(${textRgb.r}, ${textRgb.g}, ${textRgb.b}, 0.65)`);

                // Surface & card tints
                if (isBgDark) {
                    const cardBg = `rgba(${Math.min(255, bgRgb.r + 14)}, ${Math.min(255, bgRgb.g + 16)}, ${Math.min(255, bgRgb.b + 20)}, 0.94)`;
                    doc.style.setProperty('--guest-bg-surface', cardBg);
                    doc.style.setProperty('--guest-bg-sidebar', cardBg);
                    doc.style.setProperty('--guest-bg-input', `rgba(0,0,0,0.3)`);
                    doc.style.setProperty('--guest-border', 'rgba(255,255,255,0.14)');
                    doc.style.setProperty('--guest-nav-bg', `rgba(${bgRgb.r}, ${bgRgb.g}, ${bgRgb.b}, 0.92)`);
                    doc.style.setProperty('--guest-overlay-opacity', '0.2');
                } else {
                    const cardBg = '#ffffff';
                    doc.style.setProperty('--guest-bg-surface', cardBg);
                    doc.style.setProperty('--guest-bg-sidebar', cardBg);
                    doc.style.setProperty('--guest-bg-input', `rgba(0,0,0,0.04)`);
                    doc.style.setProperty('--guest-border', 'rgba(0,0,0,0.12)');
                    doc.style.setProperty('--guest-nav-bg', `rgba(255, 255, 255, 0.94)`);
                    doc.style.setProperty('--guest-overlay-opacity', '0.05');
                }
            } else {
                // Remove custom inline property overrides so preset classes take full charge
                doc.style.removeProperty('--guest-bg-base');
                doc.style.removeProperty('--guest-page-gradient');
                doc.style.removeProperty('--guest-text-main');
                doc.style.removeProperty('--guest-text-headings');
                doc.style.removeProperty('--guest-text-muted');
                doc.style.removeProperty('--guest-bg-surface');
                doc.style.removeProperty('--guest-bg-sidebar');
                doc.style.removeProperty('--guest-bg-input');
                doc.style.removeProperty('--guest-border');
                doc.style.removeProperty('--guest-nav-bg');
                doc.style.removeProperty('--guest-overlay-opacity');
            }

            updateContrastFeedback();
            saveSettings();
            window.dispatchEvent(new CustomEvent('studentThemeChanged', { detail: { theme: currentSettings.mode, accent: currentSettings.accent, fontSize: currentSettings.fontSize, settings: currentSettings } }));
            window.dispatchEvent(new CustomEvent('adminThemeChanged', { detail: { theme: currentSettings.mode, accent: currentSettings.accent, fontSize: currentSettings.fontSize, settings: currentSettings } }));
        }

        window.selectThemeMode = function(mode) {
            if (mode === 'dark' || mode === 'oled') mode = 'light';
            currentSettings.mode = mode;
            // Provide gentle default accents matching mode if default forest green was unchanged
            if (mode === 'light' && (currentSettings.accent === '#00f5d4' || currentSettings.accent === '#b45309')) {
                currentSettings.accent = '#0c4233';
            } else if (mode === 'sepia' && (currentSettings.accent === '#00f5d4' || currentSettings.accent === '#0c4233')) {
                currentSettings.accent = '#b45309';
            }

            applyTheme();
            syncUIFromSettings();
        };

        window.selectAccentColor = function(color) {
            currentSettings.accent = color;
            applyTheme();
            syncUIFromSettings();
        };

        window.setFontScale = function(scale) {
            currentSettings.fontSize = scale;
            applyTheme();
            syncUIFromSettings();
        };

        window.onCustomBgChange = function(val) {
            currentSettings.mode = 'custom';
            currentSettings.customBg = val;
            applyTheme();
            syncUIFromSettings();
        };

        window.onCustomBgTextChange = function(val) {
            if (/^#[0-9A-F]{6}$/i.test(val)) {
                onCustomBgChange(val);
            }
        };

        window.onCustomTextChange = function(val) {
            currentSettings.mode = 'custom';
            currentSettings.customText = val;
            applyTheme();
            syncUIFromSettings();
        };

        window.onCustomTextTextChange = function(val) {
            if (/^#[0-9A-F]{6}$/i.test(val)) {
                onCustomTextChange(val);
            }
        };

        window.autoFixContrast = function() {
            const bg = currentSettings.customBg || '#0b1119';
            const bgRgb = hexToRgb(bg);
            const isDark = getLuminance(bgRgb.r, bgRgb.g, bgRgb.b) < 0.45;
            
            // Set optimal contrasting text
            const newText = isDark ? '#ffffff' : '#0f172a';
            currentSettings.customText = newText;
            currentSettings.mode = 'custom';
            applyTheme();
            syncUIFromSettings();
        };

        function updateContrastFeedback() {
            const bg = (currentSettings.mode === 'custom') ? currentSettings.customBg : 
                       (currentSettings.mode === 'sepia') ? '#fbf7ee' : '#ffffff';

            const text = (currentSettings.mode === 'custom') ? currentSettings.customText :
                         (currentSettings.mode === 'sepia') ? '#262019' : '#0f172a';

            const ratio = calculateContrastRatio(bg, text);
            const badge = document.getElementById('contrastBadge');
            const textEl = document.getElementById('contrastText');
            const preview = document.getElementById('contrastMiniPreview');

            if (badge && textEl) {
                badge.className = 'contrast-status-badge';
                if (ratio >= 7) {
                    textEl.textContent = `Excellent Contrast (${ratio.toFixed(1)}:1 — AAA)`;
                } else if (ratio >= 4.5) {
                    textEl.textContent = `Good Contrast (${ratio.toFixed(1)}:1 — AA)`;
                } else if (ratio >= 3) {
                    badge.classList.add('warning');
                    textEl.textContent = `Moderate Contrast (${ratio.toFixed(1)}:1)`;
                } else {
                    badge.classList.add('danger');
                    textEl.textContent = `Low Contrast (${ratio.toFixed(1)}:1 — Hard to read)`;
                }
            }

            if (preview) {
                preview.style.backgroundColor = bg;
                preview.style.color = text;
                const title = preview.querySelector('.mini-preview-title');
                const snippet = preview.querySelector('.mini-preview-snippet');
                if (title) title.style.color = text;
                if (snippet) {
                    try {
                        const trgb = hexToRgb(text);
                        snippet.style.color = `rgba(${trgb.r}, ${trgb.g}, ${trgb.b}, 0.7)`;
                    } catch(e) {}
                }
            }
        }

        window.resetAllDisplayDefaults = function() {
            currentSettings = { ...defaults };
            applyTheme();
            syncUIFromSettings();
        };

        function syncUIFromSettings() {
            // Toggle Custom Palette Tuning section: ONLY show if mode is 'custom'
            const customBox = document.getElementById('customColorBox');
            if (customBox) {
                if (currentSettings.mode === 'custom') {
                    customBox.style.display = 'flex';
                } else {
                    customBox.style.display = 'none';
                }
            }

            // Active Mode Card
            document.querySelectorAll('.guest-theme-card').forEach(card => {
                if (card.getAttribute('data-mode') === currentSettings.mode) {
                    card.classList.add('active');
                } else {
                    card.classList.remove('active');
                }
            });

            // Accent Swatch Active
            document.querySelectorAll('.accent-swatch-btn').forEach(btn => {
                if (btn.getAttribute('data-accent').toLowerCase() === currentSettings.accent.toLowerCase()) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            // Font Scale Active
            document.querySelectorAll('.font-scale-btn').forEach(btn => {
                if (btn.getAttribute('data-scale') === currentSettings.fontSize) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            // Color inputs
            const bgInput = document.getElementById('customBgInput');
            const bgText = document.getElementById('customBgText');
            const bgHex = document.getElementById('customBgHex');
            if (bgInput && currentSettings.customBg) bgInput.value = currentSettings.customBg;
            if (bgText && currentSettings.customBg) bgText.value = currentSettings.customBg;
            if (bgHex && currentSettings.customBg) bgHex.textContent = currentSettings.customBg;

            const textInput = document.getElementById('customTextInput');
            const textText = document.getElementById('customTextText');
            const textHex = document.getElementById('customTextHex');
            if (textInput && currentSettings.customText) textInput.value = currentSettings.customText;
            if (textText && currentSettings.customText) textText.value = currentSettings.customText;
            if (textHex && currentSettings.customText) textHex.textContent = currentSettings.customText;

            updateContrastFeedback();
        }

        // Initialize on DOM load
        loadSettings();
        loadSavedPosition();
        applyTheme();
        syncUIFromSettings();

        // Also handle window resize to re-clamp button if offscreen
        window.addEventListener('resize', function() {
            loadSavedPosition();
        });
    })();
</script>
@endif
