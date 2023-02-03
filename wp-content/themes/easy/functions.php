<?php

    function easy_wp_enqueue_scripts()
    {
        wp_deregister_script('jquery');
        wp_enqueue_style('bootstrap', get_template_directory_uri() . "/html/css/bootstrap.min.css");
        wp_enqueue_style('magnific-popup', get_template_directory_uri() . "/html/css/magnific-popup.min.css");
        wp_enqueue_style('font-awesome', get_template_directory_uri() . "/html/css/font-awesome.min.css");
        wp_enqueue_style('style', get_template_directory_uri() . "/html/css/style.css");
        wp_enqueue_script('modernizr', get_template_directory_uri() . "/html/js/modernizr.js", [], false, false);
        wp_enqueue_script('jquery', get_template_directory_uri() . "/html/js/jquery.min.js", [], false, false);
        wp_enqueue_script('plugins', get_template_directory_uri() . "/html/js/plugins.js", [], false, true);
        wp_enqueue_script('smooth-scroll', get_template_directory_uri() . "/html/js/smooth-scroll.js", [], false, true);
        wp_enqueue_script('jquery.countTo', get_template_directory_uri() . "/html/js/jquery.countTo.js", [], false, true);
        wp_enqueue_script('particles', get_template_directory_uri() . "/html/js/particles.min.js", [], false, true);
        wp_enqueue_script('main', get_template_directory_uri() . "/html/js/main.js", ['jquery'], false, true);
    }

    add_action('wp_enqueue_scripts', 'easy_wp_enqueue_scripts');
