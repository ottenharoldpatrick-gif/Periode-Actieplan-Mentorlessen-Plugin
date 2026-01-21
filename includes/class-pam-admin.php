<?php
/**
 * Admin functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class PAM_Admin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_ajax_pam_save_settings', array($this, 'ajax_save_settings'));
        add_action('wp_ajax_pam_save_student_meta', array($this, 'ajax_save_student_meta'));
        add_action('wp_ajax_pam_toggle_period_status', array($this, 'ajax_toggle_period_status'));
        add_action('wp_ajax_pam_save_mentor_notes', array($this, 'ajax_save_mentor_notes'));
    }
    
    public function add_admin_menu() {
        add_menu_page(
            __('Mentorlessen', 'periode-actieplan'),
            __('Mentorlessen', 'periode-actieplan'),
            'edit_posts',
            'pam-dashboard',
            array($this, 'render_dashboard_page'),
            'dashicons-welcome-learn-more',
            30
        );
        
        add_submenu_page(
            'pam-dashboard',
            __('Dashboard', 'periode-actieplan'),
            __('Dashboard', 'periode-actieplan'),
            'edit_posts',
            'pam-dashboard',
            array($this, 'render_dashboard_page')
        );
        
        add_submenu_page(
            'pam-dashboard',
            __('Leerlingen', 'periode-actieplan'),
            __('Leerlingen', 'periode-actieplan'),
            'edit_posts',
            'pam-students',
            array($this, 'render_students_page')
        );
        
        add_submenu_page(
            'pam-dashboard',
            __('Onvoldoendes', 'periode-actieplan'),
            __('Onvoldoendes', 'periode-actieplan'),
            'edit_posts',
            'pam-failures',
            array($this, 'render_failures_page')
        );
        
        add_submenu_page(
            'pam-dashboard',
            __('Instellingen', 'periode-actieplan'),
            __('Instellingen', 'periode-actieplan'),
            'manage_options',
            'pam-settings',
            array($this, 'render_settings_page')
        );
    }
    
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'pam-') === false) {
            return;
        }
        
        wp_enqueue_style('pam-admin', PAM_PLUGIN_URL . 'assets/css/admin.css', array(), PAM_VERSION);
        wp_enqueue_script('pam-admin', PAM_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), PAM_VERSION, true);
        
        wp_localize_script('pam-admin', 'pamAdminData', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('pam_admin_nonce'),
            'texts' => array(
                'confirm_delete' => __('Weet je zeker dat je dit wilt verwijderen?', 'periode-actieplan'),
                'saving' => __('Opslaan...', 'periode-actieplan'),
                'saved' => __('Opgeslagen!', 'periode-actieplan')
            )
        ));
    }
    
    public function render_dashboard_page() {
        $periods = get_option('pam_periods', array());
        $active_period = null;
        
        foreach ($periods as $index => $period) {
            if (isset($period['active']) && $period['active']) {
                $active_period = $index + 1;
                break;
            }
        }
        
        // Get statistics
        $stats = $this->get_dashboard_stats($active_period);
        
        include PAM_PLUGIN_DIR . 'templates/admin-dashboard.php';
    }
    
    private function get_dashboard_stats($period_number) {
        global $wpdb;
        $grades_table = $wpdb->prefix . 'pam_grades';
        
        $stats = array(
            'total_students' => 0,
            'completed_students' => 0,
            'students_with_failures' => 0,
            'average_grade' => 0
        );
        
        if (!$period_number) {
            return $stats;
        }
        
        // Total students with data in this period
        $stats['total_students'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT user_id) FROM $grades_table WHERE period_number = %d",
            $period_number
        ));
        
        // Completed students (status = closed)
        $stats['completed_students'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT user_id) FROM $grades_table WHERE period_number = %d AND status = 'closed'",
            $period_number
        ));
        
        // Students with 2+ failures
        $failures = PAM_Database::get_students_with_failures($period_number);
        $stats['students_with_failures'] = count($failures);
        
        // Average grade
        $stats['average_grade'] = $wpdb->get_var($wpdb->prepare(
            "SELECT AVG(grade) FROM $grades_table WHERE period_number = %d AND grade IS NOT NULL",
            $period_number
        ));
        
        return $stats;
    }
    
    public function render_students_page() {
        // Get all students (users enrolled in Sensei course "Mentor")
        $students = $this->get_mentor_students();
        
        // Get selected student if any
        $selected_student_id = isset($_GET['student']) ? intval($_GET['student']) : null;
        $selected_student_data = null;
        
        if ($selected_student_id) {
            $selected_student_data = $this->get_student_full_data($selected_student_id);
        }
        
        include PAM_PLUGIN_DIR . 'templates/admin-students.php';
    }
    
    private function get_mentor_students() {
        // Get users enrolled in Sensei "Mentor" course
        // This is a simplified version - you'll need to integrate with Sensei's API
        $args = array(
            'role__in' => array('subscriber', 'student'),
            'orderby' => 'display_name',
            'order' => 'ASC'
        );
        
        $users = get_users($args);
        $students = array();
        
        foreach ($users as $user) {
            $meta = PAM_Database::get_student_meta($user->ID);
            $students[] = array(
                'id' => $user->ID,
                'name' => $user->display_name,
                'email' => $user->user_email,
                'class' => $meta ? $meta['class_name'] : '',
                'mentor_1' => $meta ? $meta['mentor_1'] : null,
                'mentor_2' => $meta ? $meta['mentor_2'] : null
            );
        }
        
        return $students;
    }
    
    private function get_student_full_data($user_id) {
        $user = get_userdata($user_id);
        if (!$user) {
            return null;
        }
        
        $meta = PAM_Database::get_student_meta($user_id);
        $subjects = PAM_Database::get_student_subjects($user_id);
        
        $periods_data = array();
        for ($i = 1; $i <= 4; $i++) {
            $grades = PAM_Database::get_grades($user_id, $i);
            $status = PAM_Database::get_period_status($user_id, $i);
            $mentor_notes = PAM_Database::get_mentor_notes($user_id, $i);
            
            $improvement_plans = array();
            foreach ($grades as $grade) {
                if ($grade['grade'] !== null && $grade['grade'] < 6.0) {
                    $plan = PAM_Database::get_improvement_plan($user_id, $i, $grade['subject_name']);
                    if ($plan) {
                        $improvement_plans[] = $plan;
                    }
                }
            }
            
            $periods_data[] = array(
                'number' => $i,
                'grades' => $grades,
                'status' => $status,
                'improvement_plans' => $improvement_plans,
                'mentor_notes' => $mentor_notes
            );
        }
        
        return array(
            'user' => $user,
            'meta' => $meta,
            'subjects' => $subjects,
            'periods' => $periods_data
        );
    }
    
    public function render_failures_page() {
        $period_filter = isset($_GET['period']) ? intval($_GET['period']) : null;
        $students_with_failures = PAM_Database::get_students_with_failures($period_filter);
        
        // Enrich with user data
        foreach ($students_with_failures as &$student) {
            $user = get_userdata($student['user_id']);
            $student['name'] = $user ? $user->display_name : 'Unknown';
            $student['email'] = $user ? $user->user_email : '';
            
            // Get actual failing grades
            $grades = PAM_Database::get_grades($student['user_id'], $student['period_number']);
            $failing_subjects = array();
            foreach ($grades as $grade) {
                if ($grade['grade'] !== null && $grade['grade'] < 6.0) {
                    $failing_subjects[] = array(
                        'subject' => $grade['subject_name'],
                        'grade' => $grade['grade']
                    );
                }
            }
            $student['failing_subjects'] = $failing_subjects;
        }
        
        include PAM_PLUGIN_DIR . 'templates/admin-failures.php';
    }
    
    
    public function render_settings_page() {
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'subjects';
    
        $subjects = get_option('pam_subjects', array());
        $periods = get_option('pam_periods', array());
    
    // TOEVOEGEN: Initialiseer periodes als ze nog niet bestaan
    if (empty($periods)) {
        $periods = array(
            array('name' => 'Periode 1', 'active' => true),
            array('name' => 'Periode 2', 'active' => false),
            array('name' => 'Periode 3', 'active' => false),
            array('name' => 'Periode 4', 'active' => false)
            );
            update_option('pam_periods', $periods);
        }
        
        $mentor_role = get_option('pam_mentor_role', 'teacher');
        $email_template = get_option('pam_email_template', '');
        $pdro_questions = get_option('pam_pdro_questions', array());
        
        include PAM_PLUGIN_DIR . 'templates/admin-settings.php';
    }
    
        
        public function ajax_save_settings() {
        check_ajax_referer('pam_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Geen toegang', 'periode-actieplan')));
        }
        
        $setting_type = isset($_POST['setting_type']) ? sanitize_text_field($_POST['setting_type']) : '';
        
        switch ($setting_type) {
            case 'subjects':
                $subjects = isset($_POST['subjects']) ? array_map('sanitize_text_field', $_POST['subjects']) : array();
                update_option('pam_subjects', array_filter($subjects));
                break;
                
            case 'periods':
                $periods = isset($_POST['periods']) ? $_POST['periods'] : array();
                update_option('pam_periods', $periods);
                break;
                
            case 'mentor_role':
                $role = sanitize_text_field($_POST['mentor_role']);
                update_option('pam_mentor_role', $role);
                break;
                
            case 'email':
                $template = wp_kses_post($_POST['email_template']);
                update_option('pam_email_template', $template);
                break;
                
            case 'pdro':
                $questions = isset($_POST['questions']) ? array_map('sanitize_textarea_field', $_POST['questions']) : array();
                update_option('pam_pdro_questions', $questions);
                break;
        }
        
        wp_send_json_success(array('message' => __('Instellingen opgeslagen', 'periode-actieplan')));
    }
    
    public function ajax_save_student_meta() {
        check_ajax_referer('pam_admin_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Geen toegang', 'periode-actieplan')));
        }
        
        $user_id = intval($_POST['user_id']);
        $data = array(
            'mentor_1' => isset($_POST['mentor_1']) ? intval($_POST['mentor_1']) : null,
            'mentor_2' => isset($_POST['mentor_2']) ? intval($_POST['mentor_2']) : null,
            'class_name' => isset($_POST['class_name']) ? sanitize_text_field($_POST['class_name']) : null
        );
        
        PAM_Database::save_student_meta($user_id, $data);
        
        wp_send_json_success(array('message' => __('Gegevens opgeslagen', 'periode-actieplan')));
    }
    
    public function ajax_toggle_period_status() {
        check_ajax_referer('pam_admin_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Geen toegang', 'periode-actieplan')));
        }
        
        $user_id = intval($_POST['user_id']);
        $period_number = intval($_POST['period_number']);
        $action = sanitize_text_field($_POST['action_type']);
        
        if ($action === 'open') {
            PAM_Database::open_period($user_id, $period_number);
        } else {
            PAM_Database::close_period($user_id, $period_number);
        }
        
        wp_send_json_success(array('message' => __('Status aangepast', 'periode-actieplan')));
    }
    
    public function ajax_save_mentor_notes() {
        check_ajax_referer('pam_admin_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Geen toegang', 'periode-actieplan')));
        }
        
        $user_id = intval($_POST['user_id']);
        $period_number = intval($_POST['period_number']);
        $mentor_id = get_current_user_id();
        $notes = wp_kses_post($_POST['notes']);
        
        PAM_Database::save_mentor_notes($user_id, $period_number, $mentor_id, $notes);
        
        wp_send_json_success(array('message' => __('Gespreksverslag opgeslagen', 'periode-actieplan')));
    }
}
