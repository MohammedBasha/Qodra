<?php
/*
 * Copyright (C) joomla-monster.com
 * Website: http://www.joomla-monster.com
 * Support: info@joomla-monster.com
 *
 * JM Counter is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * JM Counter is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with JM Counter. If not, see <http://www.gnu.org/licenses/>.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$row_number = ( $elements > $span_size ) ? $span_size : $elements;

$i = 0;

?>

<div id="<?php echo $id; ?>" class="jmm-counter <?php echo $theme_class . ' ' . $mod_class_suffix; ?>">
	<div class="jmm-counter-in">
		<div class="jmm-mod-row row-<?php echo $row_number; ?>">
				<?php

				foreach($output_data as $item) {

					$from = ( $item->from ) ? $item->from : 0;

					$speed = ( $item->speed ) ? ' data-speed="' . $item->speed . '"' : '';
					$interval = ( $item->interval ) ? ' data-refresh-interval="' . $item->interval . '"' : '';
					
					$alt = ( !empty($item->alt) ) ? $item->alt : '';
					
					$i++;

				?>

					<?php if( $item->to ) : ?>
						<div class="jmm-item item-<?php echo $i; ?>">
							<?php if( !empty($item->image_icon) ) {
								echo '<div class="jmm-icon image">';
								echo '<img src="' . $item->image_icon . '" alt="' . $alt . '">';
								echo'</div>';
							} elseif( !empty($item->icon) ) {
								echo '<div class="jmm-icon">';
								echo '<span class="' . $item->icon . '" aria-hidden="true"></span>';
								echo '</div>';
							} ?>
							<div class="jm-count">
								<span class="jmm-timer" data-from="<?php echo $from; ?>" data-to="<?php echo $item->to; ?>"<?php echo $speed . $interval; ?>><?php echo $item->to; ?></span><?php if( $item->unit ) : ?><span class="jmm-unit"><?php echo $item->unit; ?></span><?php endif; ?>
							</div>
							<?php if( $item->subtitle ) : ?>
								<div class="jmm-subtitle"><?php echo $item->subtitle; ?></div>
							<?php endif; ?>
						</div>
					<?php endif; ?>

				<?php

				}

				?>

		</div>
	</div>
</div>
