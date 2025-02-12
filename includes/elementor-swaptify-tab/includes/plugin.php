<?php

namespace ElementorSwaptifyTab;

use Swaptify;
use Elementor\Controls_Manager;
use stdClass;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Plugin class for Elementor Swaptify Tab addon
 *
 * @since 1.0.0
 */
final class Plugin {

	/**
	 * Elementor Swaptify Tab Addon Version
	 *
	 * @since 1.0.0
	 * @var string The addon version.
	 */
	const VERSION = '1.0.0';

	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.0.0
	 * @var string Minimum Elementor version required to run the addon.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '3.15.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 * @var string Minimum PHP version required to run the addon.
	 */
	const MINIMUM_PHP_VERSION = '7.4';

	/**
	 * Instance
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 * @var \ElementorSwaptifyTab\Plugin The single instance of the class.
	 */
	private static $_instance = null;

    /**
     * Swaptify Connection instance, called to instantiate, then stored in variable
     *
     * @var null|false|stdClass 
     * @see Swaptify::connect()
     */
    public $swaptifyConnection = null;
    
    /**
     * Whether or note preview scripts have been loaded
     * used to avoid adding duplicates within different Elementor control methods
     *
     * @var boolean
     */
    public $previewScriptsLoaded = false;
    
    /**
     * Slug for Swaptify Elementor Tab
     *  
     * @var string
     */
    public const TAB_NAME = 'swaptify-tab';
    
    /**
     * Tab display name, shown in Elementor editor window
     * 
     * @var string
     */
    public const TAB_DISPLAY_NAME = 'Swaptify';
    
	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return \ElementorSwaptifyTab\Plugin An instance of the class.
	 */
	public static function instance() {

		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
        
		return self::$_instance;
	}

	/**
	 * Constructor
	 *
	 * Perform some compatibility checks to make sure basic requirements are meet.
	 * If all compatibility checks pass, initialize the functionality.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		if ($this->is_compatible()) {
			add_action('elementor/init', [$this, 'init']);
            
            if (!$this->swaptifyConnection)
            {
                $swaptify = new \Swaptify();
                $connect = $swaptify::connect();
                
                $this->swaptifyConnection = $connect;
            }
		}
	}

	/**
	 * Compatibility Checks
	 *
	 * Checks whether the site meets the addon requirement.
	 * 
     * NOTE: this is default code from addon template
     * 
	 * @since 1.0.0
	 * @access public
	 */
	public function is_compatible() {

		// Check if Elementor installed and activated
		if (!did_action( 'elementor/loaded')) {
			
            /**
             * don't include the warning because it is not required for full plugin usage
             */
            return false;
		}

		// Check for required Elementor version
		if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
			add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_version']);
			
            return false;
		}

		// Check for required PHP version
		if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
			add_action('admin_notices', [$this, 'admin_notice_minimum_php_version']);
			
            return false;
		}

		return true;
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
     * NOTE: this is default code from addon template
     * 
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {

		if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'elementor-swaptify-tab'),
			'<strong>' . esc_html__('Elementor Swaptify Tab', 'elementor-swaptify-tab') . '</strong>',
			'<strong>' . esc_html__('Elementor', 'elementor-swaptify-tab') . '</strong>'
		);

		printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
     * NOTE: this is default code from addon template
     * 
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {

		if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'elementor-swaptify-tab'),
			'<strong>' . esc_html__('Elementor Swaptify Tab', 'elementor-swaptify-tab') . '</strong>',
			'<strong>' . esc_html__('Elementor', 'elementor-swaptify-tab') . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);

		printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
     * 
     * NOTE: this is default code from addon template
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {

		if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'elementor-swaptify-tab'),
			'<strong>' . esc_html__('Elementor Swaptify Tabsies', 'elementor-swaptify-tab') . '</strong>',
			'<strong>' . esc_html__('PHP', 'elementor-swaptify-tab') . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
	}

	/**
	 * Initialize
	 *
	 * Load the addons functionality only after Elementor is initialized.
	 *
	 * Fired by `elementor/init` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {
        
        /** 
         * register swaptify controls for Segment and Swap add/edit functionality in Elementor editor
         */
        add_action('elementor/controls/controls_registered', [$this, 'register_controls']);

        /**
         * add hook for after saving a page/post
         */
        add_action('elementor/editor/after_save', [$this, 'after_save'], 10, 2);
        
        /**
         * add hook for processing Swaptify data prior to rendering the page/post
         */
        add_action('elementor/frontend/before_render', [$this, 'prerender_action']);
 
        /**
         * add Swaptify tab to Elementor editor
         */
        \Elementor\Controls_Manager::add_tab(
            self::TAB_NAME,
            __('Swaptify')
        );
        
        /**
         * add controls to Elementor editor
         */
        add_action('elementor/element/before_section_start', [$this, 'inject_custom_control'], 10, 3);
	}
    
    /**
     * add classes and data attributes to each element if they have the swaptify-swap setting data
     * this will add information used in rendering swaps on the front end
     * 
     *
     * @param array $elements - the Elementor elements array
     * @param string $segmentKey - the current Segment key
     * @return void
     */
    public function hide_elements($elements, $segmentKey)
    {
        /**
         * $elements must be an array in order to process
         */
        if ($elements && is_array($elements))
        {
            /**
             * loop over each element
             */
            foreach ($elements as $element)
            {
                /**
                 * get the type of element, will be either a Section or Container
                 */
                $type = $element->get_type();
                /**
                 * used for Section types to determine if the element is an Inner Section
                 */
                $isInner = $element->get_data()['isInner']; 
                
                /**
                 * if it's a column it is a section -> column -> innerSection type and you'll need to go one level deeper
                 */
                if ($type == 'column')
                {
                    /**
                     * run the same method on the children, which will be an Inner Section type
                     */ 
                    $this->hide_elements($element->get_children(), $segmentKey);
                }
                else if ($type == 'section' && $isInner)
                {
                    /**
                     * check if there is a Swap key in the settings
                     */
                    $swapKey = $element->get_settings('swaptify-swap');
                    if ($swapKey)
                    {   
                        /**
                         * if so, add classes and data attribute to the element
                         */
                        $class = 'swap-preview-hide swaptify-render-swap';
                        
                        $data = [
                            'class' => $class,
                        ];
                        
                        $data['data-swaptify_swap'] = $swapKey;
                        
                        $element->add_render_attribute(
                            '_wrapper',
                            $data
                        );
                    }
                }
                elseif ($type == 'container')
                {
                    /**
                     * check if there is a Swap key in the settings
                     */
                    $swapKey = $element->get_settings('swaptify-swap');
                    if ($swapKey)
                    {
                        /**
                         * if so, add classes and data attribute to the element
                         */
                        $class = 'swap-preview-hide swaptify-render-swap';
                        
                        $data = [
                            'class' => $class,
                        ];
                        
                        $data['data-swaptify_swap'] = $swapKey;
                        
                        $element->add_render_attribute(
                            '_wrapper',
                            $data
                        );
                    }
                }
            }
        }
    }
    
    /**
     * Make modifications/additions to the element data prior to rendering
     *
     * @param object $element
     * @return void
     */
    public function prerender_action($element)
    {
        /**
         * if view as a preview, load the preview_js, but make sure to only do it once
         */
        if (!$this->previewScriptsLoaded && is_preview())
        {
            require_once(__DIR__ . '/../assets/php/preview_js.php');
            $this->previewScriptsLoaded = true;
        }
        
        /**
         * if it's a Section
         */
        if ($element->get_type() == 'section')
        {
            $isInner = $element->get_data()['isInner']; 
            
            /**
             * and it is NOT an Inner Section
             */
            if (!$isInner)
            {
                $children = $element->get_children();
                
                $segmentKey = $element->get_settings('swaptify-segment');
                /**
                 * and the Segment key data 
                 */
                if ($segmentKey)
                {   
                    /**
                     * if there is a Segment key,
                     * hide the child elements and add classes and data attributes for front end rendering
                     */
                    $this->hide_elements($children, $segmentKey);
                    $data = [
                        'class' => 'swaptify-render-segment swap-type-elementor',
                    ];
                    
                    $data['data-swaptify_segment'] = $segmentKey;
                    
                    $element->add_render_attribute(
                        '_wrapper',
                        $data
                    );
                }
            }
        }
        elseif ($element->get_type() == 'container')
        {
            $segmentKey = $element->get_settings('swaptify-segment');
            /**
             * if it's a Container, check for Segment key data 
             */
            if ($segmentKey)
            {
                /**
                 * if there is a Segment key,
                 * hide the child elements and add classes and data attributes for front end rendering
                 */
                $this->hide_elements($element->get_children(), $segmentKey);
                $data = [
                    'class' => 'swaptify-render-segment swap-type-elementor',
                ];
                
                $data['data-swaptify_segment'] = $segmentKey;
                
                $element->add_render_attribute(
                    '_wrapper',
                    $data
                );
            }
        }
    }
    
    /**
     * read the available Segment and Swap keys when a page/post is saved and extract the data to store in the database
     *
     * @param int|string $post_id
     * @param array $editor_data - Elementor editor data passed in via POST
     * 
     * @return void
     */
    public function after_save($post_id, $editor_data) {
        /**
         * build arrays to store all the extracted data
         * for Segment keys
         * for Segments that are enabled (non-enabled Segments will only show default Swaps on render)
         * for Swap objects with key, name, content and other data, used to store default Swaps
         * @see Plugin::extract_swap_data()
         */
        $segment_key_array = [];
        $segment_keys_enabled = [];
        $swap_array = [];
        
        /**
         * extract the respective data and add to the approriate arrays
         */
        $this->extract_segment_keys($editor_data, $segment_key_array, $segment_keys_enabled);
        $this->extract_swap_data($editor_data, $swap_array);
        
        /**
         * run the save functions for each type for the given post_id
         */
        Swaptify::saveSegmentKeys($post_id, '', $segment_key_array);
        Swaptify::setActiveSegments($post_id, $segment_keys_enabled);
        Swaptify::updateSwapsByKey($swap_array);
        
        return $editor_data;
    }
    
    /**
     * Search through the Elementor editor data (as an array) to find any Segment key in the 'swaptify-segment' setting
     * will run recursively through the editor data
     *
     * @param array $array - the Elementor editor data
     * @param array $segment_key_array - array of Segment keys to which found keys will be added
     * @param array $segment_keys_enabled - array of enabled Segment keys to which found keys will be added
     * @return void
     */
    public function extract_segment_keys($array, &$segment_key_array, &$segment_keys_enabled) {
        /**
         * loop over the editor data
         */
        foreach ($array as $key => $item) {
            
            /**
             *  if it's an array, search for particular settings
             */
            if (is_array($item)) {
                
                if (isset($item['settings']) && isset($item['settings']['swaptify-segment']) && $item['settings']['swaptify-segment'])
                {
                    $active = (isset($item['settings']['swaptify-segment-enabled-control']) && $item['settings']['swaptify-segment-enabled-control'] == 'yes') ? true : false; 
                    $segment_keys_enabled[$item['settings']['swaptify-segment']] = $active;
                }
                /**
                 * recursively run through the children
                 */
                $this->extract_segment_keys($item, $segment_key_array, $segment_keys_enabled);
            } elseif ($key == 'swaptify-segment' && $item) {
                /**
                 * when the segment key is found, add it to the array
                 */
                $segment_key_array[] = $item;
            }
        }
    }
    
    /**
     * Add to array of object for the Swap data
     *
     * @param array $data - Elementor editor data array
     * @param array $swapArray - array of Swap objects
     *      Each data object will have this structure:
     *      {
     *          "segment_key": "SEGMENT_KEY",
     *          "swap_key": "SWAP_KEY",
     *          "name": "Swap name",
     *          "content": "content",
     *          "sub_content": "sub content...",
     *          "publish": true,
     *          "active":true,
     *          "set_as_default": true
     *      }
     * 
     * @return void
     */
    public function extract_swap_data($data, &$swapArray)
    {
        /**
         * Loop over editor data array to find settings
         */
        foreach ($data as $key => $item)
        {
            /**
             * if the item is an array, look for settings
             */
            if (is_array($item))
            {
                /**
                 * if the item has Segment data AND has an elements array, go deeper to get the data
                 */
                if (isset($item['settings']) && isset($item['settings']['swaptify-segment']) && isset($item['elements']))
                {
                    /**
                     * if the Segment value is set, loop over the elements
                     */
                    if ($item['settings']['swaptify-segment'])
                    {
                        $segmentKey = $item['settings']['swaptify-segment'];
                        
                        /**
                         * looking for columns/inner sections OR containers
                         */
                        foreach ($item['elements'] as $columnKey => $column)
                        {
                            if (isset($item['elements'][$columnKey]['elements']))
                            {
                                /**
                                 * THIS IS FOR THE CONTAINER TYPE
                                 */
                                if (isset($column['settings']) && isset($column['settings']['swaptify-swap']))
                                {
                                    if ($column['settings']['swaptify-swap'])
                                    {
                                        $swapKey = $column['settings']['swaptify-swap'];
                                        /**
                                         * check if it's NOT set OR value is true
                                         */
                                        $active = (!isset($column['settings']['swaptify-swap-enabled-control']) || $column['settings']['swaptify-swap-enabled-control']);
                                        $element = new stdClass();
                                        // name is excluded as there is currently no way to edit it
                                        $element->segment_key = $segmentKey;
                                        $element->swap_key = $swapKey;
                                        /**
                                         * using the entirety of the data as the content
                                         */
                                        $element->content = json_encode($column['elements']);
                                        $element->active = $active;
                                        
                                        /**
                                         * will always publish swap. user can disable swap if not wanting to use
                                         */
                                        $element->publish = true;
                                        
                                        $swapArray[] = $element;
                                    }
                                }
                                else
                                { 
                                    /**
                                     * THIS IS FOR THE INNERSECTION TYPE
                                     */
                                    foreach ($item['elements'][$columnKey]['elements'] as $innerSectionKey => $innerSection)
                                    {
                                        if (isset($innerSection['settings']['swaptify-swap']))
                                        {
                                            $swapKey = $innerSection['settings']['swaptify-swap'];
                                            $active = (!isset($innerSection['settings']['swaptify-swap-enabled-control']) || $innerSection['settings']['swaptify-swap-enabled-control']);
                                            $element = new stdClass();
                                            // name is excluded as there is currently no way to edit it
                                            $element->segment_key = $segmentKey;
                                            $element->swap_key = $swapKey;
                                            /**
                                             * using the entirety of the data as the content
                                             */
                                            $element->content = json_encode($innerSection['elements']);
                                            $element->active = $active;
                                            
                                            /**
                                             * will always publish swap. user can disable swap if not wanting to use
                                             */
                                            $element->publish = true;
                                            
                                            $swapArray[] = $element;
                                        }
                                    }
                                }
                                
                            }
                        }
                    }
                }
                else 
                {
                    /**
                     * run recursively to get all the child elements
                     */
                    $this->extract_swap_data($item, $swapArray);
                }
            }
        }     
    }
    
    /**
     * Add Swaptify controls to element
     * NOTE: this is built from an Elementor code template
     * 
     * @param \Elementor\Controls_Stack $element The element type.
     * @param string $section_id Section ID.
     * @param array $args Section arguments.
     */
    function inject_custom_control($element, $section_id, $args) {
        /**
         * if the element has a name of 'section' or 'container' AND the section_id is 'section_effects' add the controls
         */
        if (
            ('section' === $element->get_name() && 'section_effects' === $section_id)
            || ('container' === $element->get_name() && 'section_effects' === $section_id) 
        ) {
            /**
             * the section and inner sections BOTH have a name of 'section'
             * it looks to be necessary to look up the actual type on the front end
             * there is supposedly an isInner property, but not sure how to access it from the elements here...
             */
            
            /**
             * add the controls section to the Swaptify tab
             */
            $element->start_controls_section(
                'section_swaptify',
                [
                    'tab' => self::TAB_NAME,
                    'label' => __('Swaptify Settings'),
                ]
            );
            
            /**
             * add the Segment control
             * 
             * NOTE: the type is from Swaptify_Segment_Control::get_type()
             */
            $element->add_control(
                'swaptify-segment',
                [
                    'type' => 'swaptify-segment',
                    'label' => __('Segment'),
                ]
            );
            
            /**
             * add the Segment enabled control, to enable/disable Segments
             */
            $element->add_control(
                'swaptify-segment-enabled-control',
                [
                    'type' => Controls_Manager::SWITCHER,
                    'label' => __('Swaptify Segment Enabled'),
                    'default' => 'no',
                    'return_value' => 'yes',
                    'classes' => 'swaptify-segment-enabled-control',
                ]
            );
            
            /**
             * add the Swap control
             * 
             * NOTE: the type is from Swaptify_Swap_Control::get_type()
             */
            $element->add_control(
                'swaptify-swap',
                [
                    'type' => 'swaptify-swap',
                    'label' => __('Swap'),
                    'options' => [],
                ]
            );
            
            /**
             * add Swap enabled control
             */
            $element->add_control(
                'swaptify-swap-enabled-control',
                [
                    'type' => Controls_Manager::SWITCHER,
                    'label' => __('Swap Enabled'),
                    'default' => 'yes',
                    'return_value' => 'yes',
                    'classes' => 'swaptify-swap-enabled-control',
                ]
            );
            
            $element->end_controls_section();
        }
    }

	/**
	 * Register Controls
	 *
	 * Load controls files and register new Elementor controls.
	 *
	 * Fired by `elementor/controls/register` action hook.
	 *
	 * @param \Elementor\Controls_Manager $controls_manager Elementor controls manager.
	 */
	public function register_controls( $controls_manager ) {
		
        require_once( __DIR__ . '/controls/swaptify-segment-control.php' );
        require_once( __DIR__ . '/controls/swaptify-swap-control.php' );

		$controls_manager->register( new Swaptify_Segment_Control() );
		$controls_manager->register( new Swaptify_Swap_Control() );
	}
}