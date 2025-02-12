<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/swaptify
 * @since      1.0.0
 *
 * @package    Swaptify
 * @subpackage Swaptify/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Swaptify
 * @subpackage Swaptify/admin
 * @author     Swaptify <support@swaptify.com>
 */
class Swaptify_Admin 
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    public $_message;
    
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) 
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        
        add_action('admin_menu', [$this, 'addPluginAdminMenu'], 9);
        add_action('admin_init', [$this, 'configurationFields']);
        add_action('admin_init', [$this, 'defaultContentFields']);
        add_action('admin_init', [$this, 'eventSettingsFields']);
        add_action('admin_init', [$this, 'visitorTypesFields']);
        add_action('admin_init', [$this, 'cookiesFields']);
        add_action('admin_post_update_default_content', [$this, 'admin_update_default_content']);
        add_action('admin_post_add_new_event', [$this, 'admin_add_new_event']);
        add_action('admin_post_add_new_visitor_type', [$this, 'admin_add_new_visitor_type']);
        add_action('admin_post_add_new_cookie', [$this, 'admin_add_new_cookie']);
        add_action('admin_post_save_swaptify_segment', [$this, 'admin_save_swaptify_segment']);
        add_action('admin_post_create_swaptify_segment', [$this, 'admin_create_swaptify_segment']);
        add_action('admin_post_delete_swaptify_swap', [$this, 'admin_delete_swaptify_swap']);
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() 
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Swaptify_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Swaptify_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/swaptify-admin.css', [], $this->version, 'all');
    }

    /**
     * add CSS to generate the custom dashboard icon
     *
     * @return void
     */
    public function swaptify_dashicon() {
        echo '
            <style>
                .dashicons-swaptify {
                    background-image: url("' . plugins_url('images/swaptify-dashicon.png', __FILE__) . '");
                    background-repeat: no-repeat;
                    background-position: center; 
                    background-size: 22px;
                }
            </style>
        '; 
    }
    
    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Swaptify_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Swaptify_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/swaptify-admin.js', ['jquery'], $this->version, false);
        wp_localize_script($this->plugin_name, 'swaptify_image_path', ['swaptify_image_path' => plugin_dir_url(__FILE__)]);
        wp_localize_script($this->plugin_name, 'swaptify_admin_url', ['swaptify_admin_url' => admin_url('')]);
    }

    /**
     * Build the menu items
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function addPluginAdminMenu() 
    {
        $icon = 'dashicons-swaptify';

        add_action('admin_head', [$this, 'swaptify_dashicon']);

        /**
         * main menu item
         */
        add_menu_page('Swaptify', 'Swaptify', 'administrator', $this->plugin_name, [$this, 'adminMainPage'], $icon, 26);
        
        /**
         * submenu items
         */
        add_submenu_page($this->plugin_name, 'Swaptify Configuration', 'Configuration', 'administrator', $this->plugin_name.'-configuration', [$this, 'adminConfigurationPage']);
        add_submenu_page($this->plugin_name, 'Swaptify Default Content', 'Default Content', 'administrator', $this->plugin_name.'-default-content', [$this, 'adminDefaultContentPage']);
        add_submenu_page($this->plugin_name, 'Swaptify Visitor Types', 'Visitor Types', 'administrator', $this->plugin_name.'-visitor-types', [$this, 'adminVisitorTypesPage']);
        add_submenu_page($this->plugin_name, 'Swaptify Event Settings', 'Event Settings', 'administrator', $this->plugin_name.'-event-settings', [$this, 'adminEventSettingsPage']);
        add_submenu_page($this->plugin_name, 'Swaptify Cookies', 'Cookies', 'administrator', $this->plugin_name.'-cookies', [$this, 'adminCookiesPage']);
        add_submenu_page($this->plugin_name, 'Swaptify Shortcode Generator', 'Shortcode Generator', 'administrator', $this->plugin_name.'-shortcode-generator', [$this, 'adminShortcodeGeneratorPage']);
    }
    
    /**
     * 
     * 
     * PAGE RENDERS
     *
     * 
     */
    
    /**
     * Render the main admin page for the plugin
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function adminMainPage() 
    {
        /**
         * check if the tab parameter is passed, otherwise, default to "home"
         */
        $tab = strtolower(Swaptify::getVariable($_GET, 'tab', 'home'));
        
        /**
         * the tab links are defined here with
         * this array will be used to render the tabs and content on the page
         */
        $tabs = [
            'home' => ['name' => 'About', 'active' => false, 'url' => 'home', 'file' => 'home.php'],
            'how-to-use' => ['name' => 'How to Use', 'active' => false, 'url' => 'how-to-use', 'file' => 'how-to-use.php'],
            'terminology' => ['name' => 'Terminology', 'active' => false, 'url' => 'terminology', 'file' => 'terminology.php'],
            /**
             * @todo add this when we have a support mechanism
             */
            //'support' => ['name' => 'Support', 'active' => false, 'url' => 'support', 'file' => 'support.php'],
        ];

        /**
         * if the tab parameter is set and not in the available tabs, set it to "home"
         */
        if ($tab && !isset($tabs[$tab]))
        {
            $tab = 'home';
        }
        
        /**
         * set the active tab and define the content path based on the 'file' parameter for the tab key
         */
        $tabs[$tab]['active'] = true;
        
        /**
         * the $path variable will be used in an include_once() method inside the partials/home/index.php file
         */
        $path = __DIR__.'/partials/home/tabs/' . $tabs[$tab]['file'];
        
        require_once 'partials/home/index.php';
    }
    
    /**
     * Render the page for configuration
     * This will include the Swaptify token, a select for the available properties (once the token is set)
     * and the ability to turn off the "enable/disable" the plugin
     * 
     * With the plugin disabled, there will be no visitor information sent to Swaptify, 
     * and only default content will be rendered
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function adminConfigurationPage() 
    {
        // set this var to be used in the settings-display view
        $active_tab = Swaptify::getVariable($_GET, 'tab', 'general');
        
        if(isset($_GET['error_message']))
        {
            add_action('admin_notices', [$this,'configurationErrorMessage']);
            do_action('admin_notices');
        }
        
        $propertyKey = get_option('swaptify_property_key');
        /**
         * $propertySet is used in the partial file below
         */
        $propertySet = false;
        
        if ($propertyKey)
        {
            $propertySet = true;
        }
        
        require_once 'partials/configuration/index.php';
    }
    
    /**
     * Render the page for Default Content
     * This page will display a table of all of the default content.
     * The default content is saved in the database so that in the event the Swaptify server cannot be reached,
     * content will still be shown
     * 
     * There is a form button to update the default content
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function adminDefaultContentPage()
    {        
        global $wpdb;
        /**
         * check for error or success messages
         */
        if(isset($_GET['error_message']))
        {
            add_action('admin_notices', [$this,'defaultContentErrorMessage']);
            do_action('admin_notices');
        }
        elseif (isset($_GET['success']))
        {
            add_action('admin_notices', [$this,'defaultContentSuccessMessage']);
            do_action('admin_notices');
        }
        
        /**
         * query the database on the {$wpdb->prefix}swap_default_contents table
         */
        $query = $wpdb->prepare(
            "SELECT 
                swap_segment_key,
                swap_key,
                name,
                swap_name,
                type,
                content,
                sub_content
            FROM 
                {$wpdb->prefix}swap_default_contents
            WHERE 
                1 = %d
            ",
            [
                1,
            ]);

        /**
         * connect to Swaptiy
         */
        $swaptify = new Swaptify();
        $connect = $swaptify::connect();
        
        /** 
         * if the Swaptify connection is established, render the default content page,
         * otherwise, display the setup required page
         */
        if ($connect)
        {
            $items = $wpdb->get_results($query, 'OBJECT');
            require_once 'partials/default-content/index.php';
        }
        else
        {
            require_once 'partials/general/setup-required.php';
        }
    }

    /**
     * Render the Event Settings page
     * 
     * This page will pull event data from the Swaptify API 
     * and display it along with sample code to register events met
     * 
     * Additionally, there is a form to add new events, that will POST to the API
     * 
     * @since 1.0.0
     *
     * @return void
     */
    public function adminEventSettingsPage()
    {
        global $wpdb;
        
        /**
         * check if the error_message GET parameter is passed. 
         * if so, display the error message
         * 
         * otherwise, check if the save_message is passed.
         * if so, display the save message
         */
        if(isset($_GET['error_message']))
        {
            /**
             * if any error message is set, display the main error
             */
            add_action('admin_notices', [$this,'eventSettingsErrorMessage']);
            
            /**
             * if there is an error on the name field
             */
            if (isset($_GET['name_error']))
            {
                add_action('admin_notices', [$this,'eventSettingsNameErrorMessage']);
            }
            
            /**
             * if there is an error on the type field
             */
            if (isset($_GET['type_error']))
            {
                add_action('admin_notices', [$this,'eventSettingsTypeErrorMessage']);
            }
            
            do_action('admin_notices');
        }
        elseif (isset($_GET['save_message']))
        {
            /**
             * if the success message is set, display the success message
             */
            add_action('admin_notices', [$this,'eventAddedMessage']);
            
            do_action('admin_notices');
        }
        
        /**
         * Swaptify must be setup to view the page
         * Check the Swaptify connection,
         * if it's not set, show the setup required page
         */
        $swaptify = new Swaptify();
        
        $connect = $swaptify::connect();
        if ($connect)
        {
            $events = Swaptify::getEvents();
            require_once 'partials/events/index.php';
        }
        else
        {
            require_once 'partials/general/setup-required.php';
        }
    }
    
    /**
     * Render the Cookies page
     * 
     * This page will allow a user to select which cookies are sent via the Swaptify API as part of user data
     * 
     * The only cookie data that is sent will be based on what is selected here
     * 
     * @since 1.0.0
     *
     * @return void
     */
    public function adminCookiesPage()
    {
        global $wpdb;
        
        /**
         * check if the error_message GET parameter is passed. 
         * if so, display the error message
         * 
         * otherwise, check if the save_message is passed.
         * if so, display the save message
         */
        if(isset($_GET['error_message']))
        {
            /**
             * if any error message is set, display the main error
             */
            add_action('admin_notices', [$this,'cookiesErrorMessage']);
            
            /**
             * if there is an error on the name field
             */
            if (isset($_GET['name_error']))
            {
                add_action('admin_notices', [$this,'cookiesNameErrorMessage']);
            }
            
            do_action('admin_notices');
        }
        elseif (isset($_GET['save_message']))
        {
            /**
             * if the success message is set, display the success message
             */
            add_action('admin_notices', [$this,'cookiesAddedMessage']);
            
            do_action('admin_notices');
        }
        
        /**
         * query the database on the {$wpdb->prefix}swap_cookies table
         */
        $query = $wpdb->prepare(
            "SELECT 
                `id`,
                `name`
            FROM 
                {$wpdb->prefix}swap_cookies
            WHERE 
                1 = %d
            ",
            [
                1,
            ]);
        
        
        /**
         * Swaptify must be setup to view the page
         * Check the Swaptify connection,
         * if it's not set, show the setup required page
         */
        $swaptify = new Swaptify();
        
        $connect = $swaptify::connect();
        if ($connect)
        {
            $items = $wpdb->get_results($query, 'OBJECT');
            require_once 'partials/cookies/index.php';
        }
        else
        {
            require_once 'partials/general/setup-required.php';
        }
    }
    
    public function adminShortcodeGeneratorPage()
    {
        
        global $wpdb;
        
        $swaptify = new Swaptify();
        
        if(isset($_GET['error_message']))
        {
            /**
             * if any error message is set, display the main error
             */
            $this->_message = $_GET['error_message'];
            add_action('admin_notices', [$this,'shortcodeGeneratorErrorMessage']);
            
            do_action('admin_notices');
        }
        elseif (isset($_GET['save_message']))
        {
            /**
             * if the success message is set, display the success message
             */
            $this->_message = $_GET['save_message'];
            add_action('admin_notices', [$this,'shortcodeGeneratorSuccessMessage']);
            
            do_action('admin_notices');
        }
        
        $connect = $swaptify::connect();
        if ($connect)
        {
            
            $visitorTypesResponse = $swaptify->getVisitorTypes();
            
            $visitor_types = new stdClass();
            
            if ($visitorTypesResponse)
            {
                $visitor_types = $visitorTypesResponse;
            }
            
            
            $segmentResponse = $swaptify->getSegmentsAndSwaps(false, true, true);
            if (isset($segmentResponse->success) && $segmentResponse->success && isset($segmentResponse->segments))
            {
                $segments = $segmentResponse->segments;
            }
            else
            {
                $segments = new stdClass();
            }
            
            $key = Swaptify::getVariable($_GET, 'key');
            
            if ($key && isset($segments->$key))
            {
                /**
                 * @todo base the includes off the type
                 */
                wp_enqueue_media();
                wp_enqueue_editor();
                $segment = $segments->$key;
                require_once 'partials/shortcode-generator/segment.php';
            }
            else
            {
                $types = Swaptify::getSegmentTypes();
                require_once 'partials/shortcode-generator/index.php';
            }
        }
        else
        {
            require_once 'partials/general/setup-required.php';
        }
    }
    
    /**
     * Render the Visitor Types page
     * 
     * This page will pull visitor type data from the Swaptify API 
     * and display it along with sample code to set a visitor type for a given visitor
     * 
     * Additionally, there is a form to add new visitor types, that will POST to the API
     * 
     * @since 1.0.0
     *
     * @return void
     */
    public function adminVisitorTypesPage()
    {
        global $wpdb;
        
        /**
         * check if the error_message GET parameter is passed. 
         * if so, display the error message
         * 
         * otherwise, check if the save_message is passed.
         * if so, display the save message
         */
        if(isset($_GET['error_message']))
        {
            /**
             * if any error message is set, display the main error
             */
            add_action('admin_notices', [$this,'visitorTypeErrorMessage']);
            
            /**
             * if there is an error on the name field
             */
            if (isset($_GET['name_error']))
            {
                add_action('admin_notices', [$this,'visitorTypeNameErrorMessage']);
            }
            
            if (isset($_GET['unable_to_add']))
            {
                add_action('admin_notices', [$this,'visitorTypeFailedToAddErrorMessage']);
            }

            do_action('admin_notices');
        }
        elseif (isset($_GET['save_message']))
        {
            /**
             * if the success message is set, display the success message
             */
            add_action('admin_notices', [$this,'visitorTypeAddedMessage']);
            
            do_action('admin_notices');
        }
        
        /**
         * Swaptify must be setup to view the page
         * Check the Swaptify connection,
         * if it's not set, show the setup required page
         */
        $swaptify = new Swaptify();
        
        $connect = $swaptify::connect();
        if ($connect)
        {
            $visitor_types = Swaptify::getVisitorTypes();
            require_once 'partials/visitor-types/index.php';
        }
        else
        {
            require_once 'partials/general/setup-required.php';
        }
    }
    
    /**
     * GENERAL PAGE FUNCTIONS
     */
    
    /**
     * Generate a standard Wordpress message
     * 
     * @since 1.0.0
     * 
     * @param string $settingsField
     * @param string $errorCode
     * @param string $message
     * @param string $type error|success|warning|info
     * @return void
     */
    public function displayMessage($message, $type = 'error', $settingsField = 'swaptify-message', $errorCode = 'swaptify-message')
    {
        add_settings_error(
            $settingsField,
            $errorCode,
            $message,
            $type
        );
    }

    /**
     * Generate the input/select/textarea html for display
     * 
     * @since 1.0.0
     * 
     * @param [array] $args
     *          [
     *              wp_data - options|post_meta - wp data type to retrieve
     *              id - string - the id of the field
     *              name - string - the html name attribute
     *              post_id - string|int - the WP post id
     *              value - string - a value that can be passed in case 
     *              type - input|select|textarea - the main html tag 
     *              value_type - string - normal|serialized
     *              subtype - text|checkbox|radio|number the type of input
     *              options - an array of arrays, for select and radio types/subtypes - each element should have the keys 'name' and 'value'
     *                          the 'value' will be the value of the option, the 'name' with be the displayed value to the user
     *              include_blank_option - boolean - whether or not to include a blank option in a select
     *              required - boolean - whether or not the the field is required
     *              disabled - boolean - whether or not the field is disabled
     *              step - int - amount of increment/decrement a number value with move via controls(up and down keys/arrows)
     *              min - int - minimum value for a number subtype
     *              max - int - max value for a number subtype
     *              rows - int - the number of rows in a textarea
     *              cols - int - the number or columns in a textarea
     *          ]
     * 
     * @return void
     */
    public function swaptify_render_settings_field($args = []) 
    {
        /**
         * set all the available variables from the $args array
         */
        /**
         * set the text values
         */
        $wp_data = (isset($args['wp_data'])) ? $args['wp_data'] : null;
        $id = (isset($args['id'])) ? $args['id'] : null;
        $name = (isset($args['name'])) ? $args['name'] : null;
        $post_id = (isset($args['post_id'])) ? $args['post_id'] : null;
        $value = (isset($args['value'])) ? $args['value'] : null;
        $type = (isset($args['type'])) ? $args['type'] : null;
        $value_type = (isset($args['value_type'])) ? $args['value_type'] : null;
        $subtype = (isset($args['subtype'])) ? $args['subtype'] : 'text';
        
        /**
         * confirm options is an array
         */
        $options = (isset($args['options']) && is_array($args['options'])) ? $args['options'] : [];
        
        /**
         * set the boolean values
         */
        $include_blank_option = (isset($args['include_blank_option']) && $args['include_blank_option']) ? true : false;
        $disabled = (isset($args['disabled']) && $args['disabled']) ? true : false;
        $required = (isset($args['required']) && $args['required']) ?  true : false;
        $checked = (isset($args['checked']) && $args['checked']) ?  true : false;
        
        /**
         * set the numeric values
         */
        $step = (isset($args['step']) && is_numeric($args['step'])) ? $args['step'] : null;
        $min = (isset($args['min']) && is_numeric($args['min'])) ? $args['min'] : null;
        $max = (isset($args['max']) && is_numeric($args['max'])) ? $args['max'] : null;
        $rows = (isset($args['rows']) && is_numeric($args['rows'])) ? $args['rows'] : null;
        $cols = (isset($args['cols']) && is_numeric($args['cols'])) ? $args['cols'] : null;
        
        /**
         * set the default value to false
         */
        $wp_data_value = false;
        
        /**
         * check the wp_data type for how to get the value
         */
        if ($wp_data == 'option')
        {
            $wp_data_value = get_option($name);
        } 
        elseif ($wp_data == 'post_meta')
        {
            $wp_data_value = get_post_meta($post_id, $name, true);
        }
        else
        {
            $wp_data_value = $value;
        }
        
        /**
         * if there is no value, and the value arg is passed, use that.
         * note: this is typically for an empty saved option with a passed value
         */
        if ($wp_data_value === false && $value)
        {
            $wp_data_value = $value;
        }
        
        /**
         * start building the input
         */
        $input = '';
        
        if ($type == 'input')
        {
            $value = ($value_type == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
            $input .= '<input size="40" ';
            
            $input .= $required ? ' required="required"' : '';
            $input .= ' value="' . esc_attr($value) . '"';
            
            if ($subtype == 'hidden')
            {
                // nothing special now
            }
            elseif ($subtype == 'radio')
            {
                // nothing special now
            }
            elseif ($subtype == 'checkbox')
            {
                if ($checked)
                {
                    $input .= ' checked="checked"';
                }
            }
            elseif ($subtype == 'number')
            {
                $input .= $step ? ' rows="' . $step . '"' : '';
                $input .= $min ? ' cols="' . $min . '"' : '';
                $input .= $max ? ' cols="' . $max . '"' : '';    
            }
            else
            {
                /**
                 * any subtype not in the above is treated as text
                 */
                $subtype = 'text';
            }
            
            $input .= ' type="' . $subtype . '"';
            
            /**
             * if it's disabled, append "_disabled" to the name and id fields
             */
            if ($disabled)
            {
                $input .= $id ? ' id="' . $id . '_disabled"' : '';
                $input .= $name ? ' name="' . $name. '_disabled"' : '';
                $input .= ' disabled="disabled"';
                $input .= ' value="' . esc_attr($value) . '"';
            }
            else
            {
                $input .= $id ? ' id="' . $id . '"' : '';
                $input .= $name ? ' name="' . $name . '"' : '';
            }            
            
            $input .= ' />';
            
            /**
             * add a hidden input with the name and value so it is passed in the form
             */
            if ($disabled)
            {
                $input .= '<input type="hidden" name="' . $name . '" id="' . $id . '" value="' . esc_attr($value) . '" />';
            }
        }
        elseif ($type == 'select')
        {
            $input .= '<select';
            $input .= $id ? ' id="' . $id . '"' : '';
            $input .= $name ? ' name="' . $name. '"' : '';
            $input .= $required ? ' required="required"' : '';
            $input .= '>';
            
            if ($include_blank_option)
            {
                $input .= '<option value="">&mdash;</option>';
            }
            
            foreach ($options as $option)
            {
                /**
                 * if name and value key isn't set for an element, skip to the next
                 */
                if (!isset($option['value']) || !isset($option['name']))
                {
                    continue;
                }
                
                $input .= '<option value="' . $option['value'] . '"';
                if ($wp_data_value == $option['value'])
                {
                    $input .= ' selected="selected"';
                }
                $input .= '>';
                $input .= esc_attr($option['name']);
                $input .= '</option>';
            }
            
            $input .= '</select>';    
        }
        elseif ($type == 'textarea')
        {
            $input .= '<textarea';
            $input .= $id ? ' id="' . $id . '"' : '';
            $input .= $name ? ' name="' . $name. '"' : '';
            $input .= $rows ? ' rows="' . $rows . '"' : '';
            $input .= $cols ? ' cols="' . $cols . '"' : '';
            $input .= $required ? ' required="required"' : '';
            $input .= '>';
            $input .= esc_attr($wp_data_value);
            $input .= '</textarea>';
        }
        
        echo($input);
    }

    /**
     * PAGE MESSAGE FUNCTIONS 
     */
    
    /**
     * Display the error message for inability to save config settings
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function configurationErrorMessage()
    {
        $this->displayMessage('There was an error adding updating settings. Please try again.', 'error');
    }
    
    /**
     * Display the error message for issue updating default content
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function defaultContentErrorMessage()
    {
        $this->displayMessage('There was an error updating default content. Please try again.', 'error');
    }
    
    /**
     * Display the success message for updating default content
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function defaultContentSuccessMessage()
    {
        $this->displayMessage('Default content updated successfully', 'success');
    }
    
    /**
     * Display the general error for incomplete new event form
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function eventSettingsErrorMessage()
    {
        $this->displayMessage('Please complete the form', 'error');
    }
    
    /**
     * Display the error message for missing name
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function eventSettingsNameErrorMessage()
    {
        $this->displayMessage('Event name is required', 'error', 'event_name');
    }
    
    /**
     * Display the error message for invalid event type
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function eventSettingsTypeErrorMessage()
    {
        $this->displayMessage('Invalid event type', 'error', 'event_type');
    }
    
    /**
     * Display the event added successfully message
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function eventAddedMessage()
    {
        $this->displayMessage('Event added successfully', 'success');
    }
    
    /**
     * Display the general error for incomplete new event form
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function cookiesErrorMessage()
    {
        $this->displayMessage('Please complete the form', 'error');
    }
    
    /**
     * Display the error message for missing name
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function cookiesNameErrorMessage()
    {
        $this->displayMessage('Name is required', 'error', 'cookie_name');
    }
    
    /**
     * Display the event added successfully message
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function cookiesAddedMessage()
    {
        $this->displayMessage('Added successfully', 'success');
    }
    
    
    /**
     * Display the general error for incomplete new visitor type form
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function visitorTypeErrorMessage()
    {
        $this->displayMessage('Please complete the form', 'error');
    }
    
    /**
     * Display the error message for missing name
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function visitorTypeNameErrorMessage()
    {
        $this->displayMessage('Visitor Type name is required', 'error', 'visitor_type_name');
    }
    
    /**
     * Display the error message for being unable to add visitor type
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function visitorTypeFailedToAddErrorMessage()
    {
        $this->displayMessage('Unable to add Visitor Type', 'error', 'visitor_type_name');
    }
    
    /**
     * Display the event added successfully message
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function visitorTypeAddedMessage()
    {
        $this->displayMessage('Visitor Type added successfully', 'success');
    }
    
    
    public function shortcodeGeneratorErrorMessage()
    {
        $this->displayMessage($this->_message, 'error');
    }
    
    public function shortcodeGeneratorSuccessMessage()
    {
        $this->displayMessage($this->_message, 'success');
    }
    
    
    
    /**
     * 
     * PAGE FIELDS
     * 
     */
    
    /**
     * Build the fields for configuration
     * 
     * This will display in 2 phases.
     * The first is entering the account key
     * The second is after the account key is set, the user can then select the property and
     * have the ability to disable the plugin
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function configurationFields() 
    {
        add_settings_section(
            'swaptify_configuration_section', 
            '',  
            '', 
            'swaptify_configuration_settings'                   
        );
        
        /**
         * create the account token field
         */
        $args = [
            'type' => 'textarea',
            'id' => 'swaptify_account_token',
            'name' => 'swaptify_account_token',
            'required' => true,
            'rows' => 5,
            'cols' => 40,
            'get_options_list' => '',
            'value_type'=>'normal',
            'wp_data' => 'option'
        ];
        
        add_settings_field(
            'swaptify_account_token',
            'Swaptify API Access Token',
            [$this, 'swaptify_render_settings_field'],
            'swaptify_configuration_settings',
            'swaptify_configuration_section',
            $args
       );

        register_setting(
            'swaptify_configuration_settings',
            'swaptify_account_token'
        );
        
        /**
         * check if swaptify is connected.
         * if so, display properties
         */
        if (Swaptify::connect(false))
        {
            /**
             * get the available properties from the API
             */
            $options = Swaptify::getPropertiesForSelect();
            
            /**
             * build the property field
             */
            $args = [
                'type'      => 'select',
                'subtype' => 'text',
                'id'    => 'swaptify_property_key',
                'name' => 'swaptify_property_key',
                'required' => true,
                'get_options_list' => '',
                'value_type'=>'normal',
                'wp_data' => 'option',
                'options' => $options,
                'include_blank_option' => true,
            ];
            
            add_settings_field(
                'swaptify_property_key',
                'Property',
                [$this, 'swaptify_render_settings_field'],
                'swaptify_configuration_settings',
                'swaptify_configuration_section',
                $args
            );

            register_setting(
                'swaptify_configuration_settings',
                'swaptify_property_key'
            );
            
            /**
             * get the enabled value
             */
            $value = Swaptify::enabledValue();
            
            $options = Swaptify::enabledOptions();
            $fieldName = Swaptify::$enabledOptionName;
            
            /**
             * build the enabled field
             */
            $args = [
                'type'      => 'select',
                'subtype' => 'text',
                'id'    => $fieldName,
                'name' => $fieldName,
                'required' => true,
                'get_options_list' => '',
                'value_type'=>'normal',
                'value' => $value,
                'wp_data' => 'option',
                'options' => $options,
            ];
            
            add_settings_field(
                $fieldName,
                'Enabled',
                [$this, 'swaptify_render_settings_field'],
                'swaptify_configuration_settings',
                'swaptify_configuration_section',
                $args
           );

            register_setting(
                'swaptify_configuration_settings',
                $fieldName
           );
        }
    }
    
    /**
     * Build the fields for the Default Content page
     * 
     * This consists of a hidden input field and a button that will trigger the default content to update
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function defaultContentFields()
    { 
        add_settings_section(
            'swaptify_default_content_section', 
            '', // set the heading blank 
            '', // set the content blank
            'swaptify_default_content' 
        );
        $args = [
            'type' => 'input',
            'subtype' => 'hidden',
            'id' => 'action',
            'name' => 'action',
            'required' => true,
            'get_options_list' => '',
            'value_type'=>'normal',
            'wp_data' => 'none',
            'value' => 'update_default_content'
        ];

        add_settings_field(
            'action',
            '', // exclude form label for the hidden input
            [$this, 'swaptify_render_settings_field'],
            'swaptify_default_content',
            'swaptify_default_content_section',
            $args
        );

        register_setting(
            'swaptify_default_content',
            'action'
        );
    }
    
    /**
     * Generate the fields for the create new event form
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function eventSettingsFields()
    { 
        add_settings_section(
            'swaptify_event_settings_section', 
            'Add New Event', 
            '', // don't set the label/description
            'swaptify_event_settings' 
        );
        
        /**
         * build the event type options
         */
        $eventOptions = Swaptify::getEventTypes();
        
        $options = [];
        
        foreach ($eventOptions as $option)
        {
            $options[] = [
                'value' => $option->id,
                'name' => $option->name,
            ];    
        }
        
        $args = [
            'type' => 'select',
            'subtype' => 'text',
            'id' => 'event_type',
            'name' => 'event_type',
            'required' => true,
            'get_options_list' => '',
            'value_type'=>'normal',
            'wp_data' => 'none',
            'value' => 'add_new_event',
            'options' => $options,
        ];

        add_settings_field(
            'event_type',
            'Event Type',
            [$this, 'swaptify_render_settings_field'],
            'swaptify_event_settings',
            'swaptify_event_settings_section',
            $args
       );

       /**
        * add the event name field
        */
        $args = [
            'type' => 'input',
            'subtype' => 'text',
            'id' => 'event_name',
            'name' => 'event_name',
            'required' => true,
            'get_options_list' => '',
            'value_type'=>'normal',
            'wp_data' => 'none',
            'value' => ''
        ];

        add_settings_field(
            'event_name',
            'Event Name',
            [$this, 'swaptify_render_settings_field'],
            'swaptify_event_settings',
            'swaptify_event_settings_section',
            $args
        );
        
        /**
        * add the is_terminal field
        */
        $args = [
            'type' => 'input',
            'subtype' => 'checkbox',
            'id' => 'is_terminal',
            'name' => 'is_terminal',
            'required' => false,
            'get_options_list' => '',
            'value_type'=>'normal',
            'wp_data' => 'none',
            'value' => '1'
        ];
        
        /*
        add_settings_field(
            'is_terminal',
            'Is Conversion Event?',
            [$this, 'swaptify_render_settings_field'],
            'swaptify_event_settings',
            'swaptify_event_settings_section',
            $args
        );
        */
        
        /**
         * add the add event button
         */
        $args = [
            'type' => 'input',
            'subtype' => 'hidden',
            'id' => 'action',
            'name' => 'action',
            'required' => true,
            'get_options_list' => '',
            'value_type'=>'normal',
            'wp_data' => 'none',
            'value' => 'add_new_event'
        ];

        add_settings_field(
            'action',
            '',
            [$this, 'swaptify_render_settings_field'],
            'swaptify_event_settings',
            'swaptify_event_settings_section',
            $args
       );
        
        register_setting(
            'swaptify_event_settings',
            'action'
       );
    }
    
    /**
     * Generate the fields for the create new visitor type form
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function visitorTypesFields()
    { 
        add_settings_section(
            'swaptify_visitor_types_section', 
            'Add New Visitor Type', 
            '', // don't set the label/description
            'swaptify_visitor_types' 
        );
        
        /**
         * add the visitor type name field
         */
        $args = [
            'type' => 'input',
            'subtype' => 'text',
            'id' => 'visitor_type_name',
            'name' => 'visitor_type_name',
            'required' => true,
            'get_options_list' => '',
            'value_type'=>'normal',
            'wp_data' => 'none',
            'value' => ''
        ];

        add_settings_field(
            'visitor_type_name',
            'Visitor Type Name',
            [$this, 'swaptify_render_settings_field'],
            'swaptify_visitor_types',
            'swaptify_visitor_types_section',
            $args
        );
        
        /**
         * add the add visitor type button
         */
        $args = [
            'type' => 'input',
            'subtype' => 'hidden',
            'id' => 'action',
            'name' => 'action',
            'required' => true,
            'get_options_list' => '',
            'value_type'=>'normal',
            'wp_data' => 'none',
            'value' => 'add_new_visitor_type'
        ];

        add_settings_field(
            'action',
            '',
            [$this, 'swaptify_render_settings_field'],
            'swaptify_visitor_types',
            'swaptify_visitor_types_section',
            $args
       );
        
        register_setting(
            'swaptify_visitor_types',
            'action'
       );
    }
    
    /**
     * Generate the fields for adding cookies names to sent to Swaptify
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function cookiesFields()
    {
        
        add_settings_section(
            'swaptify_cookies_section', 
            'Add New Cookie', 
            '', // don't set the label/description
            'swaptify_cookies' 
        );
        
        /**
         * add the cookie name field
         */
        $args = [
            'type' => 'input',
            'subtype' => 'text',
            'id' => 'cookie_name',
            'name' => 'cookie_name',
            'required' => true,
            'get_options_list' => '',
            'value_type'=>'normal',
            'wp_data' => 'none',
            'value' => ''
        ];

        add_settings_field(
            'cookie_name',
            'Name',
            [$this, 'swaptify_render_settings_field'],
            'swaptify_cookies',
            'swaptify_cookies_section',
            $args
        );
        
        /**
         * add the cookie type field
         */
        $args = [
            'type' => 'input',
            'subtype' => 'text',
            'id' => 'cookie_name',
            'name' => 'cookie_name',
            'required' => true,
            'get_options_list' => '',
            'value_type'=>'normal',
            'wp_data' => 'none',
            'value' => ''
        ];

        add_settings_field(
            'cookie_name',
            'Name',
            [$this, 'swaptify_render_settings_field'],
            'swaptify_cookies',
            'swaptify_cookies_section',
            $args
        );
        
        /**
         * add the add cookie button
         */
        $args = [
            'type' => 'input',
            'subtype' => 'hidden',
            'id' => 'action',
            'name' => 'action',
            'required' => true,
            'get_options_list' => '',
            'value_type'=>'normal',
            'wp_data' => 'none',
            'value' => 'add_new_cookie'
        ];

        add_settings_field(
            'action',
            '',
            [$this, 'swaptify_render_settings_field'],
            'swaptify_cookies',
            'swaptify_cookies_section',
            $args
       );
        
        register_setting(
            'swaptify_cookies',
            'action'
       );
    }
    
    
    /**
     * 
     * POST ENDPOINTS
     *
     */
    /**
     * Endpoint for updating default content
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function admin_update_default_content() 
    {
        $success = '';
        
        $swaptify = new Swaptify();
        $connect = $swaptify::connect();

        if ($connect)
        {
            $swaptify->updateDefaultContent();
            $success = '&success=1';
        }
        
        wp_redirect(admin_url('admin.php?page=swaptify-default-content'.$success));
        exit();
    }

    /**
     * Confirm the event data is passed via POST and send the information to the Swaptify API
     * to create a new event
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function admin_add_new_event() 
    {
        /**
         * confirm Swaptify is configured and a property option is set
         */
        $swaptify = new Swaptify();
        $connect = $swaptify::connect();

        $error_messages = [];
        $success = false;
        
        if ($connect)
        {
            /**
             * check if the fields for name and type are passed
             */
            $name = Swaptify::getVariable($_POST, 'event_name');
            $type = Swaptify::getVariable($_POST, 'event_type');
            $is_terminal = Swaptify::getVariable($_POST, 'is_terminal', []);
            
            /**
             * if terminal is set, create it as a single element array
             */
            if ($is_terminal)
            {
                $is_terminal = [true];
            }
            
            /**
             * get the valid event types from the API
             */
            $eventTypes = Swaptify::getEventTypes();
            $availableTypes = [];
            
            foreach ($eventTypes as $eventType)
            {
                /**
                 * the id property is the name passed in the form
                 */
                $availableTypes[] = $eventType->id;
            }
            
            /**
             * if the name is not set, add the error
             */
            if (!$name)
            {
                $error_messages[] = 'name_error=1';
            }
            
            /**
             * if the type is not set, add the error
             */
            if (!$type)
            {
                $error_messages[] = 'type_error=1';
            }
            else
            {
                /**
                 * if the type is not in the available types array, add the error
                 */
                if (in_array($type, $availableTypes))
                {
                    // good
                }
                else
                {
                    $error_messages[] = 'Type+not+found';
                }
            }
            
            /**
             * if there are no errors in the error_messages array, save the new event
             */
            if (count($error_messages) == 0)
            {
                $success = $swaptify->addNewItem($name, $type, 'events', $is_terminal);
            }
        }

        $error_string = '';
        
        /**
         * if there are errors, create an URL string from the array
         * if success is true, the event was saved, so, display the success message
         */
        if (count($error_messages))
        {
            $error_string = '&error_message=1&' . implode('&', $error_messages);
        }
        elseif ($success)
        {
            $error_string = '&save_message=New+event+added';
        }
        
        wp_redirect(admin_url('admin.php?page=swaptify-event-settings' . $error_string));
        exit();
    }

    /**
     * Confirm the cookie data is passed via POST and add it to the db
     * 
     * @since 1.0.0
     * 
     * @return void
     */
    public function admin_add_new_cookie() 
    {
        $error_messages = [];
        $success = false;

            /**
             * check if the fields for name and type are passed
             */
            $name = Swaptify::getVariable($_POST, 'cookie_name');
            
            /**
             * replace spaces with underscores. 
             */
            $name = str_replace(' ', '_', $name);
            /**
             * if the name is not set, add the error
             */
            if (!$name)
            {
                $error_messages[] = 'name_error=1';
            }
            
            /**
             * if there are no errors in the error_messages array, save the new event
             */
            if (count($error_messages) == 0)
            {
                global $wpdb;

                $insert = $wpdb->insert( 
                    $wpdb->prefix . 'swap_cookies', 
                    [ 
                        'name' => $name
                    ]
                );

                $success = true;
            }
        

        $error_string = '';
        
        /**
         * if there are errors, create an URL string from the array
         * if success is true, the event was saved, so, display the success message
         */
        if (count($error_messages))
        {
            $error_string = '&error_message=1&' . implode('&', $error_messages);
        }
        elseif ($success)
        {
            $error_string = '&save_message=New+cookie+added';
        }
        
        wp_redirect(admin_url('admin.php?page=swaptify-cookies' . $error_string));
        exit();
    }
    
    public function admin_save_swaptify_segment()
    {
        $swaptify = new Swaptify();
        $connect = $swaptify::connect();

        $error_messages = [];
        $success = false;
        
        if ($connect)
        {
            $segmentKey = Swaptify::getVariable($_POST, 'segment_key');
            $swapsArray = Swaptify::createArrayForRequestBodyForEditingSwaps($_POST, $segmentKey);
            $newSwapsArray = Swaptify::createArrayForRequestBodyForEditingSwaps($_POST, $segmentKey, true);

            if ($segmentKey && ($swapsArray || $newSwapsArray))
            {
                $error_string = '';
                
                if ($newSwapsArray)
                {
                    $response = $swaptify->createNewSwaps($newSwapsArray, true);
                    
                    if ($response && isset($response['success']))
                    {
                        if (isset($response['errors']) && $response['errors'] && is_array($response['errors']))
                        {
                            // show the error message
                            $error_string .= urlencode(implode(', ', $response['errors']));
                        }
                    }
                }
                
                if ($swapsArray)
                {
                    $response = $swaptify->updateSwapsByKey($swapsArray, true);
                    
                    if ($response && isset($response['success']))
                    {
                        if (isset($response['errors']) && $response['errors'] && is_array($response['errors']))
                        {
                            // show the error message
                            $error_string .= urlencode(implode(', ', $response['errors']));
                        }
                    }
                }
                
                if ($error_string != '')
                {
                    // show the error message
                    wp_redirect(admin_url('admin.php?page=swaptify-shortcode-generator&error_message=' . $error_string));
                    exit();
                }
                else
                {
                    $message = urlencode('Swaps saved successfully')    ;
                    wp_redirect(admin_url('admin.php?page=swaptify-shortcode-generator&save_message=' . $message));
                    exit();
                }
                
                
            }
        }
        
        wp_redirect(admin_url('admin.php?page=swaptify-shortcode-generator&error_message=' . urlencode('There was an error saving your Swaps. Please try again')));
        exit();
    }
    
    public function admin_delete_swaptify_swap()
    {
        
        $swaptify = new Swaptify();
        $connect = $swaptify::connect();

        if ($connect)
        {
            $swapKey = Swaptify::getVariable($_POST, 'swap');
            
            if (!$swapKey)
            {
                $error_string = urlencode('Swap not found');
                wp_redirect(admin_url('admin.php?page=swaptify-shortcode-generator&error_message=' . $error_string));
                exit();
            }
            
            $deleted = $swaptify->deleteSwap($swapKey);
            
            if ($deleted)
            {
                wp_redirect(admin_url('admin.php?page=swaptify-shortcode-generator&save_message=' . urlencode('Swap deleted')));
                exit();
            }
        }
        
        wp_redirect(admin_url('admin.php?page=swaptify-shortcode-generator&error_message=' . urlencode('There was an error deleting a Swap. Please try again')));
        exit();
    }
    
    public function admin_create_swaptify_segment()
    {
        $swaptify = new Swaptify();
        $connect = $swaptify::connect();

        $error_messages = [];
        
        if ($connect)
        {
            $name = Swaptify::getVariable($_POST, 'name');
            $type = Swaptify::getVariable($_POST, 'type');
            
            if (!$name)
            {
                $error_messages[] = 'Segment name is required';
            }
           
            if (!$type)
            {
                $error_messages[] = 'Segment type is required';
            }
            
            if (count($error_messages))
            {
                $error_string = urlencode(implode(', ', $error_messages));
                wp_redirect(admin_url('admin.php?page=swaptify-shortcode-generator&error_message=' . $error_string));
                exit();
            }
            
            $key = $swaptify->addNewItem($name, $type, 'segments', [], true);
            
            if ($key)
            {
                wp_redirect(admin_url('admin.php?page=swaptify-shortcode-generator&key=' . $key));
                exit();
            }
        }
        
        wp_redirect(admin_url('admin.php?page=swaptify-shortcode-generator&error_message=' . urlencode('There was an error creating a new Segment. Please try again')));
        exit();
    }
    
    /**
     * Confirm the visitor type data is passed via POST and send the information to the Swaptify API
     * to create a new visitor type
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function admin_add_new_visitor_type() 
    {
        /**
         * confirm Swaptify is configured and a property option is set
         */
        $swaptify = new Swaptify();
        $connect = $swaptify::connect();

        $error_messages = [];
        $success = false;
        
        if ($connect)
        {
            /**
             * check if the fields for name is passed
             */
            $name = Swaptify::getVariable($_POST, 'visitor_type_name');
            
            /**
             * if the name is not set, add the error
             */
            if (!$name)
            {
                $error_messages[] = 'name_error=1';
            }
            
            /**
             * if there are no errors in the error_messages array, save the new event
             */
            if (count($error_messages) == 0)
            {
                $success = $swaptify->addNewItem($name, null, 'visitor_types');
            }
        }

        $error_string = '';
        
        /**
         * if there are errors, create an URL string from the array
         * if success is true, the event was saved, so, display the success message
         */
        if (count($error_messages))
        {
            $error_string = '&error_message=1&' . implode('&', $error_messages);
        }
        elseif ($success)
        {
            $error_string = '&save_message=New+Visitor+Type+added';
        }
        else
        {
            $error_string = '&error_message=1&unable_to_add=failed+to+add+Visitor+Type';
        }
        
        wp_redirect(admin_url('admin.php?page=swaptify-visitor-types' . $error_string));
        exit();
    }
}
