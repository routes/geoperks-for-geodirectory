<?php 
if(!class_exists('GeopFeaturedFirstInSearch')) {
    class GeopFeaturedFirstInSearch {
        public static function init() {
            add_filter('geodir_posts_order_by_sort', array(__CLASS__, 'geodir_cust_post_order_by_featured_first') ,10,4);
        }
        
        public static function  geodir_cust_post_order_by_featured_first($orderby, $sort_by, $table, $query)
        {
            if(geodir_is_page('search'))
            {
                if(!empty($orderby)){
                    $orderby = $table. '.featured DESC , ' . $orderby ;
                }
                else {
                    $orderby = $table. '.featured DESC ' ;    
                }
                
            }

            return $orderby ;

       }
    }
    
    GeopFeaturedFirstInSearch::init();
}
?>