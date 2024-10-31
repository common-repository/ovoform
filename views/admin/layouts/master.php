<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="page-wrapper default-version">
    <div class="body-wrapper">

        
        <ul class="breadcrumb-nav">
            <button class="breadcrumb-nav-close"><i class="las la-times"></i></button>
            <?php ovoform_stack('ovoform_topnav') ?>
        </ul>


        <div class="breadcrumb-area">
            <div class="admin-container">
                <?php
                ovoform_include('admin/partials/breadcrumb', compact('pageTitle'));
                ?>
            </div>
        </div>
        <div class="admin-container">
            <div class="bodywrapper__inner">
                {{yield}}
            </div>
        </div>
    </div>
</div>
<?php
ovoform_include('partials/notify');
?>