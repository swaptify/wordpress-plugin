(function() {

    // Register plugin
    
    tinymce.create( 'tinymce.plugins.swaptify', {

        init: function(editor, url)  {

            editor.addButton( 'swaptify', {
                text: 'Swaptify',
                //icon: 'icons dashicons-icon',
                tooltip: 'Insert Swaptify Segment',
                cmd: 'swaptify_command'
            });

            editor.addCommand('swaptify_command', function(){
                editor.windowManager.open({
                    // Modal settings
                    title: 'Insert Swaptify Segment',
                    width: jQuery( window ).width() * 0.7,
                    // minus head and foot of dialog box
                    height: (jQuery( window ).height() - 36 - 50) * 0.7,
                    inline: 1,
                    id: 'swap-insert-dialog',
                    buttons: [{
                        text: 'Insert',
                        id: 'swap-button-insert',
                        class: 'insert',
                        onclick: function(e) {

                            // segments
                            let segments = jQuery('#swaptify-segments');

                            let shortcode = jQuery('#swaptify-segments').find('.swaptify-segment-selected').find('.shortcode').html();
                            editor.insertContent(shortcode);
                            //return;
                            
                            jQuery(segments).find('.swaptify-segment-selecteddd').each(function(index){
                                let shortcode = jQuery(this).find('.shortcode');

                                editor.insertContent(shortcode);
                            });
                            
                            editor.windowManager.close();
                        },
                    },
                    {
                        text: 'Cancel',
                        id: 'swap-button-cancel',
                        onclick: 'close'
                    }],
                });

                addSwaptifySegmentForm('swap-insert-dialog', editor);
                 
            });
        }
    });

    tinymce.PluginManager.add('swaptify', tinymce.plugins.swaptify);

    /**
     * these are the vanilla settings for tinyMCE from Wordpress
     * It excludes 
     */
    let tinymceSettings = {
        plugins:"charmap,colorpicker,hr,lists,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpautoresize,wpeditimage,wpemoji,wpgallery,wplink,wpdialogs,wptextpattern,wpview",
        toolbar1:"formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,wp_add_media,wp_adv",
        toolbar2:"strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help",
        toolbar3:"separator,swaptify",
        toolbar4:"",
        classic_block_editor:true,
        content_style:" "};
	
    function addSwaptifySegmentForm(id, editor) {
        let containerSelector = '#' + id + '-body';
        let segmentsDiv = jQuery('<div>').attr('id', 'swaptify-segments');
        
        let infoParagraph = jQuery('<p>').html('Select a Segment to insert a shortcode.');
        let infoEditUrl = jQuery('<p>').attr('id', 'edit-segments-link').html('You can add/edit Segments <a href="' + swaptify_admin_url.swaptify_admin_url + 'admin.php?page=swaptify-shortcode-generator" target="_blank">here</a>');
        
        let infoSelect = jQuery('<select>').attr('id', 'segment-type').html('<option value="">&mdash; Select Type &mdash;</option>')
                .on('input', function(){
                    filterSegments();
                });
        
        let infoSearch = jQuery('<input>').attr({
            id: 'segment-search',
            placeholder: 'Search Segments by name',
            type: 'search'
        })
        .on('input', function(){
            filterSegments();
        });
        
        let infoDiv = jQuery('<div>').attr('id', 'swaptify-info')
                        .append(infoParagraph)
                        .append(infoEditUrl)
                        .append(infoSelect)
                        .append(infoSearch);
                        
        
        jQuery(containerSelector)
            .append(infoDiv)
            .append(segmentsDiv);
        
        
        SwaptifyMCE.addLoader(containerSelector);
        
        // let segment_types;
        
        jQuery.post(ajaxurl, {
            action: 'tinymce_get_segment_types'
        }, function(response) {
            segment_types = response.types;
            
            for (let i = 0; i < segment_types.length; i++)
            {
                let option = jQuery('<option>').attr('value', segment_types[i].id).html(segment_types[i].name);
                jQuery('#segment-type').append(option);
            }
        },
        'json');
        
        jQuery.post( ajaxurl, {
            action: 'tinymce_get_swaps'
        }, function(response) {
            for (segment_key in response.segments)
            {
                let div = generateSegmentSelector(segment_key, response.segments[segment_key]);
                jQuery(containerSelector).find('#swaptify-segments').append(div);
            }
            
            SwaptifyMCE.removeLoader(containerSelector);
        }, 
        'json');
        // jQuery(containerSelector).html(segment_types);
        editor.getContent();
    }
    
    function generateSegmentSelector(key, segment)
    {
        let div = jQuery('<div>')
                    .addClass('swaptify-segment-selector')
                    .attr('data-key', key)
                    .attr('data-type', segment.type)
                    .attr('data-name', segment.name.replaceAll('"', "'"));
        
        div.on('click', function(){
            jQuery('.swaptify-segment-selector').removeClass('swaptify-segment-selected');
            jQuery(this).addClass('swaptify-segment-selected');
        });
        
        let typeString = '';
        
        if (segment.type == 'url' || segment.type == 'image')
        {
            typeString = '_' + segment.type;
        }
        
        let shortcode = '[swap_segment' + typeString + ' key="' + key + '" name="' + segment.name.replaceAll('"', "'") + '"]';
        
        let html = jQuery('<div>');
        let segmentName = jQuery('<p>').addClass('segment-name').html(segment.name);
        let segmentType = jQuery('<p>').html(segment.type);
        let segmentContent = jQuery('<p>');
        
        if (segment.type == 'url')
        {
            let segmentUrl = jQuery('<a>').attr({
                href: segment.content,
                target: '_blank'
            }).html(segment.sub_content);
            
            segmentContent.append(segmentUrl);
        }
        else if (segment.type == 'image')
        {
            let segmentImage = jQuery('<img>').attr({
                src: segment.content,
                alt: segment.sub_content
            })
            .css({
                width: '300px'
            });
            
            segmentContent.append(segmentImage);
        }
        else
        {
            segmentContent.append(segment.content);
        }
        
        let segmentEdit = jQuery('<p>').addClass('edit').html('<a href="/wp-admin/admin.php?page=swaptify-shortcode-generator&key=' + key + '" target="_blank">edit</a>');
        let shortcodeDiv = jQuery('<div>').addClass('shortcode').append(shortcode);
        
        html.append(segmentEdit);
        html.append(segmentName);
        html.append(segmentType);
        html.append(segmentContent);
        html.append(shortcodeDiv);
        
        div.html(html);
        
        return div;
    }
    
    function filterSegments()
    {
        let searchValue = jQuery('#segment-search').val().toLowerCase();
        let searchType = jQuery('#segment-type').val();
            
            if (!searchValue && !searchType)
            {
                jQuery('.swaptify-segment-selector').show();
                return;
            }
            
            jQuery('.swaptify-segment-selector').each(function(){
                let name = jQuery(this).find('.segment-name').html().toLowerCase();
                let type = jQuery(this).data('type');
                
                if (searchType == '')
                {
                    if (name.includes(searchValue))
                    {
                        jQuery(this).show();
                    }
                    else 
                    {
                        jQuery(this).hide();    
                    }
                }
                else
                {
                    if (type == searchType && name.toLowerCase().includes(searchValue))
                    {
                        jQuery(this).show();
                    }
                    else 
                    {
                        jQuery(this).hide();    
                    }
                }
               
            });
    }

})();

class SwaptifyMCE 
{
    static addLoader(selector)
    {
        let loaderHtml = '<div id="overlay"><div class="text-center"><div class="spinner-border"><span>loading...</span></div></div></div>';
        jQuery(selector).append(loaderHtml);
    }
    
    static removeLoader(selector)
    {
        jQuery(selector).find('#overlay').remove();
    }
}