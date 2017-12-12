<?php global $wp; ?>

<div class="simple-site-locker-unlocked">
    <a href="<?= add_query_arg('sitelocker','lock', home_url( $wp->request )) ?>"></a>
</div>