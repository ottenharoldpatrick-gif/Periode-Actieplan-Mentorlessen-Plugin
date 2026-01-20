<?php
/**
 * Email functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class PAM_Email {
    
    public static function send_period_completion_email($user_id, $period_number) {
        $user = get_userdata($user_id);
        if (!$user) {
            return false;
        }
        
        $student_meta = PAM_Database::get_student_meta($user_id);
        $grades = PAM_Database::get_grades($user_id, $period_number);
        $periods = get_option('pam_periods', array());
        $period_name = isset($periods[$period_number - 1]['name']) ? $periods[$period_number - 1]['name'] : 'Periode ' . $period_number;
        
        // Get template
        $template = get_option('pam_email_template', '');
        
        // Prepare data
        $cijfer_overzicht = self::format_grades_for_email($grades);
        $verbeterplan_overzicht = self::format_plans_for_email($user_id, $period_number, $grades);
        $admin_link = admin_url('admin.php?page=pam-students&student=' . $user_id);
        
        // Prepare email content for student
        $student_content = str_replace(
            array('{naam}', '{leerling_naam}', '{periode_nummer}', '{cijfer_overzicht}', '{verbeterplan_overzicht}', '{admin_link}'),
            array($user->display_name, $user->display_name, $period_name, $cijfer_overzicht, $verbeterplan_overzicht, $admin_link),
            $template
        );
        
        // Send to student
        $student_subject = sprintf(__('Je hebt %s afgerond', 'periode-actieplan'), $period_name);
        wp_mail($user->user_email, $student_subject, $student_content, array('Content-Type: text/html; charset=UTF-8'));
        
        // Send to mentors
        if ($student_meta) {
            $mentors = array();
            if (!empty($student_meta['mentor_1'])) {
                $mentors[] = get_userdata($student_meta['mentor_1']);
            }
            if (!empty($student_meta['mentor_2'])) {
                $mentors[] = get_userdata($student_meta['mentor_2']);
            }
            
            foreach ($mentors as $mentor) {
                if ($mentor) {
                    $mentor_content = str_replace(
                        array('{naam}', '{leerling_naam}', '{periode_nummer}', '{cijfer_overzicht}', '{verbeterplan_overzicht}', '{admin_link}'),
                        array($mentor->display_name, $user->display_name, $period_name, $cijfer_overzicht, $verbeterplan_overzicht, $admin_link),
                        $template
                    );
                    
                    $mentor_subject = sprintf(__('%s heeft %s afgerond', 'periode-actieplan'), $user->display_name, $period_name);
                    wp_mail($mentor->user_email, $mentor_subject, $mentor_content, array('Content-Type: text/html; charset=UTF-8'));
                }
            }
        }
        
        return true;
    }
    
    private static function format_grades_for_email($grades) {
        if (empty($grades)) {
            return '<p>' . __('Geen cijfers ingevuld.', 'periode-actieplan') . '</p>';
        }
        
        $html = '<table style="width:100%; border-collapse: collapse; margin: 20px 0;">';
        $html .= '<thead>';
        $html .= '<tr style="background: #667eea; color: white;">';
        $html .= '<th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Vak</th>';
        $html .= '<th style="padding: 12px; text-align: center; border: 1px solid #ddd;">Cijfer</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        foreach ($grades as $grade) {
            $grade_value = $grade['grade'] !== null ? number_format($grade['grade'], 1, ',', '') : '-';
            $is_failing = $grade['grade'] !== null && $grade['grade'] < 6.0;
            
            $row_style = $is_failing ? 'background: #fff3cd;' : '';
            $grade_style = $is_failing ? 'color: #d63031; font-weight: bold;' : '';
            
            $html .= '<tr style="' . $row_style . '">';
            $html .= '<td style="padding: 12px; border: 1px solid #ddd;">' . esc_html($grade['subject_name']) . '</td>';
            $html .= '<td style="padding: 12px; text-align: center; border: 1px solid #ddd; ' . $grade_style . '">' . esc_html($grade_value) . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody>';
        $html .= '</table>';
        
        return $html;
    }
    
    private static function format_plans_for_email($user_id, $period_number, $grades) {
        $html = '';
        $has_plans = false;
        
        foreach ($grades as $grade) {
            if ($grade['grade'] !== null && $grade['grade'] < 6.0) {
                $plan = PAM_Database::get_improvement_plan($user_id, $period_number, $grade['subject_name']);
                
                if ($plan) {
                    $has_plans = true;
                    
                    $html .= '<div style="background: #ffeaa7; padding: 20px; border-radius: 8px; margin: 20px 0;">';
                    $html .= '<h3 style="margin: 0 0 15px 0; color: #2d3436;">📚 ' . esc_html($grade['subject_name']) . ' (cijfer: ' . number_format($grade['grade'], 1, ',', '') . ')</h3>';
                    
                    for ($i = 1; $i <= 4; $i++) {
                        $question = $plan['question_' . $i];
                        $answer = $plan['answer_' . $i];
                        
                        if (!empty($question) && !empty($answer)) {
                            $html .= '<div style="background: white; padding: 15px; border-radius: 6px; margin-bottom: 15px;">';
                            $html .= '<p style="margin: 0 0 8px 0;"><strong>' . $i . '. ' . esc_html($question) . '</strong></p>';
                            $html .= '<p style="margin: 0; color: #495057;">' . nl2br(esc_html($answer)) . '</p>';
                            $html .= '</div>';
                        }
                    }
                    
                    $html .= '</div>';
                }
            }
        }
        
        if (!$has_plans) {
            return '<p>' . __('Geen verbeterplannen nodig - alle cijfers zijn voldoende!', 'periode-actieplan') . ' 🎉</p>';
        }
        
        return $html;
    }
    
    public static function send_test_email($to_email) {
        $subject = __('Test E-mail - Periode Actieplan', 'periode-actieplan');
        $message = '<p>' . __('Dit is een test e-mail van de Periode Actieplan plugin.', 'periode-actieplan') . '</p>';
        $message .= '<p>' . __('Als je deze e-mail ontvangt, werkt de e-mail functionaliteit correct.', 'periode-actieplan') . '</p>';
        
        return wp_mail($to_email, $subject, $message, array('Content-Type: text/html; charset=UTF-8'));
    }
}
