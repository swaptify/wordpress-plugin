/**
 * run the swaps
 */
SwaptifyWP.get_swaps();
    
(function($) {
    'use strict';
    /**
     * on document ready, set all the actions for swaptify
     */
    $(document).ready(function(){

        /**
         * EVENTS
         */
        
        /**
         * set actions for swaptify-event class with data attribute
         */
        $('.swaptify-event').on('click', function() {
            const key = $(this).data('swaptify_key'); 
            SwaptifyWP.event(key); 
        });
        
        $('.swaptify-event').on('submit', function() {
            const key = $(this).data('swaptify_key'); 
            SwaptifyWP.event(key); 
        });
        
        /**
         * set actions for swaptify-event-click/submit-* without data attribute
         */
        $("*[class*='swaptify-event-click-']").each(function() {
            const key = $(this).attr("class").match(/(?:^|\s)swaptify\-event\-click\-([^\s]*)/)[1];
            
            $(this).on('click', function(){
                SwaptifyWP.event(key);
            });
        });
    
        $("*[class*='swaptify-event-submit-']").each(function() {
            const key = $(this).attr("class").match(/(?:^|\s)swaptify\-event\-submit\-([^\s]*)/)[1];
            $(this).on('submit', function(){
                SwaptifyWP.event(key);
            });
        });
    
        /**
         * VISITOR TYPES
         */
        
        /**
         * set actions for swaptify-visitor-type class with data attribute
         */
        $('.swaptify-visitor-type').on('click', function(){
            const key = $(this).data('swaptify_key'); 
            SwaptifyWP.visitor_type(key); 
        });
        
        $('.swaptify-visitor-type').on('submit', function(){
            const key = $(this).data('swaptify_key'); 
            SwaptifyWP.visitor_type(key); 
        });
        
        /**
         * set actions for swaptify-visitor-type-click/submit-* without data attribute
         */
        $("*[class*='swaptify-visitor-type-click-']").each(function() {
            const key = $(this).attr("class").match(/(?:^|\s)swaptify\-visitor\-type\-click\-([^\s]*)/)[1];
            
            $(this).on('click', function(){
                SwaptifyWP.visitor_type(key);
            });
        });
        
        $("*[class*='swaptify-visitor-type-submit-']").each(function() {
            const key = $(this).attr("class").match(/(?:^|\s)swaptify\-visitor\-type\-submit\-([^\s]*)/)[1];
            
            $(this).on('submit', function(){
                SwaptifyWP.visitor_type(key);
            });
        });
    });
    
    
})(jQuery);