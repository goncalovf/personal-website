<?php
/**
 * Structured data's handler and generator.
 */

defined( 'ABSPATH' ) || exit;

class GVF_Structured_Data_OGP {

    /**
     * Constructor.
     */
    public function __construct() {

        add_action( 'wp_head', array( $this, 'output_structured_data' ) );
    }

    /**
     * Output post structured data.
     */
    public function output_post_structured_data() {

        $post = gvf()->get_post( get_the_ID() );

        if ( ! $post || is_wp_error( $post ) ) return;

        $twitter_metas = array(
            'card'      => 'summary',
            'creator'   => '@goncalovf'
        );

        /**
         * Print basic metas
         */
        foreach ( $twitter_metas as $name => $content ) : ?>
            <meta name="twitter:<?php echo $name ?>" content="<?php echo $content ?>" />
        <?php endforeach;

        $ogp_metas = array(
            'og:type'                   => 'article',
            'og:title'                  => $post->get_title(),
            'og:image'                  => wp_get_attachment_url( 47 ),
            'og:image:type'             => 'image/jpeg',
            'og:image:width'            => '200',
            'og:image:height'           => '200',
            'og:image:alt'              => 'Site logo',
            'og:description'            => $post->get_excerpt(),
            'og:url'                    => get_permalink( $post->get_id() ),
            'og:site_name'              => get_bloginfo( 'name' ),
            'article:published_time'    => date( 'Y-m-d', $post->get_date_created()->getTimestamp() ),
            'article:modified_time'     => date( 'Y-m-d', $post->get_date_modified()->getTimestamp() ),
        );

        /**
         * Print basic metas
         */
        foreach ( $ogp_metas as $property => $content ) : ?>
            <meta property="<?php echo $property ?>" content="<?php echo $content ?>" />
        <?php endforeach;

        /**
         * Print array metas (tags).
         */
        foreach ( array_column( get_the_tags( $post->get_id() ), 'name' ) as $tag_name ) : ?>
            <meta property="tag" content="<?php echo $tag_name ?>" />
        <?php endforeach;
    }

    /**
     * Identify which type to output and output it.
     */
    public function output_structured_data() {

        if ( is_single() ) {
            $this->output_post_structured_data();
        }
    }
}

new GVF_Structured_Data_OGP();
