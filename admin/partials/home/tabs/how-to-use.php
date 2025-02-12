<?php

/**
 * Home page tab for How to Use
 *
 * @link       swaptify.com
 * @since      1.0.0
 *
 * @package    Swaptify
 * @subpackage swaptify/admin/partials/home/tabs
 */
?>
<h3>
    How to Use Swaptify
</h3>

<h4>
    Setup
</h4>
<p>
    Firstly, you'll need to connect your Swaptify account by adding your API Access Token. You can find your Token <a href="<?php echo(Swaptify::$url); ?>/account/api" target="_blank">here</a>.
</p>

<p>
    To connect your Swaptify account, add your API Access Token under <a href="<?= admin_url('admin.php?page=swaptify-configuration') ?>">Swaptify > Configuration</a>.
</p>

<br />
<h4>
    Segments and Swaps
</h4>

<p>
    Segments are parts of your site that will house swaps (content that will change). To get started, you'll need to create your first segment. We recommend starting with a simple piece of content such as welcome text on your homepage, a main call-to-action, or a key image that gets a lot of attention.
</p>

<p>
    You can create Segments and their Swaps directly from the Swaptify website (<a href="<?= Swaptify::$url ?>/segments/create" target="_blank"><?= Swaptify::$prettyUrl ?></a>), or you can create your Segments and Swaps using the Shortcode Generator feature of this plugin. The content you build with the Shortcode Generator will be automatically sent to Swaptify. We recommend starting with the Shortcode Generator because this interface will be familiar to WordPress users.
</p>

<p>
    Once you have created a shortcode for your first Segment, place the shortcode onto the page or post where you want the swappable content to appear.
</p>

<p>Swaptify will take care of the rest!</p>

<br />
<h4>
    Default Content
</h4>

<p>
    Once you have your Segments and Swaps in place, you'll want to update your Default Content. By updating your Default Content, the content will be stored on WordPress and will be used as a fallback if there is any issue connecting to Swaptify.
</p>

<p>
    You can update your Default Content under <a href="<?= admin_url('admin.php?page=swaptify-default-content') ?>">Swaptify > Default Content</a>.
</p>

<br />
<h4>
    Visitor Types
</h4>

<p>
    Visitor Types are categories of visitors you can assign with triggers such as clicks on particular elements, pageviews, or custom-coded detection scripts. When a user triggers a Visitor Type, this identification can be used to serve specific content, such as showing pictures of dogs for a Visitor Type named "Dog Lovers".
</p>

<p>
    Visitor Types are not required but are useful when you have a range of visitor interests. For most use cases, Visitor Types is the simpler way to set up personalized content. For more detailed personalization, you will use Rules instead.
</p>

<p>
    You can setup Visitor Types under <a href="<?= admin_url('admin.php?page=swaptify-visitor-types') ?>">Swaptify > Visitor Types</a>. You will also find code samples for how to trigger a Visitor Type.
</p>

<br />
<h4>
    Rules
</h4>
<p>
    Rules are sets of conditions used to determine which Swaps to display. After you've created a Segment with Swaps, you can tell Swaptify which Swaps to show based on Rules that you can configure here: <a href="<?= Swaptify::$url ?>/rules" target="_blank"><?= Swaptify::$prettyUrl?>/rules</a>. When there is a conflict between Visitor Types and Rules, Rules will override the Visitor Type. Be careful with your Rule logic so that you don't create conflicting personalization experiences.

    There is not currently a Rules interface within this plugin.
</p>

<br />
<h4>
    Events
</h4>

<p>
    A Event is a conversion objective you define for a visitor. Events can come in various types such as submitting a form or clicking a link.
</p>

<p>
    Creating Events allows you to analyze the effectiveness of Swaps and the overall user experience. When an Event occurs, Swaptify will reset the user's Session so that the conversion Event can be attributed to the Swap content that was seen up until this conversion Event. This attribution can help you identify your most effective Swaps, Rules, and Visitor Types using the reporting tools on the Swaptify website.
</p>

<p>
    You can setup Events under <a href="<?= admin_url('admin.php?page=swaptify-event-settings') ?>">Swaptify > Event Settings</a>. You will also find code samples for how to trigger an Event.
</p>

<br />
<h4>
    Service and Support
</h4>

<p>
    To get the most out of Swaptify, you will need strong content strategy, a firm understanding of your users' behavior, and accurate event tracking methods. If you want help maximizing your Swaptify effectiveness, we partner with Swaptify-certified marketing agencies who can assist with consulting, data strategy, content personalization strategy, Swaptify setup, event/conversion tracking, and data analysis. If you would like to be matched with a partner agency, please email us at <a href="mailto:consulting@swaptify.com">consulting@swaptify.com</a>
</p>