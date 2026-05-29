<?php global $current_view; ?>
<div class="listing-switch-view ps-2" role="group">
	<ul class="list-inline m-0" role="list">
		<li class="list-inline-item">
			<a class="switch-btn btn-list<?php echo $current_view === 'list' ? ' active' : ''; ?>" href="#" role="button" aria-pressed="<?php echo $current_view === 'list' ? 'true' : 'false'; ?>">
				<i class="houzez-icon icon-layout-bullets" aria-hidden="true"></i>
			</a>
		</li>
		<li class="list-inline-item">
			<a class="switch-btn btn-grid<?php echo $current_view === 'grid' ? ' active' : ''; ?>" href="#" role="button" aria-pressed="<?php echo $current_view === 'grid' ? 'true' : 'false'; ?>">
				<i class="houzez-icon icon-layout-module-1" aria-hidden="true"></i>
			</a>
		</li>
	</ul>
</div><!-- listing-switch-view -->