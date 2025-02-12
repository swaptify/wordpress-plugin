<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/swaptify
 * @since      1.0.0
 *
 * @package    Swaptify
 * @subpackage Swaptify/admin/partials
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
<h2>Swaptify Overview</h2>  
    <nav class="nav-tab-wrapper">
    <?php foreach ($tabs as $tab): ?>
        <a href="?page=swaptify&tab=<?= $tab['url'] ?>" class="nav-tab <?= ($tab['active'] ? 'nav-tab-active' : '')?>"><?= $tab['name'] ?></a>
    <?php endforeach; ?>
        <!-- <a href="?page=swaptify" class="nav-tab <?php if($tab===null):?>nav-tab-active<?php endif; ?>">About</a>
    <a href="?page=swaptify&tab=terminology" class="nav-tab <?php if($tab==='terminology'):?>nav-tab-active<?php endif; ?>">Terminology</a>
    <a href="?page=swaptify&tab=tools" class="nav-tab <?php if($tab==='tools'):?>nav-tab-active<?php endif; ?>">Other?</a> -->
    </nav>
    <div class="tab-content">
        <?php include_once($path); ?>
    </div>
</div>