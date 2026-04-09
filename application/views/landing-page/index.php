<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Smesco Express - <?= $title ?></title>
	<meta name="description" content="SMESCO Express — jasa pengiriman terpercaya untuk UMKM Indonesia. Layanan pengiriman udara, darat, ekspor impor, dan fulfillment ke seluruh Indonesia dan mancanegara." />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="https://smesco.kodesis.id/" />
	<meta property="og:title" content="SMESCO Express — Teman Kirim Terpercaya UMKM Indonesia" />
	<meta property="og:description" content="Layanan pengiriman udara, darat, ekspor impor, dan fulfillment untuk UMKM Indonesia. Cepat, aman, dan terpercaya." />
	<meta property="og:image" content="<?= base_url() ?>assets/logo/logo-smesco-hera-2.png" />
	<meta property="og:locale" content="id_ID" />
	<meta property="og:site_name" content="SMESCO Express" />
	<meta name="twitter:card" content="summary_large_image" />
	<meta name="twitter:title" content="SMESCO Express — Teman Kirim Terpercaya UMKM Indonesia" />
	<meta name="twitter:description" content="Layanan pengiriman udara, darat, ekspor impor, dan fulfillment untuk UMKM Indonesia." />
	<meta name="twitter:image" content="<?= base_url() ?>assets/logo/logo-smesco-hera-2.png" />
	<link href="<?= base_url() ?>assets/logo/icon-smesco.png" rel="icon">
	<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" />

	<style>
		/* ── TOKENS ─────────────────────────────────────────── */
		:root {
			--navy: #193b5c;
			--navy-deep: #0f2740;
			--grey: #4d545e;
			--grey-light: #8a9199;
			--yellow: #ffcf26;
			--yellow-dim: #e8ba00;
			--white: #ffffff;
			--off: #f4f6f9;
			--border: #e2e6eb;
			--border-mid: #cdd3da;
		}

		*,
		*::before,
		*::after {
			box-sizing: border-box;
			margin: 0;
			padding: 0;
		}

		html {
			scroll-behavior: smooth;
			font-size: 16px;
		}

		ol,
		ul {
			padding-left: 0;
		}

		dl,
		ol,
		ul {
			margin-top: 0;
			margin-bottom: 0;
		}

		body {
			font-family: 'Inter', sans-serif;
			font-feature-settings: 'cv02', 'cv03', 'cv04', 'cv11';
			color: var(--grey);
			background: var(--white);
			overflow-x: hidden;
			-webkit-font-smoothing: antialiased;
		}

		/* ── TYPOGRAPHY ─────────────────────────────────────── */
		.t-label {
			font-size: 0.68rem;
			font-weight: 700;
			letter-spacing: 0.12em;
			text-transform: uppercase;
			color: var(--grey-light);
		}

		.t-section {
			font-size: clamp(2rem, 4vw, 3.2rem);
			font-weight: 800;
			line-height: 1.05;
			letter-spacing: -0.03em;
			color: var(--navy);
		}

		.t-body {
			font-size: 0.95rem;
			font-weight: 400;
			line-height: 1.7;
			color: var(--grey);
		}

		/* ── BUTTONS ────────────────────────────────────────── */
		.btn-primary-cta {
			display: inline-flex;
			align-items: center;
			gap: 8px;
			background: var(--yellow);
			color: var(--navy);
			font-family: 'Inter', sans-serif;
			font-size: 0.875rem;
			font-weight: 700;
			letter-spacing: -0.01em;
			padding: 13px 26px;
			border-radius: 10px;
			border: none;
			text-decoration: none;
			cursor: pointer;
			transition: background 0.18s, transform 0.18s, box-shadow 0.18s;
		}

		.btn-primary-cta:hover {
			background: var(--yellow-dim);
			color: var(--navy);
			transform: translateY(-2px);
			box-shadow: 0 8px 20px rgba(255, 207, 38, 0.32);
		}

		.btn-ghost-white {
			display: inline-flex;
			align-items: center;
			gap: 8px;
			background: rgba(255, 255, 255, 0.15);
			color: var(--white);
			font-family: 'Inter', sans-serif;
			font-size: 0.875rem;
			font-weight: 600;
			letter-spacing: -0.01em;
			padding: 12px 24px;
			border-radius: 10px;
			border: 1.5px solid rgba(255, 255, 255, 0.35);
			text-decoration: none;
			cursor: pointer;
			backdrop-filter: blur(4px);
			transition: background 0.18s, border-color 0.18s, transform 0.18s;
		}

		.btn-ghost-white:hover {
			background: rgba(255, 255, 255, 0.25);
			border-color: rgba(255, 255, 255, 0.6);
			color: var(--white);
			transform: translateY(-2px);
		}

		.btn-dark {
			display: inline-flex;
			align-items: center;
			gap: 8px;
			background: var(--navy);
			color: var(--white);
			font-family: 'Inter', sans-serif;
			font-size: 0.875rem;
			font-weight: 600;
			letter-spacing: -0.01em;
			padding: 11px 22px;
			border-radius: 10px;
			border: none;
			text-decoration: none;
			cursor: pointer;
			transition: background 0.18s, transform 0.18s;
		}

		.btn-dark:hover {
			background: var(--navy-deep);
			color: var(--white);
			transform: translateY(-1px);
		}

		/* ── NAVBAR ─────────────────────────────────────────── */
		.site-nav {
			position: sticky;
			top: 0;
			z-index: 900;
			background: rgba(255, 255, 255, 0.96);
			backdrop-filter: blur(12px);
			border-bottom: 1px solid var(--border);
		}

		.nav-inner {
			display: flex;
			align-items: center;
			height: 64px;
		}

		.nav-brand {
			display: flex;
			align-items: center;
			gap: 10px;
			text-decoration: none;
			flex-shrink: 0;
			padding-right: 32px;
			border-right: 1px solid var(--border);
			margin-right: 24px;
		}

		.brand-icon {
			width: 34px;
			height: 34px;
			background: var(--navy);
			border-radius: 8px;
			display: flex;
			align-items: center;
			justify-content: center;
			color: var(--yellow);
			font-size: 16px;
			flex-shrink: 0;
		}

		.brand-text-main {
			font-size: 1rem;
			font-weight: 800;
			letter-spacing: -0.03em;
			color: var(--navy);
			line-height: 1;
		}

		.brand-text-sub {
			font-size: 0.6rem;
			font-weight: 600;
			letter-spacing: 0.1em;
			text-transform: uppercase;
			color: var(--grey-light);
			margin-top: 1px;
		}

		.nav-menu {
			display: flex;
			align-items: center;
			gap: 2px;
			list-style: none;
			flex: 1;
		}

		.nav-menu a {
			display: block;
			padding: 7px 14px;
			font-size: 0.845rem;
			font-weight: 500;
			letter-spacing: -0.01em;
			color: var(--grey);
			text-decoration: none;
			border-radius: 8px;
			transition: color 0.15s, background 0.15s;
		}

		.nav-menu a:hover {
			color: var(--navy);
			background: var(--off);
		}

		.nav-end {
			margin-left: auto;
			display: flex;
			align-items: center;
			gap: 12px;
			padding-left: 24px;
			border-left: 1px solid var(--border);
		}

		/* ══════════════════════════════════════════════════════
       HERO SLIDER
    ══════════════════════════════════════════════════════ */
		.hero-slider {
			position: relative;
			width: 100%;
			height: 640px;
			overflow: hidden;
			background: var(--navy-deep);
		}

		/* ── Slides ── */
		.slide {
			position: absolute;
			inset: 0;
			opacity: 0;
			transition: opacity 0.9s ease;
			pointer-events: none;
		}

		.slide.active {
			opacity: 1;
			pointer-events: auto;
		}

		.slide-img {
			position: absolute;
			inset: 0;
			width: 100%;
			height: 100%;
			object-fit: cover;
			object-position: center;
			display: block;
		}

		.slide-placeholder {
			position: absolute;
			inset: 0;
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			gap: 12px;
		}

		.slide-placeholder-label {
			font-size: 0.72rem;
			font-weight: 700;
			letter-spacing: 0.12em;
			text-transform: uppercase;
			color: rgba(255, 255, 255, 0.3);
			margin-top: 8px;
		}

		.slide-placeholder-hint {
			font-size: 0.78rem;
			font-weight: 400;
			color: rgba(255, 255, 255, 0.2);
			text-align: center;
			max-width: 280px;
			line-height: 1.5;
		}

		/* ── Progress bar ── */
		.slider-progress {
			position: absolute;
			top: 0;
			left: 0;
			height: 3px;
			background: var(--yellow);
			z-index: 20;
			width: 0%;
			transition: none;
		}

		.slider-progress.animating {
			transition: width 5s linear;
			width: 100%;
		}

		/* ── Navigation arrows ── */
		.slider-arrow {
			position: absolute;
			top: 50%;
			transform: translateY(-50%);
			z-index: 20;
			width: 48px;
			height: 48px;
			background: rgba(255, 255, 255, 0.12);
			border: 1px solid rgba(255, 255, 255, 0.2);
			border-radius: 12px;
			display: flex;
			align-items: center;
			justify-content: center;
			color: var(--white);
			font-size: 18px;
			cursor: pointer;
			backdrop-filter: blur(8px);
			transition: background 0.2s, border-color 0.2s, transform 0.2s;
			user-select: none;
		}

		.slider-arrow:hover {
			background: rgba(255, 255, 255, 0.22);
			border-color: rgba(255, 255, 255, 0.4);
			transform: translateY(-50%) scale(1.05);
		}

		.slider-arrow.prev {
			left: 24px;
		}

		.slider-arrow.next {
			right: 24px;
		}

		/* ── Dot indicators ── */
		.slider-dots {
			position: absolute;
			bottom: 24px;
			left: 50%;
			transform: translateX(-50%);
			z-index: 20;
			display: flex;
			align-items: center;
			gap: 8px;
		}

		.slider-dot {
			width: 8px;
			height: 8px;
			border-radius: 50%;
			background: rgba(255, 255, 255, 0.3);
			border: none;
			cursor: pointer;
			transition: background 0.25s, width 0.25s, border-radius 0.25s;
			padding: 0;
		}

		.slider-dot.active {
			background: var(--yellow);
			width: 28px;
			border-radius: 4px;
		}

		/* ── Slide counter ── */
		.slider-counter {
			position: absolute;
			bottom: 20px;
			right: 24px;
			z-index: 20;
			display: flex;
			align-items: center;
			gap: 4px;
			font-size: 0.75rem;
			font-weight: 700;
			letter-spacing: 0.05em;
		}

		.counter-current {
			color: var(--yellow);
			font-size: 1rem;
			font-weight: 900;
			letter-spacing: -0.03em;
		}

		.counter-sep {
			color: rgba(255, 255, 255, 0.2);
			margin: 0 2px;
		}

		.counter-total {
			color: rgba(255, 255, 255, 0.35);
		}

		/* ── Pause indicator ── */
		.slider-pause-badge {
			position: absolute;
			top: 16px;
			right: 16px;
			z-index: 20;
			background: rgba(0, 0, 0, 0.4);
			border: 1px solid rgba(255, 255, 255, 0.15);
			border-radius: 8px;
			padding: 6px 10px;
			display: flex;
			align-items: center;
			gap: 5px;
			font-size: 0.65rem;
			font-weight: 700;
			letter-spacing: 0.08em;
			text-transform: uppercase;
			color: rgba(255, 255, 255, 0.5);
			opacity: 0;
			transition: opacity 0.2s;
			backdrop-filter: blur(6px);
			pointer-events: none;
		}

		.hero-slider.paused .slider-pause-badge {
			opacity: 1;
		}

		/* ── TICKER BAR ─────────────────────────────────────── */
		.ticker-bar {
			background: var(--navy);
			padding: 13px 0;
			overflow: hidden;
		}

		.ticker-track {
			display: flex;
			animation: ticker 32s linear infinite;
			white-space: nowrap;
			width: max-content;
		}

		.ticker-item {
			display: inline-flex;
			align-items: center;
			gap: 10px;
			padding: 0 36px;
			font-size: 0.7rem;
			font-weight: 700;
			letter-spacing: 0.1em;
			text-transform: uppercase;
			color: rgba(255, 255, 255, 0.4);
		}

		.ticker-item i {
			color: var(--yellow);
			font-size: 9px;
		}

		@keyframes ticker {
			from {
				transform: translateX(0);
			}

			to {
				transform: translateX(-50%);
			}
		}

		/* ── STATS BAND ─────────────────────────────────────── */
		.stats-band {
			background: var(--white);
			border-bottom: 1px solid var(--border);
			padding: 56px 0;
		}

		.stat-block {
			text-align: center;
		}

		.stat-number {
			font-size: 3.2rem;
			font-weight: 900;
			letter-spacing: -0.05em;
			color: var(--navy);
			line-height: 1;
			display: block;
		}

		.stat-accent-char {
			color: var(--yellow);
		}

		.stat-label {
			font-size: 0.7rem;
			font-weight: 600;
			letter-spacing: 0.1em;
			text-transform: uppercase;
			color: var(--grey-light);
			margin-top: 8px;
			display: block;
		}

		/* ── TRACKING WIDGET ────────────────────────────────── */
		.widget-section {
			padding: 88px 0;
			background: var(--off);
			border-bottom: 1px solid var(--border);
		}

		.widget-frame {
			background: var(--white);
			border: 1px solid var(--border);
			border-radius: 16px;
			overflow: hidden;
			box-shadow: 0 4px 24px rgba(25, 59, 92, 0.06);
		}

		.widget-tabbar {
			display: flex;
			border-bottom: 1px solid var(--border);
			background: var(--off);
		}

		.wtab {
			flex: 1;
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 8px;
			padding: 16px 20px;
			font-size: 0.78rem;
			font-weight: 700;
			letter-spacing: 0.06em;
			text-transform: uppercase;
			color: var(--grey-light);
			background: transparent;
			border: none;
			border-bottom: 2px solid transparent;
			margin-bottom: -1px;
			cursor: pointer;
			transition: color 0.15s, background 0.15s, border-color 0.15s;
		}

		.wtab.active {
			color: var(--navy);
			background: var(--white);
			border-bottom-color: var(--yellow);
		}

		.wtab:not(.active):hover {
			color: var(--navy);
		}

		.widget-body {
			padding: 36px 40px;
		}

		.wpane {
			display: none;
		}

		.wpane.active {
			display: block;
		}

		.field-label {
			font-size: 0.72rem;
			font-weight: 700;
			letter-spacing: 0.08em;
			text-transform: uppercase;
			color: var(--navy);
			display: block;
			margin-bottom: 8px;
		}

		.field-input {
			width: 100%;
			background: var(--off);
			border: 1.5px solid var(--border);
			border-radius: 10px;
			padding: 13px 16px;
			font-family: 'Inter', sans-serif;
			font-size: 0.9rem;
			font-weight: 400;
			color: var(--navy);
			outline: none;
			transition: border-color 0.15s, background 0.15s;
			-webkit-appearance: none;
		}

		.field-input:focus {
			border-color: var(--navy);
			background: var(--white);
		}

		.field-input::placeholder {
			color: var(--grey-light);
		}

		/* ── SERVICES ───────────────────────────────────────── */
		.services-section {
			padding: 96px 0;
			background: var(--white);
		}

		.section-header {
			display: flex;
			align-items: flex-end;
			justify-content: space-between;
			margin-bottom: 52px;
			gap: 32px;
		}

		.svc-grid {
			display: grid;
			grid-template-columns: repeat(3, 1fr);
			gap: 1px;
			background: var(--border);
			border: 1px solid var(--border);
			border-radius: 16px;
			overflow: hidden;
		}

		.svc-cell {
			background: var(--white);
			padding: 36px 32px;
			transition: background 0.2s;
			position: relative;
		}

		.svc-cell:hover {
			background: var(--off);
		}

		.svc-num {
			font-size: 0.68rem;
			font-weight: 800;
			letter-spacing: 0.1em;
			color: var(--border-mid);
			margin-bottom: 20px;
		}

		.svc-icon-wrap {
			width: 44px;
			height: 44px;
			background: var(--navy);
			border-radius: 10px;
			display: flex;
			align-items: center;
			justify-content: center;
			color: var(--yellow);
			font-size: 20px;
			margin-bottom: 20px;
			transition: background 0.2s, color 0.2s;
		}

		.svc-cell:hover .svc-icon-wrap {
			background: var(--yellow);
			color: var(--navy);
		}

		.svc-name {
			font-size: 1rem;
			font-weight: 700;
			letter-spacing: -0.02em;
			color: var(--navy);
			margin-bottom: 10px;
		}

		.svc-desc {
			font-size: 0.855rem;
			font-weight: 400;
			line-height: 1.6;
			color: var(--grey);
		}

		.svc-arrow {
			position: absolute;
			bottom: 28px;
			right: 28px;
			font-size: 1.1rem;
			color: var(--border-mid);
			transition: color 0.2s, transform 0.2s;
		}

		.svc-cell:hover .svc-arrow {
			color: var(--navy);
			transform: translate(3px, -3px);
		}

		/* ── COVERAGE ───────────────────────────────────────── */
		.coverage-section {
			padding: 96px 0;
			background: var(--off);
			border-top: 1px solid var(--border);
		}

		.cov-left {
			padding-right: 48px;
		}

		.feature-list {
			margin-top: 36px;
		}

		.feat-item {
			display: flex;
			gap: 16px;
			padding: 20px 0;
			border-bottom: 1px solid var(--border);
		}

		.feat-item:first-child {
			border-top: 1px solid var(--border);
		}

		.feat-icon {
			width: 38px;
			height: 38px;
			background: var(--white);
			border: 1px solid var(--border);
			border-radius: 9px;
			display: flex;
			align-items: center;
			justify-content: center;
			color: var(--navy);
			font-size: 16px;
			flex-shrink: 0;
		}

		.feat-title {
			font-size: 0.875rem;
			font-weight: 700;
			letter-spacing: -0.01em;
			color: var(--navy);
			margin-bottom: 3px;
		}

		.feat-desc {
			font-size: 0.8rem;
			font-weight: 400;
			line-height: 1.55;
			color: var(--grey-light);
		}

		.cov-card {
			border: 1px solid var(--border);
			border-radius: 14px;
			overflow: hidden;
			background: var(--white);
		}

		.cov-card-head {
			background: var(--navy);
			padding: 10px 18px;
			display: flex;
			align-items: center;
			gap: 8px;
		}

		.cov-card-head .dot {
			width: 6px;
			height: 6px;
			background: var(--yellow);
			border-radius: 50%;
			flex-shrink: 0;
		}

		.cov-card-head span {
			font-size: 0.65rem;
			font-weight: 700;
			letter-spacing: 0.1em;
			text-transform: uppercase;
			color: rgba(255, 255, 255, 0.5);
		}

		.cov-card-body {
			background: var(--off);
			padding: 28px 20px;
			text-align: center;
		}

		.cov-card-stat {
			font-size: 2rem;
			font-weight: 900;
			letter-spacing: -0.04em;
			color: var(--navy);
		}

		.cov-card-sub {
			font-size: 0.72rem;
			font-weight: 600;
			letter-spacing: 0.08em;
			text-transform: uppercase;
			color: var(--grey-light);
			margin-top: 4px;
		}

		.cov-card-foot {
			padding: 11px 18px;
			font-size: 0.78rem;
			font-weight: 600;
			color: var(--navy);
			border-top: 1px solid var(--border);
		}

		.express-banner {
			background: var(--navy);
			border-radius: 14px;
			padding: 22px 24px;
			display: flex;
			align-items: center;
			justify-content: space-between;
		}

		.express-tag {
			font-size: 0.65rem;
			font-weight: 700;
			letter-spacing: 0.1em;
			text-transform: uppercase;
			color: rgba(255, 255, 255, 0.35);
			margin-bottom: 6px;
		}

		.express-text {
			font-size: 1rem;
			font-weight: 700;
			letter-spacing: -0.02em;
			color: var(--white);
			line-height: 1.3;
		}

		.express-text span {
			color: var(--yellow);
		}

		/* ── CTA ────────────────────────────────────────────── */
		.cta-section {
			padding: 96px 0;
			background: var(--white);
			border-top: 1px solid var(--border);
		}

		.cta-box {
			background: var(--navy);
			border-radius: 20px;
			padding: 72px 64px;
			display: flex;
			align-items: center;
			justify-content: space-between;
			gap: 48px;
			flex-wrap: wrap;
			position: relative;
			overflow: hidden;
		}

		.cta-box::before {
			content: '';
			position: absolute;
			width: 280px;
			height: 280px;
			border: 40px solid rgba(255, 207, 38, 0.05);
			border-radius: 50%;
			top: -80px;
			right: 80px;
		}

		.cta-box::after {
			content: '';
			position: absolute;
			width: 140px;
			height: 140px;
			border: 20px solid rgba(255, 207, 38, 0.06);
			border-radius: 50%;
			bottom: -40px;
			right: 260px;
		}

		.cta-eyebrow {
			font-size: 0.68rem;
			font-weight: 700;
			letter-spacing: 0.12em;
			text-transform: uppercase;
			color: rgba(255, 255, 255, 0.3);
			margin-bottom: 14px;
		}

		.cta-title {
			font-size: clamp(1.8rem, 3vw, 2.8rem);
			font-weight: 900;
			letter-spacing: -0.04em;
			line-height: 1.05;
			color: var(--white);
			margin-bottom: 12px;
		}

		.cta-sub {
			font-size: 0.9rem;
			font-weight: 400;
			line-height: 1.65;
			color: rgba(255, 255, 255, 0.45);
			max-width: 420px;
		}

		.cta-actions {
			display: flex;
			flex-direction: column;
			gap: 10px;
			align-items: center;
			flex-shrink: 0;
		}

		.cta-link {
			font-size: 0.78rem;
			font-weight: 500;
			color: rgba(255, 255, 255, 0.3);
			text-decoration: none;
			transition: color 0.15s;
		}

		.cta-link:hover {
			color: rgba(255, 255, 255, 0.6);
		}

		/* ── FOOTER ─────────────────────────────────────────── */
		.site-footer {
			background: var(--off);
			border-top: 1px solid var(--border);
			padding: 60px 0 0;
		}

		.footer-col-head {
			font-size: 0.68rem;
			font-weight: 700;
			letter-spacing: 0.1em;
			text-transform: uppercase;
			color: var(--navy);
			margin-bottom: 18px;
		}

		.footer-links {
			list-style: none;
		}

		.footer-links li {
			margin-bottom: 11px;
		}

		.footer-links a {
			font-size: 0.855rem;
			font-weight: 400;
			color: var(--grey);
			text-decoration: none;
			transition: color 0.15s;
		}

		.footer-links a:hover {
			color: var(--navy);
		}

		.footer-tagline {
			font-size: 0.82rem;
			font-weight: 400;
			line-height: 1.6;
			color: var(--grey-light);
			max-width: 220px;
		}

		.footer-bottom {
			border-top: 1px solid var(--border);
			margin-top: 52px;
			padding: 22px 0;
			display: flex;
			justify-content: space-between;
			align-items: center;
			flex-wrap: wrap;
			gap: 12px;
		}

		.footer-copy {
			font-size: 0.78rem;
			font-weight: 400;
			color: var(--grey-light);
		}

		/* ── WA FLOAT ───────────────────────────────────────── */
		.wa-float {
			position: fixed;
			bottom: 32px;
			right: 32px;
			width: 52px;
			height: 52px;
			background: #25d366;
			border-radius: 12px;
			display: flex;
			align-items: center;
			justify-content: center;
			color: var(--white);
			font-size: 22px;
			text-decoration: none;
			box-shadow: 0 8px 20px rgba(37, 211, 102, 0.35);
			z-index: 800;
			transition: transform 0.18s;
		}

		.wa-float:hover {
			transform: translateY(-3px);
			color: var(--white);
		}

		/* ── RESPONSIVE ─────────────────────────────────────── */
		@media (max-width: 991px) {
			.hero-slider {
				height: 480px;
			}

			.svc-grid {
				grid-template-columns: repeat(2, 1fr);
			}

			.cov-left {
				padding-right: 0;
				margin-bottom: 40px;
			}

			.cta-box {
				padding: 48px 32px;
			}

			.section-header {
				flex-direction: column;
				align-items: flex-start;
			}
		}

		@media (max-width: 767px) {

			.nav-menu,
			.nav-end {
				display: none;
			}

			.hero-slider {
				height: 360px;
			}

			.slider-arrow {
				width: 38px;
				height: 38px;
				font-size: 15px;
			}

			.slider-arrow.prev {
				left: 12px;
			}

			.slider-arrow.next {
				right: 12px;
			}

			.svc-grid {
				grid-template-columns: 1fr;
			}

			.cta-box {
				padding: 36px 24px;
			}

			.widget-body {
				padding: 24px 20px;
			}
		}

		/* ── HAMBURGER ──────────────────────────────────── */
		.nav-hamburger {
			display: none;
			flex-direction: column;
			justify-content: center;
			gap: 5px;
			width: 40px;
			height: 40px;
			background: var(--off);
			border: 1px solid var(--border);
			border-radius: 9px;
			cursor: pointer;
			padding: 8px;
			margin-left: auto;
			transition: background 0.15s;
		}

		.nav-hamburger span {
			display: block;
			width: 100%;
			height: 2px;
			background: var(--navy);
			border-radius: 2px;
			transition: transform 0.3s, opacity 0.3s;
		}

		.nav-hamburger.open span:nth-child(1) {
			transform: translateY(7px) rotate(45deg);
		}

		.nav-hamburger.open span:nth-child(2) {
			opacity: 0;
		}

		.nav-hamburger.open span:nth-child(3) {
			transform: translateY(-7px) rotate(-45deg);
		}

		/* ── MOBILE OVERLAY ─────────────────────────────── */
		.mobile-overlay {
			display: none;
			position: fixed;
			inset: 0;
			background: rgba(15, 39, 64, 0.5);
			z-index: 1000;
			backdrop-filter: blur(3px);
		}

		.mobile-overlay.show {
			display: block;
		}

		/* ── MOBILE DRAWER ──────────────────────────────── */
		.mobile-drawer {
			position: fixed;
			top: 0;
			right: 0;
			width: 300px;
			height: 100%;
			background: var(--white);
			z-index: 1001;
			transform: translateX(100%);
			transition: transform 0.32s cubic-bezier(0.4, 0, 0.2, 1);
			display: flex;
			flex-direction: column;
			box-shadow: -8px 0 40px rgba(15, 39, 64, 0.15);
		}

		.mobile-drawer.open {
			transform: translateX(0);
		}

		.drawer-header {
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding: 20px 20px;
			border-bottom: 1px solid var(--border);
		}

		.drawer-close {
			width: 36px;
			height: 36px;
			background: var(--off);
			border: 1px solid var(--border);
			border-radius: 8px;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 15px;
			color: var(--navy);
			cursor: pointer;
			transition: background 0.15s;
		}

		.drawer-close:hover {
			background: var(--border);
		}

		.drawer-menu {
			list-style: none;
			padding: 16px 12px;
			flex: 1;
			overflow-y: auto;
		}

		.drawer-menu li a {
			display: flex;
			align-items: center;
			gap: 12px;
			padding: 13px 16px;
			font-size: 0.92rem;
			font-weight: 600;
			color: var(--grey);
			text-decoration: none;
			border-radius: 10px;
			transition: background 0.15s, color 0.15s;
		}

		.drawer-menu li a:hover {
			background: var(--off);
			color: var(--navy);
		}

		.drawer-menu li a i {
			font-size: 16px;
			color: var(--navy);
			width: 20px;
			text-align: center;
		}

		.drawer-footer {
			padding: 20px;
			border-top: 1px solid var(--border);
		}

		/* ── RESPONSIVE UPDATE ──────────────────────────── */
		@media (max-width: 767px) {

			.nav-menu,
			.nav-end {
				display: none;
			}

			.nav-hamburger {
				display: flex;
			}
		}
	</style>
</head>

<body>

	<!-- ════════════════════════════════════════════
     NAVBAR
════════════════════════════════════════════ -->
	<nav class="site-nav">
		<div class="container">
			<div class="nav-inner">
				<a href="<?= base_url('home') ?>" class="nav-brand">
					<img src="<?= base_url() ?>assets/logo/logo-smesco-hera-2.png" width="150" height="" alt="SMESCO Express" class="navbar-brand-image">
				</a>

				<ul class="nav-menu">
					<li><a href="<?= base_url('home') ?>">Home</a></li>
					<li><a href="<?= base_url('home/tracking') ?>">Lacak Resi</a></li>
					<li><a href="#layanan">Layanan</a></li>
					<li><a href="#jangkauan">Jangkauan</a></li>
				</ul>

				<div class="nav-end">
					<?php if ($this->session->userdata('is_logged_in')) : ?>
						<a href="<?= base_url('dashboard') ?>" class="btn-dark">Dashboard</a>
					<?php else : ?>
						<a href="<?= base_url('auth') ?>" class="btn-dark">Login</a>
					<?php endif; ?>
				</div>

				<!-- Hamburger Button (mobile only) -->
				<button class="nav-hamburger" id="navToggle" aria-label="Toggle menu">
					<span></span>
					<span></span>
					<span></span>
				</button>
			</div>
		</div>
	</nav>

	<!-- Mobile Drawer -->
	<div class="mobile-overlay" id="mobileOverlay"></div>
	<div class="mobile-drawer" id="mobileDrawer">
		<div class="drawer-header">
			<img src="<?= base_url() ?>assets/logo/logo-smesco-hera-2.png" width="130" alt="SMESCO Express">
			<button class="drawer-close" id="drawerClose" aria-label="Close menu">
				<i class="bi bi-x-lg"></i>
			</button>
		</div>
		<ul class="drawer-menu">
			<li><a href="<?= base_url('home') ?>"><i class="bi bi-house"></i> Home</a></li>
			<li><a href="<?= base_url('home/tracking') ?>"><i class="bi bi-search"></i> Lacak Resi</a></li>
			<li><a href="#layanan"><i class="bi bi-box-seam"></i> Layanan</a></li>
			<li><a href="#jangkauan"><i class="bi bi-geo-alt"></i> Jangkauan</a></li>
		</ul>
		<div class="drawer-footer">
			<?php if ($this->session->userdata('is_logged_in')) : ?>
				<a href="<?= base_url('dashboard') ?>" class="btn-primary-cta" style="width:100%;justify-content:center;">
					<i class="bi bi-speedometer2"></i> Dashboard
				</a>
			<?php else : ?>
				<a href="<?= base_url('auth') ?>" class="btn-primary-cta" style="width:100%;justify-content:center;">
					<i class="bi bi-box-arrow-in-right"></i> Login
				</a>
			<?php endif; ?>
		</div>
	</div>

	<?php $this->load->view($pages) ?>

	<!-- ════════════════════════════════════════════
     FOOTER
════════════════════════════════════════════ -->
	<footer class="site-footer">
		<div class="container">
			<div class="row g-5">
				<div class="col-lg-4">
					<div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
						<img src="<?= base_url() ?>assets/logo/logo-smesco-hera-2.png" width="300" alt="Smesco Express">
					</div>
					<!-- UPDATED: friendlier tagline -->
					<p class="footer-tagline">
						Teman kirim terpercaya untuk UMKM Indonesia — dari dalam negeri sampai mancanegara.
					</p>
				</div>
				<div class="col-6 col-lg-2">
					<div class="footer-col-head">Layanan</div>
					<ul class="footer-links">
						<li><a href="#">Pengiriman Udara</a></li>
						<li><a href="#">Pengiriman Darat</a></li>
						<li><a href="#">Ekspor Impor</a></li>
						<li><a href="#">Asuransi Kargo</a></li>
						<li><a href="#">Fulfillment</a></li>
					</ul>
				</div>
				<div class="col-6 col-lg-2">
					<div class="footer-col-head">Perusahaan</div>
					<ul class="footer-links">
						<li><a href="#">Tentang Kami</a></li>
						<li><a href="#">Karir</a></li>
						<li><a href="#">Blog</a></li>
						<li><a href="#">Kontak</a></li>
					</ul>
				</div>
				<div class="col-lg-3">
					<div class="footer-col-head">Hubungi Kami</div>
					<ul class="footer-links">
						<li><a href="#"><i class="bi bi-whatsapp me-2" style="color:#25d366;"></i>+62 812-3456-7890</a></li>
						<li><a href="#"><i class="bi bi-envelope me-2" style="color:var(--navy);"></i>info@smesco.go.id</a></li>
						<li><a href="#"><i class="bi bi-geo-alt me-2" style="color:var(--navy);"></i>DKI Jakarta, Indonesia</a></li>
					</ul>
				</div>
			</div>
			<div class="footer-bottom">
				<span class="footer-copy">© 2026 Smesco Express. All rights reserved.</span>
				<span class="footer-copy">Teman Kirim Terpercaya untuk UMKM Indonesia</span>
			</div>
		</div>
	</footer>

	<!-- WA Float -->
	<a href="https://wa.me/6281234567890" class="wa-float" target="_blank" aria-label="WhatsApp">
		<i class="bi bi-whatsapp"></i>
	</a>

	<script>
		const navToggle = document.getElementById('navToggle');
		const mobileDrawer = document.getElementById('mobileDrawer');
		const mobileOverlay = document.getElementById('mobileOverlay');
		const drawerClose = document.getElementById('drawerClose');

		function openDrawer() {
			mobileDrawer.classList.add('open');
			mobileOverlay.classList.add('show');
			navToggle.classList.add('open');
			document.body.style.overflow = 'hidden';
		}

		function closeDrawer() {
			mobileDrawer.classList.remove('open');
			mobileOverlay.classList.remove('show');
			navToggle.classList.remove('open');
			document.body.style.overflow = '';
		}

		navToggle.addEventListener('click', openDrawer);
		drawerClose.addEventListener('click', closeDrawer);
		mobileOverlay.addEventListener('click', closeDrawer);

		// Tutup drawer saat klik link anchor
		document.querySelectorAll('.drawer-menu a').forEach(link => {
			link.addEventListener('click', closeDrawer);
		});
	</script>
</body>

</html>
