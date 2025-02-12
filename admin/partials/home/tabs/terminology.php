<?php

/**
 * Home page tab for Terminology
 *
 * @link       swaptify.com
 * @since      1.0.0
 *
 * @package    Swaptify
 * @subpackage swaptify/admin/partials/home/tabs
 */
?>
<h3>Terminology</h3>

<p>Below you'll find the definition of some words and phrases used in the the Swaptify ecosystem.</p>

<h4>Property</h4>
<blockquote>A Property is your domain/website. You can have multiple Properties in your Swaptify account, but only one property can be connected to this plugin.</blockquote>

<h4>Segment</h4>
<blockquote>    
    A Segment is a part of your website such as a header, image or block of text. Segments contain content that is "swapped" based on your preferences.
    <br />
    A page or post can have multiple Segments.
</blockquote>

<h4>Swap</h4>
<blockquote>
    A Swap is the content contained within a Segment. Each Segment can have multiple Swaps, but only one will be displayed. For example, a Segment could contain two Swaps — a picture of a dog versus a picture of a cat. Visitor Types and Rules will be used to determine whether the Segment will show the picture of the cat or the dog. 
</blockquote>

<h4>Visitor Type</h4>
<blockquote>A Visitor Type is a classification of a visitor. You can define however many Visitor Types you want. A visitor can be assigned to multiple Visitor Types.</blockquote>

<h4>Rules</h4>
<blockquote>Rules are sets of conditions used to determine which Swaps to show for given Segments. When there is a conflict between Visitor Types and Rules, Rules will override the Visitor Type. Rules can be made and edited on <a href="<?= Swaptify::$url ?>/rules" target="_blank"><?= Swaptify::$prettyUrl ?></a></blockquote>

<h4>Default Content</h4>
<blockquote>
    Default Content is a Swap for each Segment that will be used when either a determination cannot be made what to show or there is some delay with getting a response from the Swaptify server.
    <br />
    Default Content is used to ensure there is no interuption for your users.
    <br /><br />
    <em>Note: you'll need to update your Default Content on WordPress, since the content is stored on WordPress. You can update it under <a href="<?= admin_url('admin.php?page=swaptify-default-content') ?>">Swaptify > Default Content</a>.</em>
</blockquote>

<h4>Event</h4>
<blockquote>
    Events are conversion actions such as clicked a link or submitted a form. These events are used to determine the effectiveness of Swaps by attributing the Swaps that were seen to the Event outcome. In order to correctly attribute the influence of Swaps, Swaptify resets the user Session when an Event is triggered. Please note that because of this difference in how Swaptify handles Sessions, the number of Sessions that you see on Swaptify will likely be higher than the number of Sessions you see on other analytics software such as GA4.
</blockquote>