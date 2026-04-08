<div class="navbar">
	<div class="container-xl">
		<ul class="navbar-nav">
			<?php
			$role_slug = $this->session->userdata('user')['role_slug'];
			$menus     = $this->config->item('menus');
			$nav_items = isset($menus[$role_slug]) ? $menus[$role_slug] : [];
			$segment   = $this->uri->segment(1);

			foreach ($nav_items as $item):
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
