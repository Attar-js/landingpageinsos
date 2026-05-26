<!DOCTYPE html>
<html lang="en">

<x-head/>

<body class="<?php echo (isset($bodyClass) ?  $bodyClass   : 'rbt-header-sticky')?>">

    <?php 
        if (!isset($switcher)) {
            ?>
            <x-switcher/>
            <?php
        }
    ?>

    <?php 
        if (!isset($header)) {
            ?>
            <x-header/>
            <?php
        }
    ?>

    <!-- Side Navigation for Mobile -->
    <x-sideVav/>

    @yield('content')

    <?php 
        if (isset($topToBottom) && $topToBottom === 'true') {
            ?>
            <x-topToBottom />
            <?php
        }
    ?>

    <?php 
        if (isset($footer) && $footer === 'true') {
            ?>
            <x-footer />
            <?php
        }
    ?>
    

    <x-script/>
    
</body>

</html>

