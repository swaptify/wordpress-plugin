<?php

/**
 * Cookies page
 *
 * @link       swaptify.com
 * @since      1.0.0
 *
 * @package    Swaptify
 * @subpackage swaptify/admin/partials/cookies
 */
?>

<div class="wrap">
    <div id="icon-themes" class="icon32"></div>  
        <h2>Swaptify Cookie Settings</h2>
        <?php settings_errors(); ?>
        <p class="content-wrapper">On this page, you can set which cookies to send to Swaptify as part of visitor data. These cookies can then be used within your Swaptify Rules to control your Swaps. Keep in mind that case matters, i.e. "Swap" is a different cookie name than "swap."</p>
    </div>
    <?php if ($items): ?>
        <table border="1" cellpadding="20" width="100%">
            <thead>
                <tr>
                    <th>Cookie Name</th>
                    <?php /* <th>Action</th> */ ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo($item->name); ?></td>
                        <?php /*
                        <td>
                            <button>delete</button>
                        </td>
                        */ ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div> There are no cookies set.</div>
    <?php endif; ?>
    
    <form method="POST" action="/wp-admin/admin-post.php">  
        <?php 
            settings_fields('swaptify_cookies');
            do_settings_sections('swaptify_cookies'); 
        ?>             
        <?php submit_button('Add'); ?>  
        <input type="hidden" name="action" value="add_new_cookie" />
    </form> 
</div>