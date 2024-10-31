<?php
/**
 * The Categories widget replaces the default WordPress Categories widget. This version gives total
 * control over the output to the user by allowing the input of all the arguments typically seen
 * in the wp_list_categories() function.
 *
 */
class Post_Recycle_Widget extends WP_Widget {

	// Set prefix for the widget
	var $textdomain;

	
	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 * @since 0.6.0
	 */
	function __construct() {
		
		// Set up the widget options
		$widget_options = array(
			'classname' => 'widget-post-recycle',
			'description' => esc_html__( '[+] Advanced widget gives you total control over your post recycle.', $this->textdomain )
		);

		// Set up the widget control options
		$control_options = array(
			'width' => 460,
			'height' => 350,
			'id_base' => 'post-recycle'
		);

		// Create the widget
		parent::__construct( "post-recycle", esc_attr__( 'Post Recycle', $this->textdomain ), $widget_options, $control_options );
		
		// Load the widget stylesheet for the widgets admin screen
		add_action( 'load-widgets.php', array(&$this, 'load_widgets') );
		add_action( 'admin_print_styles', array(&$this, 'the_countdown_pro_widget_admin_style') );
		
		
		// Print the user costum style sheet
		if ( is_active_widget(false, false, $this->id_base, false ) && ! is_admin() ) {
			wp_enqueue_style( 'the-countdown-pro', THE_COUNTDOWN_PRO_URL . 'css/countdown.css', array(), THE_COUNTDOWN_PRO_VERSION );
			wp_enqueue_style( 'post-recycle', POST_RECYCLE_URL . 'css/post-recycle.css', array(), THE_COUNTDOWN_PRO_VERSION );
			wp_enqueue_script( 'the-countdown-pro', THE_COUNTDOWN_PRO_URL . 'js/jquery.countdown.min.js', array('jquery'), THE_COUNTDOWN_PRO_VERSION );
			wp_localize_script( 'the-countdown-pro', 'tcp', array(
				'nonce'		=> wp_create_nonce( 'the-countdown-pro' ),
				'action'	=> 'server_sync',
				'ajaxurl'	=> admin_url('admin-ajax.php')
			));
			wp_enqueue_script( 'post-recycle', POST_RECYCLE_URL . 'js/jquery.post-recycle.js', array('jquery'), POST_RECYCLE_VERSION );
			add_action( 'wp_head', array( &$this, 'print_script') );
		}
	}

	
	// Push the widget stylesheet widget.css into widget admin page
	function load_widgets() {
		wp_enqueue_media();
		wp_enqueue_style( 'total-dialog' );
		wp_enqueue_script( 'total-dialog' );
		wp_enqueue_script( 'the-countdown-pro-dialog', THE_COUNTDOWN_PRO_URL . 'js/jquery.datepicker.js', array( 'jquery' ), THE_COUNTDOWN_PRO_VERSION );
	}
	
	
	// Push the widget stylesheet widget.css into widget admin page
	function the_countdown_pro_widget_admin_style() {
		echo '<style type="text/css"> .tcpControls .timestamp { background-image: url(images/date-button.gif); background-position: left top; background-repeat: no-repeat; padding-left: 18px; }</style>';
	}
	
	
	/**
	 * Outputs the widget styles and user custom styles and scripts
	 * @since 0.6.0
	 */	
	function print_script() {
		$settings = $this->get_settings();
		foreach ( $settings as $key => $setting ){
			
			// User defined styles
			echo '<style type="text/css">'. "\n";
					if ( $setting['counter_size'] )		echo "#{$this->id_base}-$key .countdown-amount {font-size: {$setting['counter_size']}px;}". "\n";
					if ( $setting['label_size'] )		echo "#{$this->id_base}-$key .countdown-period {font-size: {$setting['label_size']}px;}". "\n";				
					if ( $setting['counter_color'] )	echo "#{$this->id_base}-$key .countdown-amount {color: {$setting['counter_color']};}". "\n";
					if ( $setting['label_color'] )		echo "#{$this->id_base}-$key .countdown-section {color: {$setting['label_color']};}". "\n";						
					if ( $setting['counter_bg_color'] )	echo "#{$this->id_base}-$key .countdown-amount {background-color: {$setting['counter_bg_color']};}". "\n";
					if ( $setting['label_bg_color'] )	echo "#{$this->id_base}-$key .countdown-period {background-color: {$setting['label_bg_color']};}". "\n";
			echo '</style>'. "\n";

			// User custom styles and scripts
			if ( ! empty( $setting['customstylescript'] ) )
				echo $setting['customstylescript'];
		}
	}
	
	
	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 * @since 0.6.0
	 */
	function widget( $args, $instance ) {
		extract( $args );

		// Set up the arguments for wp_list_categories()
		$args = array(
			'id'					=> $this->id,
			'post_type' 			=> $instance['post_type'],
			'posts' 				=> $instance['posts'],
			'until' 				=> $instance['until'],
			'cycle' 				=> $instance['cycle'],
			'duration' 				=> $instance['duration'],
			'format' 				=> $instance['format'],
			'hideExpired' 			=> !empty( $instance['hideExpired'] ) ? true : false,
			'alwaysExpire' 			=> !empty( $instance['alwaysExpire'] ) ? true : false,
			'compact' 				=> !empty( $instance['compact'] ) ? true : false,
			'label_bg_color' 		=> $instance['label_bg_color'],
			'counter_bg_color' 		=> $instance['counter_bg_color'],
			'counter_size' 			=> $instance['counter_size'],
			'label_size' 			=> $instance['label_size'],
			'counter_color' 		=> $instance['counter_color'],
			'label_color' 			=> $instance['label_color'],
			'tabs'					=> $instance['tabs'],
			'intro_text' 			=> $instance['intro_text'],
			'outro_text' 			=> $instance['outro_text'],
			'customstylescript'		=> $instance['customstylescript']
		);

		// Output the theme's widget wrapper
		echo $before_widget;	

		// If a title was input by the user, display it
		if ( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		// Print intro text if exist
		if ( !empty( $instance['intro_text'] ) )
			echo '<p class="'. $this->id . '-intro-text intro-text">' . $instance['intro_text'] . '</p>';
		
		// Here it comes
		echo post_recycle( $args );		
		
		// Print outro text if exist
		if ( !empty( $instance['outro_text'] ) )
			echo '<p class="'. $this->id . '-outro_text outro_text">' . $instance['outro_text'] . '</p>';

		// Close the theme's widget wrapper
		echo $after_widget;
	}

	
	/**
	 * Updates the widget control options for the particular instance of the widget.
	 * @since 0.6.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// Set the instance to the new instance
		$instance = $new_instance;

		// If new taxonomy is chosen, reset includes and excludes
		if ( $instance['post_type'] !== $old_instance['post_type'] && '' !== $old_instance['post_type'] ) {
			$instance['include'] = array();
			$instance['posts'] = array();
		}
		
		if ( $instance['include'] !== $old_instance['include'] && '' !== $old_instance['include'] ) {
			$instance['posts'] = $instance['include'];
		}
		
		$instance['title'] 				= strip_tags( $new_instance['title'] );
		$instance['post_type'] 			= $new_instance['post_type'];
		$instance['cycle'] 				= $new_instance['cycle'];
		$instance['duration'] 			= $new_instance['duration'];
		$instance['format'] 			= $new_instance['format'];
		$instance['counter_bg_color'] 	= $new_instance['counter_bg_color'];
		$instance['label_bg_color'] 	= $new_instance['label_bg_color'];
		$instance['counter_size'] 		= $new_instance['counter_size'];
		$instance['label_size'] 		= $new_instance['label_size'];
		$instance['counter_color'] 		= $new_instance['counter_color'];
		$instance['label_color'] 		= $new_instance['label_color'];
		$instance['tabs'] 				= $new_instance['tabs'];
		$instance['intro_text'] 		= $new_instance['intro_text'];
		$instance['outro_text'] 		= $new_instance['outro_text'];
		$instance['customstylescript']	= $new_instance['customstylescript'];
		
		return $instance;
	}

	
	/**
	 * Displays the widget control options in the Widgets admin screen.
	 * @since 0.6.0
	 */
	function form( $instance ) {

		// Set up the default form values
		// date-time: mm jj aa hh mn
		$time_adj = current_time('timestamp');
		$cur_jj = gmdate( 'd', $time_adj );
		$cur_mm = gmdate( 'm', $time_adj );
		$cur_aa = gmdate( 'Y', $time_adj );
		$cur_hh = gmdate( 'H', $time_adj );
		$cur_mn = gmdate( 'i', $time_adj );
	
		$defaults = array(
			'title' 			=> esc_attr__( 'Post Recycle', $this->textdomain ),
			'post_type' 		=> 'post',
			'posts' 			=> array(),
			'include' 			=> array(),
			'cycle' 			=> 30,
			'duration' 			=> 10,
			'format' 			=> 'dHMS',
			'until' 			=> array( 0 => $cur_mm, 1 => $cur_jj, 2 => $cur_aa, 3 => $cur_hh, 4 => $cur_mn ),
			'counter_size' 		=> '',
			'label_size' 		=> '',
			'counter_color' 	=> '#333333',
			'label_color' 		=> '#333333',
			'counter_bg_color' 	=> '#dddddd',
			'label_bg_color' 	=> '#e7e7e7',
			'tabs'				=> array( 0 => true, 1 => false, 2 => false, 3 => false, 4 => false ),
			'intro_text' 		=> '',
			'outro_text' 		=> '',
			'customstylescript'	=> ''
		);

		// Merge the user-selected arguments with the defaults.
		$instance = wp_parse_args( (array) $instance, $defaults );

		$tabs = array( 
			__( 'General', $this->textdomain ),  
			__( 'Format', $this->textdomain ),
			__( 'Advanced', $this->textdomain )
		);	
		
		// Set the default value of each widget input
		global $wp_locale;
		$time_adj = current_time('timestamp');
		$counterList = array( 'until' => __( 'Until', $this->textdomain) , 'since' => __( 'Since', $this->textdomain  ));
		$types = array( 'post' => 'post', 'page' => 'page' ) + get_post_types( array( '_builtin' => false, 'public' => true ), 'names' );
		
		$get_posts = new WP_Query;
		$rgs = array( 'orderby' => 'post_date', 'post_type' => $instance['post_type'], 'order' => 'DESC', 'posts_per_page' => '-1' );
		$posts_array = $get_posts->query( $rgs );	
		?>

		<div class="pluginName">Post Recycle<span class="pluginVersion"><?php echo POST_RECYCLE_VERSION; ?> - The Countdown Pro Addon</span></div>

		<div id="tcp-<?php echo $this->id ; ?>" class="total-options tabbable tabs-left">
			<ul class="nav nav-tabs">
				<?php foreach ($tabs as $key => $tab ) : ?>
					<li class="<?php echo $instance['tabs'][$key] ? 'active' : '' ; ?>"><?php echo $tab; ?><input type="hidden" name="<?php echo $this->get_field_name( 'tabs' ); ?>[]" value="<?php echo $instance['tabs'][$key]; ?>" /></li>
				<?php endforeach; ?>	
			</ul>
			<ul class="tab-content">
				<li class="tab-pane <?php if ( $instance['tabs'][0] ) : ?>active<?php endif; ?>">
					<ul>
						<li>
							<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'Give the widget title, or let empty for no title.', $this->textdomain ); ?></span>			
							<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post Type', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'Select the custom post type from the list.', $this->textdomain ); ?></span>					
							<select onchange="wpWidgets.save(jQuery(this).closest('div.widget'),0,1,0);"  id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>">
								<?php foreach ( $types as $type ) { ?>
									<option value="<?php echo esc_attr( $type ); ?>" <?php selected( $instance['post_type'], $type  ); ?>><?php echo $type; ?></option>
								<?php } ?>
							</select>
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'include' ); ?>"><?php _e( 'Select Post(s)', $this->textdomain ); ?></label> 
							<span class="description"><?php _e( 'Select the posts ro recycling mode', $this->textdomain ); ?></span>
							<select  onchange="wpWidgets.save(jQuery(this).closest('div.widget'),0,1,0);" id="<?php echo $this->get_field_id( 'include' ); ?>" name="<?php echo $this->get_field_name( 'include' ); ?>[]" size="4" multiple="multiple">
								<?php foreach ( $posts_array as $p ) { ?>
									<option value="<?php echo esc_attr( $p->ID ); ?>" <?php echo ( in_array( $p->ID, (array) $instance['include'] ) ? 'selected="selected"' : '' ); ?>><?php echo $p->post_title; ?></option>
								<?php } ?>
							</select>
						</li>
						<li>
							<label><?php _e( 'Selected Post(s)', $this->textdomain ); ?></label> 
							<span class="description"><?php _e( 'Select the post(s) from the above selectbox and it should be added here automatically.', $this->textdomain ); ?></span>
							<div id="<?php echo $this->id; ?>roleWrapper">
								<?php if ( is_array ( $instance['posts'] ) ) { ?>
									<?php foreach ( $instance['posts'] as $pID ) { ?>
										<div class="role">
											<?php  $gp = get_post($pID); ?>
											<?php echo $gp->post_title . '<span class="totalUser">' . $pID . '</span>'; ?></label>
											<input type="hidden" name="<?php echo $this->get_field_name( 'posts' ); ?>[]" value="<?php echo $pID; ?>">
										</div>
									<?php } ?>
								<?php } ?>
							</div>
						</li>						
					</ul>
				</li>
				<li class="tab-pane <?php if ( $instance['tabs'][1] ) : ?>active<?php endif; ?>">
					<ul>
						<li>
							<div id ="until-<?php echo $this->id; ?>" class="curtime tc-curtime">
								<label style="margin-bottom: 5px"><?php _e( 'Start Time', $this->textdomain ); ?></label>
								<span class="timestamp"><span><?php echo $wp_locale->get_month_abbrev( $wp_locale->get_month( $instance['until'][0] ) ) . ' ' . $instance['until'][1] . ', ' . $instance['until'][2] . ' @ ' . $instance['until'][3] . ':' . $instance['until'][4]; ?></span></span>
								<a tabindex="4" class="edit-timestamp hide-if-no-js" href="#"><?php _e( 'Edit', $this->textdomain ); ?></a>
								
								<div class="hide-if-js timestampdiv">
									<div class="timestamp-wrap">
										<?php
											$month = "<select class='mm' name='" . $this->get_field_name( 'until' ) . "[]'>";
											for ( $i = 1; $i < 13; $i = $i +1 ) {
												$monthnum = zeroise($i, 2);
												$month .= "\t\t\t" . '<option value="' . $monthnum . '"';
												if ( $i == $instance['until'][0] )
													$month .= ' selected="selected"';
												/* translators: 1: month number (01, 02, etc.), 2: month abbreviation */
												$month .= '>' . sprintf( __( '%1$s-%2$s' ), $monthnum, $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) ) . "</option>\n";
											}
											$month .= '</select>';
											echo $month;
										?>
										<input type="text" autocomplete="off" tabindex="4" maxlength="2" size="2" value="<?php echo $instance['until'][1]; ?>" name="<?php echo $this->get_field_name( 'until' ); ?>[]" class="jj" />, 
										<input type="text" autocomplete="off" tabindex="4" maxlength="4" size="4" value="<?php echo $instance['until'][2]; ?>" name="<?php echo $this->get_field_name( 'until' ); ?>[]" class="aa" /> @ 
										<input type="text" autocomplete="off" tabindex="4" maxlength="2" size="2" value="<?php echo $instance['until'][3]; ?>" name="<?php echo $this->get_field_name( 'until' ); ?>[]" class="hh"> : 
										<input type="text" autocomplete="off" tabindex="4" maxlength="2" size="2" value="<?php echo $instance['until'][4]; ?>" name="<?php echo $this->get_field_name( 'until' ); ?>[]" class="mn">

										<a class="save-timestamp hide-if-no-js button" href="#"><?php _e( 'OK', $this->textdomain ); ?></a>
										<a class="cancel-timestamp hide-if-no-js" href="#"><?php _e( 'Cancel', $this->textdomain ); ?></a>
									</div>
									
									<input type="hidden" value="11" name="ss" class="ss" />
									<input type="hidden" value="<?php echo esc_attr( $instance['until']['0'] ); ?>" name="hidden_mm" class="hidden_mm">
									<input type="hidden" value="<?php echo gmdate( 'd', $time_adj ); ?>" name="cur_mm" class="cur_mm">
									<input type="hidden" value="<?php echo esc_attr( $instance['until']['1'] ); ?>" name="hidden_jj" class="hidden_jj">
									<input type="hidden" value="<?php echo gmdate( 'm', $time_adj ); ?>" name="cur_jj" class="cur_jj">
									<input type="hidden" value="<?php echo esc_attr( $instance['until']['2'] ); ?>" name="hidden_aa" class="hidden_aa">
									<input type="hidden" value="<?php echo gmdate( 'Y', $time_adj ); ?>" name="cur_aa" class="cur_aa">
									<input type="hidden" value="<?php echo esc_attr( $instance['until']['3'] ); ?>" name="hidden_hh" class="hidden_hh">
									<input type="hidden" value="<?php echo gmdate( 'h', $time_adj ); ?>" name="cur_hh" class="cur_hh">
									<input type="hidden" value="<?php echo esc_attr( $instance['until']['4'] ); ?>" name="hidden_mn" class="hidden_mn">
									<input type="hidden" value="<?php echo gmdate( 'i', $time_adj ); ?>" name="cur_mn" class="cur_mn">
								</div>
								<span class="description"><?php _e( "new Date(year, mth - 1, day, hr, min, sec) - date/time to count up from or numeric for seconds offset, or string for unit offset(s): 'Y' years, 'O' months, 'W' weeks, 'D' days, 'H' hours, 'M' minutes, 'S' seconds. <b>Note</b>: save the widget instance first before using this date picker.", $this->textdomain ); ?></span>							
							</div>	
						</li>	
						<li>
							<label for="<?php echo $this->get_field_id( 'cycle' ); ?>"><?php _e( 'Cycle Every', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'The total seconds to cycle for next post.', $this->textdomain ); ?></span>	
							<input type="text" id="<?php echo $this->get_field_id( 'cycle' ); ?>" name="<?php echo $this->get_field_name( 'cycle' ); ?>" value="<?php echo esc_attr( $instance['cycle'] ); ?>" />							
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'duration' ); ?>"><?php _e( 'Content Duration', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'The duration for the post content in second.', $this->textdomain ); ?></span>
							<input type="text" id="<?php echo $this->get_field_id( 'duration' ); ?>" name="<?php echo $this->get_field_name( 'duration' ); ?>" value="<?php echo esc_attr( $instance['duration'] ); ?>" />							
						</li>						
						<li>
							<label for="<?php echo $this->get_field_id( 'format' ); ?>"><?php _e( 'Date Format', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'Format for display - upper case for always, lower case only if non-zero, \'Y\' years, \'O\' months, \'W\' weeks, \'D\' days, \'H\' hours, \'M\' minutes, \'S\' seconds', $this->textdomain ); ?></span>	
							<input type="text" id="<?php echo $this->get_field_id( 'format' ); ?>" name="<?php echo $this->get_field_name( 'format' ); ?>" value="<?php echo esc_attr( $instance['format'] ); ?>" />							
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'counter_size' ); ?>"><?php _e( 'Font Size', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'Counter and label font size in pixels unit.', $this->textdomain ); ?></span>	
							<input type="text" class="smallfat" placeholder="24" id="<?php echo $this->get_field_id( 'counter_size' ); ?>" name="<?php echo $this->get_field_name( 'counter_size' ); ?>" value="<?php echo $instance['counter_size']; ?>" />							
							<input type="text" class="smallfat" placeholder="10" id="<?php echo $this->get_field_id( 'label_size' ); ?>" name="<?php echo $this->get_field_name( 'label_size' ); ?>" value="<?php echo $instance['label_size']; ?>" />							
						</li>						
						<li>
							<label for="<?php echo $this->get_field_id( 'counter_color' ); ?>"><?php _e( 'Font Color', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'The counter and label color respectively.', $this->textdomain ); ?></span>
							<input class="color-picker" type="text" id="<?php echo $this->get_field_id( 'counter_color' ); ?>" name="<?php echo $this->get_field_name( 'counter_color' ); ?>" value="<?php echo esc_attr( $instance['counter_color'] ); ?>">
							<input class="color-picker" type="text" id="<?php echo $this->get_field_id( 'label_color' ); ?>" name="<?php echo $this->get_field_name( 'label_color' ); ?>" value="<?php echo esc_attr( $instance['label_color'] ); ?>">
						</li>
						<li>
							<label for="<?php echo $this->get_field_id( 'label_bg_color' ); ?>"><?php _e( 'Background Color', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'The label background color.', $this->textdomain ); ?></span>
							<input class="color-picker" type="text" id="<?php echo $this->get_field_id( 'counter_bg_color' ); ?>" name="<?php echo $this->get_field_name( 'counter_bg_color' ); ?>" value="<?php echo esc_attr( $instance['counter_bg_color'] ); ?>">
							<input class="color-picker" type="text" id="<?php echo $this->get_field_id( 'label_bg_color' ); ?>" name="<?php echo $this->get_field_name( 'label_bg_color' ); ?>" value="<?php echo esc_attr( $instance['label_bg_color'] ); ?>">
						</li>
					</ul>
				</li>
				<li class="tab-pane <?php if ( $instance['tabs'][2] ) : ?>active<?php endif; ?>">
					<ul>
						<li>
							<label for="<?php echo $this->get_field_id('intro_text'); ?>"><?php _e( 'Intro Text', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'This option will display addtional text before the widget content and HTML supports.', $this->textdomain ); ?></span>
							<textarea name="<?php echo $this->get_field_name( 'intro_text' ); ?>" id="<?php echo $this->get_field_id( 'intro_text' ); ?>" rows="2" class="widefat"><?php echo esc_textarea($instance['intro_text']); ?></textarea>
						</li>
						<li>
							<label for="<?php echo $this->get_field_id('outro_text'); ?>"><?php _e( 'Outro Text', $this->textdomain ); ?></label>
							<span class="description"><?php _e( 'This option will display addtional text after widget and HTML supports.', $this->textdomain ); ?></span>
							<textarea name="<?php echo $this->get_field_name( 'outro_text' ); ?>" id="<?php echo $this->get_field_id( 'outro_text' ); ?>" rows="2" class="widefat"><?php echo esc_textarea($instance['outro_text']); ?></textarea>
							
						</li>				
						<li>
							<label for="<?php echo $this->get_field_id('customstylescript'); ?>"><?php _e( 'Custom Script & Stylesheet', $this->textdomain ) ; ?></label>
							<span class="description"><?php _e( 'Use this box for additional widget CSS style of custom javascript. Current widget selector: ', $this->textdomain ); ?><?php echo '<tt>#' . $this->id . '</tt>'; ?></span>
							<textarea name="<?php echo $this->get_field_name( 'customstylescript' ); ?>" id="<?php echo $this->get_field_id( 'customstylescript' ); ?>" rows="3" class="widefat code"><?php echo htmlentities($instance['customstylescript']); ?></textarea>
						</li>
					</ul>
				</li>
			</ul>
		</div>
		<script type="text/javascript">
			// Tabs function
			jQuery(document).ready(function($){
				$('#until-<?php echo $this->id; ?>').tcpDateTime();
				$('#<?php echo $this->id; ?>roleWrapper').sortable({ 
					items: '.role', 
					placeholder: 'placeholder'
				});
			});
		</script>		
	<?php
	}
}

?>