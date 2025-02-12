<?php

/**
 * Form field for showing visitor types when editing a page
 *
 * @link       swaptify.com
 * @since      1.0.0
 *
 * @package    Swaptify
 * @subpackage swaptify/admin/partials/visitor-types/elements
 */
?>
<select name="swap_visitor_type">
    <option value="">&mdash; Select Visitor Type &mdash;</option>
    <?php foreach ($visitor_types as $key => $object): ?>   
        <option 
            value="<?= $key ?>"
            <?php if ($selected == $key): ?>
                selected="selected"
            <?php endif; ?>
        >
            <?= $object->name ?>
        </option>
    <?php endforeach; ?>
</select>