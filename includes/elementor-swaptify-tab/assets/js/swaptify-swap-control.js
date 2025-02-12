/**
 * Swap class used to display and manipulate Swap assignments
 */
class ElementorSwaptifySwap
{
    /**
     * add and show a message on Swap eligibility and hide irrelevant fields
     * 
     * @param {String} message 
     */
    static showSwapEligibility(message) {
        jQuery('#elementor-swaptify-swap-eligible-container').html(message).show();
        jQuery('#elementor-controls').find('#add-new-swap-form').hide();
        jQuery('#elementor-swaptify-swap-selected-container').hide();
        jQuery('.swaptify-swap-enabled-control').hide();
    }
    
    /**
     * dispaly the Add New Swap form
     */
    static showNewSwapForm() {    
        jQuery('#elementor-controls').find('#add-new-swap-form').show();
        jQuery('#elementor-swaptify-swap-selected-container').hide();
        jQuery('.swaptify-swap-enabled-control').hide();
    }
    
    /**
     * dispaly current selected Swap information
     */
    static showCurrentSwap() {
        jQuery('#elementor-swaptify-swap-selected-container').show();
        jQuery('.swaptify-swap-enabled-control').show();
        jQuery('#elementor-controls').find('#add-new-swap-form').hide();
    }
    
    /**
     * Set the current Swap name based on Segment key and Swap key
     * information retreaved from the availableSwaps array
     * 
     * @param {String} segmentKey 
     * @param {String} swapKey 
     * @returns {boolean}
     */
    static setCurrentSwap(segmentKey, swapKey) {
        let swapName = '';
        
        if (availableSwaps[segmentKey] && availableSwaps[segmentKey]['swaps'])
        {
            let swaps = availableSwaps[segmentKey]['swaps'];
            for (const swap in swaps) 
            {
                if (swaps[swap].key == swapKey)
                {
                    swapName = swaps[swap].name;
                    
                    if (swaps[swap].is_default)
                    {
                        swapName += ' <em>(default)</em>';
                    }
                    
                    break;
                }
            }
        }
        
        /**
         * only display it if the Swap name is actually set
         */
        if (swapName != '')
        {        
            jQuery('#elementor-swaptify-swap-selected').html('Swap: ' + swapName);
            this.showCurrentSwap();
            return true;
        }
        
        return false;
    }
    
    /**
     * Get the Segment key from the parent container
     * used to confirm Swap and Segment data
     * 
     * @param {Object} container 
     * @returns {String|null}
     */
    static getSegmentKeyFromContainer(container)
    {
        if (container.parent.args.settings.attributes['swaptify-segment'])
        {
            return container.parent.args.settings.attributes['swaptify-segment'];
        }
        
        return null;
    }
    
    
    /**
     * Get the Segment key from the parent Section, used for Section/Inner Section types
     * used to confirm Swap and Segment data
     * 
     * @param {Object} container 
     * @returns {String|null}
     */
    static getSegmentKeyFromParentSection(container)
    {
        
        let parent = container.parent;
        
        /**
         * set an iterator as a failsafe against endless loops
         */
        let i = 1;
        
        let segmentKey = null;
        
        while (parent)
        {
            /**
             * if it's a section AND isInner is false, it's a parent section
             */
            if (parent.type == 'section' && parent.model.attributes.isInner == false)
            {
                segmentKey = parent.args.settings.attributes['swaptify-segment'] ?? null;
                break;
            }
            
            /**
             * set the parent in the loop to continue up the tree
             */
            parent = parent.parent;
            
            i++;
            
            /**
             * to prevent browser lockup if there is anything not assumed by the layout structure
             */
            if (i >= 25)
            {
                break;
            }
        }
        
        return segmentKey;
    }
    
    /**
     * Delete the current Swap attribute for the control element
     * will show new Swap form after deletion
     * 
     * @param {Object} control 
     */
    static deleteSwap(control)
    {
        control.setValue(null);
        ElementorSwaptifySwap.showNewSwapForm();
    }
}
/**
 * build the Swap Control with the Elementor structure
 */
let swaptifySwapControl = elementor.modules.controls.BaseData.extend({
    
    /**
     * the onShow method will display Swap data/new form if it is eligible for Swaps
     * it is determined by data contained within parent elements (parent container or Section)
     */
    onShow : function(){
        
        const self = this;
        /**
         * DO NOT hide controls again. they are hidden at the outset with the Segment control code
         * @see swaptifySegmentControl - in swaptify-segment-control.js
         */
        self.controlValue = this.getControlValue();
        
        /**
         * confirm the displayed element is either a container OR Inner Section
         * otherwise, it's a widget or other element that will not contain the Swaptify Tab
         */
        if (
            (ElementorSwaptify.isContainer(this.container))
            || ElementorSwaptify.isInnerSection(this.container)
        )
        {
            let showNewForm = false;
            let showSelectedSwap = false;
            /**
             * whether or not to show the Swap eligibility message
             * shown only when a Swap cannot be used for a given element
             */
            let showSwapEligibility = false;
            /**
             * the message displayed when an element is ineligible for a Swap
             */
            let showSwapEligibilityMessage = '';
            
            let segmentKey = null;

            if (ElementorSwaptify.isContainer(this.container)) {
                /**
                 * for container types, confirm the current container is eligible for Swaps
                 * i.e. the direct parent container has a Segment value set
                 * this will assign messages and triggers for whether or not to show certain forms below
                 */
                if (ElementorSwaptify.containerIsEligibleForSwaps(this.container))
                { 
                    segmentKey = ElementorSwaptifySwap.getSegmentKeyFromContainer(this.container);
                    
                    /**
                     * if there's a Segment key in the parent, check if there is a controlValue(Swap is set)
                     * if so, show the selected Swap, otherwise, the add Swap form
                     */
                    if (segmentKey)
                    {
                        if (self.controlValue)
                        {
                            showSelectedSwap = true;
                        }
                        else
                        {
                            showNewForm = true;
                        }
                    }
                    else
                    {
                        /**
                         * determine the message to show for an element that is ineligible for a Swap
                         */
                        if (ElementorSwaptify.checkChildrenForSetting(this.container, 'swaptify-segment'))
                        {
                            showSwapEligibilityMessage = 'A child container already has a Segment assigned';
                            showSwapEligibility = true;
                        }
                        else if (ElementorSwaptify.checkParentsForSetting(this.container, 'swaptify-segment'))
                        {
                            showSwapEligibilityMessage = 'This container is ineligible for Swaps. A parent container is configured for Swaps.';
                            showSwapEligibility = true;
                        }
                    }
                }
                else
                {
                    /**
                     * if a child element contains a Segment key, the current element is ineligible for Swaps
                     */
                    if(ElementorSwaptify.checkChildrenForSetting(this.container, 'swaptify-segment'))
                    {
                        showSwapEligibilityMessage = 'A child container has a Segment assigned';
                        showSwapEligibility = true;
                    }
                }
            } else {
                /**
                 * for Section types, the element must be an Inner Section AND the parent must have a Segment value
                 */
                if (ElementorSwaptify.isInnerSection(this.container)) {
                    segmentKey = ElementorSwaptifySwap.getSegmentKeyFromParentSection(this.container);
                    
                    if (segmentKey)
                    {
                        if (self.controlValue)
                        {
                            showSelectedSwap = true;
                        }
                        else {
                            showNewForm = true;
                        }
                    }
                    else
                    {
                        showSwapEligibilityMessage = 'Select a Segment in the Section in Order to Create Swaps';
                        showSwapEligibility = true;
                    }
                }
            }
            
            if (showNewForm) {
                /**
                 * show the new Swap form
                 */
                ElementorSwaptifySwap.showNewSwapForm();
            } else if (showSelectedSwap) {
                /**
                 * this checks to make sure the segment is set and the swap value is present. if they are, the value is 
                 * displayed. if not, it returns false and we show the new swap form
                 */
                let swapAvailable = ElementorSwaptifySwap.setCurrentSwap(segmentKey, self.controlValue);
                
                if (!swapAvailable)
                {
                    ElementorSwaptifySwap.showNewSwapForm();
                }
            }
            else if (showSwapEligibility) {
                /**
                 * show the Swap eligibility message
                 */
                ElementorSwaptifySwap.showSwapEligibility(showSwapEligibilityMessage);
            }
            
            /**
             * add a trigger for pressing enter when typing the swap name
             */
            jQuery('#swaptify-swap-name-input').on('keypress',function(e){
                jQuery(this).removeClass('required');
                if (e.keyCode == 13)
                {
                    jQuery('#add-swap-button').click();
                }
            });
            
            /**
             * add a handler for adding the Swap value
             */
            jQuery('#add-swap-button').on('click', function(){
                jQuery('#swaptify-swap-name-input').removeClass('required');
                let name = jQuery('#swaptify-swap-name-input').val();
                
                if (!name)
                {
                    jQuery('#swaptify-swap-name-input').addClass('required');
                }
                else
                {
                    ElementorSwaptify.addLoadingOverlay();
                    jQuery.post( ajaxurl, {
                        action: 'add_elementor_swaps',
                        name: name,
                        segmentKey: segmentKey
                    }, function(response) {
                        if (response.success && response.key)
                        {
                            ElementorSwaptify.getAvailableSwaps(function(){
                                ElementorSwaptifySwap.setCurrentSwap(segmentKey, response.key);
                                self.setValue(response.key);
                                ElementorSwaptify.removeLoadingOverlay();
                            });   
                        }
                        else 
                        {
                            ElementorSwaptify.removeLoadingOverlay();
                        }
                        
                    },
                    'json');
                }
            });
            
            /**
             * add handler for deleting Swap value
             */
            jQuery('#swaptify-delete-swap').on('click', function(){
                if (confirm('Are you sure you want to remove this Swap?'))
                {
                    ElementorSwaptifySwap.deleteSwap(self);
                }
            });
        }
    },
    applySavedValue: function(){
        /**
         * default Elementor method
         */
    },
    saveValue: function(self){
        /**
         * default Elementor method
         */
    },
    onBeforeDestroy(){
        /**
         * default Elementor method
         */
    }
});

elementor.addControlView('swaptify-swap', swaptifySwapControl);