/**
 * the object containing segment and swap data (keys and names)
 * used to display settings values in the editor
 */
let availableSwaps = {}

/**
 * main Swaptify class that is used to determine which controls can be used and
 * get available data
 */
class ElementorSwaptify
{
    /**
     * retreave the available swaps from the API
     */
    static getAvailableSwaps(callback)
    {
        jQuery.post( ajaxurl, {
            action: 'get_available_swaps'
        }, function(response) {
            if (response.success && response.data)
            {
                availableSwaps = response.data;
                
                if (callback !== undefined)
                {    
                    callback();
                }
            }
            else
            {
                /**
                 * @todo add notice to refresh page? since setting swaps errored
                 */
            }
        },
        'json');
    }

    /**
     * add loading overlay for when data is being processed
     */
    static addLoadingOverlay()
    {
        let loaderHtml = '<div class="swaptify-overlay"><div class="text-center"><div class="spinner-border"><span>loading...</span></div></div></div>';
        jQuery('body').append(loaderHtml);
    }

    /**
     * remove loading overlay
     */
    static removeLoadingOverlay() {
        jQuery('.swaptify-overlay').remove();
    }
    
    /**
     * find the controls that are rendered and hide them. Other functions will display
     * the relevant fields based on the underlying data
     */
    static hideAllControls() {
        /**
         * segment controls
         */
        jQuery('#elementor-controls').find('#add-new-segment-form').hide();
        jQuery('#elementor-swaptify-segment-selected-container').hide();
        jQuery('.swaptify-segment-enabled-control').hide();
        
        /**
         * swap controls
         */
        jQuery('#elementor-swaptify-swap-eligible-container').hide();
        jQuery('#elementor-swaptify-swap-selected-container').hide();
        jQuery('#elementor-controls').find('#add-new-swap-form').hide();
        jQuery('.swaptify-swap-enabled-control').hide();
    }
    
    /**
     * Determine if the container is a Section type
     * NOTE: this will likely be irrelevant once Elementor moves to Container types exclusively
     * 
     * @param {Object} container  - the this.container object from elementor.modules.controls.BaseData
     * @returns {boolean}
     */
    static isSection(container) {
        if (container.type == 'section')
        {
            if (container.model.attributes.isInner)
            {
                // pass
            }
            else
            {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Determine if the container is an Inner Section type widget
     * NOTE: this will likely be irrelevant once Elementor moves to Container types exclusively
     * 
     * @param {Object} container  - the this.container object from elementor.modules.controls.BaseData
     * @returns {boolean}
     */
    static isInnerSection(container) {
        if (container.type == 'section')
        {
            if (container.model.attributes.isInner)
            {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Determine if the container is a Container type
     * 
     * @param {Object} container  - the this.container object from elementor.modules.controls.BaseData
     * @returns {boolean}
     */
    static isContainer(container) {
        if (container.type == 'container')
        {
            return true;
        }
        
        return false;
    }
    
    /**
     * Determine if the Container is eligible to have a Segment assigned to it
     * Will check if any parent or child already a segment set as a setting
     * 
     * @param {Object} container  - the this.container object from elementor.modules.controls.BaseData
     * @returns {boolean}
     */
    static containerIsEligibleForSegments(container) {
        
        let parentHasSegment = ElementorSwaptify.checkParentsForSetting(container, 'swaptify-segment');
        let childHasSegment = ElementorSwaptify.checkChildrenForSetting(container, 'swaptify-segment');
        
        if (!parentHasSegment && !childHasSegment)
        {
            return true;
        }
        
        return false;
    }
    
    /**
     * Determine if the container parents/grandparents/etc has a specific setting, recursively
     * 
     * @param {Object} container  - the this.container object from elementor.modules.controls.BaseData
     * @param {String} setting  - the name of the setting being searched for
     * @returns {boolean}
     */
    static checkParentsForSetting(container, setting) {
        
        let hasSetting = false;
        
        let parentContainer = container ? container.parent : false;
        
        /**
         * if it doesn't have a parent, it is at the very top
         */
        if (parentContainer && parentContainer.model && parentContainer.model.attributes && parentContainer.model.attributes.settings.attributes[setting])
        {
            return true;
        }
        
        if (!parentContainer || parentContainer.type == 'document')
        {
            return false;
        }
        
        let i = 0;
        
        while (!hasSetting)
        {
            hasSetting = ElementorSwaptify.checkParentsForSetting(parentContainer, setting);
            
            i++;
            /**
             * add a break after 30 iterations to ensure a runaway script doesn't happen
             * 
             * this is unlikely to occur, but we don't want the page to freeze in the 
             * event a parent data structure somehow gets warped
             */
            if (i >= 30)
            {
                break;
            }
        }
    
        return hasSetting;
    }
    
    /**
     * Determine if the container children/grandchildren/etc has a specific setting, recursively
     * 
     * @param {Object} container  - the this.container object from elementor.modules.controls.BaseData
     * @param {String} setting  - the name of the setting being searched for
     * @returns {boolean}
     */
    static checkChildrenForSetting(container, setting) {
        
        let hasSetting = false;
        let children = container.children;
        
        if (children)
        {
            for (let i = 0; i < children.length; i++)
            {
                if (children[i].type != 'container')
                {
                    continue;
                }

                if (children[i].model.attributes.settings.attributes[setting])
                {
                    return true;
                }
                
                if (children[i].children.length)
                {
                    let j = 0;
                    while (!hasSetting)
                    {
                        hasSetting = ElementorSwaptify.checkChildrenForSetting(children[i], setting);
                        
                        j++;
                        
                        /**
                         * add a break after 10 iterations to ensure a runaway script doesn't happen
                         * 
                         * this is unlikely to occur, but we don't want the page to freeze in the 
                         * event a child data structure somehow gets warped
                         * 
                         * NOTE: this is a lower threshold than parents because there are multiple iterators
                         */
                        if (j >= 10)
                        {
                            break;
                        }
                    }
                }
                
                /**
                 * add a break after 10 iterations to ensure a runaway script doesn't happen
                 * 
                 * this is unlikely to occur, but we don't want the page to freeze in the 
                 * event a child data structure somehow gets warped
                 * 
                 * NOTE: this is a lower threshold than parents because there are multiple iterators
                 */
                if (i >= 10)
                {
                    break;
                }
            }
        }
        
        return hasSetting;
    }
    
    /**
     * Determine if the container is a top level container
     * 
     * @param {Object} container  - the this.container object from elementor.modules.controls.BaseData
     * @returns {boolean}
     */
    static isParentContainer(container) {
        
        if (this.isContainer(container) && container.parent.type == 'document')
        {
            return true;
        }
        
        return false;
    }
    
    /**
     * Determine if the container is eligible for Swaps, i.e. the container is not the top level
     * 
     * future checks will determine if the container can have swaps, 
     * e.g. the parent must have the swaptify-segment setting
     * 
     * @param {Object} container  - the this.container object from elementor.modules.controls.BaseData
     * @returns {boolean}
     */
    static containerIsEligibleForSwaps(container) {
        if (container.parent && container.parent.type != 'document')
        {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get the Inner Section widgets for a given container
     * 
     * NOTE: only works for Section type, not Container type 
     * 
     * @param {Object} container  - the this.container object from elementor.modules.controls.BaseData
     * @returns {Array|boolean}
     */
    static getInnerSections(container) {
        if (ElementorSwaptify.isSection(container))
        {
            let innerSections = [];
            // get the child column, then the child inner sections
            for (let i = 0; i < container.children.length; i++)
            {
                let element = container.children[i];
                if (element.type == 'column')
                {
                    // find more children
                    for (let j = 0; j < element.children.length; j++)
                    {
                        let subElement = element.children[j];
                        
                        if (subElement.type == 'section' && subElement.args.model.attributes.isInner)
                        {
                            innerSections.push(subElement);
                        }
                    }
                }
                else if (element.type == 'section' && element.args.model.attributes.isInner)
                {
                    innerSections.push(element);                    
                }
            }
            
            return innerSections;
        }
        
        return false; 
    }
}

/**
 * get all the available swaps and segments
 * this data will populate the form/control data when user opens the swaptify tab
 */
ElementorSwaptify.getAvailableSwaps();

/**
 * class for Segment Control form
 */
class ElementorSwaptifySegment
{
    /**
     * display the form for new Segment
     */
    static showNewForm() {
        jQuery('#elementor-swaptify-segment-selected-container').hide();
        jQuery('#elementor-controls').find('#add-new-segment-form').show();
        jQuery('.swaptify-segment-enabled-control').hide();
        jQuery('#swaptify-segment-name-input').val('');
        jQuery('#swaptify-segment-name-input').focus();
    }
    
    /**
     * display the current Segment
     */
    static showCurrentSegment() {
        jQuery('#elementor-swaptify-segment-selected-container').show();
        jQuery('#elementor-controls').find('#add-new-segment-form').hide();
        jQuery('.swaptify-segment-enabled-control').show();
    }
    
    /**
     * update the content for the current Segment and display it
     * 
     * @param {String} segmentKey 
     */
    static setCurrentSegmentView(segmentKey) {
        if (availableSwaps[segmentKey])
        {   
            let segmentName = 'Swaptify Segment: ' + availableSwaps[segmentKey].name;
            jQuery('#elementor-swaptify-segment-selected').html(segmentName);
            this.showCurrentSegment();
        }
    }
    
    /**
     * delete the Segment key from the control value
     * 
     * @todo this will need to also delete all the child swaps
     * @param {Object} control 
     */
    static deleteSegment(control) {
        /**
         * @todo find out how to do this in order to cleanse the unused data
         */
        let innerSections = ElementorSwaptify.getInnerSections(control.container);
        
        /**
         * what's supposed to happen here is a removal of all the 'swaptify-swap' values from inner sections
         */
        /**
        @todo  uncomment this when cleansing the data is confirmed
        if (innerSections)
        {
            for (let i = 0; i < innerSections.length; i++)
            {
                innerSections[i].model.attributes.settings.attributes['swaptify-swap'];
            }
        }
        */
        
        control.setValue(null);
        ElementorSwaptifySegment.showNewForm();
    }

}

/**
 * build the Segment Control with the Elementor structure
 */
let swaptifySegmentControl = elementor.modules.controls.BaseData.extend({
    /**
     * the onShow method will show Segment data when ran
     * there is processing logic to determine what should show and what actions are relevant
     * this works in conjunction with swaptifySwapControl.onShow
     * 
     * @see swaptifySwapControl - in swaptify-swap-control.js
     */
    onShow: function(){
        const self = this;
        
        ElementorSwaptify.hideAllControls();
        
        self.controlValue = this.getControlValue();
        
        /**
         * determine what type of element this is, a Section or a Container AND whether
         * or not it is eligible for Segments (must be a Section or a Container without a child Container with a Segment)
         */
        if (
            (ElementorSwaptify.isContainer(this.container) && ElementorSwaptify.containerIsEligibleForSegments(this.container))
            || ElementorSwaptify.isSection(this.container)
        )
        {
            /**
             * if there's a Segment value set, display it
             */
            if (self.controlValue)
            {
                ElementorSwaptifySegment.setCurrentSegmentView(self.controlValue);
            }
            else
            {
                /**
                 * otherwise, show th add new Segment form
                 */
                ElementorSwaptifySegment.showNewForm();
            }
            
            /**
             * set the event handlers for the form elements
             */
            jQuery('#swaptify-segment-name-input').on('keypress',function(e){
                jQuery(this).removeClass('required');
                if (e.keyCode == 13)
                {
                    jQuery('#add-segment-button').click();
                }
            });
            
            jQuery('#add-segment-button').on('click', function(){
                jQuery('#swaptify-segment-name-input').removeClass('required');
                let name = jQuery('#swaptify-segment-name-input').val();
                
                if (!name)
                {
                    jQuery('#swaptify-segment-name-input').addClass('required');
                }
                else
                {
                    ElementorSwaptify.addLoadingOverlay();
                    jQuery.post( ajaxurl, {
                        action: 'add_elementor_segment',
                        name: name
                    }, function(response) {
                        if (response.success && response.key)
                        {
                            ElementorSwaptify.getAvailableSwaps(function(){
                                ElementorSwaptifySegment.setCurrentSegmentView(response.key);
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
            
            jQuery('#swaptify-delete-segment').on('click', function(){
                if (confirm('Are you sure you want to remove this Segment setting? All Swaps will show'))
                {
                    ElementorSwaptifySegment.deleteSegment(self);
                }
            });
        }
        else
        {
            // pass
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

elementor.addControlView('swaptify-segment', swaptifySegmentControl);