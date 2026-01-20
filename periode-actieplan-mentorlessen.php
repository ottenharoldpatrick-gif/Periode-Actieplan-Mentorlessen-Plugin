<?php
/**
 * Plugin Name: Periode Actieplan Mentorlessen
 * Plugin URI: https://eco.isdigitaal.nl
 * Description: Systeem voor leerlingen om per periode cijfers en actieplannen in te voeren met mentor begeleiding
 * Version: 1.0.0
 * Author: ECO
 * Text Domain: periode-actieplan
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('PAM_VERSION', '1.0.0');
define('PAM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PAM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PAM_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Main plugin class
class Periode_Actieplan_Mentorlessen {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    private function load_dependencies() {
        require_once PAM_PLUGIN_DIR . 'includes/class-pam-database.php';
        require_once PAM_PLUGIN_DIR . 'includes/class-pam-student.php';
        require_once PAM_PLUGIN_DIR . 'includes/class-pam-frontend.php';
        require_once PAM_PLUGIN_DIR . 'includes/class-pam-admin.php';
        require_once PAM_PLUGIN_DIR . 'includes/class-pam-email.php';
        require_once PAM_PLUGIN_DIR . 'includes/class-pam-export.php';
    }
    
    private function init_hooks() {
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('init', array($this, 'init'));
    }
    
    public function activate() {
        PAM_Database::create_tables();
        $this->set_default_options();
        flush_rewrite_rules();
    }
    
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    private function set_default_options() {
        $defaults = array(
            'pam_subjects' => array('Biologie', 'Wiskunde', 'Nederlands', 'Engels'),
            'pam_periods' => array(
                array('name' => 'Periode 1', 'active' => true),
                array('name' => 'Periode 2', 'active' => false),
                array('name' => 'Periode 3', 'active' => false),
                array('name' => 'Periode 4', 'active' => false)
            ),
            'pam_mentor_role' => 'teacher',
            'pam_email_template' => $this->get_default_email_template(),
            'pam_pdro_questions' => array(
                'PLAN - Terugkijken: hoe heb je geleerd voor de laatste toets en wat ging goed / minder goed?',
                'PLAN - Kies één duidelijk doel (SMART): welk cijfer wil je minimaal halen en op welke toets / in welke periode?',
                'DO - Wat ga je precies anders doen? Noem minimaal twee concrete acties (wat, wanneer, hoe lang, met welk materiaal).',
                'REVIEW - Hoe ga je zelf controleren of je plan werkt? (Bijv. oefentoets maken, wekelijkse check in je planner, uitleg vragen aan docent/mentor.)'
            )
        );
        
        foreach ($defaults as $key => $value) {
            if (get_option($key) === false) {
                add_option($key, $value);
            }
        }
    }
    
    private function get_default_email_template() {
        return "Beste {naam},

{leerling_naam} heeft periode {periode_nummer} afgerond.

Hieronder vind je een overzicht van de ingevoerde cijfers:

{cijfer_overzicht}

{verbeterplan_overzicht}

Je kunt het volledige overzicht bekijken in de admin: {admin_link}

Met vriendelijke groet,
Mentorlessen Systeem";
    }
    
    public function load_textdomain() {
        load_plugin_textdomain('periode-actieplan', false, dirname(PAM_PLUGIN_BASENAME) . '/languages');
    }
    
    public function init() {
        PAM_Frontend::get_instance();
        
        if (is_admin()) {
            PAM_Admin::get_instance();
        }
    }
}

// Initialize plugin
function pam_init() {
    return Periode_Actieplan_Mentorlessen::get_instance();
}

add_action('plugins_loaded', 'pam_init');
