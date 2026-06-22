<div class="navbar">
	<div class="container-xl">
		<ul class="navbar-nav">
			<?php
			$user_session = $this->session->userdata('user');
			$can_scan_pickup   = $user_session['can_scan_pickup_as_driver'] ?? false;
			$can_scan_at_origin   = $user_session['can_scan_at_origin'] ?? false;
			$role_slug    = $user_session['role_slug'];
			$menus        = $this->config->item('menus');
			$nav_items    = $menus[$role_slug] ?? [];
			$segment      = $this->uri->segment(1);

			foreach ($nav_items as $item):
				if (!empty($item['show_if_can_scan_pickup']) && !$can_scan_pickup) {
					continue;
				}
				if (!empty($item['show_if_can_scan_at_origin']) && !$can_scan_at_origin) {
					continue;
				}
				
				$has_child = !empty($item['children']);
				$is_active = ($segment == $item['segment']) ? 'active' : '';
			?>
				<li class="nav-item <?= $has_child ? 'dropdown' : '' ?> <?= $is_active ?>">
					<a href="<?= $has_child ? '#' : base_url($item['url']) ?>"
						class="nav-link <?= $has_child ? 'dropdown-toggle' : '' ?>"
						<?= $has_child ? 'data-bs-toggle="dropdown" role="button" aria-expanded="false"' : '' ?>>
						<span class="nav-link-icon d-md-none d-lg-inline-block">
							<?= tabler_icon($item['icon']) ?>
						</span>
						<span class="nav-link-title"><?= $item['title'] ?></span>
					</a>

					<?php if ($has_child): ?>
						<div class="dropdown-menu">
							<?php foreach ($item['children'] as $child): ?>
								<a class="dropdown-item <?= ($this->uri->uri_string() == $child['url']) ? 'active' : '' ?>"
									href="<?= base_url($child['url']) ?>">
									<?= $child['title'] ?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
