<?php

/**
 * Visitor Types page
 *
 * @link       swaptify.com
 * @since      1.0.0
 *
 * @package    Swaptify
 * @subpackage swaptify/admin/partials/visitor-types
 */
?>
<div class="wrap">
                <div id="icon-themes" class="icon32"></div>  
                <h2>Swaptify Visitor Types</h2>  

                <p class="content-wrapper">Visitor types allow you to tag Swaps for displaying to certain users based on the behaviors. For example, if you run a pet store website, you could have "Cat Owners" and "Dog Owners" as Vistor Types. You would then need to create triggers, based on user behavior, to set the Visitor Type. Once set, Swaptify will serve either the Cat Swaps or Dog Swaps, depending on the user's current Visitor Type.
                <br />
                <br />
                Use the tools below to define your Visitor Types and then create your triggers using the code samples provided. In addition to controlling Swaps, the Visitor Type Body Class is a useful tool for restyling certain elements based on the active Visitor Type. Keep in mind, a visitor can only be assigned a single vistor type at any given time. In the case of our pet store example, a visitor who owns both dogs and cats might trigger both visitor types, but they will be served swaps based on the most recent visitor type triggered. When the Visitor Type changes, the Swaps will change in response.
                </p>
  
                <?php settings_errors(); ?>
                                
                <?php if ($visitor_types): ?>
                    <table border="1" cellpadding="20" width="100%">
                        <thead>
                            <tr>
                                <th>Visitor Type</th>
                                <th>Swaptify Key</th>
                                <th>Body Class</th>
                                <th>Code Sample</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($visitor_types as $visitor_type): ?>
                                <tr>
                                    <td>
                                        <?php echo($visitor_type->name); ?>
                                    </td>
                                    <td><?php echo($visitor_type->key); ?></td>
                                    <td><?php echo(Swaptify::$slugPrefix . $visitor_type->slug); ?></td>
                                    <td>
                                            <h3>Trigger on a Click</h3>
                                            <p>
                                                Add the class <code>swaptify-visitor-type-click-<?php echo($visitor_type->key); ?></code> to a clickable element
                                            </p>
                                            <p><strong>Example:</strong></p>
                                            <pre><code>&lt;a class="swaptify-visitor-type-click-<?php echo($visitor_type->key); ?>"&gt;...&lt;/a&gt;</code></pre>
                                            
                                            <div>&mdash; OR &mdash;</div>
                                            
                                            <p>
                                                Add the class <code>swaptify-visitor-type</code> 
                                                and the data attribute <code>data-swaptify_key</code> 
                                                with a value of <code><?php echo($visitor_type->key); ?></code> 
                                                to a clickable element
                                            </p>
                                            <p><strong>Example:</strong></p>
                                            <pre><code>&lt;a class="swaptify-visitor-type" data-swaptify_key="<?php echo($visitor_type->key); ?>"&gt;...&lt;/a&gt;</code></pre>
                                           
                                            <h3>Trigger on a Form Submission</h3>                                        
                                            <p>
                                                Add the class <code>swaptify-visitor-type-submit-<?php echo($visitor_type->key); ?></code> to a form
                                            </p>
                                            <p><strong>Example:</strong></p>
                                            <pre><code>&lt;form class="swaptify-visitor-type-submit-<?php echo($visitor_type->key); ?>"&gt;...&lt;/form&gt;</code></pre>
                                            
                                            <div>&mdash; OR &mdash;</div>
                                            
                                            <p>
                                                Add the class <code>swaptify-visitor-type</code> 
                                                and the data attribute <code>data-swaptify_key</code> 
                                                with a value of <code><?php echo($visitor_type->key); ?></code> 
                                                to a form
                                            </p>
                                            <p><strong>Example:</strong></p>
                                            <pre><code>&lt;form class="swaptify-visitor-type" data-swaptify_key="<?php echo($visitor_type->key); ?>"&gt;...&lt;/form&gt;</code></pre>
                                            <div>&mdash; OR &mdash;</div>
                                            
                                            <h3>Trigger on Pageview, Tag Manager, or Custom JS</h3>                                        
                                            <p>Use the follow tag to set the visitor type. This can be set on pageview by pasting the tag into an HTML block on the desired page. It can also be used as a tag inside Google Tag Manager or called from any custom JS event listener or behavior detection script.</p>
<pre><code>&lt;script&gt;
    SwaptifyWP.visitor_type('<?php echo($visitor_type->key); ?>');
&lt;/script&gt;</code></pre>
                                            
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div> There are no visitor types</div>
                <?php endif; ?>

                <form method="POST" action="/wp-admin/admin-post.php">  
                    <?php 
                        settings_fields('swaptify_visitor_types');
                        do_settings_sections('swaptify_visitor_types'); 
                    ?>             
                    <?php submit_button('Add New Visitor Type'); ?>
                    <input type="hidden" name="action" value="add_new_visitor_type" />
                </form> 
</div>