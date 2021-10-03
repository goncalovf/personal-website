<nav class="nav-drill">
    <div class="nav-close">
    <?php gvf_print_icon( 'close' ) ?>
    </div>
    <div class="nav-section">
        <h3 class="nav-section-title"><?php _e( "Post Index", 'gvf') ?></h3>
        <?php
        global $page;
        gvf_print_index_for_ham_menu( get_permalink(), $page, $index );
        ?>
    </div>
    <div class="nav-site-title">
        <?php bloginfo( 'name' ) ?>
    </div>
</nav>
