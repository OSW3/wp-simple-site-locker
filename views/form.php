<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {exit;}
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="<?= get_image('favicon.png') ?>" type="image/x-icon" />
    <?php wp_head(); ?>
</head>
<body class="default">

    <div class="row">
        <form action="/" class="col-md-3 ml-auto mr-auto text-center" method="POST" novalidate>
            <?php wp_nonce_field('sitelocker', 'sitelocker-token'); ?>
            <div class="form-group">
                <p>
                    <small class="form-text text-muted">This website is locked.</small>
                </p>
                
                <div class="input-group">

                <input type="password" class="form-control" name="sitelocker-password" placeholder="password">

                    <span class="input-group-addon">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-unlock"></i>
                        </button>
                    </span>

                </div>            
            </div>
        </form>
    </div>

    <!-- Scripts -->
    <?php wp_footer() ?>

</body>
</html>