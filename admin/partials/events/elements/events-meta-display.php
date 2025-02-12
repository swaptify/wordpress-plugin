<?php

/**
 * Form field for showing page view events when editing a page
 *
 * @link       swaptify.com
 * @since      1.0.0
 *
 * @package    Swaptify
 * @subpackage swaptify/admin/partials/events/elements
 */
?>
<?php foreach ($events as $key => $object): ?>   
    <?php $swap_event_id = 'swap-' . $key; ?>
    <div>
        <label for="<?php echo($swap_event_id); ?>">
            <input id="<?php echo($swap_event_id); ?>" type="checkbox" name="swap_events[]" value="<?php echo($key); ?>" <?php echo((isset($object->checked) && $object->checked) ? 'checked="checked"':''); ?> />
            <?php echo($object->name); ?>
        </label>
    </div>
<?php endforeach;
