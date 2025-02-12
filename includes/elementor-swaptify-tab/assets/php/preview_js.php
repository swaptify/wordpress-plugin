
<?php
/**
 * This file contains CSS and JS used in previewing in Elementor
 */
?>
<style>
    .selected-segment:not(.hide-swap-border) .swap-preview-show {
        -webkit-box-shadow:inset 0px 0px 4px 1px #234997;
        -moz-box-shadow:inset 0px 0px 4px 1px #234997;
        box-shadow:inset 0px 0px 4px 1px #234997;
    }

    .swap-preview-hide {
        display: none !important;
        height: 0 !important;
        width: 0 !important;
    }
    #swap-change-div select{
        margin: 3px;
        border-radius: 5px;
        padding: 6px;
    }
    
    #hide-swap-toggle {
        color: #fff;
        font-size: 14px;
        margin-left: 5px;
        cursor: pointer;
        text-decoration: none;
    }
    .swap-spinner-container {
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 6px;
    }
    .swap-spinner {
        width: 32px;
        height: 32px;
        border: 4px #ffffff solid;
        border-top: 4px #0e85f4 solid;
        border-radius: 50%;
        animation: swap-spinner 1s infinite linear;
    }
    
    #site-header {
        margin-top: 50px;
    }
    
    @-webkit-keyframes swap-spinner {
        to {
        transform: rotate(360deg);
        }
    }
    
    @keyframes swap-spinner {
        to {
        transform: rotate(360deg);
        }
    }
</style>

<script>
    jQuery('body').addClass('preview');
    
    /**
     * define all the available Swaps for the current page
     */
    let previewSwaps = {};
    
    jQuery(function(){
        /**
         * create a div for the Swap selection form
         */
        let div = jQuery('<div>').css({
            'min-height': '50px',
            'width': '100%',
            'background': '#234997', 
            'padding': '3px',
            'position': 'fixed',
            'top': '32px', 
            'left': 0,
            'text-align': 'center',
            'z-index': 5000
        })
        .attr('id', 'swap-change-div');
        
        /**
         * display the first Swap in each segment
         */
        jQuery('.swaptify-render-segment').each(function(){
           jQuery(this).find('.swaptify-render-swap:first').addClass('swap-preview-show').removeClass('swap-preview-hide');
        });
        
        /**
         * define loading spinner
         */
        let loadingDiv = jQuery('<div>').attr('id', 'swap-change-loading').html('<div class="swap-spinner-container"><span class="swap-spinner"></span></div>');
        
        /**
         * build the div that will house the action fields, such as changing Swaps
         */
        let actionDiv = jQuery('<div>').css({'display': 'none'}).attr('id', 'swap-change-actions');
        
        /**
         * create the Segment selector with the on change handler
         */
        let segmentSelect = jQuery('<select>')
            .attr('id', 'preview-segment').on('change', function(){

                let segmentKey = jQuery(this).val();
                jQuery('#preview-swap').empty();
                
                // remove selected from all segments
                jQuery('.swaptify-render-segment').removeClass('selected-segment').removeClass('hide-swap-border');
                jQuery('[data-swaptify_segment="' + segmentKey + '"]').addClass('selected-segment');
                
                if (jQuery('#hide-swap-toggle').hasClass('hidden'))
                {
                    jQuery('[data-swaptify_segment="' + segmentKey + '"]').addClass('hide-swap-border');
                }
                
                let top = 0;
                
                let adminBar = jQuery('#wpadminbar');
                let firstSwap = jQuery('[data-swaptify_segment="' + segmentKey + '"]:eq(0)');
                
                if (firstSwap)
                {
                    top = firstSwap.parent().offset().top - jQuery('#swap-change-div').height() - 20;
                    if (adminBar)
                    {
                        top = top - adminBar.height();
                    }
                }
                
                if (top < 0)
                {
                    top = 0;
                }
                
                jQuery('html').animate(
                    {
                        scrollTop: top,
                    },
                    5
                );
                
                /**
                 * add options for Swaps based on selected Segment
                 */
                for (const swapKey in previewSwaps[segmentKey]['swaps'])
                {
                    let swap = previewSwaps[segmentKey]['swaps'][swapKey];
                    
                    let option = jQuery('<option>').attr('value', swap.key).html(swap.name);
                    
                    if (jQuery('section, .elementor-element').find('[data-swaptify_swap="' + swap.key + '"]').is(':visible'))
                    {
                        option.attr('selected', 'selected');
                    }
                    
                    jQuery('#preview-swap').append(option);
                }
            });
        
        /**
         * build the Swap select
         */
        let swapSelect = jQuery('<select>').attr('id', 'preview-swap').on('change', function(){
            
            let segmentKey = jQuery('#preview-segment').val();
            
            let swapKey = jQuery(this).val();
            /**
             * hide all the current Swaps by Segment key
             */
            jQuery('section')
                .find('[data-swaptify_segment="' + segmentKey + '"]')
                .find('[data-swaptify_swap]')
                .addClass('swap-preview-hide')
                .removeClass('swap-preview-show');
            
            /**
             * show the selected Swap
             */
            jQuery('section')
                .find('[data-swaptify_segment="' + segmentKey + '"]')
                .find('[data-swaptify_swap="' + swapKey + '"]')
                .removeClass('swap-preview-hide')
                .addClass('swap-preview-show');
                
            jQuery('.swaptify-render-segment[data-swaptify_segment="' + segmentKey + '"]')
                .find('[data-swaptify_swap]')
                .addClass('swap-preview-hide')
                .removeClass('swap-preview-show');
                
            jQuery('.swaptify-render-segment[data-swaptify_segment="' + segmentKey + '"]')
                .find('[data-swaptify_swap="' + swapKey + '"]')
                .removeClass('swap-preview-hide')
                .addClass('swap-preview-show');

        });
        
        /**
         * handler to toggle the Segment outline in the preview
         */
        let hideSwapToggle = jQuery('<a>')
            .attr('id', 'hide-swap-toggle')
            .attr('title', 'Hide border around Swaptify Segment')
            .on('click', function(){
            if (jQuery(this).hasClass('hidden'))
            {
                jQuery('.selected-segment').removeClass('hide-swap-border');
                jQuery(this).html('hide').attr('title', 'Hide border around Swaptify Segment');
                jQuery(this).removeClass('hidden');
            }
            else
            {
                jQuery('.selected-segment').addClass('hide-swap-border');
                jQuery(this).html('show').attr('title', 'Show border around Swaptify Segment');
                jQuery(this).addClass('hidden');
            }
        }).html('hide');
        

        actionDiv.append(segmentSelect);
        actionDiv.append(swapSelect);
        actionDiv.append(hideSwapToggle);
        
        div.append(loadingDiv);
        div.append(actionDiv);
        
        jQuery('body').prepend(div); 
        
        /**
         * get the available Swaps for previewing
         */
        jQuery.post( '<?php echo(admin_url( 'admin-ajax.php')); ?>', {
            action: 'get_available_swaps'
        }, function(response) {
            
            let availableSegments = [];
            jQuery.each(jQuery('[data-swaptify_segment]'), function(index){
                availableSegments.push(jQuery(this).data('swaptify_segment'));
            });
            
            if (response.success && response.data)
            {
                previewSwaps = response.data;
                for (const segmentKey in previewSwaps)
                {
                    if (jQuery.inArray(segmentKey, availableSegments) != -1)
                    {
                        let option = jQuery('<option>').attr('value', segmentKey).html(previewSwaps[segmentKey]['name']);
                        
                        jQuery('#preview-segment').append(option);
                    }
                }
                
                actionDiv.show();
                loadingDiv.hide();
                
                jQuery('#preview-segment').trigger('change');
            }
        },
        'json');
        
    });
</script>