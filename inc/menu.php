<?php
/**
 * 自定义菜单
 */
class MyMenu extends Walker_Nav_Menu
{
    public $tree_type = array('post_type', 'taxonomy', 'custom');

    public $db_fields = array(
        'parent' => 'menu_item_parent',
        'id'     => 'db_id',
    );


    public function start_lvl(&$output, $depth = 0, $args = array())
    {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"child-menu\">\n";
        // $output .= "\n$indent<ul class=\"select\">\n";
    }

    public function end_lvl(&$output, $depth = 0, $args = null)
    {
        $t = "\t";
        $n = "\n";
        $indent  = str_repeat($t, $depth);
        $output .= $indent . "</ul>{$n}";
    }


    public function start_el(&$output, $data_object, $depth = 0, $args = null, $current_object_id = 0)
    {
        $menu_item = $data_object;
        // p($menu_item);

        $t = "\t";
        $n = "\n";

        $indent = ($depth) ? str_repeat($t, $depth) : '';

        // li 标签类名
        $classes = empty($menu_item->classes) ? array() : (array) $menu_item->classes;
        if (in_array('menu-item-has-children', $classes)) {
            $class_names = 'menu-item-has-children';
        }

        // echo "\n$menu_item->title\n";
        // p($menu_item->classes); 

        // a 标签属性
        $atts['href'] = !empty($menu_item->url) ? $menu_item->url : '';
        $atts = apply_filters('nav_menu_link_attributes', $atts, $menu_item, $args, $depth);
        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (is_scalar($value) && '' !== $value && false !== $value) {
                $value       = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $class_names = implode(' ', apply_filters('nav_menu_css_class', array_filter($classes), $menu_item, $args, $depth));
        $class_names = $class_names ? ' class="' . $class_names . '"' : '';
        $output .= $indent . '<li' . $class_names . '>';

        $title = apply_filters('the_title', $menu_item->title, $menu_item->ID);
        $item_output  = $args->before;

        // if( $menu_item->classes[0] ){
        //     $item_output .= '<i class="iconfont '.$menu_item->classes[0].'"></i>';
        // }

        $item_output .= '<a' . $attributes . '>';
        // $item_output .= "<a class=\"category-title\" href=\"#id{$menu_item->ID}\">";
        $item_output .= $args->link_before . $title . $args->link_after;
        if (in_array('menu-item-has-children', $classes)) {
            $item_output .= '<i class="fa-solid fa-chevron-down"></i></a>';
            // $item_output .= '</a><i class="iconfont">&#xe8a4;</i>';
        } else {
            $item_output .= '</a>';
        }
        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $menu_item, $depth, $args);
    }


    /**
     * Ends the element output, if needed.
     *
     * @since 3.0.0
     * @since 5.9.0 Renamed `$item` to `$data_object` to match parent class for PHP 8 named parameter support.
     *
     * @see Walker::end_el()
     *
     * @param string   $output      Used to append additional content (passed by reference).
     * @param WP_Post  $data_object Menu item data object. Not used.
     * @param int      $depth       Depth of page. Not Used.
     * @param stdClass $args        An object of wp_nav_menu() arguments.
     */
    public function end_el(&$output, $data_object, $depth = 0, $args = null)
    {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $output .= "</li>{$n}";
    }

    public static function fallback($args)
    {
        extract($args);

        $fb_output = null;

        if ($container) {
            $fb_output = '<' . $container;

            if ($container_id) {
                $fb_output .= ' id="' . $container_id . '"';
            }

            if ($container_class) {
                $fb_output .= ' class="' . $container_class . '"';
            }

            $fb_output .= '>';
        }

        $fb_output .= '<ul';

        if ($menu_id) {
            $fb_output .= ' id="' . $menu_id . '"';
        }

        if ($menu_class) {
            $fb_output .= ' class="' . $menu_class . '"';
        }

        $fb_output .= '>';
        $fb_output .= '<li class="menu-item"><a href="' . esc_url(admin_url('nav-menus.php')) . '">' . __('添加菜单', 'vt') . '</a></li>';
        $fb_output .= '</ul>';

        if ($container) {
            $fb_output .= '</' . $container . '>';
        }

        echo wp_kses($fb_output, array(
            'ul'   => array('id' => array(), 'class' => array()),
            'li'   => array('class' => array()),
            'a'    => array('href' => array()),
            'span' => array(),
        ));
    }
}


/******************************************************************************
 * 侧边小工具使用的自定义菜单
 */
class SideMenu extends Walker_Nav_Menu
{
    public $tree_type = array('post_type', 'taxonomy', 'custom');

    public $db_fields = array(
        'parent' => 'menu_item_parent',
        'id'     => 'db_id',
    );


    public function start_lvl(&$output, $depth = 0, $args = array())
    {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"side-child-menu\">\n";
        // $output .= "\n$indent<ul class=\"select\">\n";
    }

    public function end_lvl(&$output, $depth = 0, $args = null)
    {
        $t = "\t";
        $n = "\n";
        $indent  = str_repeat($t, $depth);
        $output .= $indent . "</ul>{$n}";
    }


    public function start_el(&$output, $data_object, $depth = 0, $args = null, $current_object_id = 0)
    {
        $menu_item = $data_object;

        $t = "\t";
        $n = "\n";

        $indent = ($depth) ? str_repeat($t, $depth) : '';

        // li 标签类名
        $classes = empty($menu_item->classes) ? array() : (array) $menu_item->classes;
        if (in_array('menu-item-has-children', $classes)) {
            $class_names = 'menu-item-has-children';
        }

        // a 标签属性
        $atts['href'] = !empty($menu_item->url) ? $menu_item->url : '';
        $atts = apply_filters('nav_menu_link_attributes', $atts, $menu_item, $args, $depth);
        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (is_scalar($value) && '' !== $value && false !== $value) {
                $value       = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $class_names = implode(' ', apply_filters('nav_menu_css_class', array_filter($classes), $menu_item, $args, $depth));
        $class_names = $class_names ? ' class="' . $class_names . '"' : '';
        $output .= $indent . '<li' . $class_names . '>';

        $title = apply_filters('the_title', $menu_item->title, $menu_item->ID);
        $item_output  = $args->before;
        
        $item_output .= '<a' . $attributes . '>';
        // if (in_array('current-menu-item', $classes)) {
        //     $item_output .= '<i class="fa-solid fa-check"></i>';
        // } else {
        //     $item_output .= '';
        // }

        // $item_output .= "<a class=\"category-title\" href=\"#id{$menu_item->ID}\">";
        $item_output .= $args->link_before . $title . $args->link_after;


        if($depth == 1){
            $item_output .= '('.get_category($menu_item->object_id)->count.')';
        }

        if (in_array('menu-item-has-children', $classes)) {
            $item_output .= '<i class="fa-solid fa-chevron-up arrow"></i></a>';
        } else {
            $item_output .= '</a>';
        }
        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $menu_item, $depth, $args);
    }


    /**
     * Ends the element output, if needed.
     *
     * @since 3.0.0
     * @since 5.9.0 Renamed `$item` to `$data_object` to match parent class for PHP 8 named parameter support.
     *
     * @see Walker::end_el()
     *
     * @param string   $output      Used to append additional content (passed by reference).
     * @param WP_Post  $data_object Menu item data object. Not used.
     * @param int      $depth       Depth of page. Not Used.
     * @param stdClass $args        An object of wp_nav_menu() arguments.
     */
    public function end_el(&$output, $data_object, $depth = 0, $args = null)
    {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $output .= "</li>{$n}";
    }

    public static function fallback($args)
    {
        extract($args);

        $fb_output = null;

        if ($container) {
            $fb_output = '<' . $container;

            if ($container_id) {
                $fb_output .= ' id="' . $container_id . '"';
            }

            if ($container_class) {
                $fb_output .= ' class="' . $container_class . '"';
            }

            $fb_output .= '>';
        }

        $fb_output .= '<ul';

        if ($menu_id) {
            $fb_output .= ' id="' . $menu_id . '"';
        }

        if ($menu_class) {
            $fb_output .= ' class="' . $menu_class . '"';
        }

        $fb_output .= '>';
        $fb_output .= '<li class="menu-item"><a href="' . esc_url(admin_url('nav-menus.php')) . '">' . __('添加菜单', 'vt') . '</a></li>';
        $fb_output .= '</ul>';

        if ($container) {
            $fb_output .= '</' . $container . '>';
        }

        echo wp_kses($fb_output, array(
            'ul'   => array('id' => array(), 'class' => array()),
            'li'   => array('class' => array()),
            'a'    => array('href' => array()),
            'span' => array(),
        ));
    }

    
}
