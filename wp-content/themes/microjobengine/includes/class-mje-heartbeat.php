<?php
/**
 * Class MJE_Heartbeat
 * Manage all heartbeat actions
 *
 * @since 1.3.2
 * @author Tat Thien
 */
class MJE_Heartbeat
{
    /**
     * MJE_Heartbeat constructor.
     */
    public function __construct() {
        add_filter( 'heartbeat_settings', array( $this, 'config_heartbeat' ) );
        add_filter( 'heartbeat_received', array( $this, 'response_heartbeat' ), 15, 2 );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_heartbeat_scripts' ) );
    }

    /**
     * Hook into heartbeat config filters
     *
     * @param array $settings
     * @return array $settings
     */
    public function config_heartbeat( $settings ) {
        $settings['interval'] = 15;
        return $settings;
    }

    /**
     * Add heartbeat related scripts
     */
    public function enqueue_heartbeat_scripts() {
        if( is_user_logged_in() ) {
            wp_enqueue_script('heartbeat');
            wp_enqueue_script('mje-heartbeat', get_template_directory_uri() . '/assets/js/heartbeat.js', array(
                'front'
            ), ET_VERSION, true);

            /**
             * Fire filter for heartbeat localize
             *
             * @param array
             */
            $heartbeat_localize = apply_filters('mje_heartbeat_localize', array(
                'conversation_nonce' => wp_create_nonce('mje_heartbeat_conversation_nonce'),
                'notification_nonce' => wp_create_nonce('mje_heartbeat_notification_nonce'),
                'is_single_message' => is_singular(array('ae_message'))
            ));
            wp_localize_script('mje-heartbeat', 'mje_heartbeat', $heartbeat_localize);
        }
    }

    /**
     * Hook into heartbeat response filters
     *
     * @param array $response
     * @param array $data
     * @return array $response
     */
    public function response_heartbeat( $response, $data ) {
        if( isset( $data['conversation_nonce'] ) && wp_verify_nonce( $data['conversation_nonce'], 'mje_heartbeat_conversation_nonce' ) ) {
            $response['unread_messages'] = $this->get_unread_messages( $data );
        }

        return $response;
    }

    /**
     * Get unread messages for heartbeat response
     *
     * @return array $response
     */
    public function get_unread_messages( $data ) {
        $response = array();

        $unread_messages_count = mje_get_unread_conversation_count();
        if( $unread_messages_count === 0 ) {
            return $response;
        }

        $response['count'] = mje_get_unread_conversation_count();

        ob_start();
        mje_get_user_dropdown_conversation();
        $output = ob_get_clean();
        $response['dropdown_html'] = $output;

        if( $data['is_single_message'] === '1' ) {
            if( mje_get_unread_message_count( $data['conversation_id'] ) > 0) {
                $response['fetch_message'] = true;
            }
        }

        return $response;
    }
}

new MJE_Heartbeat();