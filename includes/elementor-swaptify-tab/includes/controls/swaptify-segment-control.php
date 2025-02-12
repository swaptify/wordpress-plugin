<?php

namespace ElementorSwaptifyTab;

/**
 * class for Swaptify Segment control in Elementor
 * 
 * @see also assets/js/swaptify-segment-control.js
 */
class Swaptify_Segment_Control extends \Elementor\Base_Data_Control {

    /**
	 * Get control type.
	 *
	 * Retrieve the control type, in this case `swaptify-segment`.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Control type.
	 */
	public function get_type() {
		return 'swaptify-segment';
	}
    
    /**
     * add javascript for Segment control
     *
     * @return void
     */
    public function enqueue() {
        wp_register_script('swaptify-segment-control', plugin_dir_url( __FILE__ ) . '../../assets/js/swaptify-segment-control.js', [], '1.0.0', true);
        wp_enqueue_script('swaptify-segment-control');
    }
    
    /**
     * build the form display for the Segment control
     * 
     * NOTE: this is an extended function from Elementor core functionality 
     *
     * @return void
     */
    public function content_template()
    {
        /**
         * the Elementor control id
         */
        $control_uid = $this->get_control_uid();

        ?>
        <style>
            #add-new-swap-form > #add-swap-buttons,
            #add-new-segment-form > #add-segment-buttons {
                text-align:center;
            }
            #add-new-swap-form,
            #add-new-segment-form {
                padding: 5px;
                margin:0 5px;
            }
            
            #swaptify-swap-name-input.required,
            #swaptify-segment-name-input.required {
                background:#e17272;
            }
            
            #add-swap-buttons > button, 
            #elementor-swaptify-segment-selected-action > button,
            #elementor-swaptify-swap-selected-action > button,
            
            #add-segment-buttons > button {
                padding: 5px;
                margin:5px;
                text-align:center;
            }
            
            @-webkit-keyframes spinner-border {
                to {
                transform: rotate(360deg);
                }
            }
            
            @keyframes spinner-border {
                to {
                transform: rotate(360deg);
                }
            }
            .swaptify-overlay {
                position: fixed !important;
                width: 100% !important;
                height: 100% !important;
                background: rgba(0,0,0,0.65) !important;
                z-index: 100;
                top: 0;
                left: 0;
                text-align: center;
                color: white;
                padding-top: 300px;
            }
            
            .swaptify-overlay .spinner-border {
                display: inline-block !important;
                width: 3rem !important;
                height: 3rem !important;
                vertical-align: text-bottom !important;
                border: 0.4em solid #234997 !important;
                border: 0.4em solid #FFFFFF !important;
                border-right-color: transparent !important;
                border-radius: 50% !important;
                -webkit-animation: spinner-border 0.75s linear infinite !important;
                        animation: spinner-border 0.75s linear infinite !important;
            }
            
            .swaptify-overlay .spinner-border span
            {
                display: none;
            }
            
            
        </style>
        
        <input type="hidden" id="<?= $control_uid ?>" class="swaptify-segment-key" value="{{{data.controlValue}}}" />
        
        <div id="elementor-swaptify-segment-selected-container" style="display:none;">
            <div id="elementor-swaptify-segment-selected"></div>
            <div id="elementor-swaptify-segment-selected-action">
                <button id="swaptify-delete-segment">Delete Segment</button>
            </div>
        </div>
        
        <div id="add-new-segment-form" style="display:none;">    
            <div>
                <label>Segment Name</label>
                <input type="text" id="swaptify-segment-name-input" placeholder="Enter a New Segment Name...">
            </div>
            <div id="add-segment-buttons">
                <button id="add-segment-button">Add &gt;</button>
            </div>
        </div>
        <?php
    }
}