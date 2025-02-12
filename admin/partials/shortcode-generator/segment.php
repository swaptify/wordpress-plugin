<style>
    .swaptify-segment-form-left 
    {
        width: 25%;
        background: #E5E5E5;
    }
    
    .swaptify-segment-form-right 
    {
        width: 75%;
        background: #DCDCDC;
    }
    .swaptify-segment-form-left,
    .swaptify-segment-form-right {
        float:left;
        padding: 10px;
        box-sizing: border-box;
    }
    
    .swap-preview-image {
        max-width: 300px;
        vertical-align: middle;
    }
    .swap-preview-link span {
        vertical-align: text-top;
        text-decoration: none;
    }
    .swap-preview-link a {
        text-decoration: none;
    }
    .swap-preview-link {
        margin-left: 10px;
        text-decoration: none;
    }
    .swap-div {
        margin-bottom: 30px; 
        background:#DFDFDF; 
        display:flex;
    }
    
    .button.remove,
    .button.delete {
        margin: 10px 0;
        background:rgba(225,0,0,0.7);
    }
    .button.remove, 
    .button.remove:hover, 
    .button.remove:enabled, 
    .button.remove:focus, 
    .button.remove:active,
    .button.delete, 
    .button.delete:hover, 
    .button.delete:enabled, 
    .button.delete:focus, 
    .button.delete:active {
        color: white;
        border-color: rgb(255, 0, 0);
    }
    .button.remove:active, .button.remove:focus,
    .button.delete:active, .button.delete:focus {
        background:rgba(225,0,0,0.8);
    }
    .button.remove:hover,
    .button.delete:hover {
        background:rgba(225,0,0,0.6);
    }
</style>

<div class="wrap"> <!-- wrapper -->
<div>
    <a href="?page=swaptify-shortcode-generator">&lt;&lt; back</a>
</div>
<input type="hidden" id="segment_type" value="<?php echo($segment->type); ?>" />

<?php 
/**
 * create a default editor to grab the tinyMCE and quicktag settings below
 */
if ($segment->type == 'text'): ?>
    <div style="display:none;">
        <?php wp_editor('', 'DEFAULTEDITOR'); ?>
    </div>
    <p class="wrap-content">
        Your Swap content may contain HTML, JavaScript (inside &lt;script&gt; tags), CSS (inside &lt;style&gt; tags), and shortcodes.
    </p>
<?php endif; ?>

<form method="POST" id="edit-swap-form" action="/wp-admin/admin-post.php">
    <div>
        <h3>Edit <?php echo(htmlentities($segment->name)); ?></h3>
    </div>
    
    <?php if (!$segment->swaps): ?>
        <h4 style="margin: 40px 0; text-align: center;">No Swaps yet!</h4>
    <?php endif; ?>
    
    <?php foreach ($segment->swaps as $swap): ?>
        <div class="swap-div" data-swap_key="<?php echo($swap->key); ?>">
            <div class="swaptify-segment-form-left">
                <label for="swap_name_<?php echo($swap->key); ?>">Swap Name:</label> 
                <input 
                    type="text" 
                    name="swap_name[<?php echo($swap->key); ?>]" 
                    id="swap_name_<?php echo($swap->key); ?>" 
                    value="<?php echo(htmlentities($swap->name)); ?>" 
                    size="40"  
                    required="required" 
                />
                <br />
                
                <div>
                    <label for="publish-<?php echo($swap->key); ?>">
                        <input 
                            type="checkbox"
                            id="publish-<?php echo($swap->key); ?>"
                            name="publish[<?php echo($swap->key); ?>]"
                            <?php if ($swap->published): ?>
                                checked="checked"
                            <?php endif; ?>
                        />
                        Publish
                    </label>    
                </div>
                
                <div>
                    <label for="active-<?php echo($swap->key); ?>">
                        <input 
                            type="checkbox"
                            id="active-<?php echo($swap->key); ?>"
                            name="active[<?php echo($swap->key); ?>]"
                            <?php if ($swap->active): ?>
                                checked="checked"
                            <?php endif; ?>
                        />
                        Active
                    </label>    
                </div>
                
                <div>
                    <label for="default-<?php echo($swap->key); ?>">
                        <input 
                            type="radio"
                            id="default-<?php echo($swap->key); ?>"
                            name="default"
                            value="<?php echo($swap->key); ?>"
                            <?php if ($swap->is_default): ?>
                                checked="checked"
                            <?php endif; ?>
                        />
                        Set as Default
                    </label>    
                </div>
                
                <?php if ($visitor_types): ?>
                <div class="visitor-types">
                    <h3>Visitor Types</h3>
                    <?php foreach ($visitor_types as $visitor_type): ?>
                        <div style="margin:5px;">
                            <label for="<?php echo($visitor_type->key); ?>-<?php echo($swap->key); ?>">
                                <input 
                                    type="checkbox" 
                                    id="<?php echo($visitor_type->key); ?>-<?php echo($swap->key); ?>"
                                    name="visitor_type[<?php echo($visitor_type->key); ?>][<?php echo($swap->key); ?>]"
                                    <?php 
                                        $visitorTypeKey = $visitor_type->key;
                                        if (isset($swap->visitor_types->$visitorTypeKey)): ?>
                                        checked="checked"
                                    <?php endif; ?>
                                />
                                <?php echo($visitor_type->name); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="visitor-type-notice">
                <p>Visitor Types are not associated with Default Content.</p>
                </div>
                <?php endif; ?>
                <a href="javascript:void(0);" class="button delete">Delete Swap</a>
            </div>
            <div class="swaptify-segment-form-right">
                <?php if ($segment->type == 'text'): ?>
                    <?php wp_editor($swap->content, 'content-' . $swap->key); ?><br />
                <?php elseif ($segment->type == 'url'): ?>
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="swap_content_<?php echo($swap->key); ?>">URL</label>
                                </th>
                                <td>
                                    <input 
                                        type="text" 
                                        id="swap_content_<?php echo($swap->key); ?>"
                                        name="content-<?php echo($swap->key); ?>" 
                                        value="<?php echo(htmlentities($swap->content)); ?>" 
                                        size="40"
                                        required="required"
                                    />
                                    <span class="swap-preview-link" id="swap_preview_<?php echo($swap->key); ?>">
                                        <a target="_blank" href="<?php echo($swap->content); ?>" title="Preview Link">
                                            preview 
                                            <span class="dashicons dashicons-external"></span>
                                        </a>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="swap_subcontent_<?php echo($swap->key); ?>">Link Text</label>
                                </th>
                                <td>
                                    <input 
                                        type="text" 
                                        id="swap_subcontent_<?php echo($swap->key); ?>"
                                        name="sub_content[<?php echo($swap->key); ?>]" 
                                        value="<?php echo(htmlentities(($swap->sub_content ?? ''))); ?>" 
                                        size="40"
                                        required="required"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                <?php elseif ($segment->type == 'image'): ?>
                    <div>
                        <img class="swap-preview-image" id="swap_image_<?php echo($swap->key); ?>" src="<?php echo($swap->content); ?>" />
                        <span class="swap-preview-link" id="swap_preview_<?php echo($swap->key); ?>">
                            <a target="_blank" href="<?php echo($swap->content); ?>" title="Preview Image">
                                preview 
                                <span class="dashicons dashicons-external"></span>
                            </a>
                        </span>
                    </div>
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="swap_content_<?php echo($swap->key); ?>">Image URL</label>
                                </th>
                                <td>
                                    <input 
                                        type="text" 
                                        id="swap_content_<?php echo($swap->key); ?>"
                                        name="content-<?php echo($swap->key); ?>" 
                                        value="<?php echo(htmlentities($swap->content)); ?>" 
                                        size="80"
                                        required="required"
                                    /> 
                                </td>
                                
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="swap_subcontent_<?php echo($swap->key); ?>">Alt Text</label>
                                </th>
                                <td>
                                    <input 
                                        type="text" 
                                        id="swap_subcontent_<?php echo($swap->key); ?>"
                                        name="sub_content[<?php echo($swap->key); ?>]" 
                                        value="<?php echo(htmlentities(($swap->sub_content ?? ''))); ?>" 
                                        size="40"
                                    />
                                </td>
                            </tr>
                            <tr id="swap_sizes_div_<?php echo($swap->key); ?>" style="display:none;">
                                <th scope="row">
                                    <label for="swap_sizes_<?php echo($swap->key); ?>">Size</label>
                                </th>
                                <td>
                                    <select id="swap_sizes_<?php echo($swap->key); ?>">
                                        
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="swaptify-media-library-edit-button" data-swap_key="<?php echo($swap->key); ?>">
                        <a href="javascript:void(0);">Add from Media Library</a>
                    </div> 
                <?php endif; ?> 
                
            </div>
            <div style="clear:both;"></div>
        </div>
    <?php endforeach; ?>
    <div id="new-swaps"></div>
    <a href="javascript:void(0);" class="button" id="add-new-swap-button">Add new Swap</a>
    <?php submit_button('Save'); ?>  
    <input type="hidden" name="segment_key" value="<?php echo($segment->key); ?>" />
    <input type="hidden" name="action" value="save_swaptify_segment" />
</form> 

<div id="new-swap-field" style="display:none";>
    <div class="swaptify-segment-form-left">
        <label for="swap_name_">Swap Name:</label> 
        <input 
            type="text" 
            name="swap_name[]" 
            id="swap_name_" 
            value="" 
            size="40"  
            required="required" 
        />
        <br />
        
        <div>
            <label for="publish-">
                <input 
                    type="checkbox"
                    id="publish-"
                    name="publish[]"
                />
                Publish
            </label>    
        </div>
        
        <div>
            <label for="active-">
                <input 
                    type="checkbox"
                    id="active-"
                    name="active[]"
                />
                Active
            </label>    
        </div>
        
        <div>
            <label for="default-">
                <input 
                    type="radio"
                    id="default-"
                    name="default"
                    value=""
                />
                Set as Default
            </label>    
        </div>
        
        <?php if ($visitor_types): ?>
        <div class="visitor-types">
            <h3>Visitor Types</h3>
            <?php foreach ($visitor_types as $visitor_type): ?>
                <div style="margin:5px;" class="visitor_type_input" data-visitor_type_key="<?php echo($visitor_type->key); ?>">
                    <label for="<?php echo($visitor_type->key); ?>-">
                        <input 
                            type="checkbox" 
                            id="<?php echo($visitor_type->key); ?>-"
                            name="visitor_type[<?php echo($visitor_type->key); ?>][]"
                        />
                        <?php echo($visitor_type->name); ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="visitor-type-notice">
            <p>Visitor Types are not associated with Default Content.</p>
        </div>
        <?php endif; ?>
        
        <a href="javascript:void(0);" class="button remove">Remove</a>
    </div>
    <div class="swaptify-segment-form-right">
        <?php  if ($segment->type == 'text'): ?>
            <textarea></textarea>
        <?php elseif ($segment->type == 'url'): ?>
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="swap_content_">URL</label>
                        </th>
                        <td>
                            <input 
                                type="text" 
                                id="swap_content_"
                                name="content-" 
                                value="" 
                                size="40"
                                required="required"
                            />
                            <span class="swap-preview-link" id="swap_preview_">
                                <a target="_blank" href="" title="Preview Link">
                                    preview 
                                    <span class="dashicons dashicons-external"></span>
                                </a>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="swap_subcontent_">Link Text</label>
                        </th>
                        <td>
                            <input 
                                type="text" 
                                id="swap_subcontent_"
                                name="sub_content_" 
                                value="" 
                                size="40"
                                required="required"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php elseif ($segment->type == 'image'): ?>
            <div>
                <img class="swap-preview-image" id="swap_image_" src="" />
                <span class="swap-preview-link" id="swap_preview_">
                    <a target="_blank" href="" title="Preview Image">
                        preview 
                        <span class="dashicons dashicons-external"></span>
                    </a>
                </span>
            </div>
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="swap_content_">Image URL</label>
                        </th>
                        <td>
                            <input 
                                type="text" 
                                id="swap_content_"
                                name="content-" 
                                value="" 
                                size="80"
                                required="required"
                            /> 
                        </td>
                        
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="swap_subcontent_">Alt Text</label>
                        </th>
                        <td>
                            <input 
                                type="text" 
                                id="swap_subcontent_"
                                name="sub_content_" 
                                value="" 
                                size="40"
                            />
                        </td>
                    </tr>
                    <tr id="swap_sizes_div_" style="display:none;">
                        <th scope="row">
                            <label for="swap_sizes_">Size</label>
                        </th>
                        <td>
                            <select id="swap_sizes_">
                                
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="swaptify-media-library-edit-button" data-swap_key="">
                <a href="javascript:void(0);">Add from Media Library</a>
            </div> 
        <?php endif; ?> 
        
    </div>
    <div style="clear:both;"></div>
</div>

<form id="delete-swap-form" action="/wp-admin/admin-post.php" method="POST" style="display:none;">
    <input type="hidden" name="swap" value="" />
    <input type="hidden" name="action" value="delete_swaptify_swap" />
</form>

<!-- end wrapper --></div> 
<script>
    
    const updatePreview = (element, isNew) => {
        let type = jQuery('#segment_type').val();
        
        let previewPrefix = '#swap_preview_';
        let imagePrefix = '#swap_image_';
        
        if (isNew)
        {
            previewPrefix = '#new_swap_preview_';
            imagePrefix = '#new_swap_image_';
        }
        
        
        let name = element.attr('name');
        let key = null;
        
        if (name)
        {
            key = name.split('-')[1];
        }
        
        let value = element.val();
        if (key && jQuery(previewPrefix + key).length)
        {
            jQuery(previewPrefix + key).find('a').attr('href', value);
            if (type == 'image')
            {
                jQuery(imagePrefix + key).attr('src', value);
            }
        }
    }
    
    let submit = false;
    
    const beforeUnloadHandler = (event) => {
        // Recommended
        event.preventDefault();

        // Included for legacy support, e.g. Chrome/Edge < 119
        event.returnValue = true;
    };
    
    let newSwapId = 1;
    
    jQuery(function(){
        
        jQuery('#add-new-swap-button').on('click', function(e){
            
            let type = jQuery('#segment_type').val();
            e.preventDefault();
            
            let div = jQuery('<div>').addClass('swap-div').html(jQuery('#new-swap-field').html());
            
            /**
             * general inputs
             */
            div.find('.swaptify-segment-form-left label[for="swap_name_"]').attr('for', 'new_swap_name_' + newSwapId);
            div.find('.swaptify-segment-form-left input[name="swap_name[]"]')
                .attr('name', 'new_swap_name[' + newSwapId + ']')
                .attr('id', 'new_swap_name_' + newSwapId);
            
            div.find('.swaptify-segment-form-left label[for="publish-"]').attr('for', 'new_publish_' + newSwapId);
            div.find('.swaptify-segment-form-left input[name="publish[]"]')
                .attr('name', 'new_publish[' + newSwapId + ']')
                .attr('id', 'new_publish_' + newSwapId)
                .prop('checked', 'checked');
            
            div.find('.swaptify-segment-form-left label[for="active-"]').attr('for', 'new_active_' + newSwapId);
            div.find('.swaptify-segment-form-left input[name="active[]"]')
                .attr('name', 'new_active[' + newSwapId + ']')
                .attr('id', 'new_active_' + newSwapId)
                .prop('checked', 'checked');
            
            div.find('.swaptify-segment-form-left label[for="default-"]').attr('for', 'new_default_' + newSwapId);
            div.find('.swaptify-segment-form-left input[id="default-"]').attr('id', 'new_default_' + newSwapId).val(newSwapId);
            
            div.find('.visitor_type_input').each(function(){
                let input = jQuery(this);
                let key = jQuery(this).data('visitor_type_key');
                input.find('label').attr('for', key + '-new_swap_' + newSwapId);
                input.find('input')
                    .attr('id', key + '-new_swap_' + newSwapId)
                    .attr('name', 'new_visitor_type[' + key + '][' + newSwapId+ ']');
            });
            
            div.find('.swaptify-segment-form-right label[for="swap_content_"]').attr('for', 'new_swap_content_' + newSwapId);
            div.find('.swaptify-segment-form-right input[name="content-"]')
                .attr('name', 'new_content-' + newSwapId)
                .attr('id', 'new_swap_content_' + newSwapId)
                .on('change', function(){
                    updatePreview(jQuery(this), true);
                })
                .on('keyup', function(){
                    updatePreview(jQuery(this), true);
                });
                
            div.find('textarea').attr({
                id: 'new_content-' + newSwapId,
                name: 'new_content-' + newSwapId,
                rows: 20
            }).addClass('wp-editor-area');
            
            div.find('.swaptify-segment-form-right label[for="swap_subcontent_"]').attr('for', 'new_swap_subcontent_' + newSwapId);
            div.find('.swaptify-segment-form-right input[name="sub_content_"]')
                .attr('name', 'new_sub_content[' + newSwapId + ']')
                .attr('id', 'new_swap_subcontent_' + newSwapId);
            
            div.find('.swap-preview-image')
                .attr('id', 'new_swap_image_' + newSwapId)
                .attr('src', swaptify_image_path.swaptify_image_path + 'images/image.jpg');
                
            div.find('.swap-preview-link').attr('id', 'new_swap_preview_' + newSwapId);
            div.find('#swap_sizes_div_').attr('id', 'new_swap_sizes_div_' + newSwapId);
            
            div.find('.swaptify-media-library-edit-button').data('new_swap_key', newSwapId);
            
            div.find('.remove').on('click', function(){
                if (confirm('Are you sure you want to remove this Swap?'))
                {
                    jQuery(this).closest('.swap-div').remove();
                }
            });
            
            jQuery('#new-swaps').append(div);
            
            if (div.find('textarea'))
            {
                let content_id = 'new_content-' + newSwapId;
                
                let tinyMCESettings = tinyMCEPreInit.mceInit['DEFAULTEDITOR'];
                tinyMCESettings.selector = '#' + content_id;
                tinyMCESettings.body_class = content_id;
                tinyMCESettings.elementpath = true;
                
                let quickTagSettings = tinyMCEPreInit.qtInit['DEFAULTEDITOR'];
                quickTagSettings.id = content_id;
                
                wp.editor.initialize(content_id, {
                    tinymce: tinyMCESettings, 
                    quicktags: quickTagSettings, 
                    mediaButtons: true
                });    
            }
            
            jQuery('#new_swap_name_' + newSwapId).focus();
            
            newSwapId++;
            
            window.addEventListener("beforeunload", beforeUnloadHandler);
        });
        
        jQuery('form').find('.delete').on('click', function(){
                if (confirm('Are you sure you want to delete this Swap? This cannot be undone'))
                {
                    let form = jQuery('#delete-swap-form');
                    let swap_key = jQuery(this).closest('.swap-div').data('swap_key');
                    form.find('input[name="swap"]').val(swap_key);
                    form.submit();
                }
            });
        
        jQuery('input, textarea, select').on('change', function(){
            window.addEventListener("beforeunload", beforeUnloadHandler);
        });
        
        jQuery('input, textarea').on('keyup', function(){
            window.addEventListener("beforeunload", beforeUnloadHandler);
        });
        
        
        jQuery('#edit-swap-form').on('submit', function(){
            window.removeEventListener("beforeunload", beforeUnloadHandler);
        });
        
        jQuery('input[name^="content-"]').on('change', function(){
            updatePreview(jQuery(this), false);
        });
        
        jQuery('input[name^="content-"]').on('keyup', function(){
            updatePreview(jQuery(this), false);
        });
        
        jQuery(document).on('click','.swaptify-media-library-edit-button > a', function(e) 
        {
            jQuery('[id^=swap_sizes_div_]').hide();
            jQuery('[id^=new_swap_sizes_div_]').hide();
            
            let  swap_key = jQuery(this).closest('.swaptify-media-library-edit-button').data('swap_key');
            let new_swap = false;
            
            if (!swap_key)
            {
                swap_key = jQuery(this).closest('.swaptify-media-library-edit-button').data('new_swap_key');
                if (swap_key)
                {
                    new_swap = true;
                }
            }
            
            
            let imagePrefix = '#swap_image_';
            let previewPrefix = '#swap_preview_';
            let contentPrefix = '#swap_content_';
            let subcontentPrefix = '#swap_subcontent_';
            let sizesPrefix = '#swap_sizes_div_';
            
            if (new_swap)
            {
                imagePrefix = '#new_swap_image_';
                previewPrefix = '#new_swap_preview_';
                contentPrefix = '#new_swap_content_';
                subcontentPrefix = '#new_swap_subcontent_';
                sizesPrefix = '#new_swap_sizes_div_';
            }
            
            let frame;
            
            e.preventDefault();
            // If the upload object has already been created, reopen the dialog
            if (frame) 
            {
                frame.open();
                return;
            }
            
            // Extend the wp.media object
            frame = wp.media.frames.file_frame = wp.media({
                title: 'Select media',
                button: {
                    text: 'Select media'
                }, 
                multiple: false 
            });

            // When a file is selected, grab the URL and set it as the text field's value
            frame.on('select', function() {
                let selection = frame.state().get('selection');
                selection.map(function(attachment)
                {
                    // new-swap-name
                    // new-swap-content
                    // new-swap-sub_content
                    let attachmentObject = attachment.toJSON();
                    
                    let element = jQuery('#swap-form-inputs-div');
                    
                    if (attachmentObject.sizes.thumbnail)
                    {
                        jQuery(imagePrefix + swap_key).attr({'src': attachmentObject.sizes.thumbnail.url});
                    }
                    
                    const value = attachmentObject.sizes.full.url;
                    
                    jQuery(contentPrefix + swap_key).val(value);
                    jQuery(subcontentPrefix + swap_key).val(attachmentObject.title);
                    jQuery(contentPrefix + swap_key).trigger('change');
                    
                    
                    let select = jQuery(sizesPrefix + swap_key + ' select');
                    select.empty();
                    
                    for (const size of Object.keys(attachmentObject.sizes))
                    {
                        const option = jQuery('<option>').val(attachmentObject.sizes[size].url).html(size);
                        
                        if (attachmentObject.sizes[size].url == value)
                        {
                            option.attr('selected', 'selected');
                        }
                        
                        select.append(option);   
                    }
                    
                    
                    select.off('change');
                    select.on('change', function(){
                        jQuery(contentPrefix + swap_key).val(jQuery(this).val());
                        jQuery(contentPrefix + swap_key).trigger('change');
                    });
                    
                    jQuery(sizesPrefix + swap_key).show();
                    
                });
            });
            
            // Open the upload dialog
            frame.open();
        }); 

        //control visitor type visibility based on default selection
        jQuery('#add-new-swap-button').click(function(){ //first swap force to default
            if (jQuery('.swap-div').length === 1) {
                jQuery('.swap-div').addClass('default');
                jQuery('.swap-div label[for*="default"] > input').prop("checked", true);
            }
        });
        
        jQuery('label[for*="default"] > input:checked').closest('.swap-div').addClass('default'); //hide visitor types for current default

        jQuery('label[for*="default').click(function(){ //allow default switching
            jQuery('label[for*="default"] > input').closest('.swap-div').removeClass('default');
            jQuery('label[for*="default"] > input:checked').closest('.swap-div').addClass('default');
        });
  });
</script>