<?php

/**
 * Configuration page
 *
 * @link       swaptify.com
 * @since      1.0.0
 *
 * @package    Swaptify
 * @subpackage swaptify/admin/partials/configuration
 */
?>
<div class="wrap">
                <div id="icon-themes" class="icon32"></div>  
                <h2>Swaptify Settings</h2>  
                <?php settings_errors(); ?> 
                
                <p>Connect your Swaptify Account by adding your API Access Token and selecting a property.</p>
                <p>You can find your API Access Token <a href="<?php echo(Swaptify::$url); ?>/account/api" target="_blank">here</a>.</p>
                <p>You can turn Swaptify off at any time by setting "Enabled" to "No". All Segments will show Default Content.</p>
                
                <form method="POST" action="options.php" id="swaptify_config_form">  
                    <?php 
                        settings_fields('swaptify_configuration_settings');
                        do_settings_sections('swaptify_configuration_settings'); 
                    ?>    
                    <?php if ($propertySet): ?>  
                        <div>
                            <label for="swaptify_confirm_property_change">
                                <input type="checkbox" id="swaptify_confirm_property_change"/> 
                                Confirm changing property? Changing the property will have adverse affects due on any existing swaptify data 
                            </label>
                        </div>
                    <?php endif; ?>
                    <?php submit_button(); ?>  
                </form> 
                
                <?php if ($propertySet): ?>
                <script>
                    jQuery(document).ready(function(){ 
                        /**
                         * remove the default value
                         */
                        jQuery('select[name="swaptify_property_key"] option[value=""]').remove();
                        jQuery('select[name="swaptify_property_key"]').prop('disabled', true);
                        jQuery('#swaptify_confirm_property_change').on('change', function(){
                            if (jQuery(this).is(':checked'))
                            {
                                jQuery('select[name="swaptify_property_key"]').prop('disabled', false);
                            }
                            else
                            {
                                jQuery('select[name="swaptify_property_key"]').prop('disabled', true);
                            } 
                        });
                        
                        jQuery('#swaptify_config_form').on('submit', function(){
                            
                            const input = jQuery('<input>').attr('type', 'hidden').prop('name', 'swaptify_property_key').val(jQuery('select[name="swaptify_property_key"]').val());
                            
                            jQuery(this).append(input);
                            
                            return true;
                        });
                    });
                </script>
                <?php endif; ?>
</div>