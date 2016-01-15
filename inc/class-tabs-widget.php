<?php
/**
 * Tabs widget - using the Page Builder layout builder
 */

if ( ! class_exists( 'PT_Tabs_Widget' ) ) {
	class PT_Tabs_Widget extends WP_Widget {

		public function __construct() {
			$this->widget_id_base     = 'tabs';
			$this->widget_name        = esc_html__( 'Tabs for Page Builder', 'pt-tabs' );
			$this->widget_description = esc_html__( 'Bootstrap tabs widget for use in Page Builder.', 'pt-tabs' );
			$this->widget_class       = 'pt-widget-tabs';

			parent::__construct(
				'pt_' . $this->widget_id_base,
				sprintf( '%s by ProteusThemes', $this->widget_name ),
				array(
					'description' => $this->widget_description,
					'classname'   => $this->widget_class,
				)
			);
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args
		 * @param array $instance
		 */
		public function widget( $args, $instance ) {
			$instance['widget_title'] = empty( $instance['widget_title'] ) ? '' : $args['before_title'] . apply_filters( 'widget_title', $instance['widget_title'], $instance ) . $args['after_title'];
			$items                    = isset( $instance['items'] ) ? array_values( $instance['items'] ) : array();

			echo $args['before_widget'];
			?>
			<div class="pt-tabs">
				<?php if ( ! empty( $instance['widget_title'] ) ) : ?>
					<?php echo wp_kses_post( $instance['widget_title'] ); ?>
				<?php endif; ?>

				<?php
					if ( ! empty( $items ) ) :
						$items[0]['active'] = true; // first tab should be active
				?>
					<ul class="pt-tabs__navigation  nav  nav-tabs" role="tablist">
						<?php foreach ( $items as $item ) : ?>
							<li class="nav-item">
								<a class="nav-link<?php echo empty( $item['active'] ) ? '' : '  active'; ?>" data-toggle="tab" href="#tab-<?php echo esc_attr( $item['builder_id'] ); ?>" role="tab"><?php echo wp_kses_post( $item['title'] ); ?></a>
							</li>
						<?php endforeach; ?>
					</ul>

					<div class="pt-tabs__content  tab-content">
					<?php foreach ( $items as $item ) : ?>
						<div class="tab-pane  fade<?php echo empty( $item['active'] ) ? '' : '  in  active'; ?>" id="tab-<?php echo esc_attr( $item['builder_id'] ); ?>" role="tabpanel">
							<?php echo siteorigin_panels_render( 'w'.$item['builder_id'], true, $item['panels_data'] ); ?>
						</div>
					<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php
			echo $args['after_widget'];
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @param array $new_instance The new options
		 * @param array $old_instance The previous options
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();

			$instance['widget_title'] = sanitize_text_field( $new_instance['widget_title'] );

			if ( ! empty( $new_instance['items'] )  ) {
				foreach ( $new_instance['items'] as $key => $item ) {
					$instance['items'][ $key ]['id']          = sanitize_key( $item['id'] );
					$instance['items'][ $key ]['title']       = sanitize_text_field( $item['title'] );
					$instance['items'][ $key ]['builder_id']  = uniqid();
					$instance['items'][ $key ]['panels_data'] = empty( $item['panels_data'] ) ? '' : $item['panels_data'];
				}
			}

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {
			$widget_title = ! empty( $instance['widget_title'] ) ? $instance['widget_title'] : '';
			$items        = isset( $instance['items'] ) ? $instance['items'] : array();

			// Page Builder fix when using repeating fields
			if ( 'temp' === $this->id ) {
				$this->current_widget_id = $this->number;
			}
			else {
				$this->current_widget_id = $this->id;
			}
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'widget_title' ) ); ?>"><?php esc_html_e( 'Widget title:', 'pt-tabs' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'widget_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_title' ) ); ?>" type="text" value="<?php echo esc_attr( $widget_title ); ?>" />
		</p>

		<hr>

		<h3><?php esc_html_e( 'Tabs:', 'pt-tabs' ); ?></h3>

		<script type="text/template" id="js-pt-tab-<?php echo esc_attr( $this->current_widget_id ); ?>">
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-title"><?php _ex( 'Tab title:', 'backend', 'pt-tabs' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-title" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][title]" type="text" value="{{title}}" />
			</p>

			<label><?php _ex( 'Tab content:', 'backend', 'pt-tabs' ); ?></label>
			<div class="siteorigin-page-builder-widget siteorigin-panels-builder siteorigin-panels-builder--pt-tabs" id="siteorigin-page-builder-widget-{{builder_id}}" data-builder-id="{{builder_id}}" data-type="layout_widget">
				<p>
					<a href="#" class="button-secondary siteorigin-panels-display-builder" ><?php _e('Open Builder', 'pt-tabs') ?></a>
				</p>

				<input type="hidden" data-panels-filter="json_parse" value="{{panels_data}}" class="panels-data" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][panels_data]" />
			</div>

			<p>
				<input name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][id]" type="hidden" value="{{id}}" />
				<a href="#" class="pt-remove-tab  js-pt-remove-tab"><span class="dashicons dashicons-dismiss"></span> <?php _ex( 'Remove tab', 'backend', 'pt-tabs' ); ?></a>
			</p>
		</script>
		<div class="pt-widget-tabs" id="tabs-<?php echo esc_attr( $this->current_widget_id ); ?>">
			<div class="tabs"></div>
			<p>
				<a href="#" class="button  js-pt-add-tab"><?php _ex( 'Add new tab', 'backend', 'pt-tabs' ); ?></a>
			</p>
		</div>

		<script type="text/javascript">
			(function() {
				var tabsJSON = <?php echo wp_json_encode( $items ) ?>;

				// Get the right widget id and remove the added < > characters at the start and at the end.
				var widgetId = '<<?php echo esc_js( $this->current_widget_id ); ?>>'.slice( 1, -1 );

				if ( _.isFunction( PTTabs.Utils.repopulateTabs ) ) {
					PTTabs.Utils.repopulateTabs( tabsJSON, widgetId );
				}
			})();
		</script>

		<?php
		}

	}
	register_widget( 'PT_Tabs_Widget' );
}