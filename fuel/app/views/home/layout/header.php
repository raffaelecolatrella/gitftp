<script>
    var base = '<?php echo Uri::base(FALSE); ?>';
    var home_url = '<?php echo home_url; ?>';
    var dash_url = '<?php echo dash_url; ?>';
    var is_dash = '<?php echo is_dash; ?>';
</script>
<?php
$color = 'black';
$e = '';
$s = Uri::segments();
if (count($s) == 0 || $s[0] == 'welcome') {
//    $color = 'white';
    $e = 'home';
}
?>

<header id="header" class="transparent <?php echo $color; ?>-header header active-section <?php echo $e ?>">
    <div class="container">
        <div class="row" style="position: relative">
            <div class="col-md-3">
                <div class="logo">
                    <a href="<?php echo home_url; ?>" title="">
                        <img src="assets/img/logo-sm-2-name.png" class="black-logo standard-logo middle-content" alt="">
                        <img src="assets/img/logo-sm-2w-name.png" class="white-logo standard-logo middle-content" alt="">
                    </a>
                </div>
            </div>
            <div class="col-md-9 menu-col text-right">
                <div class="menu">
                    <ul class="list-inline">
                        <?php if (\Auth::instance()->check()) { ?>
                            <li class="">
                                <a class="blue" href="<?php echo dash_url; ?>">
                                    <?php echo strtolower(\Auth::instance()->get('username')); ?>
                                </a>
                                <a class="gray" href="<?php echo home_url . 'user/logout'; ?>"> logout</a>
                            </li>
                        <?php } else { ?>
                            <li>
                                <a class="" href="<?php echo home_url . 'login'; ?>">login</a>
                            </li>
                            <li class="">
                                <a class="green" href="<?php echo home_url . 'signup'; ?>">signup</a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="menu-toggle">
                <i class="ti-menu"></i>
            </div>
        </div>
    </div>
</header>