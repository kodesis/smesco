<style>
	.bulk-off {
		display: none !important;
	}

	.chk-lg {
		width: 1.4rem;
		height: 1.4rem;
	}

	.ui-autocomplete {
		z-index: 1060 !important;
		max-width: calc(100vw - 2rem);
	}

	.sticky-bar {
		position: fixed;
		left: 0;
		right: 0;
		bottom: 0;
		z-index: 1030;
		background: var(--tblr-bg-surface, #fff);
		border-top: 1px solid rgba(0, 0, 0, .1);
		padding: .75rem 1rem calc(.75rem + env(safe-area-inset-bottom));
		box-shadow: 0 -4px 12px rgba(0, 0, 0, .08);
	}

	@media (max-width: 991.98px) {
		.page-body {
			padding-bottom: 6rem;
		}
	}

	@media (max-width: 767.98px) {
		.page-body {
			padding-bottom: 6rem;
		}

		.page-header .btn-list {
			width: 100%;
			flex-wrap: wrap;
		}

		.page-header .btn-list .btn {
			flex: 1 1 40%;
		}

		.page-header .btn-list .btn-back {
			flex: 0 0 auto;
		}

		.btn {
			min-height: 44px;
		}

		.form-control,
		.form-select {
			min-height: 46px;
			font-size: 16px;
		}

		/* cegah zoom iOS */
		.select2-container .select2-selection--single {
			height: 46px;
		}

		.select2-container--default .select2-selection--single .select2-selection__rendered {
			line-height: 44px;
			font-size: 16px;
		}

		.select2-container--default .select2-selection--single .select2-selection__arrow {
			height: 44px;
		}

		.tracking-list {
			max-height: none !important;
		}

		/* hindari scroll bersarang */
	}
</style>
