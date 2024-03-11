<?php 

/**
 * Plugin Name: Our First Plugin
 * Description: This is our first plugin. 
 * Version: 1.0.0
 * Author: Hasin Hayder
 * Author URI: http://hasin.me
 * Plugin URI: http://google.com
 */
if(!defined('ABSPATH')) exit;
 class our_first_plugin{

    private $size = 150;
    private $color = 'ff0000';
    public function __construct() 
    {
        add_action('init',array($this,'init'));
    }
    public function init(){
        // add_filter('the_title',array($this,'change_the_title'));
        add_filter('the_content',array($this,'add_qr_code_in_content'),199);
        add_filter('the_content',array($this,'content_change'));
        add_action('wp_footer',array($this,'check_footer'));
        add_filter('excerpt_length',array($this,'change_the_excerpt_lenght'));
        $this->size = apply_filters('ofp_qr_code_size',$this->size);
        add_filter('world_city_times_cities',array($this,'add_city_time_in_admin_bar'));
        add_filter('manage_posts_columns',[$this,'add_post_column']);
        add_action('manage_posts_custom_column',[$this,'show_thumbnail_on_post'],10,2);
        add_filter('manage_posts_columns',[$this,'add_id_column']);
        add_action('manage_posts_custom_column',[$this,'show_id_on_post'],10,2);
        add_filter('manage_edit-post_sortable_columns',[$this,'add_sortable_column']);

        // For Pagees change

        add_filter('manage_pages_columns',[$this,'add_post_column']);
        add_action('manage_pages_custom_column',[$this,'show_thumbnail_on_post'],10,2);
        add_filter('manage_pages_columns',[$this,'add_id_column']);
        add_action('manage_pages_custom_column',[$this,'show_id_on_post'],10,2);
        add_filter('manage_edit-page_sortable_columns',[$this,'add_sortable_column']);

        // For show view Column on Post
        add_filter('manage_posts_columns',[$this,'add_view_post_column']);
        add_filter('manage_edit-post_sortable_columns',[$this,'add_sortable_column_for_view']);

        //add count when view the post

        add_action('wp_head',[$this,'count_view']);
        add_action('manage_posts_custom_column',[$this,'show_view_on_post'],10,2);

        //add column on user screen
        add_filter('manage_users_columns',[$this,'add_registration_column']);
        add_action('manage_users_custom_column',[$this,'show_registration_on_user'],10,3);

        require_once plugin_dir_path(__FILE__).'query-data.php';
        new Query_data();
    }

    public function add_registration_column($columns) {
        $columns['user_register'] = 'Registered Date';
        return $columns;
    }
    public function show_registration_on_user($value,$column_name,$user_id) {
        if($column_name == 'user_register'){
           $user = get_user_by('id',$user_id);
           $date = $user->user_registered;
           return $date;
        }
    }
    public function count_view(){
        if(is_single()){
           $view_count  = get_post_meta(get_the_ID(),'view_count',true);
           $view_count = $view_count ? $view_count : 0;
           $view_count++;
           update_post_meta(get_the_ID(),'view_count',$view_count);
        }
    }
    public function add_view_post_column($columns) {
        $columns['view_column'] = 'Total View';
        return $columns;
    }
    public function show_view_on_post($column_name,$post_id) {
        if($column_name == 'view_column'){
           $view_count = get_post_meta($post_id,'view_count',true);
           $view_count = $view_count ? $view_count : 0;
           echo $view_count;
        }
    }

    public function change_the_title($title) {
        if(!is_page()){
            return $title;
        }
        $id = get_the_id();
        return $title.'The Id Is : '.$id;
    }

    public function add_qr_code_in_content($content)  {

        $get_the_current_link = get_permalink();
        $get_the_title = get_the_title();
        $custom_content = '<div style="border: 1px solid #ddd; padding: 10px; margin: 20px 0;">';
       
        $custom_content .="<img src='https://api.qrserver.com/v1/create-qr-code/?color={$this->color}&size={$this->size}x{$this->size}&data={$get_the_current_link}' alt='{$get_the_title}' />";
        $custom_content .= '</div>';
        
        $content .= $custom_content;
        return $content;
    }
    public function content_change($content) {
        if(!is_page()){
            $view_count = get_post_meta(get_the_ID(),'view_count',true);
            $view_count = $view_count ? $view_count : 0;
            $content = $content.'<p> Hello my name is Ronok</p><p> Hello my name is Ronok</p>';
            $content =$content.'<p>The post Id is '.get_the_ID().'</p>';
            $content = $content.'<p>Total Visit of the post is : '.($view_count+1).'</p>';
        }
        return $content;
    }
    public function check_footer(){
        echo "<h1>Hello this is test!</h1>";
    }

    public function change_the_excerpt_lenght($number){
        return 20;
    }
    public function add_city_time_in_admin_bar($cities) {
        array_push($cities,'Khulna');
        array_push($cities,'Dhaka');
        return $cities;
    }

    public function add_post_column($columns){

        $new_columns =[];
        foreach ($columns as $key => $value) {
            if($key == 'author'){
                $new_columns[$key] = $value;
                $new_columns['thumbnail'] = 'Thumbnail';

            }
            else{
                $new_columns[$key] = $value;
            }
        }
        return $new_columns;
    }

    public function show_thumbnail_on_post($column_name,$post_id){

        if($column_name == 'thumbnail'){
            $has_thumbnail = has_post_thumbnail($post_id);
            if($has_thumbnail){
                echo get_the_post_thumbnail($post_id,[50,50]);
            }
            else{
                echo 'No Thumnail There';
            }
        }
    }
    public function add_id_column($columns){

        $new_columns =[];
        foreach ($columns as $key => $value) {
            if($key == 'cb'){
                $new_columns[$key] = $value;
                $new_columns['id'] = 'ID';

            }
            else{
                $new_columns[$key] = $value;
            }
        }
        return $new_columns;
    }

    public function show_id_on_post($column_name,$post_id){

        if($column_name == 'id'){
           echo get_the_ID();
        }
    }
    public function add_sortable_column($columns){
        $columns ['id'] = 'ID';
        return $columns;
    }
    public function add_sortable_column_for_view($columns) {
        $columns ['view_column'] = 'Total View';
        return $columns;
    }
 }  
    
new our_first_plugin();

?>