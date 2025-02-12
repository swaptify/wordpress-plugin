let swaps = null;

SwaptifyWP = {
    visitor_type: function(keyOrName) {
        const url = swaptify_ajax.swaptify_ajax_url;
        
        jQuery.post(url, {
            action: 'swaptify_visitor_type',
            key: keyOrName
        }, function(response) {
            // pass
            if (response.visitor_types) 
            {
                jQuery('body').removeClass(function (index, className) {
                    let pattern = '\\b' + swaptify.slug_prefix + '\\S+';
                    let regex = new RegExp(pattern, 'g');
                    
                    return (className.match (regex) || []).join(' ');
                });
                
                for (var i = 0; i < response.visitor_types.length; i++)
                {
                    jQuery('body').addClass(swaptify.slug_prefix + response.visitor_types[i].slug);
                }
            }
        },
        'json');
    },    
    event: function(key) {
        const url = swaptify_ajax.swaptify_ajax_url;
        
        jQuery.post(url, {
            action: 'swaptify_event',
            key: key
        }, function(data) {
           // pass 
        },
        'json');
    },
    get_swaps: function() {
        if (jQuery('body').hasClass('preview') || swaptify.preview)
        {
            SwaptifyWP.clean_swaps();
            return;
        }
        
        const url = swaptify_ajax.swaptify_ajax_url;
        
        let id = jQuery('#swaptify_id').val();
        
        jQuery('#swaptify_id').remove();
        
        jQuery.post(url, {
            action: 'get_swaps',
            id: id,
            url: window.location.href
        }, function(response) {
            if (response.swaps)
            {
                swaps = response.swaps;
                
                if (response.visitor_types) 
                {
                    jQuery('body').removeClass(function (index, className) {
                        let pattern = '\\b' + swaptify.slug_prefix + '\\S+';
                        let regex = new RegExp(pattern, 'g');
                        
                        return (className.match (regex) || []).join(' ');
                    });
                    
                    for (var i = 0; i < response.visitor_types.length; i++)
                    {
                        jQuery('body').addClass(swaptify.slug_prefix + response.visitor_types[i].slug);
                    }
                }
            }
            
            jQuery(document).ready(function(){
                SwaptifyWP.render_swaps(); 
                SwaptifyWP.clean_swaps();
             });
        },
        'json');
        
    },
    render_swaps: function() {
        if (swaps)
        {
            for (const segmentKey in swaps.keys) 
            {
                var segment = jQuery('[data-swaptify_segment=' + segmentKey + ']');
                var type = swaps.types[segmentKey];
                
                if (type == 'image')
                {
                    var img = jQuery('<img>');
                    img.attr('src', swaps.data[segmentKey]);
                    img.attr('title', swaps.subdata[segmentKey]);
                    segment.html(img);
                }
                else if (type == 'url')
                {
                    var anchor = jQuery('<a>');
                    anchor.attr('href', swaps.data[segmentKey]);
                    anchor.html(swaps.subdata[segmentKey]);
                    segment.html(anchor);
                }
                else 
                {
                    segment.html(swaps.data[segmentKey]);
                }
                segment.addClass('unblur');
                segment.removeClass('swaptify-render-segment');
                segment.removeClass('swap-type-url swap-type-image swap-type-text');
                segment.removeAttr('data-swaptify_segment');
                segment.removeAttr('data-swaptify_type');
            }
        }
    },
    clean_swaps: function() {
        jQuery('.swaptify-render-segment').find('.swaptify-render-swap:not(:visible)').remove();
            
            jQuery('.swaptify-render-segment')
                .removeClass('swap-type-url swap-type-image swap-type-text')
                .removeAttr('data-swaptify_type')
                .removeAttr('data-swaptify_segment')
                .removeClass('swaptify-render-segment');
    }
}