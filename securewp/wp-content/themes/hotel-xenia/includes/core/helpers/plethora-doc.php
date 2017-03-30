<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2014

Plethora_Doc class

Version: 1.2

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


/**
 * @package Plethora Framework
 * @author Plethora Dev Team
 * @copyright Plethora Themes (c) 2016
 *
 */
class Plethora_Doc {

    public $product;
    public $index;

    public function __construct( $product = '' ) {

        if ( ! empty( $product ) ) {

            $this->product = $product ;
            $this->index   = $this->set_index();
        }
    }

    public function set_index() {

        $index = array();
        // get parent sections first
        $args = array(
            'taxonomy'   => 'doc-section',
            'parent'     => 0,
            'hide_empty' => false,
            'orderby'    => 'term_order',
        );
        $sections = get_terms( $args );

        if ( is_array( $sections ) && !empty( $sections ) ) { 

            foreach ( $sections as $section ) {

                $args = array(
 					'post_type'           => 'doc',
                   	'posts_per_page'      => -1,
                    'ignore_sticky_posts' => true,
                    'orderby'             => 'menu_order',
                    'order'               => 'ASC',
                    'tax_query'           => array(
                        'relation' => 'AND',
                        array(
                            'taxonomy' => 'doc-section',
                            'field'    => 'slug',
                            'terms'    => $section->slug,
                        ),
                        array(
                            'taxonomy' => 'kb-product',
                            'field'    => 'slug',
                            'terms'    => $this->product,
                        ),
                    )
                );

                $section_posts = new WP_Query( $args );

                if ( $section_posts->have_posts() ) { 

                    $index[$section->slug]['id']    = $section->term_id;
                    $index[$section->slug]['title'] = $section->name;
                    $index[$section->slug]['slug']  = $section->slug;
                    $index[$section->slug]['desc']  = $section->description;
                    $index[$section->slug]['icon']  = get_term_meta( $section->term_id, TERMSMETA_PREFIX .'doc-section-icon', true );
                    foreach ( $section_posts->get_posts() as $doc ) {

                        $index[$section->slug]['docs'][$doc->ID]['doc_id']       = $doc->ID;
                        $index[$section->slug]['docs'][$doc->ID]['doc_slug']     = $doc->post_name;
                        $index[$section->slug]['docs'][$doc->ID]['doc_title']    = $doc->post_title;
                        $index[$section->slug]['docs'][$doc->ID]['doc_excerpt']  = $doc->post_excerpt;
                        $index[$section->slug]['docs'][$doc->ID]['doc_content']  = $doc->post_content;
                        $index[$section->slug]['docs'][$doc->ID]['doc_sidenote'] = Plethora_Theme::option( METAOPTION_PREFIX .'doc-sidecontent', '', $doc->ID );
                    }
                }

                wp_reset_postdata();
            }
        }

        return $index;
    }

    public function get_product() {

        return $this->product;
    }

    public function get_index() {

        return $this->index;
    }

    public function get_section_data( $section ) {

        $index = $this->get_index();
        $section_data = !empty( $index[$section] ) ? $index[$section] : array();
        if ( isset( $section_data['docs'] ) ) {

            unset( $section_data['docs'] );
        } 
        return $section_data;
    }

    public function get_section_docs( $section ) {

        $index = $this->get_index();
        return !empty( $index[$section]['docs'] ) ? $index[$section]['docs'] : array();
    }
}