function __add_global_categories( $term_id )
{
    if ( get_current_blog_id() !== BLOG_ID_CURRENT_SITE || ( !$term = get_term( $term_id, 'category' ) ) )
        return $term_id; // bail

    if ( !$term->parent || ( !$parent = get_term( $term->parent, 'category' ) ) )
        $parent = null;

    global $wpdb;

    $blogs = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs} WHERE site_id = '{$wpdb->siteid}'" );
    foreach ( $blogs as $blog ) {
        $wpdb->set_blog_id( $blog );

        if ( $parent && ( $_parent = get_term_by( 'slug', $parent->slug, 'category' ) ) )
            $_parent_ID = $_parent->term_id;
        else
            $_parent_ID = 0;

        wp_insert_term( $term->name, 'category',  array(
            'slug' => $term->slug,
            'parent' => $_parent_ID,
            'description' => $term->description
        ));
    }

    $wpdb->set_blog_id( BLOG_ID_CURRENT_SITE );
}
add_action( 'created_category', '__add_global_categories' );
