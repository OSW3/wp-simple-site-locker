<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {exit;}
?>

<p><?= __( 'This website is temporarily inaccessible.', 'simple_site_locker'); ?></p>

<form action="/" method="POST" novalidate>
    <?php wp_nonce_field('sitelocker', 'sitelocker-token'); ?>
    <input type="password" name="sitelocker-password" placeholder="Unlocker code">
    <br>
    <button type="submit">Unlock</button>
</form>