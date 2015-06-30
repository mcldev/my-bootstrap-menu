<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 29/05/2015
 * Time: 16:26
 *
 * http://getbootstrap.com/2.3.2/components.html#navbar
 */


/**
 * Class My_Bootstrap_Menu_Nav_Menu_Markup
 * 
 */

class My_Bootstrap_Menu_Nav_Menu_Markup{

    private $settings;
    private $home_url;
    private $site_title;
    private $current_url;
    private $login_url;
    private $logout_url;
    private $register_url;
    private $unique_menu_id;
    /**
     * Creates the Nav Menu Markup class
     * @param My_Plugin_Settings_Public $settings
     */
    function __construct(My_Plugin_Settings_Public $settings)
    {
        $this->settings = $settings;
        $this->site_title = get_bloginfo('name');
        $this->home_url = esc_url( home_url( '/' ));
        $this->current_url = esc_url(get_permalink());
        $this->login_url = wp_login_url( $this->current_url);
        $this->logout_url = wp_logout_url($this->current_url);
        $this->register_url =  wp_registration_url();
        $this->unique_menu_id = 'menu_' . $settings->get_option_settings_db_name();
    }

    /**
     * By ref change the args array values if required, i.e. Container and Menu classes
     * @param $args
     */
    public function amend_arg_values(&$args)
    {
        $args->container = 'div';
        $args->container_class = "{$this->unique_menu_id}_container_class";
        $args->container_id = "{$this->unique_menu_id}_container";
        $args->menu_class = "{$this->settings->menu_type} {$this->settings->menu_alignment} {$this->settings->submenu_dropdown_direction}";
        $args->menu_id = "{$this->unique_menu_id}_outer_list";

    }

    /**
     * Main wrapper string for the nav menu object, to be used to surround the $nav output from the walker.
     *  Adds the menu icon button, search and login fields. Determines the type of menu, alignment and position.
     * @return string
     */
    public function get_navbar_prefix()
    {
        $html = '';
        $html .= $this->build_menu_prefix();
        return $html;
    }

    public function get_navbar_suffix()
    {
        $html = '';
        $html .= $this->build_menu_suffix();

        return $html;
    }


    /**
     * Moves the menu down if fixed top and admin bar is showing
     * @return string
     */
    private function fixed_top_spacer_div()
    {
        // Fix menu overlap bug..
        $html = '';
        if ($this->settings->navbar_fixed_type == 'navbar-fixed-top') {
            $html .= "<div style='min-height: 28px !important;'></div>";
        }
        return $html;
    }


    /**
     * Creates the prefix for the Nav Menu
     * @return string
     */
    private function build_menu_prefix ()
    {
        $html = '';

        //Wrap the whole menu in a container to limit to content width
        if ($this->settings->wrap_in_container)
            $html .= "<div class='container'>";

        //Main Nav Menu settings here - format and fixed type location
        $html .= "<nav class='navbar {$this->settings->navbar_format} {$this->settings->navbar_fixed_type}'
                        role='navigation'>";

        //Move the menu top down if the WP admin bar is displayed
        $html .=  is_admin_bar_showing() ? $this->fixed_top_spacer_div() : '';

        //Inner Nav div and Container or Container-Fluid
        $html .= "    <div class='navbar-inner'>
                            <div class='{$this->settings->class_container}'>";

        //Header section for the collapsed button and brand/logo.
        $html .= "<div class='navbar-header'>";

        //3 icon bar button for the collapsed menu only.
        if($this->settings->display_icon_bar_button)
            $html .= "<button type='button'
                            class='navbar-toggle'
                            data-toggle='collapse'
                            data-target='#{$this->unique_menu_id}'
                            aria-expanded='false'>
                        <span class='sr-only'>Toggle navigation</span>
                        <span class='icon-bar'></span>
                        <span class='icon-bar'></span>
                        <span class='icon-bar'></span>
                    </button>";

        //Get logo and title visible only for collapsed menu.
        $html .= $this->get_display_logo('visible-xs');
        $html .= $this->get_display_title('visible-xs');

        //close navbar header section
        $html .= "</div> <!-- close navbar-header-->";

        //Collapse menu target and target id
        $html .= "<div class='collapse navbar-collapse'
                       id='{$this->unique_menu_id}'>";

        //Get logo and title visible only for full menu, includes alignment left/right
        // this is deliberately separated from the navbrand above as gives flexibility for edge case of separate left/right align logo/title
        $html .= $this->get_display_logo('hidden-xs ' . $this->settings->logo_alignment);
        $html .= $this->get_display_title('hidden-xs ' . $this->settings->title_alignment);

        //Nav Menu Walker continues here...

        return $html;
    }

    /**
     * Creates the suffix for the Nav Menu
     * @return string
     */
    private function build_menu_suffix()
    {
        $html = '';

        //Add the search field if set
        $html .= $this->get_search_field();

        //Add the login/logout url
        $html .= $this->get_login();

        //Add register icon, only if user is not logged in
        $html .= $this->get_register();


        //Close standard divs
        $html .= "            </div><!-- navbar-collapse -->
                        </div> <!-- class container -->
                    </div> <!-- navbar inner -->
                </nav> <!-- nav class --> ";

        //Close wrapper class if required
        if ($this->settings->wrap_in_container)
            $html .= "</div><!-- wrapper container class --> ";

        $html .= ($this->settings->include_div_for_fixed_top) ?  $this->fixed_top_spacer_div() : '';

        return $html;
    }

    /**
     * Class used to tweak the css to fix having both the title and logo in the header menu
     * @return string
     */
    private function get_navbar_title_and_logo_class()
    {
        $html = '';
        if($this->settings->display_title && $this->settings->display_logo)
            $html .= 'navbar-title-logo';

        return $html;
    }

    /**
     * Display the logo if required
     * @param $additional_class
     * @return string
     */
    private function get_display_logo($additional_class)
    {
        $html = '';

        if ($this->settings->display_logo && ($this->settings->logo_url != '' || $this->settings->logo_small_url != '')) {

            if($additional_class == 'visible-xs') {
                $logo_url = ($this->settings->logo_small_url != '') ? $this->settings->logo_small_url : $this->settings->logo_url;
            } else {
                $logo_url = $this->settings->logo_url;
            }

            $height = ($this->settings->logo_height != '') ? "height='{$this->settings->logo_height}'" : "";
            $width = ($this->settings->logo_width != '') ? "width='{$this->settings->logo_width}'" : "";

            $html .= "<a class='navbar-brand {$additional_class} {$this->get_navbar_title_and_logo_class()}' href='{$this->home_url}'>
                            <img src='{$logo_url}'
                               title='{$this->home_url}'
                               {$height}
                               {$width}>
                       </a>";
        }
        return $html;
    }

    /*
     * Display the title if selected
     */
    private function get_display_title($additional_class)
    {
        $html = '';
        if($this->settings->display_title)
            $html .= "<a class='navbar-brand $additional_class {$this->get_navbar_title_and_logo_class()}'
                         href='{$this->home_url}'
                         {$this->get_title_style()}>{$this->site_title}</a>";
        return $html;
    }

    private function get_title_style()
    {
        $html = '';
        if($this->settings->title_text_transform != '')
            $html .= "style='text-transform:{$this->settings->title_text_transform};'";
        return $html;
    }

    /**
     * Builds a search box with either a glyphicon to search of a full button.
     * uses the default text to temporarily fill the search box
     * @return string
     */
    private function get_search_field()
    {
        $html = '';
        if ($this->settings->display_search)
            $html .= "<ul class='nav navbar-nav {$this->settings->search_alignment}'>
                        <li>
                            <form method='get'
                                  id='searchform'
                                  action='{$this->home_url}'
                                  class='navbar-form'
                                  role='search'>
                                <div class='form-group'>
                                    <input class='form-control'
                                           type='text'
                                           size={$this->settings->search_box_width}
                                           name='s'
                                           id='s'
                                           value='{$this->settings->search_default_value}'
                                           onfocus=\"if(this.value==this.defaultValue)this.value='';\"
                                           onblur=\"if(this.value=='')this.value=this.defaultValue;\"/>
                                    <input type='submit'
                                           id='{$this->unique_menu_id}_search'
                                           value='search'
                                           class='btn form-control hidden' />
                                    <label for='{$this->unique_menu_id}_search'
                                            class='btn {$this->settings->search_button_type}'>
                                            <i class='{$this->settings->search_glyphicon}'></i>{$this->settings->search_label}</label>
                                </div>
                            </form>
                        </li>
                    </ul>";

        return $html;
    }

    /**
     * Get the login if required, uses a glyphicon if selected
     * @return string
     */
    private function get_login()
    {
        $html = '';
        if($this->settings->display_login) {

            $html .= "<ul class='nav navbar-nav {$this->settings->login_alignment}'>
                            <li>";

            if(is_user_logged_in()){
                $login_logout_url =  $this->logout_url;
                $login_logout_label = $this->settings->logout_label;
                $login_logout_glyhicon = $this->settings->logout_glyphicon;
            } else {
                $login_logout_url =  $this->login_url;
                $login_logout_label = $this->settings->login_label;
                $login_logout_glyhicon = $this->settings->login_glyphicon;
            }
            $login_logout_url = esc_url($login_logout_url);

            $html .= "<a href='{$login_logout_url}'><span class='{$login_logout_glyhicon}'></span>{$login_logout_label}</a>";
            $html .= "</li>
                    </ul>";
        }
        return $html;
    }

    /**
     * Gets the register button if required, uses a glyphicon if selected
     * @return string
     */
    private function get_register()
    {
        $html = '';
        if($this->settings->display_register && !is_user_logged_in()) {
            $html .= "<ul class='nav navbar-nav {$this->settings->register_alignment}'>
                            <li>
                                <a href='{$this->register_url}'><span class='{$this->settings->register_glyphicon}'></span>{$this->settings->register_label}</a>
                            </li>
                        </ul>";
        }
        return $html;
    }


    private function nav_menu_html ()
    {
        $html = " <!-- yes/no : wrap_in_container [wrap_in_container]-->
                <div class='container'>

                    <!-- default/inverse, Fixed/static etc: Navbar_Format : navbar_format + navbar_fixed_type -->
                    <nav class='navbar [navbar_format] [navbar_fixed_type]'
                        role='navigation'>

                        <div class='navbar-inner'> <!-- standard -->

                            <div class='[class_container]'> <!-- container/container-fluid -->


                                <!-- branding and 3-bar-icon button -->
                                <!--************************************** -->
                                <div class='navbar-header'>

                                    <!-- yes/no : display_icon_bar_button [display_icon_bar_button] -->

                                    <button type='button'
                                            class='navbar-toggle'
                                            data-toggle='collapse'
                                            data-target='#[unique_menu_id]'
                                            aria-expanded='false'>
                                        <span class='sr-only'>Toggle navigation</span>
                                        <span class='icon-bar'></span>
                                        <span class='icon-bar'></span>
                                        <span class='icon-bar'></span>
                                    </button>

                                 <!-- Brand and toggle get grouped for better mobile display-->

                                 <!-- yes/no : display_logo -->
                                 <!-- if both Title and Logo: use class navbar-title-logo [display_logo] -->
                                 <!--************************************** -->
                                    <a class='navbar-brand visible-xs [navbar-title-logo]' href='[home_url]'>
                                        <img src='[logo_url]'
                                           height='[logo_height]'
                                           width='[logo_width]'
                                           title='[site_title]'> <!-- logo_url OR logo_small_url: logo_height : logo_width -->
                                    </a>
                                    <!-- yes/no : display_title -->
                                    <!-- if both Title and Logo: use class navbar-title-logo [display_title] -->
                                    <!--************************************** -->
                                    <a class='navbar-brand visible-xs [navbar-title-logo]'
                                        href='[home_url]'
                                        style='text-transform:[title_text_transform];'>[site_title]</a>

                                </div>
                                
                                
                                <!-- Collapse menu - uses unique menu id -->
                                <!-- The data in this container is hidden/reshaped on toggle size and button id is linked to unique_menu_id -->
                                <div class='collapse navbar-collapse'
                                     id='[unique_menu_id]'>

                                    <!-- yes/no : display_title - having both allows for independent left/right alignment which is then hidden on mobile version-->
                                        <!-- if both Title and Logo: use class navbar-title-logo [display_title] -->
                                         <!-- navbar-left/navbar-right : title_alignment -->
                                        <!--************************************** -->
                                        <a class='navbar-brand hidden-xs [navbar-title-logo] [title_alignment]'
                                            href='[home_url]'
                                            style='text-transform:[title_text_transform];'>[site_title]</a> 
                                            
                                    <!-- yes/no : display_logo  -->        
                                    <!-- if both Title and Logo: use class navbar-title-logo [display_logo] -->
                                     <!--************************************** -->
                                        <a class='navbar-brand hidden-xs [navbar-title-logo] [logo_alignment]' href='[home_url]'>
                                            <img src='[logo_url]'
                                               height='[logo_height]'
                                               width='[logo_width]'
                                               title='[site_title]'> <!-- title=''.blog_info_title.' '.blog_description_bootstrap.'' rel='home'
                                                                        logo_url OR logo_small_url: logo_height : logo_width -->
                                        </a>


                                <!-- Args + Nav Menu Walker control inputs from here -->
                                <!--************************************** -->
                                <!-- container_id : passed into args -->
                                <div id='[unique_menu_id]_container'
                                    class='[unique_menu_id]__class'>
                                <!-- container_class  : passed into args -->

                                    <!-- menu_id  : passed into args -->
                                    <ul id='[unique_menu_id]_list'
                                        class='[menu_type] [menu_alignment] [submenu_dropdown_direction]'>
                                         <!-- nav nav-pills/nav-tabs/navbar-nav menu_class  : Menu_Type : menu_type passed into args -->


                                        <!-- Navbar Walker START -->
                                        <!--************************************** -->

                                        <!-- Tabs/Pills and Navbar-Nav Menus -->
                                        <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
                                        <!-- start_el : Nav Menu Walker, start element-->
                                        <li class='active'>
                                            <a href='#'>Link <span class='sr-only'>(current)</span></a>
                                        </li>
                                        <!--end_el : Nav Menu Walker, ends the element -->

                                        <li>
                                            <a href='#'>Link</a>
                                        </li>

                                        <li role='presentation' class='dropdown'>
                                            <a id='dLabel'
                                               role='button'
                                               data-toggle='dropdown'
                                               class='dropdown-toggle'
                                               data-target='#'
                                               href='#'>
                                                   Dropdown <span class='caret'></span>
                                            </a>
                                            <!-- add class  multi-level if required? -->
                                            <ul class='dropdown-menu'
                                                role='menu'
                                                aria-labelledby='dropdownMenu'>

                                                <!-- Menu Item -->
                                                <li><a href='#'>Some action</a></li>
                                                <li><a href='#'>Some other action</a></li>
                                                <li class='divider'></li>

                                                <!-- Menu Item with submenu -->
                                                <li class='dropdown-submenu'>
                                                    <a tabindex='-1' href='#'>Hover me for more options</a>

                                                    <!-- start_lvl -->
                                                    <ul class='dropdown-menu'>
                                                        <li><a tabindex='-1' href='#'>Second level</a></li>

                                                        <!-- Menu Item with submenu -->
                                                        <li class='dropdown-submenu'>
                                                            <a href='#'>Even More..</a>

                                                            <!-- start_lvl -->
                                                            <ul class='dropdown-menu'>
                                                                <li><a href='#'>3rd level</a></li>
                                                                <li><a href='#'>3rd level</a></li>
                                                            </ul>
                                                        </li>

                                                        <li><a href='#'>Second level</a></li>
                                                        <li><a href='#'>Second level</a></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>
                                        <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->

                                            
                                        <!-- OR Button Group Menu here... -->
                                        <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->

                                            <div class='button_menu'>

                                                <!--  btn-group-justified   btn-group   btn-group  nav navbar-right  navbar-left  -->
                                                <div class='navbar-btn btn-group  navbar-left  ' role='group' aria-label='...'>

                                                    <div class='btn-group [button_group_size]' role='group'>
                                                        <a class='btn [button_menu_type]' href='#' role='button'>Link</a>
                                                    </div>
                                                    <div class='btn-group [button_group_size]' role='group'>
                                                        <a class='btn [button_menu_type]' href='#' role='button'>Link2</a>
                                                    </div>
                                                    <div class='btn-group [button_group_size]' role='group'>
                                                            <a type='button'
                                                                    class='btn [button_menu_type] dropdown-toggle'
                                                                    data-toggle='dropdown'
                                                                    aria-haspopup='true'
                                                                    aria-expanded='false'>Dropdown<span class='caret'></span><!-- yes/no : display_caret [display_caret]-->
                                                            </a>
                                                            <ul class='dropdown-menu [submenu_dropdown_alignment]'>
                                                                <li><a href='#'>Dropdown link</a></li>
                                                                <li><a href='#'>Dropdown link</a></li>
                                                                <li class='dropdown-submenu'>
                                                                    <a href='#'>Even More..</a>

                                                                    <!-- start_lvl -->
                                                                    <ul class='dropdown-menu [submenu_dropdown_alignment]'>
                                                                        <li><a href='#'>3rd level</a></li>
                                                                        <li><a href='#'>3rd level</a></li>
                                                                    </ul>
                                                                </li>
                                                            </ul>
                                                    </div>

                                                </div>
                                            </div>
                                            
                                            
                                            
                                        <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->

                                       
                                       </ul>
                                        <!--************************************** -->
                                        <!-- Navbar Walker END -->

                                    </div>
                                    <!--************************************** -->
                                    <!-- Args + Navbar Walker END -->



                                    <!-- yes/no : display_search [display_search]-->
                                    <!--************************************** -->

                                    <ul class='nav navbar-nav [search_alignment]'>
                                        <li>
                                            <form method='get'
                                                  id='searchform'
                                                  action='[bloginfo_url]'
                                                  class='navbar-form'
                                                  role='search'>
                                                <div class='form-group'>
                                                    <input class='form-control'
                                                           type='text'
                                                           size=[search_box_width]
                                                           name='s'
                                                           id='s'
                                                           value='[search_default_value]'
                                                           onfocus='if(this.value==this.defaultValue)this.value='';'
                                                           onblur='if(this.value=='')this.value=this.defaultValue;'/>
                                                    <input type='submit'
                                                           id='[unique_menu_id]_search'
                                                           value='search'
                                                           class='btn form-control hidden' />
                                                    <label for='[unique_menu_id]_search'
                                                            class='btn [search_button_type]'>
                                                            <i class='[search_glyphicon]'></i>[search_label]</label>
                                                </div>
                                            </form>
                                        </li>
                                    </ul>

                                    <!-- yes/no : display_login [display_login] -->
                                    <!--************************************** -->
                                    <ul class='nav navbar-nav [login_alignment]'>
                                        <li>
                                            <a href='[login_URL]'><span class='[login_glyphicon] / [logout_glyphicon]'></span>[login_label]</a>
                                        </li>
                                    </ul>

                                    <!-- yes/no : display_register -->
                                    <!--************************************** -->
                                    <ul class='nav navbar-nav [register_alignment]'>
                                        <li>
                                            <a href='[register_URL]'><span class='[register_glyphicon]'></span>[register_label]</a>
                                        </li>
                                    </ul>




                                </div><!-- /.navbar-collapse -->
                            </div><!-- /.container-fluid -->
                        </div> <!-- /.navbar-inner -->
                    </nav>
                </div>";


    }
}