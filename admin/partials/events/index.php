<?php

/**
 * Events page
 *
 * @link       swaptify.com
 * @since      1.0.0
 *
 * @package    Swaptify
 * @subpackage swaptify/admin/partials/events
 */
?>
<div class="wrap">
                <div id="icon-themes" class="icon32"></div>  
                <h2>Swaptify Event Settings</h2>  
                <p class="content-wrapper">Similar to Visitor Types, your conversion events are fired based on Triggers. Use the tools below to define your conversion events, and then create your triggers using the code samples provided. Conversion events are used to determine the effectiveness of your Swaps to help you continually optimize your personalized website content for maximum conversions.
                </p>
                <?php settings_errors(); ?>
                                
                <?php if ($events): ?>
                    <table border="1" cellpadding="20" width="100%">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Type</th>
                                <th>Swaptify Key</th>
                                <th>Code Sample</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $event): ?>
                                <tr>
                                    <td>
                                        <?php echo($event->name); ?>
                                    </td>
                                    <td>
                                        <?php echo($event->type_name); ?>
                                    </td>
                                    <td><?php echo($event->key); ?></td>
                                    <td>
                                        <?php if ($event->type == 'click'): ?>
                                            <p>
                                                Add the class <code>swaptify-event-click-<?php echo($event->key); ?></code> to a clickable element
                                            </p>
                                            <p><strong>Example:</strong></p>
                                            <pre><code>&lt;a class="swaptify-event-click-<?php echo($event->key); ?>"&gt;...&lt;/a&gt;</code></pre>
                                            
                                            <div>&mdash; OR &mdash;</div>
                                            
                                            <p>
                                                Add the class <code>swaptify-event</code> 
                                                and the data attribute <code>data-swaptify_key</code> 
                                                with a value of <code><?php echo($event->key); ?></code> 
                                                to a clickable element
                                            </p>
                                            <p><strong>Example:</strong></p>
                                            <pre><code>&lt;a class="swaptify-event" data-swaptify_key="<?php echo($event->key); ?>"&gt;...&lt;/a&gt;</code></pre>
                                            <div>&mdash; OR &mdash;</div>
                                        <?php elseif ($event->type == 'form_submitted'): ?>
                                            <h3>Trigger the Form Submission</h3>                                        
                                            <p>
                                                Add the class <code>swaptify-event-submit-<?php echo($event->key); ?></code> to a form
                                            </p>
                                            <p><strong>Example:</strong></p>
                                            <pre><code>&lt;form class="swaptify-event-submit-<?php echo($event->key); ?>"&gt;...&lt;/form&gt;</code></pre>
                                            
                                            <div>&mdash; OR &mdash;</div>
                                            
                                            <p>
                                                Add the class <code>swaptify-event</code> 
                                                and the data attribute <code>data-swaptify_key</code> 
                                                with a value of <code><?php echo($event->key); ?></code> 
                                                to a form
                                            </p>
                                            <p><strong>Example:</strong></p>
                                            <pre><code>&lt;form class="swaptify-event" data-swaptify_key="<?php echo($event->key); ?>"&gt;...&lt;/form&gt;</code></pre>
                                            <div>&mdash; OR &mdash;</div>
                                        <?php else: ?>
                                        <?php endif; ?>
                                        <p>Use the follow script to recognize the event</p>
<pre><code>&lt;script&gt;
    SwaptifyWP.event('<?php echo($event->key); ?>');
&lt;/script&gt;</code></pre>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div> There are no events</div>
                <?php endif; ?>

                <form method="POST" action="/wp-admin/admin-post.php">  
                    <?php 
                        settings_fields('swaptify_event_settings');
                        do_settings_sections('swaptify_event_settings'); 
                    ?>             
                    <?php submit_button('Add New Event'); ?>
                    <input type="hidden" name="action" value="add_new_event" />
                </form> 
</div>