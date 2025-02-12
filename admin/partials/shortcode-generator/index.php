<?php

/**
 * Shortcode Generator page
 *
 * @link       swaptify.com
 * @since      1.0.0
 *
 * @package    Swaptify
 * @subpackage swaptify/admin/partials/shortcode-generator
 */
?>
<style>
    .create-segment-form {
        margin-top: 20px;
    }
</style>
<div class="wrap">
    <div id="icon-themes" class="icon32"></div>  
    <h2>Swaptify Shortcode Generator</h2>  
    <p class="content-wrapper">This interface will allow you to create Segements and Swaps, assign Swaps to Visitor Types, and assign Default Content, all from the familiar WordPress classic editor. These Segments and Swaps will be synced with your Swaptify account.
    </p>
    <?php settings_errors(); ?>
    <table class="max-width" border="1" style="width: 100%">
        <thead>
            <th>Segment Name</th>
            <th>Segment Key</th>
            <th>Shortcode</th>
            <th>Type</th>
            <th>Swaps</th>
            <th>Edit</th>
            <th>Delete</th>
        </thead>
        <tbody>
                
            <?php foreach($segments as $key => $segment): ?>
                <tr>
                    <td><a href="?page=swaptify-shortcode-generator&key=<?= $segment->key ?>"><?= $segment->name ?></a></td>
                    <td><?= $key ?></td>
                    <td><?= Swaptify::generateDisplayShortcode($segment->type, $segment->key) ?></td>
                    <td><?= $segment->type ?></td>
                    <td>
                        <?php foreach($segment->swaps as $swap_key => $swap): ?>
                            <?= $swap->name ?><br />    
                        <?php endforeach; ?>
                    </td>
                    <td><a href="?page=swaptify-shortcode-generator&key=<?= $segment->key ?>">edit</a></td>
                    <td><a target="_blank" href="<?php Swaptify::$url ?>/segments/<?= $segment->key ?>/edit">delete</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <form method="POST" id="create-segment-form" action="/wp-admin/admin-post.php">
        <h3>Create New Segment</h3>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="segment_name">Name</label>
                    </th>
                    <td>
                        <input 
                            type="text" 
                            id="segment_name"
                            name="name" 
                            size="40"
                            required="required"
                        />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="type">Type</label>
                    </th>
                    <td>
                    <select name="type" required="required" width="40">
                        <option value="">&mdash; Select Type &mdash;</option>
                        <?php foreach($types as $type): ?>
                            <option value="<?= htmlentities($type->id) ?>"><?= htmlentities($type->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <input type="hidden" name="action" value="create_swaptify_segment" />
        <?php submit_button('Create New Segment'); ?>
    </form>
</div>