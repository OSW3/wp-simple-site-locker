<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {exit;}
?>

<form action="/" method="POST" novalidate>
    <?php wp_nonce_field('sitelocker', 'sitelocker-token'); ?>
    <p><?= __( 'This website is temporarily inaccessible.', 'simple_site_locker'); ?></p>
    <input type="password" name="sitelocker-password" placeholder="Unlocker code">
    <br>
    <button type="submit">Unlock</button>
</form>