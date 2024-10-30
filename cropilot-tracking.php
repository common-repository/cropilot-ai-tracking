<?php
/**
 * Plugin Name: CroPilot.ai Tracking
 * Description: Adds CroPilot.ai tracking code to your WordPress site.
 * Version: 1.0.3
 * Author: CroPilot AS
 * Author URI: https://cropilot.ai
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cropilot-tracking
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class CroPilot_Tracking {

    private $option_name = 'cropilot_client_id';

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'wp_head', [ $this, 'add_tracking_code' ] );
        add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );
    }

    public function load_textdomain() {
        load_plugin_textdomain( 'cropilot-tracking', false, basename( dirname( __FILE__ ) ) . '/languages' );
    }

    public function add_admin_menu() {
        add_menu_page(
            __( 'CroPilot Settings', 'cropilot-tracking' ),
            'CroPilot',
            'manage_options',
            'cropilot-tracking',
            [ $this, 'settings_page' ],
            'dashicons-analytics',
            99
        );
    }

    public function register_settings() {
        register_setting(
            'cropilot-settings-group',
            $this->option_name,
            [ $this, 'sanitize_client_id' ]
        );

        add_settings_section(
            'cropilot_main_section',
            '', // No title needed
            null,
            'cropilot-tracking'
        );

        add_settings_field(
            'cropilot_client_id_field',
            __( 'Your CroPilot Client ID:', 'cropilot-tracking' ),
            [ $this, 'client_id_field_callback' ],
            'cropilot-tracking',
            'cropilot_main_section'
        );
    }

    public function sanitize_client_id( $input ) {
        $input = trim( $input );
        
        if ( preg_match( '/^[a-zA-Z0-9]{5}$/', $input ) ) {
            return $input;
        }
        
        add_settings_error(
            $this->option_name,
            'invalid_client_id',
            __( 'Invalid Client ID format. Please enter a valid 5-character Client ID.', 'cropilot-tracking' )
        );
        return get_option( $this->option_name );
    }

    public function client_id_field_callback() {
        $client_id = get_option( $this->option_name );
        ?>
        <input type="text" id="cropilot_client_id" name="<?php echo esc_attr( $this->option_name ); ?>" value="<?php echo esc_attr( $client_id ); ?>" class="regular-text" />
        <p class="description">
            <?php esc_html_e( 'Enter your CroPilot Client ID (e.g., 0XJ92) provided in your CroPilot dashboard.', 'cropilot-tracking' ); ?>
        </p>
        <?php
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>
                <img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'assets/cropilot-logo.png' ); ?>" alt="<?php esc_attr_e( 'CroPilot.ai Logo', 'cropilot-tracking' ); ?>" style="max-width: 200px;">
            </h1>
            <h2><?php esc_html_e( 'Unlock Your Website\'s Full Potential with CroPilot.ai', 'cropilot-tracking' ); ?></h2>
            <p><?php esc_html_e( 'The one-stop solution for easy, automated conversion optimization. No experts, no hefty fees, just results.', 'cropilot-tracking' ); ?></p>

            <?php
            // Display any settings errors registered by the Settings API
            settings_errors( 'cropilot-settings-group' );
            ?>

            <form method="post" action="options.php">
                <?php
                    settings_fields( 'cropilot-settings-group' );
                    do_settings_sections( 'cropilot-tracking' );
                    submit_button();
                ?>
            </form>

            <hr />

            <h3><?php esc_html_e( 'How to Get Started', 'cropilot-tracking' ); ?></h3>
            <ol>
                <?php
                // Translators: %1$s and %2$s are opening and closing anchor tags for the CroPilot.ai link.
                ?>
                <li>
                    <?php
                    printf(
                        wp_kses(
                            /* translators: %1$s and %2$s are opening and closing anchor tags for the CroPilot.ai link. */
                            __( 'Sign up for a an account at %1$sCroPilot.ai%2$s.', 'cropilot-tracking' ),
                            array(
                                'a' => array(
                                    'href'   => array(),
                                    'target' => array(),
                                ),
                            )
                        ),
                        '<a href="https://cropilot.ai" target="_blank">',
                        '</a>'
                    );
                    ?>
                </li>
                <li><?php esc_html_e( 'Obtain your unique website ID from your CroPilot dashboard.', 'cropilot-tracking' ); ?></li>
                <li><?php esc_html_e( 'Enter your website ID above and save changes.', 'cropilot-tracking' ); ?></li>
            </ol>

            <h3><?php esc_html_e( 'Why Choose CroPilot?', 'cropilot-tracking' ); ?></h3>
            <p><?php esc_html_e( 'From effortless A/B testing powered by AI to seamless integration for all platforms, CroPilot offers a comprehensive suite of tools to optimize your website\'s performance effortlessly.', 'cropilot-tracking' ); ?></p>

            <h3><?php esc_html_e( 'Need Assistance?', 'cropilot-tracking' ); ?></h3>
            <p><?php esc_html_e( 'Have questions? Check out our FAQ section or contact our support team for assistance.', 'cropilot-tracking' ); ?></p>

            <p><a href="https://cropilot.ai/support" target="_blank"><?php esc_html_e( 'Contact Support', 'cropilot-tracking' ); ?></a></p>
        </div>
        <?php
    }

    public function add_tracking_code() {
        $client_id = get_option( $this->option_name );
        if ( ! empty( $client_id ) ) {
            ?>
            <!-- CroPilot Tracking Code -->
            <script>
            (function(c, d, p, id) {
                c.cropilot = c.cropilot || function() {
                    (c.cropilot.q = c.cropilot.q || []).push(arguments);
                };
                var s = d.createElement('script');
                s.async = true;
                s.src = p + 'tracking.js?client_id=' + encodeURIComponent(id);
                var x = d.getElementsByTagName('script')[0];
                x.parentNode.insertBefore(s, x);
            })(window, document, 'https://cropilot.ai/', '<?php echo esc_js( $client_id ); ?>');
            </script>
            <!-- CroPilot Tracking Code -->
            <?php
        }
    }
}

// Instantiate the plugin class within an action hook to prevent direct access execution.
function run_cropilot_tracking() {
    new CroPilot_Tracking();
}
add_action( 'plugins_loaded', 'run_cropilot_tracking' );
