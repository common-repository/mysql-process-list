<?php
/*
 * Plugin Name:       MySQL Process List
 * Plugin URI:           https://plugins.club/
 * Description:         Show MySQL Process list under Tools > MySQL Process List
 * Version:               1.2
 * Author:                plugins.club
 * Author URI:         https://plugins.club/
 */
 
// Add a custom menu item to the Tools menu
add_action( 'admin_menu', 'pluginsclub_mpc_mysql_process_list_menu' );
function pluginsclub_mpc_mysql_process_list_menu() {
  add_submenu_page( 'tools.php', 'MySQL Process List', 'MySQL Process List', 'manage_options', 'mysql-process-list', 'pluginsclub_mpc_render_mysql_process_list_page' );
}


// Render the custom admin page
function pluginsclub_mpc_render_mysql_process_list_page() {
  if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( __( 'You do not have sufficient permissions to access this page.', 'mysql-process-list' ) );
  }
  
    // Load CSS only on the settings page
  $screen = get_current_screen();
    if ( $screen->id === 'tools_page_mysql-process-list'){
            wp_enqueue_style( 'tools_page_mysql-process-list', plugin_dir_url( __FILE__ ) . 'includes/css/settings-page.css', array(), '1.0.0' );
            //wp_enqueue_script( 'manage_options_page_tl-settings', plugin_dir_url( __FILE__ ) . 'includes/js/emails-page.js', array(), '1.9.0', true );

    }
  
  
  ?>
  

<div id="pluginsclub-cpanel">
					<div id="pluginsclub-cpanel-header">
			<div id="pluginsclub-cpanel-header-title">
				<div id="pluginsclub-cpanel-header-title-image">
<h1><a href="http://plugins.club/" target="_blank" class="logo"><img src="<?php echo plugins_url('includes/images/pluginsclub_logo_black.png', __FILE__) ?>" style="height:27px"></a></h1></div>

				<div id="pluginsclub-cpanel-header-title-image-sep">
				</div>      
<div id="pluginsclub-cpanel-header-title-nav">
	<?php
// Get our API endpoint and from it build the menu
$plugins_club_api_link = 'https://api.plugins.club/list_of_wp_org_plugins.php';
$remote_data = file_get_contents($plugins_club_api_link);
$menuItems = json_decode($remote_data, true);

foreach ($menuItems as $menuItem) :
    $isActive = isset($_GET['page']) && ($_GET['page'] === $menuItem['page']);
    $activeClass = $isActive ? 'active' : '';
    $isInstalled = function_exists($menuItem['check_function']) && function_exists($menuItem['check_callback']);
    $name = $menuItem['name'];
    if (!$isInstalled) {
        $name = ' <span class="dashicons dashicons-plus-alt"></span> '.$name;
    } else {
        $name .= ' <span class="dashicons dashicons-plugins-checked"></span>';
    }
?>
    <div class="pluginsclub-cpanel-header-nav-item <?php echo $activeClass; ?>">
        <?php if ($isInstalled) : ?>
            <a href="<?php echo $menuItem['url']; ?>" class="tab"><?php echo $name; ?></a>
        <?php else : ?>
            <a href="<?php echo $menuItem['fallback_url']; ?>" target="_blank" class="tab"><?php echo $name; ?></a>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
</div>
      
			</div>
		</div>
		
		
		  <div class="wrap">

				<div id="pluginsclub-cpanel-admin-wrap" class="wrap">
			<h1 class="pluginsclub-cpanel-hide"><?php esc_html_e( 'MySQL Process List', 'mysql-process-list' ); ?></h1>
			<form id="pluginsclub-cpanel-form">
				<h2><?php esc_html_e( 'MySQL Process List', 'mysql-process-list' ); ?></h2>
		<p>
			<?php esc_html_e( 'The MySQL process list indicates the operations currently being performed by the set of threads executing within the website.', 'mysql-process-list' ); ?>		</p>
			
			
		<div class="pluginsclub-cpanel-sep"></div>
		
<table class="form-table" role="presentation">
      
    <table class="wp-list-table widefat fixed striped">
      <thead>
        <tr>
		<th><?php esc_html_e( 'ID', 'mysql-process-list' ); ?></th>
		<th><?php esc_html_e( 'User', 'mysql-process-list' ); ?></th>
		<th><?php esc_html_e( 'Host', 'mysql-process-list' ); ?></th>
		<th><?php esc_html_e( 'DB', 'mysql-process-list' ); ?></th>
		<th><?php esc_html_e( 'Command', 'mysql-process-list' ); ?></th>
		<th><?php esc_html_e( 'Time', 'mysql-process-list' ); ?></th>
		<th><?php esc_html_e( 'State', 'mysql-process-list' ); ?></th>
		<th><?php esc_html_e( 'Info', 'mysql-process-list' ); ?></th>
        </tr>
      </thead>
      <tbody id="process-list">
        <?php
		
        // Connect to the database
        $mysqli = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
        if ( $mysqli->connect_error ) {
          wp_die( $mysqli->connect_error );
        }
		
        // Execute the SHOW FULL PROCESSLIST command and display the results
        $result = $mysqli->query( 'SHOW FULL PROCESSLIST' );
        while ( $row = $result->fetch_assoc() ) {
        echo '<tr>';
		echo '<td>' . esc_html( $row['Id'] ) . '</td>';
        echo '<td>' . esc_html( $row['User'] ) . '</td>';
        echo '<td>' . esc_html( $row['Host'] ) . '</td>';
        echo '<td>' . esc_html( $row['db'] ) . '</td>';
        echo '<td>' . esc_html( $row['Command'] ) . '</td>';
        echo '<td>' . esc_html( $row['Time'] ) . '</td>';
        echo '<td>' . esc_html( $row['State'] ) . '</td>';
        echo '<td>' . esc_html( $row['Info'] ) . '</td>';
        echo '</tr>';
}

// Close the database connection
$mysqli->close();
?>
      </tbody>
    </table>
  </div>
</div>
  <?php
}

