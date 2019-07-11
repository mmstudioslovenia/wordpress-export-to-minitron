<hr/>

<h3><?php _e('Your Minitron Partners Lists', 'wp-minitron-export'); ?></h3>
<p><?php _e('The table below shows your Minitron partners lists and their details. If you just applied changes to your partners lists, please use the following button to renew the cached lists configuration.', 'wp-minitron-export'); ?></p>

<div id="list-fetcher">
	<form method="post" action="">
		<input type="hidden" name="_mtwp_action" value="renew_lists" />
        <p>
			<input type="submit" value="<?php _e('Renew Minitron lists', 'wp-minitron-export'); ?>" class="button" />
		</p>
	</form>
</div>

<div class="lists-overview">
	<?php if (empty($lists)) {
    ?>
		<p><?php _e('No lists were found in your Minitron account', 'wp-minitron-export'); ?>.</p>
	<?php
} else {
        printf('<p>' . __('A total of %d lists were found in your Minitron account.', 'wp-minitron-export') . '</p>', count($lists));

        echo '<table class="widefat striped">';

        $headings = array(
            __('List Name', 'wp-minitron-export'),
            __('ID', 'wp-minitron-export'),
            __('Subscribers', 'wp-minitron-export')
        );

        echo '<thead>';
        echo '<tr>';
        foreach ($headings as $heading) {
            echo sprintf('<th>%s</th>', $heading);
        }
        echo '</tr>';
        echo '</thead>';

        foreach ($lists as $k => $list) {
            
            echo '<tr>';
            echo sprintf('<td><a href="javascript:mc4wp.helpers.toggleElement(\'.list-%s-details\')">%s</a><span class="row-actions alignright"></span></td>', $list['id'], esc_html($list['cat_title']));
            echo sprintf('<td><code>%s</code></td>', esc_html($list['id']));
            echo sprintf('<td>%s</td>', esc_html($list->subscriber_count));
            echo '</tr>';

            echo sprintf('<tr class="list-details list-%s-details" style="display: none;">', $list['id']);
            echo '</tr>'; ?>
		<?php
        } // end foreach $lists
        echo '</table>';
    } // end if empty?>
</div>