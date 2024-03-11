<?php

/** 
 * Query Data From Wordpress Database
 */

 class Query_data{
    public function __construct(){
        add_shortcode('query-data',array($this,'wp_query'));
    }

    public function wp_db(){
        ob_start();
        global $wpdb;
        $results = $wpdb->get_results("Select * FROM $wpdb->posts WHERE post_type='post' AND post_status = 'publish' LIMIT 3");
        ?>
        <ul >
        <?php  
            foreach ($results as $post){
        ?>
            <li>
                <a href="<?php echo get_permalink($post->ID) ?>" 
                target="_blank"><?php echo $post->post_title ?></a
                >
            </li>
            <?php } ?>
        </ul>
        <?php
        return ob_get_clean();
        
    }

    public function wp_query(){
        ob_start();
        $args = array(
            'post__not_in' => array(get_the_ID()),
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 5,
            'orderby' => 'rand',
            'category__in' => array()

        );
        $query = new WP_Query($args);
        ?>
        <ul >

        <?php 
            if($query->have_posts())
            {
                while($query->have_posts()){
                    $query->the_post();
                    ?>
                         <li class="nav-item">
                            <a class="nav-link active" href="<?php echo get_permalink() ?>" aria-current="page"
                                ><?php echo get_the_title() ?></a
                            >
                        </li>
                    <?php 
                    
                }
            }
        ?>
        </ul>
        <?php 
        wp_reset_postdata();
        return ob_get_clean();
        
    }
    public function query_data() {
        ob_start();
        $args = array(
            'post_status' => 'publish',
            'posts_per_page' =>5,
        );

        $posts = get_posts($args);
        ?>
        <ul>
        <?php  
            foreach ($posts as $post){
        ?>
            <li>
                <a href="<?php echo get_permalink($post->ID) ?>" 
                target="_blank"><?php echo $post->post_title ?></a
                >
            </li>
            <?php } ?>
        </ul>
        <?php
        return ob_get_clean();
        
    }
 }