<?php 


// start slider addons
class Avocado_Slider_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'slider';
    }
    
	public function get_title() {
		return __( 'Slider', 'ppm-quickstart' );
	}

	public function get_icon() {
		return 'fa fa-code';
	}

	public function get_categories() {
		return [ 'general' ];
	}

	protected function _register_controls() {


        
        $this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Slides', 'plugin-name' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
        );
        
        $repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'title', [
				'label' => __( 'Title', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Slide Title' , 'plugin-domain' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'content', [
				'label' => __( 'Content', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => __( 'Slide Content' , 'plugin-domain' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'slide_btn_text', [
				'label' => __( 'Button text', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Read More' , 'plugin-domain' ),
			]
		);

		$repeater->add_control(
			'slide_link', [
				'label' => __( 'Button link', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'slide_bg', [
				'label' => __( 'Slide Background', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
			]
		);

		$this->add_control(
			'slides',
			[
				'label' => __( 'Slides', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'title' => __( 'Slide Title', 'plugin-domain' ),
						'slide_btn_text' => __( 'Read More', 'plugin-domain' ),
					]
				],
				'title_field' => '{{{ title }}}',
			]
        );
        
        $this->add_control(
            'nav',
            [
                'label' => __( 'Enable Navigation?', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes'
            ]
        );

        $this->add_control(
            'dots',
            [
                'label' => __( 'Enable Dots?', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no'
            ]
        );
        
        $this->add_control(
            'autoplay',
            [
                'label' => __( 'Enable Autoplay?', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes'
            ]
        );

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

        if(!empty($settings['slides'])) {
            $html = '';
            $random = rand(8977,897987);

            if($settings['nav'] == 'yes'){
                $nav = 'true';
            } else {
                $nav = 'false';
            }
    
            if($settings['dots'] == 'yes'){
                $dots = 'true';
            } else {
                $dots = 'false';
            }
    
            if($settings['autoplay'] == 'yes'){
                $autoplay = 'true';
            } else {
                $autoplay = 'false';
            }

            if(count($settings['slides']) > 1) {
                $html .= '<script>
                    jQuery(document).ready(function($) {
                        $("#slide-'.$random.'").slick({
                            arrows: '.$nav.',
                            dots: '.$dots.',
                            autoplay: '.$autoplay.',
                            prevArrow: "<i class=\'fa fa-angle-left slick-arrow-btn\'></i>",
                            nextArrow: "<i class=\'fa fa-angle-right slick-arrow-btn\'></i>",
                            draggable: false,
                            touchMove: false,
                            swipe: false,
                            swipeToSlide: false,
                        });
                    });
                </script>';
            }
            $html .= '<div class="slider-wrapper"><div id="slide-'.$random.'" class="slides">';
                foreach($settings['slides'] as $slide) {
                    $html .= '<div style="background-image:url('.wp_get_attachment_image_url($slide['slide_bg']['id'], 'large').')" class="single-slide-item">
                        <div class="container">
                            <div class="row justify-content-center text-center">
                                <div class="col my-auto">
                                    <div class="slide-text">
										<h2>'.$slide['title'].'</h2>
										'.wpautop(do_shortcode($slide['content'])).'
                                        <a href="'.$slide['slide_link'].'" class="boxed-btn">'.$slide['slide_btn_text'].'</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
            $html .= '</div><img class="slider-shape" src="'.get_template_directory_uri().'/assets/img/slider-bottom.png" alt=""/></div>';
        } else {
            $html = '<div class="alert alert-warning">Please add slides.</div>';
        }
        


        echo $html;

	}

} //END SLIDE ADD-ON


// START PRODUCT CATEGORY
if ( class_exists( 'WooCommerce' ) ) {

    function avocado_product_list(){
        $args = wp_parse_args( array(
            'post_type' => 'product',
            'numberposts' => -1,
            'prderby' => 'title',
            'order' => 'ASC',
        ) );

        $query_query = get_posts( $args );
        
        $dropdown_array = array();
        if ( $query_query ) {
            foreach ( $query_query as $query ) {
                $dropdown_array[ $query->ID ] = $query->post_title;
            }
        }

        return $dropdown_array;
    }




    function avocado_product_cat_list( ) {
        $elements = get_terms( 'product_cat', array('hide_empty' => false) );
        $product_cat_array = array();

        if ( !empty($elements) ) {
            foreach ( $elements as $element ) {
                $info = get_term($element, 'product_cat');
                $product_cat_array[ $info->term_id ] = $info->name;
            }
        }
    
        return $product_cat_array;
    }


    class Avocado_Categories_Widget extends \Elementor\Widget_Base {

        public function get_name() {
            return 'avocado-categories';
        }
        
        public function get_title() {
            return __( 'Avocado Cagegories', 'ppm-quickstart' );
        }

        public function get_icon() {
            return 'fa fa-code';
        }

        public function get_categories() {
            return [ 'general' ];
        }

        protected function _register_controls() {


            
            $this->start_controls_section(
                'content_section',
                [
                    'label' => __( 'Configuration', 'plugin-name' ),
                    'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                ]
            );


            $this->add_control(
                'cat_ids',
                [
                    'label' => __( 'Select Categories', 'plugin-domain' ),
                    'type' => \Elementor\Controls_Manager::SELECT2,
                    'multiple' => true,
                    'options' => avocado_product_cat_list()
                ]
            );

            $this->add_control(
                'columns',
                [
                    'label' => __( 'Columns', 'plugin-domain' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => '4',
                    'options' => [
                        '4'  => __( '4 Columns', 'plugin-domain' ),
                        '3'  => __( '3 Columns', 'plugin-domain' ),
                        '2'  => __( '2 Columns', 'plugin-domain' ),
                        '1'  => __( '1 Columns', 'plugin-domain' ),
                    ],
                ]
            );

            $this->add_control(
                'bg',
                [
                    'label' => __( 'Image as background?', 'plugin-domain' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'default' => 'no'
                ]
            );

            $this->end_controls_section();

        }

        protected function render() {

            $settings = $this->get_settings_for_display();

            if($settings['columns'] == '4') {
                $columns_markup = 'col-lg-3';
            } else if($settings['columns'] == '3') {
                $columns_markup = 'col-lg-4';
            } else if($settings['columns'] == '2') {
                $columns_markup = 'col-lg-6';
            } else {
                $columns_markup = 'col';
            }

            if(!empty($settings['cat_ids'])) {
                $html = '<div class="row">';
                foreach($settings['cat_ids'] as $cat) {
                    $thumb_id = get_woocommerce_term_meta( $cat, 'thumbnail_id', true );
                    $term_img = wp_get_attachment_image_url(  $thumb_id, 'medium' );
                    $info = get_term($cat, 'product_cat');
                    $cat_link = get_category_link($cat, 'product_cat');
                  
                    $html .= '<div class="'.$columns_markup.' single-category-item"><a href="'.$cat_link.'">';

                        if(!empty($thumb_id)) {
                            if($settings['bg'] == 'yes') {
                                $html .= '<div class="cat-img cat-img-bg" style="background-image:url('.$term_img.')"></div>';
                            } else {
                                $html .='
                                <div class="row cat-img">
                                    <div class="col text-center">
                                        <img src="'.$term_img.'" alt=""/>
                                    </div>
                                </div>';
                            }
                            
                        } else {
                            $html .= '<div class="cat-no-thumb"><p>No thumbnail</p></div>';
                        }
                        

                        $html .='

                        <h3>'.$info->name.'</h3>
                        '.$info->description.'
                    </a></div>';
                }
                $html .= '</div>';
            } else {
                $html = '<div class="alert alert-warning"><p>Please select categories.</p></div>';
            }

            echo $html;

        }

    }

}
//END PRODUCT CATEGORY 


//START PRODUCT CAROUSEL 
class Avocado_Product_Carousel extends \Elementor\Widget_Base {

    public function get_name() {
        return 'avocado-product-carousel';
    }
    
    public function get_title() {
        return __( 'Avocado Product Carousel', 'ppm-quickstart' );
    }

    public function get_icon() {
        return 'fa fa-code';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function _register_controls() {


        
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Configuration', 'plugin-name' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );


        $this->add_control(
            'from',
            [
                'label' => __( 'Add Product From', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'product' => __( 'Select Products', 'plugin-domain' ),
                    'category' => __( 'Select Categories', 'plugin-domain' ),
                ],
                'default' => 'product',
            ]
        );

        $this->add_control(
            'p_ids',
            [
                'label' => __( 'And/Or Select Products', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => avocado_product_list(),
                'condition' => [
                    'from' => 'product',
                ]
            ]
        );

        $this->add_control(
            'cat_ids',
            [
                'label' => __( 'And/Or Select Categories', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => avocado_product_cat_list(),
                'condition' => [
                    'from' => 'category',
                ]
            ]
        );

        $this->add_control(
            'nav',
            [
                'label' => __( 'Enable Navigation?', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes'
            ]
        );

        $this->add_control(
            'dots',
            [
                'label' => __( 'Enable Dots?', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no'
            ]
        );
        
        $this->add_control(
            'autoplay',
            [
                'label' => __( 'Enable Autoplay?', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes'
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings_for_display();

        if($settings['from'] == 'category') {
            $q = new WP_Query( array(
                'posts_per_page' => 10, 
                'post_type' => 'product',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => $settings['cat_ids'],
                    ),
                )
            ) );
        } else {
            $q = new WP_Query( array(
                'posts_per_page' => 10, 
                'post_type' => 'product',
                'post__in' =>  $settings['p_ids'], 
            ) );
        }

        // $q = new WP_Query( array(
        //     'posts_per_page' => 10, 
        //     'post_type' => 'product',
        //     'meta_query' => WC()->query->get_meta_query(),
        //     'post__in' => array_merge( array( 0 ), wc_get_product_ids_on_sale() ) 
        // ) );

        // $q = new WP_Query( array(
        //     'posts_per_page' => 10, 
        //     'post_type' => 'product',
        //     'post__in' => $settings['p_ids']
        // ) );

        // $q = new WP_Query( array(
        //     'posts_per_page' => 10, 
        //     'post_type' => 'product',
        //     'tax_query' => array(
        //         array(
        //             'taxonomy' => 'product_cat',
        //             'field' => 'term_id',
        //             'terms' => $settings['cat_ids'],
        //         ),
        //     ),
        // ) );

        $rand = rand(4546, 46455);

        if($settings['nav'] == 'yes'){
            $nav = 'true';
        } else {
            $nav = 'false';
        }

        if($settings['dots'] == 'yes'){
            $dots = 'true';
        } else {
            $dots = 'false';
        }

        if($settings['autoplay'] == 'yes'){
            $autoplay = 'true';
        } else {
            $autoplay = 'false';
        }
            
        $html = '
        <script>
            jQuery(document).ready(function($) {
                $("#product-carousel-'.$rand.'").slick({
                    arrows: '.$nav.',
                    dots: '.$dots.',
                    autoplay: '.$autoplay.',
                    prevArrow: "<i class=\'fa fa-angle-left slick-arrow-btn\'></i>",
                    nextArrow: "<i class=\'fa fa-angle-right slick-arrow-btn\'></i>",
                    draggable: false,
                    touchMove: false,
                    swipe: false,
                    swipeToSlide: false,
                });
            });
        </script>
        
        <div class="product-carousel" id="product-carousel-'.$rand.'">';
        while($q->have_posts()) : $q->the_post();
        global $product;
            $html .= '<div class="single-c-product">
                <div class="row">
                    <div class="col">
                        <div class="c-product-thumb-inner">
                            <div class="c-product-thumb" style="background-image:url('.get_the_post_thumbnail_url(get_the_ID(), 'medium').')">';
                                
                                if($product->is_on_sale() ) {
                                    $html .= '<span class="c-product-sale">Sale</span>';
                                }

                            $html .='
                            </div>
                        </div>
                    </div>
                    <div class="col my-auto text-center">
                        <div class="c-product-price">
                            <h3>'.get_the_title().'</h3>
                            <div class="c-product-price">'.$product->get_price_html().'</div>';

                            if($average = $product->get_average_rating()) {
                                $html .= '<div class="c-product-star-rating"><div class="star-rating" title="'.sprintf(__( 'Rated %s out of 5', 'woocommerce' ), $average).'"><span style="width:'.( ( $average / 5 ) * 100 ) . '%"><strong itemprop="ratingValue" class="rating">'.$average.'</strong>'.__( 'out of 5', 'woocommerce' ).'</span></div></div>';
                            }
                            $html .='
                            <div class="c-product-add-to-cart">'.do_shortcode('[add_to_cart style="" show_price="FALSE" id="'.get_the_ID().'"]').'</div> 
                        </div>  
                    </div>
                </div>
            </div>';
        endwhile; wp_reset_query();

        $html .= '</div>';

        if($settings['from'] == 'category' && empty($settings['cat_ids'])) {
            $html = '<div class="alert alert-warning"><p>Please select product category</p></div>';
        }

        echo $html;

    }

}

//END PRODUCT CAROUSEL


//START FEATURED PRODUCT LIST 
class Avocado_Product_List extends \Elementor\Widget_Base {

    public function get_name() {
        return 'avocado-product-list';
    }
    
    public function get_title() {
        return __( 'Avocado Product List', 'ppm-quickstart' );
    }

    public function get_icon() {
        return 'fa fa-code';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function _register_controls() {


        
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Product List Configuration', 'plugin-name' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'from',
            [
                'label' => __( 'Add Product From', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'product' => __( 'Select Products', 'plugin-domain' ),
                    'category' => __( 'Select Categories', 'plugin-domain' ),
                ],
                'default' => 'product',
            ]
        );

        $this->add_control(
            'p_ids',
            [
                'label' => __( 'And/Or Select Products', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => avocado_product_list(),
                'condition' => [
                    'from' => 'product',
                ]
            ]
        );

        $this->add_control(
            'cat_ids',
            [
                'label' => __( 'And/Or Select Categories', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => avocado_product_cat_list(),
                'condition' => [
                    'from' => 'category',
                ]
            ]
        );

        $this->add_control(
            'count',
            [
                'label' => __( 'And/Or Select Categories', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '12' 
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings_for_display();

        if($settings['from'] == 'category') {
            $q = new WP_Query( array(
                'posts_per_page' => $settings['count'], 
                'post_type' => 'product',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => $settings['cat_ids'],
                    ),
                )
            ) );
        } else {
            $q = new WP_Query( array(
                'posts_per_page' => $settings['count'], 
                'post_type' => 'product',
                'post__in' =>  $settings['p_ids'], 
            ) );
        }
 
        $html = '<div class="featured-product-list">
                    <div class="row">';
                    while($q->have_posts()) : $q->the_post();
                    global $product;
                        $html .= '
                        <div class="col-lg-3">
                            <div class="single-product text-center">
                                <a href="'.get_permalink().'" class=""> 
                                    <img src="'.get_the_post_thumbnail_url(get_the_ID(), 'medium').'" alt="">
                                </a>
                                <h2>'.get_the_title().'</h2>
                                '.$product->get_price_html().'
                                '.do_shortcode('[add_to_cart style="" show_price="FALSE" id="'.get_the_ID().'"]').'
                            </div>
                        </div>';
                    endwhile; wp_reset_query();

        $html .= '
                    </div> 
                </div>';

        if($settings['from'] == 'category' && empty($settings['cat_ids'])) {
            $html = '<div class="alert alert-warning"><p>Please select product category</p></div>';
        }

        echo $html;

    }

}

//END FEATURED PRODUCT LIST



//START PRODUCT HOVER CARD CAROUSEL 
class Avocado_ProductHoverCard_Carousel extends \Elementor\Widget_Base {

    public function get_name() {
        return 'avocado-product-hovercard';
    }
    
    public function get_title() {
        return __( 'Avocado Product Hover Card', 'ppm-quickstart' );
    }

    public function get_icon() {
        return 'fa fa-code';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function _register_controls() {


        
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Configuration', 'plugin-name' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );


        $this->add_control(
            'from',
            [
                'label' => __( 'Add Product From', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'product' => __( 'Select Products', 'plugin-domain' ),
                    'category' => __( 'Select Categories', 'plugin-domain' ),
                ],
                'default' => 'product',
            ]
        );

        $this->add_control(
            'p_ids',
            [
                'label' => __( 'And/Or Select Products', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => avocado_product_list(),
                'condition' => [
                    'from' => 'product',
                ]
            ]
        );

        $this->add_control(
            'cat_ids',
            [
                'label' => __( 'And/Or Select Categories', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => avocado_product_cat_list(),
                'condition' => [
                    'from' => 'category',
                ]
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings_for_display();

        if($settings['from'] == 'category') {
            $q = new WP_Query( array(
                'posts_per_page' => 6, 
                'post_type' => 'product',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => $settings['cat_ids'],
                    ),
                )
            ) );
        } else {
            $q = new WP_Query( array(
                'posts_per_page' =>  6, 
                'post_type' => 'product',
                'post__in' =>  $settings['p_ids'], 
            ) );
        }

       
            
        $html = '   
        <div class="product-hovercard-area">
            <div class="container">
                <div class="row">
                    <div class="col product-hovercard">';
        
        while($q->have_posts()) : $q->the_post();
        global $product;
            
            $html .= '<div class="single-hc-product">
                <div class="hc-product-base">
                    '.get_the_post_thumbnail(get_the_ID(), 'thumbnail').'
                    <span>
                        <i class="fa fa-angle-down"></i>
                    </span>
                </div>
                <div class="product-hovercard-info">
                    <div class="product-thumb-hc" style="background-image:url('.get_the_post_thumbnail_url(get_the_ID(), 'medium').')"></div>
                    <h4>'.get_the_title().'</h4>
                    <div class="c-product-price">'.$product->get_price_html().'</div>
                    <div class="c-product-add-to-cart">'.do_shortcode('[add_to_cart id="'.get_the_ID().'"]').'</div>
                </div>
            </div>';

        endwhile; wp_reset_query();

        $html .= '
                </div>
            </div>
        </div>';

        if($settings['from'] == 'category' && empty($settings['cat_ids'])) {
            $html = '<div class="alert alert-warning"><p>Please select product category</p></div>';
        }

        echo $html;

    }

}

//END PRODUCT HOVER CARD CAROUSEL


//START Step Check Out  
class StepCheckOut extends \Elementor\Widget_Base {

    public function get_name() {
        return 'stepchekcout';
    }
    
    public function get_title() {
        return __( 'Step Check Out', 'ppm-quickstart' );
    }

    public function get_icon() {
        return 'fa fa-code';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function _register_controls() {


        
        $this->start_controls_section(
            'step_1_section',
            [
                'label' => __( 'Step One Configuration', 'plugin-name' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'top_text',
            [
                'label' => __( 'Top Text', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => '<h2>Select Your Starter Kit</h2>',
            ]
        );

        $this->add_control(
            'base_products',
            [
                'label' => __( 'Select Base products', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => avocado_product_list(),
                'label_block' => true,
            ]
        );

        $this->end_controls_section();
        

        $this->start_controls_section(
            'step_two_section',
            [
                'label' => __( 'Step Two Configuration', 'plugin-name' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'step_two_title',
            [
                'label' => __( 'Step Two Title', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Choose Candle Holders, Vases, & Pillows<',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'step_two_content',
            [
                'label' => __( 'Step Two Content', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => 'Summer Elevated Kit $199.00',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'step_two_img',
            [
                'label' => __( 'Step Two Image', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'label_block' => true,
            ]
        );


        $repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'title', [
				'label' => __( 'Title', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Box Title' , 'plugin-domain' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'box_product_ids', [
				'label' => __( 'Select Box Products', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => avocado_product_list(),
                'multiple' => true,
                'label_block' => true,
			]
		);

		$this->add_control(
			'boxes',
			[
				'label' => __( 'Product Boxes', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'title' => __( 'Select Box Products', 'plugin-domain' ),
					]
				],
				'title_field' => '{{{ title }}}',
			]
        );


        $this->end_controls_section();

        

        $this->start_controls_section(
            'step_three_section',
            [
                'label' => __( 'Step Three Configuration', 'plugin-name' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );


        $this->add_control(
            'step_three_title',
            [
                'label' => __( 'Step Three Title', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Choose Add-Ons',
                'label_block' => true,
                
            ]
        );
        
        $this->add_control(
            'step_three_products',
            [
                'label' => __( 'Select Step Three Products', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => avocado_product_list(),
                'label_block' => true,
            ]
        );


        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings_for_display();

        $step_1_q = new WP_Query( array(
            'posts_per_page' => 2, 
            'post_type' => 'product',
            'post__in' =>  $settings['base_products'], 
        ) );

        $step_2_q = new WP_Query( array(
            'posts_per_page' => 8, 
            'post_type' => 'product',
            'post__in' =>  $settings['step_three_products'], 
        ) );

        
            
        $html = '
        <script>
            jQuery(document).ready(function($) {
                $( "#step-checkout-1 .step-1-product-btn a.button").on( "click", function() {
                    $(\'.nav-tabs a[href="#step-checkout-2"]\').tab("show");
                    $("html, body").animate({ scrollTop: 0 } , "slow");
                    $(\'.nav-tabs a[href="#step-checkout-2"], .nav-tabs a[href="#step-checkout-3"]\').attr("data-toggle", "tab");
                    
                });


                $( ".next-step-link a" ).on( "click", function() {
                    $(\'.nav-tabs a[href="#step-checkout-3"]\').tab("show");
                    $("html, body").animate({ scrollTop: 0 } , "slow");
                    return false;
                });
            });
        </script>
        <div class="steped-checkout"> 
            <div class="step-indicator text-center">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#step-checkout-1" role="tab" aria-controls="step-checkout-1" aria-selected="true">Step 1</a></li>
                    <li class="nav-item"><a class="nav-link" href="#step-checkout-2" role="tab" aria-controls="step-checkout-2" aria-selected="false">Step 2</a></li>
                    <li class="nav-item"><a class="nav-link" href="#step-checkout-3" role="tab" aria-controls="step-checkout-3" aria-selected="false">Step 3</a></li>
                    <li class="nav-item"><a class="nav-link" href="#checkout" role="tab" aria-controls="step-checkout-3" aria-selected="false">Step 4</a></li>
                </ul>
            </div>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="step-checkout-1" role="tabpanel">
                    <div class="step-header-text">'.do_shortcode(wpautop($settings['top_text'])).'</div>

                    <div class="row">';
                    while( $step_1_q->have_posts()) : $step_1_q->the_post();
                        global $product;

                        $html .= '<div class="col">
                            <div class="step-1-product">
                                <div class="step-1-product-img">
                                    '.get_the_post_thumbnail(get_the_ID(), 'large').'
                                </div>
                                <div class="step-1-product-title">
                                    <h3>'.get_the_title().'</h3>
                                </div>
                                <div class="step-1-product-content">
                                    '.wpautop(get_the_content()).'
                                </div>
                                <div class="step-1-product-price">'.$product->get_price_html().'</div>
                                <div class="step-1-product-btn">
                                    '.do_shortcode('[add_to_cart style="" show_price="FALSE" id="'.get_the_ID().'"]').'
                                </div>
                            </div>
                        </div>';

                    endwhile; wp_reset_query();

                    $html .= '
                    </div>
                </div>
                <div class="tab-pane fade" id="step-checkout-2" role="tabpanel">
                    <h2 class="step-two-title text-center">'.$settings['step_two_title'].'</h2>
                    <div class="row">
                        <div class="col">
                            <img src="'.wp_get_attachment_image_url($settings['step_two_img'], 'large').'" alt="" />                    
                        </div>
                        <div class="col">
                            
                            '.wpautop($settings['step_two_content']).'';

                            if(!empty($settings['boxes'])) {
                                foreach($settings['boxes'] as $box) {
                                    $html .= '<div class="step-two-box">
                                        <h3>'.$box['title'].'</h3>';
                                        if(!empty($box['box_product_ids'])) {
                                            $html .= '<div class="row">';
                                                foreach($box['box_product_ids'] as $box_p) {
                                                    $html .= '<div class="col">
                                                        <div class="boxed-single-product">
                                                            <div class="boxed-product-bg" style="background-image:url('.get_the_post_thumbnail_url($box_p, 'medium').')"></div>
                                                            '.do_shortcode('[add_to_cart style="" show_price="FALSE" id="'.get_the_ID().'"]').'
                                                        </div>
                                                    </div>';
                                                }
                                            $html .= '</div>';
                                        }
                                    $html .= '    
                                    </div>';
                                }
                            }
                            $html .= '

                            <div class="next-step-link text-right"><a href="" class="bordered-btn boxed-btn">Next Step <i class="fa fa-double-angle-right"></i></a></div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="step-checkout-3" role="tabpanel">
                    <div class="row">';
                        while( $step_2_q->have_posts()) : $step_2_q->the_post();
                        global $product;
                        $checkouturl = wc_get_checkout_url();

                        $html .= '<div class="col-lg-3">
                            <div class="step-3-product">
                                <div class="step-3-product-img">
                                    '.get_the_post_thumbnail(get_the_ID(), 'large').'
                                </div>
                                <div class="step-3-product-title">
                                    <h3>'.get_the_title().'</h3>
                                </div>
                                <div class="step-3-product-content">
                                    '.wpautop(get_the_excerpt()).'
                                </div>
                                <div class="step-3-product-price">'.$product->get_price_html().'</div>
                                <div class="step-3-product-btn">
                                    '.do_shortcode('[add_to_cart style="" show_price="FALSE" id="'.get_the_ID().'"]').'
                                </div>
                            </div>
                        </div>';

                        endwhile; wp_reset_query();
                    $html .='    
                    </div>
                    <div class="row">
                        <div class="col text-center">
                            <div class="checkout-cta">
                                <h3>Ready To Checkout?</h3>
                                <a href="'.$checkouturl.'" class="checkout-btn boxed-btn">CHECKOUT</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';  


        $html .= '</div>';

        echo $html;

    }

}

//END Step Check Out 

//START Blgo posts   
class Blog_Posts  extends \Elementor\Widget_Base {

    public function get_name() {
        return 'blog_posts';
    }
    
    public function get_title() {
        return __( 'Featured Blog Posts', 'ppm-quickstart' );
    }

    public function get_icon() {
        return 'fa fa-code';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function _register_controls() {


        
        $this->start_controls_section(
            'blog',
            [
                'label' => __( 'Blog Posts', 'plugin-name' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings_for_display();

        $q = new WP_Query( array(
            'posts_per_page' => 5, 
            'post_type' => 'post',
        ) );
 
        $html = '   
        <div class="blog-posts-area">';
        
        while($q->have_posts()) : $q->the_post();
        $html .= '
            <a href="' . esc_url( get_permalink() ) . '" rel="bookmark" class="single-post">
                <div class="single-post-bg" style="background-image:url('.get_the_post_thumbnail_url(get_the_ID(), 'medium').')">
                </div>
                
                <h6 class="entry-title" >'.get_the_title().'</h6>
			</a>';
        endwhile; wp_reset_query();

        $html .= '</div>';

        echo $html;

    }

}

//END Blog posts



//START Products Reviews   
class Product_Reviews_Carousel  extends \Elementor\Widget_Base {

    public function get_name() {
        return 'product_reviews_carousel';
    }
    
    public function get_title() {
        return __( 'Product Reviews Carousel', 'ppm-quickstart' );
    }

    public function get_icon() {
        return 'fa fa-code';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function _register_controls() {


        
        $this->start_controls_section(
            'product_reviews',
            [
                'label' => __( 'Product Reviews Configuration', 'plugin-name' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings_for_display();

        $q = new WP_Query( array(
            'posts_per_page' => 10, 
            'post_type' => 'product',
        ) );
 
        $html = '   
        <div class="product-reivews-carousel">';
        
        while($q->have_posts()) : $q->the_post();
        global $product;
        $review_comment = do_action( 'woocommerce_review_comment_text', $comment );
        $html .= '
            <div class="single-review">
            <h3>'.get_the_title().'</h3>
            '.get_comment_author().'
            
                '.$product->get_review_count().'
            </div>
        ';
        endwhile; wp_reset_query();

        $html .= '</div>';

        echo $html;

    }

}

//END Products Reviews 


