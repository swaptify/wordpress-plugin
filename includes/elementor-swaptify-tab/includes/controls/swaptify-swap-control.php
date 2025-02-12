<?php

namespace ElementorSwaptifyTab;

use Swaptify;

/**
 * class for Swaptify Swap control in Elementor
 * 
 * @see also assets/js/swaptify-swap-control.js
 */
class Swaptify_Swap_Control extends \Elementor\Base_Data_Control {

    /**
	 * Get control type.
	 *
	 * Retrieve the control type, in this case `swaptify-swap`.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Control type.
	 */
	public function get_type() {
		return 'swaptify-swap';
	}
    
    /**
     * add javascript for Swap control
     *
     * @return void
     */
    public function enqueue() {
        wp_register_script('swaptify-swap-control', plugin_dir_url( __FILE__ ) . '../../assets/js/swaptify-swap-control.js', [], '1.0.0', true);
        wp_enqueue_script('swaptify-swap-control');
    }
    
    /**
     * build the form display for the Swap control
     * 
     * NOTE: this is an extended function from Elementor core functionality 
     *
     * @return void
     */
    public function content_template() {
        
        /**
         * the Elementor control id
         */
        $control_uid = $this->get_control_uid();
        
        ?>
        
        <input type="hidden" id="<?= $control_uid ?>" class="swaptify-swap-key" value="{{{data.controlValue}}}" />
        
        <div id="elementor-swaptify-swap-eligible-container" style="display:none;"></div>
        <div id="elementor-swaptify-swap-selected-container" style="display:none;">
            <div id="elementor-swaptify-swap-selected"></div>
            <div id="elementor-swaptify-swap-selected-action">
                <button id="swaptify-delete-swap">Delete Swap</button>
            </div>
            <br />
            <a href="<?= Swaptify::$url ?>/rules" target="_blank">Modify display rules for Swaps</a>
            <br />
        </div>
        
        <div id="add-new-swap-form" style="display:none;">    
            <div>
                <label>Swap Name</label>
                <input type="text" id="swaptify-swap-name-input" placeholder="Enter a New Swap Name...">
            </div>
            <div id="add-swap-buttons">
                <button id="add-swap-button">Add Swap &gt;</button>
            </div>
        </div>

        <?php      
    }
}