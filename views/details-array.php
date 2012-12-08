<?php if( is_array( $details ) && !empty( $details ) ) : ?>
	
	<table>
		<?php foreach( $details as $key => $value ) : ?>
			<tr>
				<td><?php esc_html_e( $key ); ?></td>
				<td><?php esc_html_e( maybe_serialize( $value ) ); ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
	
	<?php if( $item->operation  == 'update_option' ) : ?>
		<p class="description">Note: This indicates an attempt to change the option above, but due to technical limitations, it is not possible to determine if the update succeeded or not.</p>
	<?php endif; ?>

<?php else : ?>
	
	<p>No details available</p>
	
<?php endif; ?>