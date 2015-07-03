<?php
/*
 * My_Plugin_Admin_Notice
 * Michael Carder
 */
namespace My_Bootstrap_Menu_Plugin_Helper {


    class My_Plugin_Admin_Notice
    {
        private $show_on_page_id;
        private $add_action_on;
        private $msg;
        private $class;

        function __construct($show_on_page_id = null, $add_action_on = 'admin_notices')
        {
            $this->show_on_page_id = $show_on_page_id;
            $this->add_action_on = $add_action_on;
        }


        public function add_admin_notice($msg, $class = My_Plugin_Notice_Type::Error)
        {
            $this->msg = ucfirst($class) . ': ' . $msg;
            $this->class = $class;

            add_action($this->add_action_on, array($this, 'print_admin_notice'));
        }

        public function print_admin_notice()
        {
            //If not on specified screen, then return
            global $current_screen;
            $current_screen_id = $current_screen->id;

            if (isset($this->show_on_page_id) && $current_screen_id != $this->show_on_page_id) return;

            self::display_admin_notice($this->msg, $this->class);
        }

        protected static function display_admin_notice($msg, $class)
        {
            ?>
            <div class="<?php esc_attr_e($class); ?>">
                <p><?php echo wp_kses($msg, wp_kses_allowed_html('post')); ?></p>
            </div>
        <?php
        }
    }

}
