<!-- ════════════════════════════════════════════
     HERO SLIDER
════════════════════════════════════════════ -->
<section class="hero-slider" id="heroSlider">

	<div class="slider-progress" id="sliderProgress"></div>
	<div class="slider-pause-badge">
		<i class="bi bi-pause-fill"></i> Paused
	</div>

	<div class="slide active" data-index="0">
		<img src="assets/landing-page/img/hero/slide-1.png" alt="Slide 1" class="slide-img" />
	</div>
	<div class="slide" data-index="1">
		<img src="assets/landing-page/img/hero/slide-2.png" alt="Slide 2" class="slide-img" />
	</div>
	<div class="slide" data-index="2">
		<img src="assets/landing-page/img/hero/slide-3.png" alt="Slide 3" class="slide-img" />
	</div>

	<button class="slider-arrow prev" id="sliderPrev" aria-label="Slide sebelumnya">
		<i class="bi bi-chevron-left"></i>
	</button>
	<button class="slider-arrow next" id="sliderNext" aria-label="Slide berikutnya">
		<i class="bi bi-chevron-right"></i>
	</button>

	<div class="slider-dots" role="tablist" aria-label="Navigasi slide">
		<button class="slider-dot active" data-target="0" aria-label="Slide 1"></button>
		<button class="slider-dot" data-target="1" aria-label="Slide 2"></button>
		<button class="slider-dot" data-target="2" aria-label="Slide 3"></button>
	</div>

	<div class="slider-counter">
		<span class="counter-current" id="counterCurrent">1</span>
		<span class="counter-sep">/</span>
		<span class="counter-total">3</span>
	</div>

</section>

<!-- ════════════════════════════════════════════
     TICKER
════════════════════════════════════════════ -->
<div class="ticker-bar" aria-hidden="true">
	<div class="ticker-track">
		<span class="ticker-item"><i class="bi bi-circle-fill"></i> Pengiriman Udara</span>
		<span class="ticker-item"><i class="bi bi-circle-fill"></i> Pengiriman Darat</span>
		<span class="ticker-item"><i class="bi bi-circle-fill"></i> Ekspor Internasional</span>
		<span class="ticker-item"><i class="bi bi-circle-fill"></i> Tracking Realtime</span>
		<span class="ticker-item"><i class="bi bi-circle-fill"></i> Asuransi Kargo</span>
		<span class="ticker-item"><i class="bi bi-circle-fill"></i> Fulfillment UMKM</span>
		<span class="ticker-item"><i class="bi bi-circle-fill"></i> Bea Cukai</span>
		<span class="ticker-item"><i class="bi bi-circle-fill"></i> 98% Area Indonesia</span>
		<span class="ticker-item"><i class="bi bi-circle-fill"></i> Pengiriman Udara</span>
		<span class="ticker-item"><i class="bi bi-circle-fill"></i> Pengiriman Darat</span>
		<span class="ticker-item"><i class="bi bi-circle-fill"></i> Ekspor Internasional</span>
		<span class="ticker-item"><i class="bi bi-circle-fill"></i> Tracking Realtime</span>
		<span class="ticker-item"><i class="bi bi-circle-fill"></i> Asuransi Kargo</span>
		<span class="ticker-item"><i class="bi bi-circle-fill"></i> Fulfillment UMKM</span>
		<span class="ticker-item"><i class="bi bi-circle-fill"></i> Bea Cukai</span>
		<span class="ticker-item"><i class="bi bi-circle-fill"></i> 98% Area Indonesia</span>
	</div>
</div>

<!-- ════════════════════════════════════════════
     STATS
════════════════════════════════════════════ -->
<section class="stats-band">
	<div class="container">
		<div class="row g-4 align-items-center">
			<div class="col-6 col-md-3">
				<div class="stat-block">
					<span class="stat-number">98<span class="stat-accent-char">%</span></span>
					<span class="stat-label">Jangkauan Dalam Negeri</span>
				</div>
			</div>
			<div class="col-6 col-md-3">
				<div class="stat-block">
					<span class="stat-number">5<span class="stat-accent-char">+</span></span>
					<span class="stat-label">Negara yang Bisa Kita Kirim</span>
				</div>
			</div>
			<div class="col-6 col-md-3">
				<div class="stat-block">
					<span class="stat-number">24<span class="stat-accent-char">/7</span></span>
					<span class="stat-label">Pantau Paketmu Kapan Saja</span>
				</div>
			</div>
			<!-- ✅ UPDATED: Ganti "2023 Melayani Sejak" dengan data Smesco -->
			<div class="col-6 col-md-3">
				<div class="stat-block">
					<span class="stat-number">82<span class="stat-accent-char">rb+</span></span>
					<span class="stat-label">UMKM Terfasilitasi Smesco</span>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- ════════════════════════════════════════════
     TRACKING WIDGET
════════════════════════════════════════════ -->
<section class="widget-section" id="tracking">
	<div class="container">
		<div class="row justify-content-center text-center mb-5">
			<div class="col-lg-6">
				<div class="t-label mb-2">Tracking & Tarif</div>
				<h2 class="t-section">Mau Kirim atau<br>Cek Paketmu?</h2>
			</div>
		</div>
		<div class="row justify-content-center">
			<div class="col-lg-8">
				<div class="widget-frame">
					<div class="widget-tabbar">
						<button class="wtab active" onclick="switchTab(event,'wpane-resi')">
							<i class="bi bi-geo-alt-fill"></i> Lacak Resi
						</button>
						<!-- <button class="wtab" onclick="switchTab(event,'wpane-ongkir')">
							<i class="bi bi-calculator-fill"></i> Cek Ongkir
						</button> -->
					</div>
					<div class="widget-body">
						<div class="wpane active" id="wpane-resi">
							<form action="<?= site_url('home/tracking') ?>" method="GET">
								<label class="field-label">Nomor Resi / AWB</label>
								<div style="display:flex;gap:10px;">
									<input name="awb" class="field-input" type="text" placeholder="Contoh: SMX2024123456..." style="flex:1;" required />
									<button type="submit" class="btn-dark" style="padding:13px 24px;border-radius:10px;white-space:nowrap;font-size:0.875rem;">
										<i class="bi bi-search"></i> Lacak
									</button>
								</div>
								<p style="font-size:0.75rem;color:var(--grey-light);margin-top:10px;margin-bottom:0;">
									Ketik nomor resimu di sini, langsung ketahuan posisinya! 📦
								</p>
							</form>
						</div>
						<div class="wpane" id="wpane-ongkir">
							<div class="row g-3">
								<div class="col-md-5">
									<label class="field-label">Kota Asal</label>
									<input class="field-input" type="text" placeholder="Contoh: Pekanbaru" />
								</div>
								<div class="col-md-5">
									<label class="field-label">Kota Tujuan</label>
									<input class="field-input" type="text" placeholder="Contoh: Jakarta" />
								</div>
								<div class="col-md-2 d-flex align-items-end">
									<button class="btn-dark w-100" style="padding:13px 12px;border-radius:10px;">
										<i class="bi bi-search"></i>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- ════════════════════════════════════════════
     SERVICES
════════════════════════════════════════════ -->
<section class="services-section" id="layanan">
	<div class="container">
		<div class="section-header">
			<div>
				<div class="t-label mb-2">Layanan Kami</div>
				<h2 class="t-section">Semua Kebutuhan<br>Kirimmu, Kami Urus</h2>
			</div>
			<!-- ✅ UPDATED: Tone lebih konsisten casual -->
			<p class="t-body" style="max-width:320px;margin:0;flex-shrink:0;">
				Mau kirim antar kota, antar pulau, sampai luar negeri? Satu platform, semua bisa diurus dari sini.
			</p>
		</div>
		<div class="svc-grid">
			<div class="svc-cell">
				<div class="svc-num">01</div>
				<div class="svc-icon-wrap"><i class="bi bi-airplane-fill"></i></div>
				<div class="svc-name">Pengiriman Udara</div>
				<!-- ✅ UPDATED: Hapus kata "ekspres" yang redundan, tone lebih natural -->
				<p class="svc-desc">Kirim kargo antar pulau lewat jalur udara — cepat, aman, dan bisa dipantau kapan saja.</p>
				<span class="svc-arrow"><i class="bi bi-arrow-up-right"></i></span>
			</div>
			<div class="svc-cell">
				<div class="svc-num">02</div>
				<div class="svc-icon-wrap"><i class="bi bi-truck"></i></div>
				<div class="svc-name">Pengiriman Darat</div>
				<!-- ✅ UPDATED: Hilangkan "Armada trucking handal" yang terlalu formal -->
				<p class="svc-desc">Jangkau seluruh pelosok Nusantara dengan tarif yang bersaing dan armada yang terpercaya.</p>
				<span class="svc-arrow"><i class="bi bi-arrow-up-right"></i></span>
			</div>
			<div class="svc-cell">
				<div class="svc-num">03</div>
				<div class="svc-icon-wrap"><i class="bi bi-globe2"></i></div>
				<div class="svc-name">Ekspor Internasional</div>
				<!-- ✅ UPDATED: Lebih ringkas dan actionable -->
				<p class="svc-desc">Urusan bea cukai dan dokumen ekspor-impor? Kami bantu dari awal sampai paketmu sampai tujuan.</p>
				<span class="svc-arrow"><i class="bi bi-arrow-up-right"></i></span>
			</div>
			<div class="svc-cell">
				<div class="svc-num">04</div>
				<div class="svc-icon-wrap"><i class="bi bi-box-seam-fill"></i></div>
				<div class="svc-name">Fulfillment UMKM</div>
				<p class="svc-desc">Solusi gudang dan fulfillment khusus buat kamu yang baru mulai atau sudah scale-up bisnis.</p>
				<span class="svc-arrow"><i class="bi bi-arrow-up-right"></i></span>
			</div>
			<div class="svc-cell">
				<div class="svc-num">05</div>
				<div class="svc-icon-wrap"><i class="bi bi-shield-check"></i></div>
				<div class="svc-name">Asuransi Kargo</div>
				<p class="svc-desc">Tenang aja — setiap kiriman bisa diasuransikan, dan proses klaimnya gak ribet.</p>
				<span class="svc-arrow"><i class="bi bi-arrow-up-right"></i></span>
			</div>
			<div class="svc-cell">
				<div class="svc-num">06</div>
				<div class="svc-icon-wrap"><i class="bi bi-qr-code-scan"></i></div>
				<div class="svc-name">Tracking Realtime</div>
				<p class="svc-desc">Pantau posisi paketmu dari mana saja, kapan saja — update otomatis biar kamu selalu tahu.</p>
				<span class="svc-arrow"><i class="bi bi-arrow-up-right"></i></span>
			</div>
		</div>
	</div>
</section>

<!-- ════════════════════════════════════════════
     COVERAGE
════════════════════════════════════════════ -->
<section class="coverage-section" id="jangkauan">
	<div class="container">
		<div class="row align-items-center g-5">
			<div class="col-lg-6 cov-left">
				<div class="t-label mb-2">Jangkauan</div>
				<h2 class="t-section mb-3">Dari Ujung Aceh<br>sampai Papua, Kita Bisa!</h2>
				<!-- ✅ UPDATED: Lebih personal dan mengalir -->
				<p class="t-body" style="max-width:420px;">
					Produk lokalmu layak go global. Smesco Express siap jadi mitra kirimmu ke mana pun tujuannya — dalam negeri maupun luar negeri.
				</p>
				<div class="feature-list">
					<div class="feat-item">
						<div class="feat-icon"><i class="bi bi-map-fill"></i></div>
						<div>
							<div class="feat-title">Domestik — 98% Area Indonesia</div>
							<div class="feat-desc">Dari Sabang sampai Merauke, kami siap menjangkau setiap sudut Indonesia buat kamu.</div>
						</div>
					</div>
					<div class="feat-item">
						<div class="feat-icon"><i class="bi bi-globe-americas"></i></div>
						<div>
							<div class="feat-title">Internasional — 5+ Negara Tujuan</div>
							<div class="feat-desc">Ekspor ke Malaysia, Singapura, Australia, Jepang, dan negara lainnya — semua bisa!</div>
						</div>
					</div>
					<div class="feat-item">
						<div class="feat-icon"><i class="bi bi-clock-fill"></i></div>
						<div>
							<div class="feat-title">Estimasi Pengiriman Tepat Waktu</div>
							<div class="feat-desc">Estimasi waktu jelas dari awal, jadi kamu bisa planning bisnis dengan lebih tenang.</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="row g-3">
					<div class="col-6">
						<div class="cov-card">
							<div class="cov-card-head">
								<div class="dot"></div><span>Domestik</span>
							</div>
							<div class="cov-card-body">
								<div class="cov-card-stat">98<span style="color:var(--yellow);font-size:1.4rem;">%</span></div>
								<div class="cov-card-sub">Area Indonesia</div>
								<img src="<?= base_url() ?>assets/landing-page/img/map-smesco-1.png" class="img-fluid" alt="">
							</div>
							<div class="cov-card-foot">Seluruh Indonesia</div>
						</div>
					</div>
					<div class="col-6">
						<div class="cov-card">
							<div class="cov-card-head">
								<div class="dot"></div><span>Internasional</span>
							</div>
							<div class="cov-card-body">
								<div class="cov-card-stat">5<span style="color:var(--yellow);font-size:1.4rem;">+</span></div>
								<div class="cov-card-sub">Negara Tujuan</div>
								<img src="<?= base_url() ?>assets/landing-page/img/map-smesco-2.png" class="img-fluid" alt="">
							</div>
							<div class="cov-card-foot">Asia Pacific & Beyond</div>
						</div>
					</div>
					<div class="col-12">
						<div class="express-banner">
							<div>
								<div class="express-tag">Next-Day Delivery</div>
								<div class="express-text">Jakarta → Surabaya<br>dalam <span>1 hari kerja</span></div>
							</div>
							<i class="bi bi-airplane-fill" style="font-size:2.2rem;color:rgba(255,255,255,0.08);"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- ════════════════════════════════════════════
     CTA
════════════════════════════════════════════ -->
<section class="cta-section">
	<div class="container">
		<div class="cta-box">
			<div>
				<div class="cta-eyebrow">Bergabung Sekarang</div>
				<h2 class="cta-title">Yuk, Gabung Jadi<br>Mitra Agen Kami!</h2>
				<!-- ✅ UPDATED: Lebih spesifik soal benefit -->
				<p class="cta-sub">
					Komisi menarik, sistem siap pakai, dan tim support kami siap bantu dari hari pertama. Mulai agenmu sekarang — gratis daftar!
				</p>
			</div>
			<div class="cta-actions">
				<a href="#" class="btn-primary-cta" style="font-size:0.95rem;padding:15px 32px;">
					<i class="bi bi-people-fill"></i> Daftar Mitra Agen
				</a>
				<!-- ✅ UPDATED: Ganti "Tanya-tanya dulu" → link WhatsApp yang actionable -->
				<a href="https://wa.me/6281234567890" target="_blank" class="cta-link">
					<i class="bi bi-whatsapp"></i> Chat CS Kami →
				</a>
			</div>
		</div>
	</div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
	/* ════════════════════════════════════════════
     HERO SLIDER LOGIC
  		════════════════════════════════════════════ */
	(function() {
		const SLIDE_DURATION = 5000;
		const FADE_DURATION = 900;

		const wrapper = document.getElementById('heroSlider');
		const slides = Array.from(wrapper.querySelectorAll('.slide'));
		const dots = Array.from(wrapper.querySelectorAll('.slider-dot'));
		const progress = document.getElementById('sliderProgress');
		const counter = document.getElementById('counterCurrent');
		const btnPrev = document.getElementById('sliderPrev');
		const btnNext = document.getElementById('sliderNext');

		let current = 0;
		let timer = null;
		let isPaused = false;
		let isAnimating = false;

		function goTo(index, direction) {
			if (isAnimating || index === current) return;
			isAnimating = true;

			slides[current].classList.remove('active');
			slides[index].classList.add('active');

			dots[current].classList.remove('active');
			dots[index].classList.add('active');

			counter.textContent = index + 1;

			current = index;

			resetProgress();

			setTimeout(() => {
				isAnimating = false;
				if (!isPaused) startProgress();
			}, FADE_DURATION);
		}

		function next() {
			goTo((current + 1) % slides.length);
		}

		function prev() {
			goTo((current - 1 + slides.length) % slides.length);
		}

		function resetProgress() {
			progress.classList.remove('animating');
			progress.style.transition = 'none';
			progress.style.width = '0%';
			void progress.offsetWidth;
		}

		function startProgress() {
			resetProgress();
			requestAnimationFrame(() => {
				requestAnimationFrame(() => {
					progress.classList.add('animating');
				});
			});
		}

		function startTimer() {
			clearInterval(timer);
			timer = setInterval(() => {
				if (!isPaused) next();
			}, SLIDE_DURATION);
		}

		function pause() {
			isPaused = true;
			wrapper.classList.add('paused');
			clearInterval(timer);
			progress.classList.remove('animating');
		}

		function resume() {
			isPaused = false;
			wrapper.classList.remove('paused');
			startProgress();
			startTimer();
		}

		btnNext.addEventListener('click', () => {
			pause();
			next();
			resume();
		});
		btnPrev.addEventListener('click', () => {
			pause();
			prev();
			resume();
		});

		dots.forEach(dot => {
			dot.addEventListener('click', () => {
				const target = parseInt(dot.dataset.target);
				pause();
				goTo(target);
				resume();
			});
		});

		wrapper.addEventListener('mouseenter', pause);
		wrapper.addEventListener('mouseleave', resume);

		document.addEventListener('keydown', (e) => {
			if (e.key === 'ArrowLeft') {
				pause();
				prev();
				resume();
			}
			if (e.key === 'ArrowRight') {
				pause();
				next();
				resume();
			}
		});

		let touchStartX = 0;
		wrapper.addEventListener('touchstart', (e) => {
			touchStartX = e.changedTouches[0].clientX;
			pause();
		}, {
			passive: true
		});

		wrapper.addEventListener('touchend', (e) => {
			const diff = touchStartX - e.changedTouches[0].clientX;
			if (Math.abs(diff) > 50) {
				diff > 0 ? next() : prev();
			}
			resume();
		}, {
			passive: true
		});

		startProgress();
		startTimer();
	})();

	/* ════════════════════════════════════════════
	   TRACKING WIDGET TAB
	════════════════════════════════════════════ */
	function switchTab(e, target) {
		document.querySelectorAll('.wtab').forEach(t => t.classList.remove('active'));
		document.querySelectorAll('.wpane').forEach(p => p.classList.remove('active'));
		e.currentTarget.classList.add('active');
		document.getElementById(target).classList.add('active');
	}
</script>
