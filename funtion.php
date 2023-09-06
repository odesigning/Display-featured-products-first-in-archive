//add new sorting option
add_filter( 'woocommerce_default_catalog_orderby_options', 'custom_woocommerce_catalog_orderby' );
add_filter( 'woocommerce_catalog_orderby', 'custom_woocommerce_catalog_orderby' );
function custom_woocommerce_catalog_orderby( $sortby ) {
    $sortby['recommended'] = 'Recommended';
    return $sortby;
}

//set default sorting for new option
add_filter( 'woocommerce_get_catalog_ordering_args', 'custom_woocommerce_get_catalog_ordering_args' );
function custom_woocommerce_get_catalog_ordering_args( $args ) {
  $orderby_value = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
    if ( 'recommended' == $orderby_value ) {
        $args['orderby'] = 'date';
        $args['order'] = 'DESC';
        $args['meta_key'] = '';
    }
    return $args;
}

//adjust order to allow for featured posts
add_filter('posts_orderby', 'show_featured_products_orderby',10,2);
function show_featured_products_orderby($order_by, $query){
  global  $wpdb ;
  if( (!is_admin()) ){
    $orderby_value = ( isset( $_GET['orderby'] ) ? wc_clean( (string) $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) ) );
    $orderby_value_array = explode( '-', $orderby_value );
    $orderby = esc_attr( $orderby_value_array[0] );
    $order = ( !empty($orderby_value_array[1]) ? $orderby_value_array[1] : 'ASC' );
    $feture_product_id = wc_get_featured_product_ids();
    //only apply to recommended sorting option
    if ( $orderby == "recommended" && is_array( $feture_product_id ) && !empty($feture_product_id) ) {
      if ( empty($order_by) ) {
        $order_by = "FIELD(" . $wpdb->posts . ".ID,'" . implode( "','", $feture_product_id ) . "') DESC ";
      } else {
        $order_by = "FIELD(" . $wpdb->posts . ".ID,'" . implode( "','", $feture_product_id ) . "') DESC, " . $order_by;
      }
    }  
  }
  return $order_by;
}
