<?php
/**
 * Plantaphilia — Single Product Template
 * Bypasses Impreza's page builder via template_include filter (priority 999).
 */
get_header();
while ( have_posts() ) {
    the_post();
    wc_get_template_part( 'content', 'single-product' );
}
get_footer();
