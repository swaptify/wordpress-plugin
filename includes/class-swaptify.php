<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/swaptify
 * @since      1.0.0
 *
 * @package    Swaptify
 * @subpackage Swaptify/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Swaptify
 * @subpackage Swaptify/includes
 * @author     Swaptify <support@swaptify.com>
 */
class Swaptify 
{
    /**
     * regex pattern for ensuring class and id are valid
     *
     * @since 1.0.0
     * 
     * @var string
     */    
    public static $class_pattern = "/[^A-Za-z0-9 -_]/";
    
    /**
     * the swaptify connection object through which API calls can be made
     *
     * @since 1.0.0
     * 
     * @var object|null
     */
    public static $connection;
    
    /**
     * swaptify cookie name that is used to identify a visitor
     * 
     * @since 1.0.0
     * 
     * @var string
     */
    public static $cookieName = 'swaptify';

    public static $slugPrefix = 'swaptify-visitor_type-';
    
    /**
     * Wordpress option name for toggling the plugin usage on the public facing side
     *
     * @since 1.0.0
     * 
     * @var string
     */
    public static $enabledOptionName = 'swaptify_enabled';
    
    /**
     * App site URL
     *
     * @since 1.0.0
     * 
     * @var string
     */
    public static $url = 'https://app.swaptify.com';
    
    /**
     * pretty version of app site url
     *
     * @since 1.0.0
     * 
     * @var string
     */
    public static $prettyUrl = 'app.swaptify.com';
    
    /**
     * API base url
     *
     * @since 1.0.0
     * 
     * @var string
     */
    public static $apiUrl = 'https://app.swaptify.com/api/';
    
    /**
     * An array of key=>value pairs that contain the segment key and content to display 
     *
     * @since 1.0.0
     * 
     * @var array
     */
    public static $swaps = [];
    
    /**
     * An array of key=>value pairs that contain the segment key and subcontent to display
     * 
     * @since 1.0.0
     * 
     * @var array
     */
    public static $sub_swaps = [];
    
    public static $current_swaps = null;
    public static $get_segments_response = null;
    public static $get_segments_and_swaps_response = null;
    public static $available_swaps = null;
    
    public static $default_swaps = null;
    
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Swaptify_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() 
    {
        /**
         * set the version and plugin name
         */
        if (defined('swaptify_version')) 
        {
            $this->version = swaptify_version;
        } 
        else 
        {
            $this->version = '1.0.0';
        }
        
        $this->plugin_name = 'swaptify';
        
        /**
         * load dependencies and hooks, part of the plugin boilerplate
         */
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }
    
    /**
     * BOILERPLATE INCLUDED METHODS
     */

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Swaptify_Loader. Orchestrates the hooks of the plugin.
     * - Swaptify_i18n. Defines internationalization functionality.
     * - Swaptify_Admin. Defines all hooks for the admin area.
     * - Swaptify_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() 
    {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-swaptify-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-swaptify-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-swaptify-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-swaptify-public.php';

        $this->loader = new Swaptify_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Swaptify_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() 
    {
        $plugin_i18n = new Swaptify_i18n();

        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() 
    {

        $plugin_admin = new Swaptify_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        
        /**
         * add tinyMCE button AJAX action
         */
        $this->loader->add_action('wp_ajax_tinymce_get_swaps', $this, 'TinyMCESwapContent');
        $this->loader->add_action('wp_ajax_tinymce_get_segment_types', $this, 'TinyMCESegmentTypes');
        $this->loader->add_action('wp_ajax_update_swap_content', $this, 'update_swap_content');
        $this->loader->add_action('wp_ajax_get_available_swaps', $this, 'get_available_swaps');
        $this->loader->add_action('wp_ajax_add_segment', $this, 'add_segment');
        $this->loader->add_action('wp_ajax_add_swaps', $this, 'add_swaps');
        $this->loader->add_filter('mce_buttons_3', $this, 'swap_register_buttons');
        $this->loader->add_filter('mce_external_plugins', $this, 'swap_register_tinymce_javascript');
        
        $this->loader->add_action('add_meta_boxes', $this, 'adminEventsField');
        $this->loader->add_action('add_meta_boxes', $this, 'adminVisitorTypesField');

        $this->loader->add_action('post_updated', $this, 'setPostSwapContentValues', 10, 3);
    }
    
    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Swaptify_Public( $this->get_plugin_name(), $this->get_version() );
        
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        $this->loader->add_action('wp', $this, 'registerPublicShortcodes');
        $this->loader->add_action('the_post', $this, 'addPostIdInput');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     * @return void
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Swaptify_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }
    
    /**
     * 
     * ADMIN functions
     * 
     */
    
    /**
     * Determine if swaptify is enabled based on the option. 
     * If the option isn't set, i.e. the user hasn't marked it as disabled, it will be treated as enabled
     *
     * @since 1.0.0
     * 
     * @return boolean
     */
    public static function enabled()
    {
        /**
         * get the enable option value
         */
        $value = static::enabledValue();
        
        /**
         * set it to a boolean
         */
        $enabled = $value == '1' ? true : false;
        
        return $enabled;
    }
    
    /**
     * Get the enabled value, return the string value of the option.
     * If the value hasn't been set, return 1
     * 
     *  1 = enabled
     *  0 = disabled
     *
     * @since 1.0.0
     * 
     * @return string
     */
    public static function enabledValue()
    {
        $value = get_option(static::$enabledOptionName);
        
        /**
         * if the option value is exactly false, set it to 1
         * which is enabled
         */
        if ($value === false)
        {
            $value = '1';    
        }
        
        return (string) $value;
    }
    
    /**
     * returns array of arrays with keys value and name, that are the options for enabling and disabling
     *
     * @since 1.0.0
     * 
     * @return array
     */
    public static function enabledOptions()
    {
        return [
            [
                'value' => '1',
                'name' => 'Yes',
            ],
            [
                'value' => '0',
                'name' => 'No',
            ],
        ];
    }
    
    /**
     * Add a box to select which page level events are associated with a given page.
     * For editing pages
     * 
     * @since 1.0.0
     *
     * @return void|string
     */
    public function adminEventsField()
    {
        global $post_type;

        $types = [
            'post',
            'page',
        ];

        if (in_array($post_type, $types))
        {
            add_meta_box('swaptify_events', __('Swaptify Page View Events', 'swaptify_plugin'), [$this, 'adminEventFieldContent'], $post_type);
        }
    }
    
    /**
     * Add a box to select which visitor type will trigger on a given page.
     * For editing pages
     * 
     * @since 1.0.0
     *
     * @return void|string
     */
    public function adminVisitorTypesField()
    {
        global $post_type;

        $types = [
            'post',
            'page',
        ];

        if (in_array($post_type, $types))
        {
            add_meta_box('swaptify_visitor_types', __('Swaptify Page Visitor Type', 'swaptify_plugin'), [$this, 'adminVisitorTypeFieldContent'], $post_type);
        }
    }

    /**
     * Render the content of the page view event select meta box
     * 
     * @since 1.0.0
     * 
     * @return void
     */
    public function adminEventFieldContent()
    {
        global $wpdb;
        $id = get_the_ID();
        
        if (!$id)
        {
            return '';
        }

        /**
         * get all the events that are of type page_view
         */
        $events = static::getEvents('page_view');

        /**
         * if there are no event return empty string
         */
        if (!$events)
        {
            return '';
        }

        /**
         * query the database for events currently associated with this post
         */
        $query = $wpdb->prepare(
            "SELECT 
                swaptify_event_key AS `key` 
            FROM 
                {$wpdb->prefix}post_swap_events         
            WHERE
                wp_post_id = %d 
            ",
            [
                $id,
            ]
        );

        $event_keys = $wpdb->get_results($query, 'OBJECT');

        /**
         * build an array of the current events
         */
        $checked = [];
        foreach ($event_keys as $event_key)
        {
            $checked[] = $event_key->key;
        }

        /**
         * Check the current events array against all the page view events.
         * Set the event as checked if it's in the current events.
         * This will render a checked checkbox
         */
        foreach ($events as $key => $event)
        {
            $event->checked = in_array($key, $checked);
        }

        require_once __DIR__.'/../admin/partials/events/elements/events-meta-display.php';
    }
    
    /**
     * Render the content of the page visitor type meta box
     * 
     * @since 1.0.0
     * 
     * @return void
     */
    public function adminVisitorTypeFieldContent()
    {
        global $wpdb;
        $id = get_the_ID();
        
        if (!$id)
        {
            return '';
        }

        /**
         * get all the events that are of type page_view
         */
        $visitor_types = static::getVisitorTypes();

        /**
         * if there are no event return empty string
         */
        if (!$visitor_types)
        {
            return '';
        }

        /**
         * query the database for events currently associated with this post
         */
        $query = $wpdb->prepare(
            "SELECT 
                swaptify_visitor_type_key AS `key` 
            FROM 
                {$wpdb->prefix}post_swap_visitor_types         
            WHERE
                wp_post_id = %d 
            ",
            [
                $id,
            ]
        );

        $visitor_type_keys = $wpdb->get_results($query, 'OBJECT');

        /**
         * build an array of the current events
         */
        $selected = null;
        foreach ($visitor_type_keys as $visitor_type_key)
        {
            $selected = $visitor_type_key->key;
            break;
        }

        require_once __DIR__.'/../admin/partials/visitor-types/elements/visitor-type-meta-display.php';
    }
    
    /**
     * Add a new segment
     * 
     * @since 1.0.0
     *
     * @return void
     */
    public function add_segment()
    {
        /**
         * get the name and type from the $_POST variable
         */
        $name = Swaptify::getVariable($_POST, 'name');
        $type = Swaptify::getVariable($_POST, 'type');
        
        $json = [
            'success' => false,
        ];;
    
        /**
         * if both segment_key and current_swap_key are set,
         * send the request to the API for the swap preview
         */
        if ($name && $type)
        {
            $postSwaps = Swaptify::getVariable($_POST, 'values', []);
            
            $swaps = [];
            
            foreach ($postSwaps as $swap)
            {
                $swaps[] = (object)$swap;
            }
            
            /**
             * get the data from the API
             */
            $data = static::addNewItem($name, $type, 'segments', $swaps);
            
            /**
             * if data is a value and the success property is true, set the data variable
             */
            if ($data)
            {
                $json['success'] = true;
                //$json['data'] = $data;
            }
        }

        /**
         * echo the JSON
         */
        echo(json_encode($json));
        
        /**
         * exit the script
         * this is required to terminate immediately and return a proper response
         */
        wp_die();
    }
    
    /**
     * Add new swaps
     * 
     * @since 1.0.0
     *
     * @return void
     */
    public function add_swaps()
    {
        /**
         * get the name and type from the $_POST variable
         */
        $key = Swaptify::getVariable($_POST, 'segmentKey');
        
        $json = [
            'success' => false,
        ];
    
        /**
         * if both segment_key and current_swap_key are set,
         * send the request to the API for the swap preview
         */
        if ($key)
        {
            $postSwaps = Swaptify::getVariable($_POST, 'values', []);
            
            $swaps = [];
            
            foreach ($postSwaps as $swap)
            {
                $swaps[] = (object)$swap;
            }
            
            /**
             * get the data from the API
             */
            $data = static::addNewSwaps($key, $swaps);
            
            /**
             * if data is a value and the success property is true, set the data variable
             */
            if ($data)
            {
                $json['success'] = true;
                //$json['data'] = $data;
            }
        }

        /**
         * echo the JSON
         */
        echo(json_encode($json));
        
        /**
         * exit the script
         * this is required to terminate immediately and return a proper response
         */
        wp_die();
    }
    
    /**
     * Get the available Segments and Swaps, will echo directly in JSON format
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function get_available_swaps()
    {
        $json = [
            'success' => true,
            'data' => [],
        ];
        
        $data = static::getSegmentsAndSwaps();

        if ($data && isset($data->segments))
        {
            $json['data'] = $data->segments;
        }
        
        /**
         * echo the JSON
         */
        echo(json_encode($json));
        
        /**
         * exit the script
         * this is required to terminate immediately and return a proper response
         */
        wp_die();
    }
    
    /**
     * Get the Swaptify connection settings 
     *
     * @since 1.0.0
     * 
     * @param boolean $includeProperty - whether or not to confirm connection when the property is set
     * 
     * @return object|boolean
     */
    public static function connect($includeProperty = true)
    {
        /**
         * if the connection is already cached, return it
         */
        if (static::$connection)
        {
            /**
             * if the property is supposed to be included AND it's in the cached object, return it
             */
            if ($includeProperty && isset(static::$connection->property_key) && static::$connection->property_key)
            {    
                return static::$connection;
            }
            elseif (!$includeProperty)
            {
                return static::$connection;
            }
        }

        /**
         * get the WordPress options for the account token and property key
         */
        $bearer_token = get_option('swaptify_account_token');
        $property_key = get_option('swaptify_property_key');
        
        /**
         * if the token is not set, return false
         */
        if (!$bearer_token)
        {
            return false;
        } 
        
        /**
         * if the property is supposed to be included and the property is not set, return false
         */
        if ($includeProperty && !$property_key)
        {
            return false;
        } 

        /**
         * build the connection object
         */
        $connection = new stdClass();
        $connection->bearer_token = $bearer_token;
        
        /**
         * set the property key
         */
        if ($includeProperty)
        {   
            $connection->property_key = $property_key;
        }
        
        /**
         * set the request timeout
         */
        $connection->request_timeout_seconds = 3;
        $connection->url = static::$apiUrl;

        static::$connection = $connection;
        
        return static::$connection; 
    }
    
    /**
     * Request default content for a given property and update the database with the default content
     * If the response is successful, it will return an array of default content, otherwise, it will return false
     *
     * @since 1.0.0
     * 
     * @return array|bool
     */
    public function updateDefaultContent()
    {
        /**
         * establish the connection
         */
        $connection = static::connect();
        
        /**
         * if it's not setup, return false
         */
        if (!$connection)
        {
            return false;
        }
        
        /**
         * Create a new HTTP request object
         */
        $request = new WP_Http();
        
        /**
         * set the URL to the default content API endpoint with the property key as a parameter
         */
        $url = $connection->url . 'swaps/default?property=' . $connection->property_key;
        
        $args = static::connectionArgs($connection);
        $args['method'] = 'GET';
        
        try 
        {
            /**
             * send the request
             */
            $response = $request->request($url, $args);
            
            /**
             * if the request was successful, check the content
             */
            if (!is_wp_error($response))
            {
                $content = (string) $response['body'];
                $json = json_decode($content);
                
                $default_data = [];
                
                /**
                 * ensure the success parameter is true and the data parameter is set
                 * this means the data return will contain the default content for the segments
                 */
                if ($json && isset($json->success) && $json->success && isset($json->data))
                {
                    /**
                     * purge the current default data
                     */
                    $this->removeDefaultData();
                    
                    /**
                     * Loop over the data in the response
                     */
                    foreach ($json->data as $key => $object)
                    {
                        $default_data[$key] = $object;
                        
                        /**
                         * add a new default data entry
                         */
                        $this->addDefaultData($object);
                    }

                    return $default_data;
                }
            }
        }
        catch (Exception $e)
        {
            // pass ...
        }
        
        return false;
    }

    /**
     * Remove all default data in the database
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function removeDefaultData(): void
    {
        global $wpdb;

        $table = $wpdb->prefix . 'swap_default_contents';
        $wpdb->query("TRUNCATE TABLE $table");
    }

    /**
     * Add a default data entry to the database
     * Will take an object directly from the swaps/default API endpoint
     *
     * @since 1.0.0
     * 
     * @param object $object
     *                  ->key - the segment key
     *                  ->name - the name of the swap
     *                  ->swap_key - the key of the swap
     *                  ->type - the type of the segment
     *                  ->content - the content of the swap
     *                  ->sub_content - the subcontent of the swap
     * 
     * @return boolean
     */
    public function addDefaultData($object)
    {
        global $wpdb;

        $insert = $wpdb->insert( 
            $wpdb->prefix . 'swap_default_contents', 
            [ 
                'swap_segment_key' => $object->key,
                'swap_key' => $object->swap_key,
                'swap_name' => $object->swap_name,
                'name' => $object->name,
                'type' => $object->type,
                'content' => $object->content,
                'sub_content' => $object->sub_content,
            ]
        );

        return $insert;
    }

    /**
     * Get the default content for each segment key in the array passed
     *
     * @since 1.0.0
     * 
     * @param array $keys 
     *                  data ->
     *                      segments -> [] - array of segment keys used to build default content
     *                  types ->
     *                  events ->
     * 
     * @return array
     */
    public static function getDefaultContent(array $keys = [])
    {
        global $wpdb;
        
        /**
         * get all the default contents from the database
         */
        $query = $wpdb->prepare(
            "SELECT 
                swap_segment_key AS `key`,
                content,
                sub_content,
                `type`
            FROM 
                {$wpdb->prefix}swap_default_contents         
            WHERE
                1 = %d 
            ",
            [
                1,
            ]
        );

        $default_contents = $wpdb->get_results($query, 'OBJECT');

        $data = [];
        $contents = [];
        
        /**
         * loop over all the results and create an array with the segment key as the key
         */
        foreach ($default_contents as $content)
        {
            $contents[$content->key] = $content;
        }
        
        /**
         * if the data => segments key is set,
         * loop over the default_contents array
         * if the key value from the segments array matches a key in the default_contents array
         * add it's content and sub_content value to the $data array
         */
        if (isset($keys['data']['segments']))
        {
            foreach ($keys['data']['segments'] as $key => $swap_key)
            {
                if (isset($contents[$swap_key]))
                {
                    $data['data'][$swap_key] = $contents[$swap_key]->content;
                    $data['subdata'][$swap_key] = $contents[$swap_key]->sub_content;
                    $data['keys'][$swap_key] = $contents[$swap_key]->key;
                    $data['types'][$swap_key] = $contents[$swap_key]->type;
                }
            }
        }
        else
        {
            foreach($contents as $swap_key => $content)
            {
                $data['data'][$swap_key] = $content->content;
                $data['subdata'][$swap_key] = $content->sub_content;
                $data['keys'][$swap_key] = $content->key;
                $data['types'][$swap_key] = $content->type;
            }
        }

        /**
         * this value will contain all the default contents based on the segment keys passed
         */
        return $data;
    }
    
    /**
     * Get all available default Swaps
     * 
     * @since 1.0.0
     * 
     * @return void
     */
    public static function getAllDefaultContent()
    {
        if (static::$default_swaps)
        {
            return static::$default_swaps;
        }
        
        static::$default_swaps = static::getDefaultContent();
        
        return static::$default_swaps;
    }
    
    /**
     * Get default Swap content for a given Segment key, formatted in HTML
     * e.g. the URL type will return HTML including the anchor tag
     *
     * @param string $key
     * @param array $attributes
     * @return string
     */
    public static function getDefaultContentForSegmentKeyPreview($key, $attributes = [])
    {
        $default_swaps = static::getAllDefaultContent();
        
        if (!is_array($default_swaps))
        {
            return '';
        }
        
        if (
            !isset($default_swaps['data'])
            || !isset($default_swaps['subdata'])
            || !isset($default_swaps['keys'])
            || !isset($default_swaps['types'])
        )
        {
            return '';
        }
        
        $data = isset($default_swaps['data'][$key]) ? $default_swaps['data'][$key] : '';
        $subdata = isset($default_swaps['subdata'][$key]) ? $default_swaps['subdata'][$key] : '';
        $type = isset($default_swaps['types'][$key]) ? $default_swaps['types'][$key] : '';
        
        $class = isset($attributes['class']) ? $attributes['class'] : '';
        $id = isset($attributes['id']) ? $attributes['id'] : '';
        
        
        if ($type == 'image')
        {
            /**
             * IMAGE
             */
            return '<img 
                    src="' . $data . '" 
                    title="' . htmlentities(($subdata ?? '')) . '" 
                    alt="' . htmlentities(($subdata ?? '')) . '" 
                    id="' . $id . '"
                    class="' . $class . '"
                />';
        }
        
        if ($type == 'url')
        {
            /**
             * URL
             */
            return '<a 
                    href="'.$data.'"
                    id="' . $id . '"
                    class="' . $class . '"
                >
                ' . $subdata . '</a>';
        }
        
        if ($type == 'text')
        {
            /**
             * TEXT
             */
        
            if ($class || $id)
            {
                return '<div 
                        id="' . $id . '"
                        class="' . $class . '"
                    >
                    ' . @do_shortcode($data) . '</div>';
            }
            else
            {
                return @do_shortcode($data);
            }
        }
        
        return '';
    }
    
    /**
     * Register shortcodes for swaps
     * shortcodes are:
     *      swap_segment_image - used for rendering swap content inside an img tag
     *      swap_segment_url - used for rendering swap content inside an a tag
     *      swap_segment - used for rendering text/HTML content, will be rendered directly
     * 
     * @since 1.0.0
     *
     * @return void
     */
    public function registerPublicShortcodes()
    {
        add_shortcode('swap_segment_image', [$this, 'renderImage']);
        add_shortcode('swap_segment_url', [$this, 'renderUrl']);
        add_shortcode('swap_segment', [$this, 'renderText']);
    }
    
    /**
     * Get the segments for the property
     *
     * @since 1.0.0
     * 
     * @return object|boolean
     */
    public static function getSegments($getFresh = false)
    {
        /**
         * confirm the connection, i.e. settings, has been established
         */
        $connection = static::connect();
        
        if (!$connection)
        {
            return false;
        }
        
        if (static::$get_segments_response !== null && !$getFresh)
        {
            return static::$get_segments_response;
        }
        
        /**
         * create the request
         */
        $request = new WP_Http();
        
        $url = $connection->url . 'segments?property=' . $connection->property_key;
        
        $args = static::connectionArgs($connection);
        /**
         * set base data variable as object with segments as properties
         */
        $data = new stdClass();
        $data->segments = [];
            
        try 
        {
            /**
             * run the request
             */    
            $response = $request->request($url, $args);
            if (!is_wp_error($response))
            {
                
                $json = null;
                /**
                 * if the response contains a body property, JSON decode it
                 */
                if (is_array($response) && isset($response['body']))
                {   
                    $content = (string) $response['body'];
                    $json = json_decode($content);
                }
                else
                {
                    /**
                     * if the response doesn't content the body property, return false
                     */
                    return false;
                }
                
                /**
                 * if the JSON object contains the success property AND the segments property,
                 * set the data variable to the JSON object
                 * otherwise, return false 
                 */
                if ($json && isset($json->success) && $json->success && isset($json->segments))
                {
                    $data = $json;
                }
                else
                {
                    return false;
                }
            }

        }
        catch (Exception $e)
        {
            /**
             * if there's an exception, return false
             */
            return false;
        }
        
        /**
         * return the data object
         */
        
        static::$get_segments_response = $data;
        return $data;
    }
    
    /**
     * Static method to get the Segments and Swaps
     * NOTE: not used for rendering
     * 
     * @since 1.0.0
     * 
     * @param boolean $getFresh - if set to true, will retreive from the Swaptify server rather than the cached data
     * @param boolean $includeVisitorTypes - if set to true, will include the Segment type in the response
     * @param boolean $includeContent - if set to true, will include the Swap content in the response
     * 
     * @return object|false
     */
    public static function getSegmentsAndSwaps($getFresh = false, $includeVisitorTypes = false, $includeContent = false)
    {
        /**
         * confirm the connection, i.e. settings, has been established
         */
        $connection = static::connect();
        
        if (!$connection)
        {
            return false;
        }
        
        if (static::$get_segments_and_swaps_response !== null && !$getFresh)
        {
            return static::$get_segments_and_swaps_response;
        }
        
        /**
         * create the request
         */
        $request = new WP_Http();
        
        $url = $connection->url . 'segments/swaps?property=' . $connection->property_key;
        
        if ($includeVisitorTypes) 
        {
            $url .= '&include_visitor_types=1';
        }
        
        if ($includeContent) 
        {
            $url .= '&include_content=1';
        }
        
        $args = static::connectionArgs($connection);
        /**
         * set base data variable as object with segments as properties
         */
        $data = new stdClass();
        $data->segments = [];
            
        try 
        {
            /**
             * run the request
             */    
            $response = $request->request($url, $args);
            if (!is_wp_error($response))
            {
                
                $json = null;
                /**
                 * if the response contains a body property, JSON decode it
                 */
                if (is_array($response) && isset($response['body']))
                {   
                    $content = (string) $response['body'];
                    $json = json_decode($content);
                }
                else
                {
                    /**
                     * if the response doesn't content the body property, return false
                     */
                    return false;
                }
                
                /**
                 * if the JSON object contains the success property AND the segments property,
                 * set the data variable to the JSON object
                 * otherwise, return false 
                 */
                if ($json && isset($json->success) && $json->success && isset($json->segments))
                {
                    $data = $json;
                }
                else
                {
                    return false;
                }
            }

        }
        catch (Exception $e)
        {
            /**
             * if there's an exception, return false
             */
            return false;
        }
        
        /**
         * return the data object
         */
        
        static::$get_segments_and_swaps_response = $data;
        return $data;
    }
    
    /**
     * Get the segment types from the API
     *
     * @since 1.0.0
     * 
     * @return array
     */
    public static function getSegmentTypes()
    {
        /**
         * set data as an empty array
         */
        $array = [];
        
        /**
         * confirm the connection, i.e. settings, has been established
         */
        $connection = static::connect(false);
        
        if (!$connection)
        {
            return $array;
        }
        
        /**
         * create the request
         */
        $request = new WP_Http();
        
        $url = $connection->url . 'segments/types';
        
        $args = static::connectionArgs($connection);
            
        try 
        {
            /**
             * run the request
             */    
            $response = $request->request($url, $args);
            if (!is_wp_error($response))
            {
                
                $json = null;
                /**
                 * if the response contains a body property, JSON decode it
                 */
                if (is_array($response) && isset($response['body']))
                {   
                    $content = (string) $response['body'];
                    $json = json_decode($content);
                }
                else
                {
                    /**
                     * if the response doesn't content the body property, return empty array
                     */
                    return $array;
                }
                
                /**
                 * if the JSON variable is set, set the array to the json
                 */
                if ($json && is_array($json))
                {
                    $array = $json;
                }
            }

        }
        catch (Exception $e)
        {
            /**
             * if there's an exception, return array
             */
            return $array;
        }
        
        /**
         * return the array
         */
        return $array;
    }
        
    /**
     * Retrieve the events from the API, will always return an array, even if empty
     *
     * @since 1.0.0
     * 
     * @param string $type - type of event to filter by, optional
     * @return array
     */
    public static function getEvents($type = null)
    {
        $data = [];
        /**
         * confirm the connection, otherwise return false
         */
        $connection = static::connect();
        
        if (!$connection)
        {
            return $data;
        }
        
        /**
         * create the request
         */
        $request = new WP_Http();
        
        $url = $connection->url . 'events/get?property=' . $connection->property_key;
        
        /**
         * if the type is passed, add to the url
         */
        if ($type)
        {
            $url .= '&type=' . $type;
        }
        
        $args = static::connectionArgs($connection);
            
        try 
        {
            /**
             * run the request
             */    
            $response = $request->request($url, $args);
            if (!is_wp_error($response))
            {
                /**
                 * if there is a response, confirm it contains a body
                 * if not, return false
                 */
                if (is_array($response) && isset($response['body']))
                {   
                    $content = (string) $response['body'];
                    $json = json_decode($content);
                }
                else
                {
                    return $data;
                }
                
                /**
                 * if it's JSON and the events property is set, set the data variable to the events value
                 */
                if ($json && isset($json->success) && $json->success && isset($json->events))
                {
                    $data = $json->events;
                }
        
                return $data;
            }

            /**
             * return empty array if request fails
             */
            return $data;
        }
        catch (Exception $e)
        {
            // pass ...
        }

        /**
         * return empty array if all else fails
         */
        return $data;
    }
    
    /**
     * Get the event types from the API
     *
     * @since 1.0.0
     * 
     * @return array
     */
    public static function getEventTypes()
    {
        $data = [];
        
        /**
         * create the connection, return the empty array if it fails
         */
        $connection = static::connect(false);
        
        if (!$connection)
        {
            return $data;
        }
        
        /**
         * create the request
         */
        $request = new WP_Http();
        
        $url = $connection->url . 'events/types';
        
        $args = static::connectionArgs($connection);
            
        try 
        {    
            /**
             * run the request
             */
            $response = $request->request($url, $args);
            if (!is_wp_error($response))
            {
                /**
                 * if there is a response, confirm it contains a body
                 * if not, return empty data
                 */
                if (is_array($response) && isset($response['body']))
                {   
                    $content = (string) $response['body'];
                    $json = json_decode($content);
                }
                else
                {
                    return $data;
                }
                
                /**
                 * if JSON is valid, set the data variable to the JSON
                 */
                if ($json && is_array($json))
                {
                    $data = $json;
                }
        
                return $data;
            }

            /**
             * return array object if request fails
             */
            return $data;
        }
        catch (Exception $e)
        {
            /**
             * if there's an exception with the request, return the empty array
             */
            return $data;
        }

        /**
         * if all else fails, return the empty array
         */
        return $data;
    }
    
    /**
     * Get an array of properties that can be displayed to the user in a select
     * the value will be the property key
     * the name will be a concatenation of the property name and domain
     * 
     * @since 1.0.0
     *
     * @return array
     */
    public static function getPropertiesForSelect()
    {
        $array = [];
        
        /**
         * get the properties
         */
        $properties = static::getProperties();
        
        /**
         * if the properties variable is an array, loop over it
         */
        if ($properties && is_array($properties))
        {
            /**
             * loop over properties and assemble an array with value as the key 
             * and name as the name and domain from the object
             */
            foreach ($properties as $property)
            {
                $array[] = [
                    'value' => $property->key,
                    'name'=> $property->name . ' (' . $property->domain . ')',
                ];
            }
        }
        
        /**
         * return the array
         */
        return $array;
    }
    
    /**
     * Retrieve the properties from the API
     * 
     * @since 1.0.0
     * 
     * @return array
     */
    public static function getProperties()
    {
        $data = [];
        
        /**
         * check the connetion, if it fails, return the empty array
         * NOTE: property key is not needed for this request
         */
        $connection = static::connect(false);
        if (!$connection)
        {
            return $data;
        }
        
        /**
         * create the request
         */
        $request = new WP_Http();
        
        $url = $connection->url . 'properties';
        
        $args = static::connectionArgs($connection);
        
        try 
        {
            /**
             * run the request
             */
            $response = $request->request($url, $args);
            if (!is_wp_error($response))
            {
                /**
                 * if there is a response, confirm it contains a body
                 * if not, return false
                 */
                if (is_array($response) && isset($response['body']))
                {   
                    $content = (string) $response['body'];
                    $json = json_decode($content);
                }
                else
                {
                    return $data;
                }
                
                /**
                 * if the JSON is valid data, set the data variable to the JSON
                 */
                if ($json)
                {
                    $data = $json;
                }
                
                return $data;
            }

            /**
             * return empty array if request fails
             */
            return $data;
        }
        catch (Exception $e)
        {
            /**
             * if there's an exception, return the empty array
             */
            return $data;
        }
        
        /**
         * if all else fails, return the empty array
         */
        return $data;
    }

    /**
     * Create a new event, segment, or visitor type via the API
     * Both require/accept the same exact parameters and return the same response format (success and key)
     *
     * @since 1.0.0
     * 
     * @param string $name - name of the event/segment/visitor_type
     * @param string $type - type of the event/segment, not necessary for visitor_type
     * @param string $submitType - either event, segment or visitor type to determine the API endpoint
     * @param array $swaps - for adding segments, adds an array of content swaps
     * 
     * @return boolean
     */
    public function addNewItem($name, $type, $submitType = 'events', $swaps = [], $returnKey = false)
    {
        /**
         * check if the connection exists
         */
        $connection = static::connect();
        
        if (!$connection)
        {
            return false;
        }
        
        /**
         * create the request
         */
        $request = new WP_Http();
        
        /**
         * check if the submitType passed is one of the available options
         */
        $availableSubmitTypes = [
            'events',
            'segments',
            'visitor_types',
        ];
        
        if (!in_array($submitType, $availableSubmitTypes))
        {
            return false;
        }
        
        /**
         * the submitType variable matches the API endpoint URL path
         */
        $url = $connection->url . $submitType .'/create';
        
        /**
         * build the object to pass
         */
        $content = new stdClass();
        
        $content->property = $connection->property_key;
        $content->type = $type;
        $content->name = $name;
        
        
        /**
         * this is adding swaps to the request
         * it is only used when adding segments
         */
        if ($swaps)
        {
            $content->swaps = $swaps;
        }
        
        /**
         * use the swaps array as the determination if the event is "terminal" or not
         */
        if ($submitType == 'events' && count($swaps))
        {
            $content->terminal = true;
        }
        
        $args = static::connectionArgs($connection);
        $args['method'] = 'POST';
        $args['body'] = json_encode($content);
        
        try 
        {
            /**
             * send the request
             */   
            $response = $request->request($url, $args);
            if (!is_wp_error($response))
            {
                
                /**
                 * if there is a response, confirm it contains a body
                 * if not, return false
                 */
                if (is_array($response) && isset($response['body']))
                {   
                    $content = (string) $response['body'];
                    $json = json_decode($content);
                }
                else
                {
                    return false;
                }
                
                /**
                 * if the JSON object exists and success is true, the event was created, 
                 * so, return true
                 */
                if ($json && isset($json->success) && $json->success)
                {
                    if (isset($json->key) && $returnKey)
                    {
                        return $json->key;
                    }
                    
                    return true;
                }
            }
            
            /**
             * otherwise, return false
             */
            return false;
        }
        catch (Exception $e)
        {
            /**
             * if there's exception, return false
             */
            return false;
        }
        
        /**
         * for all else, return false
         */
        return false;
    }
    
    public function deleteSwap($key)
    {
        /**
         * check if the connection exists
         */
        $connection = static::connect();
        
        if (!$connection)
        {
            return false;
        }
        
        /**
         * create the request
         */
        $request = new WP_Http();
        
        /**
         * check if the submitType passed is one of the available options
         */
        
        /**
         * the submitType variable matches the API endpoint URL path
         */
        $url = $connection->url . 'swap/delete';
        
        /**
         * build the object to pass
         */
        $content = new stdClass();
        
        $content->property = $connection->property_key;
        $content->swap = $key;
        
        $args = static::connectionArgs($connection);
        $args['method'] = 'POST';
        $args['body'] = json_encode($content);
        
        try 
        {
            /**
             * send the request
             */   
            $response = $request->request($url, $args);
            if (!is_wp_error($response))
            {
                /**
                 * if there is a response, confirm it contains a body
                 * if not, return false
                 */
                if (is_array($response) && isset($response['body']))
                {   
                    $content = (string) $response['body'];
                    $json = json_decode($content);
                }
                else
                {
                    return false;
                }
                
                /**
                 * if the JSON object exists and success is true, the event was created, 
                 * so, return true
                 */
                if ($json && isset($json->success) && $json->success)
                {
                    return true;
                }
            }
            
            /**
             * otherwise, return false
             */
            return false;
        }
        catch (Exception $e)
        {
            /**
             * if there's exception, return false
             */
            return false;
        }
        
        /**
         * for all else, return false
         */
        return false;
    }
    
    /**
     * Add new swaps to an existing segment
     *
     * @param string $segmentKey - the segment key
     * @param array $swaps - an array of swaps
     * @param bool $returnKeys - whether or not to return an array of keys if successful
     * 
     * @return boolean
     */
    public function addNewSwaps($segmentKey, $swaps, $returnKeys = false)
    {
        /**
         * check if the connection exists
         */
        $connection = static::connect();
        
        if (!$connection)
        {
            return false;
        }
        
        /**
         * confirm the data is passed
         */
        if (!$segmentKey || !$swaps)
        {
            return false;
        }
        
        /**
         * create the request
         */
        $request = new WP_Http();
        
        /**
         * the submitType variable matches the API endpoint URL path
         */
        $url = $connection->url . 'swap/create';
        
        /**
         * build the object to pass
         */
        $content = new stdClass();
        
        $content->property = $connection->property_key;
        $content->segment = $segmentKey;
        $content->swaps = $swaps;
                
        $args = static::connectionArgs($connection);
        $args['method'] = 'POST';
        $args['body'] = json_encode($content);

        try 
        {
            /**
             * send the request
             */   
            $response = $request->request($url, $args);
            if (!is_wp_error($response))
            {
                
                /**
                 * if there is a response, confirm it contains a body
                 * if not, return false
                 */
                if (is_array($response) && isset($response['body']))
                {   
                    $content = (string) $response['body'];
                    $json = json_decode($content);
                }
                else
                {
                    return false;
                }
                
                /**
                 * if the JSON object exists and success is true, the event was created, 
                 * so, return true
                 */
                if ($json && isset($json->success) && $json->success)
                {
                    if (isset($json->keys) && $returnKeys)
                    {
                        return $json->keys;
                    }
                    
                    return true;
                }
            }

            /**
             * otherwise, return false
             */
            return false;
        }
        catch (Exception $e)
        {
            /**
             * if there's exception, return false
             */
            return false;
        }
        
        /**
         * for all else, return false
         */
        return false;
    }
    
    /**
     * Send updated Swap data to Swaptify
     *
     * @since 1.0.0
     * 
     * @param array $swapArray
     * @param boolean $returnMessages - whether or not to return a response message
     * 
     * @return string|boolean
     */
    public static function updateSwapsByKey($swapArray, $returnMessages = false)
    {
        /**
         * check if the connection exists
         */
        $connection = static::connect();
        
        if (!$connection)
        {
            return false;
        }
        
        /**
         * confirm the data is passed
         */
        if (!is_array($swapArray))
        {
            return false;
        }
        
        /**
         * create the request
         */
        $request = new WP_Http();
        
        /**
         * the submitType variable matches the API endpoint URL path
         */
        $url = $connection->url . 'swaps/edit/';
        
        /**
         * build the object to pass
         */
        $content = new stdClass();
        
        $content->property = $connection->property_key;
        $content->update_all = true;
        $content->swaps = $swapArray;
                
        $args = static::connectionArgs($connection);
        $args['method'] = 'PUT';
        $args['body'] = json_encode($content);
        
        try 
        {
            /**
             * send the request
             */   
            $response = $request->request($url, $args);
            if (!is_wp_error($response))
            {
                
                /**
                 * if there is a response, confirm it contains a body
                 * if not, return false
                 */
                if (is_array($response) && isset($response['body']))
                {   
                    $content = (string) $response['body'];
                    $json = json_decode($content);
                }
                else
                {
                    return false;
                }
                
                /**
                 * if the JSON object exists and success is true, the event was created, 
                 * so, return true
                 */
                if ($json && isset($json->success) && $json->success)
                {
                    if ($returnMessages)
                    {
                        $messages = [
                            'success' => true,
                            'messages' => isset($json->messages) ? $json->messages : [],    
                            'errors' => isset($json->errors) ? $json->errors : [],    
                        ];
                        
                        return $messages;
                    }
                    
                    return true;
                }
            }

            /**
             * otherwise, return false
             */
            return false;
        }
        catch (Exception $e)
        {
            /**
             * if there's exception, return false
             */
            return false;
        }
        
        /**
         * for all else, return false
         */
        return false;
    }
    
    public static function createNewSwaps($swapArray, $returnMessages = false)
    {
        /**
         * check if the connection exists
         */
        $connection = static::connect();
        
        if (!$connection)
        {
            echo('1');
            return false;
        }
        
        /**
         * confirm the data is passed
         */
        if (!is_array($swapArray))
        {
            echo('2');
            return false;
        }
        
        /**
         * create the request
         */
        $request = new WP_Http();
        
        /**
         * the submitType variable matches the API endpoint URL path
         */
        $url = $connection->url . 'swaps/create/';
        
        /**
         * build the object to pass
         */
        $content = new stdClass();
        
        $content->property = $connection->property_key;
        $content->swaps = $swapArray;
                
        $args = static::connectionArgs($connection);
        $args['method'] = 'POST';
        $args['body'] = json_encode($content);
        
        try 
        {
            /**
             * send the request
             */   
            $response = $request->request($url, $args);
            if (!is_wp_error($response))
            {
                
                /**
                 * if there is a response, confirm it contains a body
                 * if not, return false
                 */
                if (is_array($response) && isset($response['body']))
                {   
                    $content = (string) $response['body'];
                    $json = json_decode($content);
                }
                else
                {
                    echo('3');
                    return false;
                }
                
                /**
                 * if the JSON object exists and success is true, the event was created, 
                 * so, return true
                 */
                if ($json && isset($json->success) && $json->success)
                {
                    if ($returnMessages)
                    {
                        $messages = [
                            'success' => true,
                            'messages' => isset($json->messages) ? $json->messages : [],    
                            'errors' => isset($json->errors) ? $json->errors : [],    
                        ];
                        
                        return $messages;
                    }
                    
                    return true;
                }
            }

            /**
             * otherwise, return false
             */
            echo('4');
            return false;
        }
        catch (Exception $e)
        {
            /**
             * if there's exception, return false
             */
            echo('5');
            return false;
        }
        
        /**
         * for all else, return false
         */
        echo('6');
        return false;
    }
    
    /**
     * Parse the post content looking for Swaptify shortcodes and page view event settings
     * Save the resulting segment keys and page view events to the database for the post
     *
     * @since 1.0.0
     * 
     * @param int $post_ID
     * @param object $post_after
     * @param object $post_before
     * 
     * @return void
     */
    public function setPostSwapContentValues($post_ID, $post_after, $post_before)
    {
        /**
         * look for Swaptify shortcodes in the post_after content
         */
        static::saveSegmentKeys($post_ID, $post_after->post_content);
    }
    
    /**
     * Save the Segment keys for a given post in the WordPress database
     *
     * @since 1.0.0
     * 
     * @param int|string $post_ID
     * @param string $content
     * @param array $additional_keys
     * 
     * @return void
     */
    public static function saveSegmentKeys($post_ID, $content = '', $additional_keys = [])
    {
        global $wpdb;
         
        /**
         * the pattern to match shortcodes for:
         *      swap_segment
         *      swap_segment_image
         *      swap_segment_url
         * 
         * Will get the key attribute that is the segment key
         */
        $pattern = '/(\[swap_segment)(_image|_url)*(\s.*key=")+([\-a-zA-Z0-9]+)("\.*)/';

        preg_match_all($pattern, $content, $keys);
        
        /**
         * get segment keys from the regex match
         */
        $segments = isset($keys[4]) ? $keys[4] : [];
        if ($additional_keys)
        {
            $segments = array_merge($segments, $additional_keys);
        }
        
        /**
         * remove any duplicates, only need to know which segment keys are contained anywhere in the post
         */
        $segments = array_unique($segments);
        
        /**
         * get revision id
         */
        $revisions = wp_get_post_revisions($post_ID);
        
        $last_id = $post_ID;
        if ($revisions)
        {
            $last_id = max(array_keys($revisions));
        }

        /**
         * purge existing post segments
         */
        $wpdb->delete(
            $wpdb->prefix . 'post_swap_segments',
            [
                'wp_post_id' => $post_ID,
            ]
        );
        
        /**
         * purge current revision existing post segments
         */
        $wpdb->delete(
            $wpdb->prefix . 'post_swap_segments',
            [
                'wp_post_id' => $last_id,
            ]
        );
        
        /**
         * add segments to table, for both post and revision
         */
        foreach ($segments as $segment)
        {
            $wpdb->insert( 
                $wpdb->prefix . 'post_swap_segments', 
                [ 
                    'wp_post_id' => $post_ID,
                    'swap_segment_key' => $segment,
                ]
            );
            
            $wpdb->insert( 
                $wpdb->prefix . 'post_swap_segments', 
                [ 
                    'wp_post_id' => $last_id,
                    'swap_segment_key' => $segment,
                ]
            );
        }

        /**
         * purge events for post and revision
         */
        $wpdb->delete(
            $wpdb->prefix . 'post_swap_events',
            [
                'wp_post_id' => $post_ID,
            ]
        );
        
        $wpdb->delete(
            $wpdb->prefix . 'post_swap_events',
            [
                'wp_post_id' => $last_id,
            ]
        );

        /**
         * if events are passed, add them to the post and revision
         */
        if (isset($_POST['swap_events']))
        {
            /**
             * confirm swap_events is an array
             */
            $events = Swaptify::getVariable($_POST, 'swap_events',[]);
            
            if (!is_array($events))
            {
                $events = [$events];
            }
            
            /**
             * loop over all the events, should be a event key, 
             * insert into swap_events table for both post and revision 
             */
            foreach ($events as $event)
            {
                $wpdb->insert( 
                    $wpdb->prefix . 'post_swap_events', 
                    [ 
                        'wp_post_id' => $post_ID,
                        'swaptify_event_key' => $event,
                    ]
                );
                
                $wpdb->insert( 
                    $wpdb->prefix . 'post_swap_events', 
                    [ 
                        'wp_post_id' => $last_id,
                        'swaptify_event_key' => $event,
                    ]
                );
            }
        }
        
        /**
         * purge visitor_type for post and revision
         */
        $wpdb->delete(
            $wpdb->prefix . 'post_swap_visitor_types',
            [
                'wp_post_id' => $post_ID,
            ]
        );
        
        $wpdb->delete(
            $wpdb->prefix . 'post_swap_visitor_types',
            [
                'wp_post_id' => $last_id,
            ]
        );

        /**
         * if visitor_type is passed, add them to the post and revision
         */
        if (isset($_POST['swap_visitor_type']))
        {
            $visitor_type = Swaptify::getVariable($_POST, 'swap_visitor_type', null);
            
            if ($visitor_type)
            {
                $wpdb->insert( 
                    $wpdb->prefix . 'post_swap_visitor_types', 
                    [ 
                        'wp_post_id' => $post_ID,
                        'swaptify_visitor_type_key' => $visitor_type,
                    ]
                );
                
                $wpdb->insert( 
                    $wpdb->prefix . 'post_swap_visitor_types', 
                    [ 
                        'wp_post_id' => $last_id,
                        'swaptify_visitor_type_key' => $visitor_type,
                    ]
                );
            }
        }
        
        /**
         * update the default content after saving segment keys
         */
        $swaptify = new Swaptify();
        $connect = $swaptify::connect();

        if ($connect)
        {
            $swaptify->updateDefaultContent();
        }
    }
    
    /**
     * Update the 'active' flag on saved Segments
     * Information is passed along to Swaptify API requests to determine if a Segment is meant to have Swaps
     *
     * @since 1.0.0
     * 
     * @param int|string $post_ID
     * @param array $keys
     * 
     * @return void
     */
    public static function setActiveSegments($post_ID, $keys = [])
    {
        global $wpdb;
        
        /**
         * get revision id
         */
        $revisions = wp_get_post_revisions($post_ID);
        
        $last_id = $post_ID;
        if ($revisions)
        {
            $last_id = max(array_keys($revisions));
        }
        
        foreach ($keys as $key => $active)
        {
            $query = $wpdb->prepare(
                "UPDATE 
                    {$wpdb->prefix}post_swap_segments         
                SET 
                    `active` = %d
                WHERE
                    swap_segment_key = %s
                    AND (wp_post_id = %d OR wp_post_id = %d) 
                ",
                [
                    $active,
                    $key,
                    $post_ID,
                    $last_id,
                ]
            );
            
            $wpdb->query($query);
        }
    }
    
    /**
     * 
     * TINYMCE FUNCTIONS
     * 
     */

    /**
     * Get the available segments for rendering inside the TinyMCE editor
     * Will echo a JSON string
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function TinyMCESwapContent() 
    {
        /**
         * get the available Segments from the API
         */
        $data = static::getSegments();
        
        foreach ($data->segments as $segment)
        {
            /**
             * escape the content and subcontent properties for HTML
             */
            $segment->content = htmlentities($segment->content);
            $segment->sub_content = htmlentities(($segment->sub_content ?? ''));
        }
        
        /**
         * set the JSON variable with segments as a property
         */
        $json = [
            'segments' => $data->segments,
        ];

        /**
         * echo the JSON
         */
        echo(json_encode($json));
        
        /**
         * exit the script
         * this is required to terminate immediately and return a proper response
         */
        wp_die();
    }
    
    /**
     * Get the available segments for rendering inside the TinyMCE editor
     * Will echo a JSON string
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function TinyMCESegmentTypes() 
    {
        /**
         * get the  segment types from the API
         */
        $types = static::getSegmentTypes();
        
        /**
         * set the JSON variable with segments as a property
         */
        $json = [
            'types' => $types,
        ];

        /**
         * echo the JSON
         */
        echo(json_encode($json));
        
        /**
         * exit the script
         * this is required to terminate immediately and return a proper response
         */
        wp_die();
    }
    
    /**
     * Add the swap button to the tinyMCE editor buttons
     * called in includes\js\tinymce-plugin.js
     *
     * @since 1.0.0
     * 
     * @param array $buttons
     * 
     * @return array
     */
    public function swap_register_buttons($buttons) 
    {
        array_push($buttons, 'separator', 'swaptify');
        return $buttons;
    }
    
    /**
     * Add the TinyMCE plugin js
     *
     * @since 1.0.0
     * 
     * @param array $plugin_array
     * 
     * @return array
     */
    public function swap_register_tinymce_javascript($plugin_array) 
    {
        
        $plugin_array['swaptify'] = plugins_url('/js/tinymce-plugin.js',__FILE__ );
        return $plugin_array;
    }
    
    /**
     * USER FRONTEND FUNCTIONS
     */
    
    /**
     * get the base user data from the $_SERVER global
     *
     * @since 1.0.0
     * 
     * @return array
     */
    public static function userData($pageUrl = null)
    {
        $userData = [
                'referrer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null,
                'ip' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'timezone' => 'America/Detroit',
            ];
            
            if ($pageUrl)
            {
                $userData['page_url'] = $pageUrl;
            }
        
            /**
             * @todo START HERE:
             *      get the cookie values from the db
             *      check if the value is set
             *      create an object for the cookie
             *      add it to the appropriate array
             */
        $userData['cookies'] = [];
        
        global $wpdb;
        
        /**
         * get all the cookies settings from the database
         */
        $query = $wpdb->prepare(
            "SELECT 
                `name`
            FROM 
                {$wpdb->prefix}swap_cookies         
            WHERE
                1 = %d 
            ",
            [
                1,
            ]
        );

        $cookies = $wpdb->get_results($query, 'OBJECT');

        /**
         * loop over all the results and check if the value is set for the given object. If it is, add it to the array
         */
        foreach ($cookies as $cookie)
        {
            if (isset($_COOKIE[$cookie->name]))
            {
                $object = new stdClass();
                $object->{$cookie->name} = $_COOKIE[$cookie->name];
                
                $userData['cookies'][] = $object;            
            }
        }       
            
        return $userData;
    }
    
    /**
     * Get the Segment and Event keys for a given post
     *
     * @since 1.0.0
     * 
     * @return array
     *          => data[]
     *              => segments[]
     *          => events[]
     */
    public static function getKeys($id = null)
    {
        global $wpdb;
        global $post;
        
        if (!$id)
        {   
            $id = get_the_ID();
        }
        
        /**
         * if there isn't an ID, return an "empty" array, with the same structure expected
         */
        if (!$id)
        {
            return [
                'data' => [
                    'segments' => [],
                    'segments_active' => [],
                ],
                'events' => [],
            ];
        }
        
        /**
         * create a query to get the segment keys associated with a post
         */
        $query = $wpdb->prepare(
            "SELECT 
                swap_segment_key AS `key`,
                `active` AS `active`
            FROM 
                {$wpdb->prefix}post_swap_segments         
            WHERE
                wp_post_id = %d 
            ",
            [
                $id,
            ]
        );
        
        /**
         * run the query
         */
        $segment_keys = $wpdb->get_results($query, 'OBJECT');
        
        /**
         * create a query to get the event keys(page view events) associated with a post
         */
        $query = $wpdb->prepare(
            "SELECT 
                swaptify_event_key AS `key`
            FROM 
                {$wpdb->prefix}post_swap_events       
            WHERE
                wp_post_id = %d 
            ",
            [
                $id,
            ]
        );

        /**
         * run the query
         */
        $event_keys = $wpdb->get_results($query, 'OBJECT');
        
        /**
         * create a query to get the visitor_type keys associated with a post
         */
        $query = $wpdb->prepare(
            "SELECT 
                swaptify_visitor_type_key AS `key`
            FROM 
                {$wpdb->prefix}post_swap_visitor_types       
            WHERE
                wp_post_id = %d 
            ",
            [
                $id,
            ]
        );

        /**
         * run the query
         */
        $visitor_type_keys = $wpdb->get_results($query, 'OBJECT');

        /**
         * create the empty arrays for event and segment keys
         */
        $segments = [];
        $visitor_types = [];
        $default_only = [];
        $events = [];

        /**
         * loop over each segment from the query and add the key to the array
         */
        foreach ($segment_keys as $segment_key)
        {
            $segments[] = $segment_key->key;
            
            if (!$segment_key->active)
            {
                $default_only[] = $segment_key->key;
            }
        }
        
        /**
         * loop over each event from the query and add the key to the array
         */
        foreach ($event_keys as $event_key)
        {
            $events[] = $event_key->key;
        }
        
        /**
         * loop over each event from the query and add the key to the array
         */
        foreach ($visitor_type_keys as $visitor_type_key)
        {
            $visitor_types[] = $visitor_type_key->key;
        }

        /**
         * return the expected data structure with the key arrays
         */
        return [
            'data' => [
                'segments' => $segments,
                'default_only' => $default_only,
            ],
            'events' => $events,
            'types' => $visitor_types,
        ];
    }
    
    /**
     * Add post id hidden input to the page
     * used to grab information on the front end and pass it to the backend
     *
     * @since 1.0.0
     * 
     * @return string
     */
    public static function addPostIdInput()
    {
        $id = get_the_ID();
        
        $div = '<input type="hidden" id="swaptify_id" value="' . $id . '" />';
        
        $pagesajax = 'wp-json/wp/v2/pages';
        $postsajax = 'wp-json/wp/v2/posts';
        /**
         *  for all json requests? 
         */
        $postsajax = 'wp-json/wp';
        
        $is_ajax_request = false;
        
        if (!empty($_SERVER['REQUEST_URI']))
        {
            $is_ajax_request = (strpos($_SERVER['REQUEST_URI'], $pagesajax) !== false);
            
            if (!$is_ajax_request)
            {
                $is_ajax_request = (strpos($_SERVER['REQUEST_URI'], $postsajax) !== false);
            }
        }
        
        if ($is_ajax_request)
        {
            // do nothing
        }
        else if (
            
            !is_admin()
            
        )
        {
            echo($div);
        }
        
        return $div;
    }

    /**
     * Get the swaps (display data) from the API for the current visitor session and page
     * This will record the visitor page view on Swaptify as well as record which content was shown
     *
     * @since 1.0.0
     * 
     * @return object|boolean
     */
    public static function get($id = null, $pageUrl = null)
    {
        
        /**
         * if the request to get swaps has already been made, do not resubmit to the API
         */
        if (static::$current_swaps !== null)
        {
            return static::$current_swaps;
        }
        
        /**
         * check if the connection is setup, if not, return false
         */
        $connection = static::connect();
        
        if (!$connection)
        {
            return false;
        }
        
        /**
         * set the key_data array. This is what is ultimately returned
         */
        $key_data = [];
        
        
        $visitor_types = [];
        
        /**
         * get the page keys
         * this will be used whether or not the plugin is enabled
         */
        $keys = static::getKeys($id);
        
        /**
         * check if the plugin is disabled, if so, return the default content
         */
        if (!static::enabled())
        {    
            $key_data = static::getDefaultContent($keys);
            return $key_data;
        }
        
        /**
         * build the request
         */
        $request = new WP_Http();
        
        $url = $connection->url . 'swaps/get';
        
        /**
         * build the request data
         */
        $post = static::connectionArgs($connection);
        $visit_array = [
            'property' => $connection->property_key,
            'visitor_key' => static::visitorCookie(),
            'verified' => true,
            'user_data' => static::userData($pageUrl)
        ];
        
        /**
         * merge the visit data and key data to put in the request body
         */
        $swap_array = array_merge($visit_array, $keys);

        $post['body'] = json_encode($swap_array);
        try 
        {
            
            /**
             * run the request
             */    
            $response = $request->post($url, $post);
            
            if (!is_wp_error($response))
            {
                $content = (string) $response['body'];
                $json = json_decode($content);
                
                /**
                 * if there is a response, confirm it contains a body
                 * if not, set the json variable to false
                 */
                if (is_array($response) && isset($response['body']))
                {   
                    $content = (string) $response['body'];
                    $json = json_decode($content);
                }
                else
                {
                    $json = false;
                }
                
                /**
                 * set visitor data
                 */
                if ($json) {
                    static::setVisitorData($json);
                }
                
                if ($json && isset($json->success) && $json->success && isset($json->visitor) && isset($json->visitor->visitor_types))
                {
                    $visitor_types = $json->visitor->visitor_types;
                }
                /**
                 * confirm the json variable is set and contains the required data
                 * otherwise set the key_data to the default content
                 */
                
                if ($json && isset($json->success) && $json->success && isset($json->data) && isset($json->subdata))
                {
                    /**
                     * if all the conditions are met, set the key_data based on the response data
                     * this will assemble an array of data and subdata for each key returned
                     */
                    foreach ($json->data as $key => $data)
                    {
                        $key_data['data'][$key] = $data;
                        $key_data['subdata'][$key] = $json->subdata->$key;
                        $key_data['keys'][$key] = $json->keys->$key;
                        $key_data['types'][$key] = $json->segment_types->$key;
                    }
                }
                else
                {
                    /**
                     * if the expected data is not in the response, 
                     * set the key_data to the default content based on the keys
                     */
                    $key_data = static::getDefaultContent($keys);
                }
            }
            else
            {
                /**
                 * if the request fails, return the default content for the keys
                 */
                $key_data = static::getDefaultContent($keys);
            }
        
        }
        catch (Exception $e)
        {
            /**
             * if there's an exception, get the default content for the keys
             */
            $key_data = static::getDefaultContent($keys);
        }

        static::$current_swaps = $key_data;
        
        /**
         * return the key_data
         */
        $object = new stdClass();
        $object->swaps = $key_data;
        $object->visitor_types = $visitor_types;
        
        return $object;
    }
    
    /**
     * Take the JSON object and if it contains the visitor property,
     * set the cookie to the visitor key
     * 
     * NOTE: this cookie will be used to match the visitor to subsequent visits
     *
     * @since 1.0.0
     * 
     * @param object $json
     * 
     * @return void
     */
    public static function setVisitorData($json)
    {
        if (isset($json->visitor))
        {
            setcookie(static::$cookieName, $json->visitor->visitor_key, time()+60*60*24*31, '/');
            $_COOKIE[static::$cookieName] = $json->visitor->visitor_key;
        }
    }
    
    /**
     * 
     * SHORT CODE RENDER FUNCTIONS
     * 
     */

    /**
     * Get the stored content from the swaps or sub_swaps array by key and type,
     * returns the value if key is present in the array
     *
     * @since 1.0.0
     * 
     * @param array $attrs
     * @param string $type - data or subdata. subdata returns sub_content values such as URL text or image title
     * 
     * @return string
     */
    public function renderSwap($attrs = [], $type = 'data')
    {
        /**
         * the stored data for swaps and sub_swaps
         */
        $swaps = static::$swaps;
        $sub_swaps = static::$sub_swaps;
        
        /**
         * confirm the key is passed in the attributes
         */
        $key = null;
        if (isset($attrs['key']))
        {
            $key = $attrs['key'];
        }
        
        /**
         * if the key is set and is a value, check the type
         */
        if ($key)
        {
            /**
             * if the type is subdata, return the value from the sub_swaps array for that key
             */
            if ($type == 'subdata')
            {    
                return isset($sub_swaps[$key]) ? $sub_swaps[$key] : '';
            }
            
            /**
             * otherwise, return the value from the swaps array for that key
             */
            return isset($swaps[$key]) ? $swaps[$key] : '';
        }
        
        /**
         * if all else fails, return an empty string
         */
        return '';	
    }

    /**
     * Renders swap content inside an img tag
     *
     * @since 1.0.0
     * 
     * @param array $attrs
     * 
     * @return void
     */
    public function renderImage($attrs = [])
    {
        //return '<div style="height:100px;width:100px;background:red;"></div>';
        /**
         * confirm the key is passed in the attrs array
         */
        if (isset($attrs['key']))
        {
            $key = $attrs['key'];
            
            $class = isset($attrs['class']) ? preg_replace(static::$class_pattern, '', $attrs['class']) : '';
            $id = isset($attrs['id']) ? preg_replace(static::$class_pattern, '', $attrs['id']) : '';
            
            $attributes = [];
            
            if ($class)
            {
                $attributes['class'] = $class;
            }
            
            if ($id)
            {
                $attributes['id'] = $id;
            }
            
            /**
             * @todo add default content to this method
             */
            
            $content = static::getDefaultContentForSegmentKeyPreview($key); 
            
            return Swaptify::renderHolderDiv($key, $attributes, 'image', $content);
            /**
             * set the content and sub_content based on the key
             */
            $content = $this->renderSwap(['key' => $key]);
            $title = $this->renderSwap(['key' => $key], 'subdata');
            
            /**
             * search for any class or id attributes, extracting the data
             */
            $class = isset($attrs['class']) ? preg_replace(static::$class_pattern, '', $attrs['class']) : '';
            $id = isset($attrs['id']) ? preg_replace(static::$class_pattern, '', $attrs['id']) : '';
            
            /**
             * if there is a value for the content variable,
             * render the img tag with available attributes
             */
            if ($content)
            {
                return '<img 
                            src="' . $content . '" 
                            title="' . htmlentities($title) . '" 
                            alt="' . htmlentities($title) . '" 
                            id="' . $id . '"
                            class="' . $class . '"
                        />';
            }
        }
    }

    /**
     * Render a URL swap inside an <a> tag 
     *
     * @since 1.0.0
     * 
     * @param array $attrs
     * 
     * @return void
     */
    public function renderUrl($attrs = [])
    {
        /**
         * confirm the key is passed in the attrs array
         */
        if (isset($attrs['key']))
        {
            $key = $attrs['key'];
            
            $key = $attrs['key'];
            
            $class = isset($attrs['class']) ? preg_replace(static::$class_pattern, '', $attrs['class']) : '';
            $id = isset($attrs['id']) ? preg_replace(static::$class_pattern, '', $attrs['id']) : '';
            
            $attributes = [];
            if ($class)
            {
                $attributes['class'] = $class;
            }
            
            if ($id)
            {
                $attributes['id'] = $id;
            }
            
            /**
             * @todo add default content to this method
             */
            
            $content = static::getDefaultContentForSegmentKeyPreview($key); 
            
            return Swaptify::renderHolderDiv($key, $attributes, 'url', $content);
            
            /**
             * set the content and sub_content based on the key
             * content will be the URL
             * text will be the text inside the anchor
             */
            $content = $this->renderSwap(['key' => $key]);
            $text = $this->renderSwap(['key' => $key], 'subdata');
            
            /**
             * allow override of link text by using 'name'
             */
            $text = (isset($attrs['text']) && trim($attrs['text']) != '') ? $attrs['text'] : $text;
            
            /**
             * search for any class or id attributes, extracting the data
             */
            $class = isset($attrs['class']) ? preg_replace(static::$class_pattern, '', $attrs['class']) : '';
            $id = isset($attrs['id']) ? preg_replace(static::$class_pattern, '', $attrs['id']) : '';
            
            /**
             * if the content is set, render the <a> tag
             */
            if ($content)
            {
                return '<a 
                            href="'.$content.'"
                            id="' . $id . '"
                            class="' . $class . '"
                        >
                        ' . $text . '</a>';
            }
        }
    }
    
    /**
     * Render text/HTML content. If a class or id is set on the swap, the content will be wrapped in a div tag
     * 
     * NOTE: this will also execute any shortcodes contained within the content
     *
     * @since 1.0.0
     * 
     * @param array $attrs
     * 
     * @return void
     */
    public function renderText($attrs = [])
    {
        /**
         * confirm the key is passed in the attrs array
         */
        if (isset($attrs['key']))
        {
            $key = $attrs['key'];
            
            $key = $attrs['key'];
            
            $class = isset($attrs['class']) ? preg_replace(static::$class_pattern, '', $attrs['class']) : '';
            $id = isset($attrs['id']) ? preg_replace(static::$class_pattern, '', $attrs['id']) : '';
            
            $attributes = [];
            if ($class)
            {
                $attributes['class'] = $class;
            }
            
            if ($id)
            {
                $attributes['id'] = $id;
            }
            
            /**
             * @todo add default content to this method
             */
            
            $content = static::getDefaultContentForSegmentKeyPreview($key, $id, $attributes); 
            
            return Swaptify::renderHolderDiv($key, $attributes, 'text', $content);
            
            /**
             * set the content based on the key
             */
            $content = $this->renderSwap(['key' => $key]);
            
            /**
             * search for any class or id attributes, extracting the data
             */
            $class = isset($attrs['class']) ? preg_replace(static::$class_pattern, '', $attrs['class']) : '';
            $id = isset($attrs['id']) ? preg_replace(static::$class_pattern, '', $attrs['id']) : '';
            
            /**
             * if content is present, render it
             */
            if ($content)
            {
                /**
                 * if there is a class or id, wrap the content in a div
                 */
                if ($class || $id)
                {
                    return '<div 
                            id="' . $id . '"
                            class="' . $class . '"
                        >
                        ' . @do_shortcode($content) . '</div>';
                }
                else
                {
                    return @do_shortcode($content);
                }
            }
        }
    }
    
    /**
     * create the WP_HTTP headers array with headers and timeout keys
     *
     * @since 1.0.0
     * 
     * @param object $connection
     * 
     * @return void
     */
    private static function connectionArgs($connection)
    {
        $args = [];
        /**
         * if the connection is not passed correctly, return empty array
         */
        if (!$connection)
        {
            return $args;
        }
        
        /**
         * set the request timeout
         */
        $args['timeout'] = $connection->request_timeout_seconds;
        
        /**
         * set the headers including the bearer_token
         */
        $args['headers'] = [
            'Content-Type' => 'application/json',
            'X-Requested-With' => '[{"key":"X-Requested-With","value":"XMLHttpRequest","description":""}]',
            'Authorization' => 'Bearer ' . $connection->bearer_token,
        ];
        
        return $args;
    }
    
    
    /**
     * Retrieve the visitor types from the API, will always return an array, even if empty
     *
     * @since 1.0.0
     * 
     * @return array
     */
    public static function getVisitorTypes()
    {
        $data = [];
        /**
         * confirm the connection, otherwise return false
         */
        $connection = static::connect();
        
        if (!$connection)
        {
            return $data;
        }
        
        /**
         * create the request
         */
        $request = new WP_Http();
        
        $url = $connection->url . 'visitor_types/get?property=' . $connection->property_key;
                
        $args = static::connectionArgs($connection);
            
        try 
        {
            /**
             * run the request
             */    
            $response = $request->request($url, $args);
            if (!is_wp_error($response))
            {
                /**
                 * if there is a response, confirm it contains a body
                 * if not, return false
                 */
                if (is_array($response) && isset($response['body']))
                {   
                    $content = (string) $response['body'];
                    $json = json_decode($content);
                }
                else
                {
                    return $data;
                }
                
                /**
                 * if it's JSON and the events property is set, set the data variable to the events value
                 */
                if ($json && isset($json->success) && $json->success && isset($json->visitor_types))
                {
                    $data = $json->visitor_types;
                }
        
                return $data;
            }

            /**
             * return empty array if request fails
             */
            return $data;
        }
        catch (Exception $e)
        {
            // pass ...
        }

        /**
         * return empty array if all else fails
         */
        return $data;
    }
    
    /**
     * Set the visitor type via the API
     * This is used as an endpoint for certain AJAX requests
     *
     * @since 1.0.0
     * 
     * @param string $key - the visitor type key
     * 
     * @return boolean
     */
    public static function setVisitorType($key)
    {
        /**
         * ensure the connection is set, if not, return false
         */
        $connection = static::connect();
        
        if (!$connection)
        {
            return false;
        }
        
        /**
         * check if plugin is disabled, if so, return false
         */
        if (!static::enabled())
        {
            return false;
        }
        
        /**
         * create the request
         */
        $request = new WP_Http();
        
        $url = $connection->url . 'visitor_types/set';
        
        $post = static::connectionArgs($connection);
        
        /**
         * build the post body
         * NOTE: this can only be one key per request, even though it's an array
         */
        $post['body'] = json_encode([
            'property' => $connection->property_key,
            'visitor_key' => static::visitorCookie(),
            'user_data' => static::userData(),
            'visitor_types' => [$key],
        ]);
        
        try 
        {
            $response = $request->post($url, $post);
            
            if (!is_wp_error($response))
            {
                /**
                 * if there is a response, confirm it contains a body
                 * if not, return false
                 */
                if (is_array($response) && isset($response['body']))
                {   
                    $content = (string) $response['body'];
                    $json = json_decode($content);
                }
                else
                {
                    return false;
                }
                
                /**
                 * if JSON is valid and success is true, visitor type is set! return true
                 */
                if ($json && isset($json->success) && $json->success)
                {
                    return $json;
                }
            }
        }
        catch (Exception $e)
        {
            // pass ...
        }
        
        /**
         * if the script gets all the way here, return false
         */
        return false;
    }
    
    /**
     * Record a event as being met via the API
     * 
     * @since 1.0.0
     * 
     * @param string $key - the key of the event
     * 
     * @return boolean
     */
    public static function setEventMet($key)
    {
        /**
         * ensure the connection is set, if not, return false
         */
        $connection = static::connect();
        
        if (!$connection)
        {
            return false;
        }
        
        /**
         * check if plugin is disabled, if so, return false
         */
        if (!static::enabled())
        {
            return false;
        }
        
        /**
         * create the request
         */
        $request = new WP_Http();
        
        $url = $connection->url . 'events/trigger';
        
        $post = static::connectionArgs($connection);
        
        /**
         * build the post body
         * NOTE: this can only be one key per request
         */
        $post['body'] = json_encode([
            'property' => $connection->property_key,
            'visitor_key' => static::visitorCookie(),
            'user_data' => static::userData(),
            'event' => $key,
        ]);
        
        try 
        {
            /**
             * run the request
             */
            $response = $request->post($url, $post);
            if (!is_wp_error($response))
            {
                /**
                 * if there is a response, confirm it contains a body
                 * if not, return false
                 */
                if (is_array($response) && isset($response['body']))
                {   
                    $content = (string) $response['body'];
                    $json = json_decode($content);
                }
                else
                {
                    return false;
                }
                
                /**
                 * if JSON is valid and success is true, event is set! return true
                 */
                if ($json && isset($json->success) && $json->success)
                {
                    return true;
                }
            }
        }
        catch (Exception $e)
        {
            // pass ...
        }
        
        /**
         * if the script gets all the way here, return false
         */
        return false;
    }
    
    /**
     * Retreive the visitor cookie value
     *
     * @since 1.0.0
     * 
     * @return string|null
     */
    public static function visitorCookie()
    {
        return isset($_COOKIE[static::$cookieName]) ? $_COOKIE[static::$cookieName] : null;
    }
    
    /**
     * Send Swap data to Swaptify for saving
     * 
     * @since 1.0.0
     *
     * @return void
     */
    public function update_swap_content()
    {
        $responseJson = [
            'success' => false,
            'message' => '',
            'errors' => [], // this will be for errors related to the content, will be an array
            'new_key' => null, // a new key for the swap will be generated upon success
        ];
        
        $segmentKey = Swaptify::getVariable($_POST, 'segmentKey');
        $contentKey = Swaptify::getVariable($_POST, 'contentKey');
        $contentName = Swaptify::getVariable($_POST, 'contentName');
        $contentData = Swaptify::getVariable($_POST, 'contentData');
        $contentSubdata = Swaptify::getVariable($_POST, 'contentSubdata');
        
        /**
         * if Swaptify is not enabled, return false and do nothing
         */
        if (!static::enabled())
        {
            $responseJson['message'] = 'Swaptify not enabled';
            echo(json_encode($responseJson));
            wp_die();
        }
        
        /**
         * check for the connection, if not set, return false and do nothing
         */
        $connection = static::connect();
        
        if (!$connection)
        {
            $responseJson['message'] = 'Swaptify not connected';
            echo(json_encode($responseJson));
            wp_die();
        }
        
        /**
         * create the request
         */
        $request = new WP_Http();
        $url = $connection->url . 'swap/edit';
        
        $put = static::connectionArgs($connection);
        
        $put['method'] = 'PUT';
        $put['body'] = json_encode([
                        'property' => $connection->property_key,
                        'segment' => $segmentKey,
                        'key' => $contentKey,
                        'name' => $contentName,
                        'content' => $contentData,
                        'sub_content' => $contentSubdata,
                        'publish' => true, // this could be changed to allow drafts, but not this version
                    ]);
        
                    
        /**
         * set the default error message
         */
        $responseJson['message'] = 'There was a problem updating the content';
        try 
        {
            /**
             * execute the request
             */
            $response = $request->request($url, $put);
            
            if (!is_wp_error($response))
            {
                /**
                 * if there is a response, confirm it contains a body
                 * if not, return false
                 */
                if (is_array($response) && isset($response['body']))
                {   
                    $content = (string) $response['body'];
                    $json = json_decode($content);
                }
                
                
                if ($json)
                {
                    /**
                     * if JSON is valid and success is true, set the key from the response
                     */   
                    if (isset($json->success) && $json->success && isset($json->key))
                    {
                        $responseJson['message'] = 'Updated Successfully';
                        $responseJson['success'] = true;
                        $responseJson['new_key'] = $json->key;
                    }
                    else
                    {
                        if (isset($json->message))
                        {
                            $responseJson['key'] = $json->key;
                        }
                        
                        if (isset($json->errors))
                        {
                            $responseJson['errors'] = $json->errors;
                        }
                    }
                }
            }
            
            /**
             * otherwise, return false
             */
        }
        catch (Exception $e)
        {
            // pass...
        }

        /**
         * echo the JSON
         */
        echo(json_encode($responseJson));
        
        /**
         * exit the script
         * this is required to terminate immediately and return a proper response
         */
        wp_die();
    }
    
    /**
     * Create an HTML div for a given Segment
     *
     * @since 1.0.0
     * 
     * @param string $segmentKey
     * @param array $attributes
     * @param string|null $type
     * @param string|null $swapContent
     * @param string|null $swapKey
     * 
     * @return void
     */
    public static function renderHolderDiv($segmentKey, $attributes = [], $type = null, $swapContent = null, $swapKey = null)
    {
        $div = '<div ';
        $classes = ['swaptify-render-segment'];
        
        $div .= 'data-swaptify_segment="' . $segmentKey . '" ';
        
        
        if ($type)
        {
            $div .= 'data-swaptify_type="' . $type . '" ';
            $classes[] = 'swap-type-' . $type;
        }
        
        if ($attributes)
        {
            foreach ($attributes as $name => $value)
            {
                if ($name == 'class')
                {
                    $classes[] = $value;
                }
                else
                {   
                    $div .= $name . '="' . $value . '" ';
                }
            }
        }
        
        $div .= 'class="' . implode(' ', $classes). '" ';
        
        if ($swapKey)
        {
            $div .= 'data-swaptify_swap="'.$segmentKey.'" ';
        }
        
        $div .= '>';
        
        if ($swapContent)
        {
            $div .= $swapContent;
        }
        
        $div .= '</div>';
    
        return $div;
    }
    
    public static function createArrayForRequestBodyForEditingSwaps($postData, $segmentKey = null, $isNew = false)
    {
        $nameKey = $isNew ? 'new_swap_name' : 'swap_name';
        $visitorTypeNameKey = $isNew ? 'new_visitor_type' : 'visitor_type';
        $contentKeyPrefix = $isNew ? 'new_content-' : 'content-'; 
        $subContentKey = $isNew ? 'new_sub_content' : 'sub_content';
        $publishKey = $isNew ? 'new_publish' : 'publish';
        $activeKey = $isNew ? 'new_active' : 'active';
        
        if (!isset($postData[$nameKey]))
        {
            return null;
        }
        
        $array = [];
        
        $defaultKey = Swaptify::getVariable($postData, 'default');
        
        $visitorTypes = Swaptify::getVariable($postData, $visitorTypeNameKey, []);
        
        foreach ($postData[$nameKey] as $swapKey => $name)
        {
            $swap = new stdClass();
            $swap->segment_key = $segmentKey;
            /**
             * if the action is not for new swaps, don't include the swap key as it is going to be for creating new ones
             */
            if (!$isNew)
            {
                $swap->swap_key = $swapKey;
            }
            $swap->name = Swaptify::getVariable($postData[$nameKey], $swapKey, null);
            $swap->content = Swaptify::getVariable($postData, $contentKeyPrefix . $swapKey, '');
            $swap->sub_content = Swaptify::getVariable($postData[$subContentKey], $swapKey, null);
            $swap->publish = Swaptify::getVariable($postData[$publishKey], $swapKey, false) ? true : false;
            $swap->active = Swaptify::getVariable($postData[$activeKey], $swapKey, false) ? true : false;
            
            $swap->default = ($swapKey == $defaultKey) ? true : false;
            
            $swap->visitor_types = [];
            foreach($visitorTypes as $visitorTypeKey => $visitorType)
            {
                if (isset($visitorType[$swapKey]))
                {
                    $swap->visitor_types[] = $visitorTypeKey;
                }
            }
            
            $array[] = $swap;
            
        }
        
        return $array;
    }
    
    public static function createRequestBodyForCreatingSwaps($postData)
    {
        $body = $postData;
        
        $json = json_encode($body);
        
        return $json;
    }
    
    public static function generateDisplayShortcode($type, $key)
    {
        $name = 'swap_segment';
        if ($type == 'image' || $type == 'url')
        {
            $name .= '_' . $type;
        }
        
        return '[' . $name . ' key="' . $key . '"]';
    }
    
    /**
     * Get a cleaned version of the $_POST/$_GET variable by key
     *
     * @param string $key - the key of the $_POST/$_GET variable
     * @param null|string|array $default - what will be returned if key is not set
     * 
     * @return string|array
     */
    public static function getVariable($array, $key, $default = null) 
    {
        $variable = isset($array[$key]) ? $array[$key] : $default;
        
        if (is_string($variable))
        {
            $variable = stripslashes($variable);
        }
        
        return $variable;
    }
    
}