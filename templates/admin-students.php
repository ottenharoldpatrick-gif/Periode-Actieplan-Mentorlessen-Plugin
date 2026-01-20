<?php
/**
 * Admin Students Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$mentor_role = get_option('pam_mentor_role', 'teacher');
$mentors = get_users(array('role' => $mentor_role));
?>

<div class="wrap pam-admin-wrap">
    <h1 class="pam-admin-title">
        <span class="dashicons dashicons-groups"></span>
        <?php _e('Leerlingen Beheren', 'periode-actieplan'); ?>
    </h1>
    
    <?php if ($selected_student_id && $selected_student_data): ?>
        <!-- Student Detail View -->
        <div class="pam-student-detail">
            <div class="pam-detail-header">
                <a href="<?php echo admin_url('admin.php?page=pam-students'); ?>" class="pam-back-button">
                    <span class="dashicons dashicons-arrow-left-alt2"></span>
                    <?php _e('Terug naar overzicht', 'periode-actieplan'); ?>
                </a>
                
                <h2><?php echo esc_html($selected_student_data['user']->display_name); ?></h2>
                
                <div class="pam-export-buttons">
                    <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=pam_export_excel&student_id=' . $selected_student_id), 'pam_export_excel'); ?>" class="button">
                        <span class="dashicons dashicons-media-spreadsheet"></span>
                        <?php _e('Excel Export', 'periode-actieplan'); ?>
                    </a>
                    <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=pam_print_view&student_id=' . $selected_student_id), 'pam_print_view'); ?>" class="button" target="_blank">
                        <span class="dashicons dashicons-printer"></span>
                        <?php _e('Print/PDF', 'periode-actieplan'); ?>
                    </a>
                </div>
            </div>
            
            <!-- Student Meta -->
            <div class="pam-meta-card">
                <h3><?php _e('Leerling Informatie', 'periode-actieplan'); ?></h3>
                <form class="pam-student-meta-form" data-student-id="<?php echo esc_attr($selected_student_id); ?>">
                    <div class="pam-form-row">
                        <div class="pam-form-field">
                            <label><?php _e('E-mail', 'periode-actieplan'); ?></label>
                            <input type="text" value="<?php echo esc_attr($selected_student_data['user']->user_email); ?>" readonly>
                        </div>
                        
                        <div class="pam-form-field">
                            <label><?php _e('Klas', 'periode-actieplan'); ?></label>
                            <input type="text" name="class_name" value="<?php echo esc_attr($selected_student_data['meta']['class_name'] ?? ''); ?>" placeholder="bijv. M3c">
                        </div>
                    </div>
                    
                    <div class="pam-form-row">
                        <div class="pam-form-field">
                            <label><?php _e('Mentor 1', 'periode-actieplan'); ?></label>
                            <select name="mentor_1">
                                <option value=""><?php _e('-- Geen mentor --', 'periode-actieplan'); ?></option>
                                <?php foreach ($mentors as $mentor): ?>
                                    <option value="<?php echo esc_attr($mentor->ID); ?>" <?php selected($selected_student_data['meta']['mentor_1'] ?? '', $mentor->ID); ?>>
                                        <?php echo esc_html($mentor->display_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="pam-form-field">
                            <label><?php _e('Mentor 2 (optioneel)', 'periode-actieplan'); ?></label>
                            <select name="mentor_2">
                                <option value=""><?php _e('-- Geen mentor --', 'periode-actieplan'); ?></option>
                                <?php foreach ($mentors as $mentor): ?>
                                    <option value="<?php echo esc_attr($mentor->ID); ?>" <?php selected($selected_student_data['meta']['mentor_2'] ?? '', $mentor->ID); ?>>
                                        <?php echo esc_html($mentor->display_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="button button-primary">
                        <span class="dashicons dashicons-yes"></span>
                        <?php _e('Opslaan', 'periode-actieplan'); ?>
                    </button>
                    <span class="pam-save-message"></span>
                </form>
            </div>
            
            <!-- Period Tabs -->
            <div class="pam-period-tabs">
                <div class="pam-tabs-nav">
                    <?php 
                    $periods = get_option('pam_periods', array());
                    foreach ($selected_student_data['periods'] as $index => $period_data): 
                        $period_name = isset($periods[$period_data['number'] - 1]['name']) ? $periods[$period_data['number'] - 1]['name'] : 'Periode ' . $period_data['number'];
                        $has_data = !empty($period_data['grades']);
                        $is_first = $index === 0;
                    ?>
                        <button class="pam-tab-button <?php echo $is_first ? 'active' : ''; ?> <?php echo !$has_data ? 'pam-tab-empty' : ''; ?>" 
                                data-period="<?php echo esc_attr($period_data['number']); ?>">
                            <?php echo esc_html($period_name); ?>
                            <?php if ($has_data): ?>
                                <span class="pam-tab-badge"><?php echo count($period_data['grades']); ?></span>
                            <?php endif; ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                
                <div class="pam-tabs-content">
                    <?php foreach ($selected_student_data['periods'] as $index => $period_data): 
                        $is_first = $index === 0;
                        $is_closed = $period_data['status'] === 'closed';
                    ?>
                        <div class="pam-tab-panel <?php echo $is_first ? 'active' : ''; ?>" data-period="<?php echo esc_attr($period_data['number']); ?>">
                            
                            <?php if (empty($period_data['grades'])): ?>
                                <div class="pam-empty-state">
                                    <span class="dashicons dashicons-info"></span>
                                    <p><?php _e('Nog geen data voor deze periode', 'periode-actieplan'); ?></p>
                                </div>
                            <?php else: ?>
                                
                                <!-- Period Status -->
                                <div class="pam-period-status">
                                    <div class="pam-status-badge <?php echo $is_closed ? 'pam-status-closed' : 'pam-status-open'; ?>">
                                        <span class="dashicons <?php echo $is_closed ? 'dashicons-lock' : 'dashicons-unlock'; ?>"></span>
                                        <?php echo $is_closed ? __('Afgesloten', 'periode-actieplan') : __('Open', 'periode-actieplan'); ?>
                                    </div>
                                    
                                    <button class="button pam-toggle-period" 
                                            data-student-id="<?php echo esc_attr($selected_student_id); ?>"
                                            data-period="<?php echo esc_attr($period_data['number']); ?>"
                                            data-action="<?php echo $is_closed ? 'open' : 'close'; ?>">
                                        <span class="dashicons <?php echo $is_closed ? 'dashicons-unlock' : 'dashicons-lock'; ?>"></span>
                                        <?php echo $is_closed ? __('Heropenen', 'periode-actieplan') : __('Afsluiten', 'periode-actieplan'); ?>
                                    </button>
                                </div>
                                
                                <!-- Grades Table -->
                                <div class="pam-grades-table-wrapper">
                                    <h3><?php _e('Cijfers', 'periode-actieplan'); ?></h3>
                                    <table class="pam-grades-table">
                                        <thead>
                                            <tr>
                                                <th><?php _e('Vak', 'periode-actieplan'); ?></th>
                                                <th><?php _e('Cijfer', 'periode-actieplan'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $failure_count = 0;
                                            foreach ($period_data['grades'] as $grade): 
                                                $grade_value = $grade['grade'] !== null ? number_format($grade['grade'], 1, ',', '') : '-';
                                                $is_failing = $grade['grade'] !== null && $grade['grade'] < 6.0;
                                                if ($is_failing) $failure_count++;
                                            ?>
                                                <tr class="<?php echo $is_failing ? 'pam-grade-failure' : ''; ?>">
                                                    <td><?php echo esc_html($grade['subject_name']); ?></td>
                                                    <td class="pam-grade-value"><?php echo esc_html($grade_value); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    
                                    <?php if ($failure_count > 0): ?>
                                        <div class="pam-failure-summary">
                                            <span class="dashicons dashicons-warning"></span>
                                            <?php printf(_n('%d onvoldoende', '%d onvoldoendes', $failure_count, 'periode-actieplan'), $failure_count); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Improvement Plans -->
                                <?php if (!empty($period_data['improvement_plans'])): ?>
                                    <div class="pam-plans-wrapper">
                                        <h3><?php _e('Verbeterplannen', 'periode-actieplan'); ?></h3>
                                        <?php foreach ($period_data['improvement_plans'] as $plan): ?>
                                            <div class="pam-plan-detail">
                                                <h4>📚 <?php echo esc_html($plan['subject_name']); ?></h4>
                                                <?php for ($i = 1; $i <= 4; $i++): 
                                                    $question = $plan['question_' . $i];
                                                    $answer = $plan['answer_' . $i];
                                                    if (!empty($question) && !empty($answer)):
                                                ?>
                                                    <div class="pam-plan-qa">
                                                        <div class="pam-plan-question">
                                                            <strong><?php echo $i; ?>.</strong> <?php echo esc_html($question); ?>
                                                        </div>
                                                        <div class="pam-plan-answer">
                                                            <?php echo nl2br(esc_html($answer)); ?>
                                                        </div>
                                                    </div>
                                                <?php 
                                                    endif;
                                                endfor; 
                                                ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Mentor Notes -->
                                <div class="pam-mentor-notes-wrapper">
                                    <h3><?php _e('Gespreksverslag Mentor', 'periode-actieplan'); ?></h3>
                                    <form class="pam-mentor-notes-form" 
                                          data-student-id="<?php echo esc_attr($selected_student_id); ?>"
                                          data-period="<?php echo esc_attr($period_data['number']); ?>">
                                        <textarea name="mentor_notes" rows="6" placeholder="<?php esc_attr_e('Notities van mentorgesprek...', 'periode-actieplan'); ?>"><?php echo esc_textarea($period_data['mentor_notes']['notes'] ?? ''); ?></textarea>
                                        <button type="submit" class="button button-primary">
                                            <span class="dashicons dashicons-yes"></span>
                                            <?php _e('Gespreksverslag Opslaan', 'periode-actieplan'); ?>
                                        </button>
                                        <span class="pam-save-message"></span>
                                    </form>
                                </div>
                                
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
        </div>
        
    <?php else: ?>
        <!-- Students List View -->
        <div class="pam-students-list">
            <div class="pam-list-filters">
                <input type="text" id="pam-search-student" placeholder="<?php esc_attr_e('Zoek leerling...', 'periode-actieplan'); ?>" class="pam-search-input">
                
                <select id="pam-filter-class" class="pam-filter-select">
                    <option value=""><?php _e('Alle klassen', 'periode-actieplan'); ?></option>
                    <?php
                    $classes = array_unique(array_filter(array_column($students, 'class')));
                    sort($classes);
                    foreach ($classes as $class):
                    ?>
                        <option value="<?php echo esc_attr($class); ?>"><?php echo esc_html($class); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <select id="pam-filter-mentor" class="pam-filter-select">
                    <option value=""><?php _e('Alle mentoren', 'periode-actieplan'); ?></option>
                    <?php foreach ($mentors as $mentor): ?>
                        <option value="<?php echo esc_attr($mentor->ID); ?>"><?php echo esc_html($mentor->display_name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <table class="wp-list-table widefat fixed striped pam-students-table">
                <thead>
                    <tr>
                        <th><?php _e('Naam', 'periode-actieplan'); ?></th>
                        <th><?php _e('E-mail', 'periode-actieplan'); ?></th>
                        <th><?php _e('Klas', 'periode-actieplan'); ?></th>
                        <th><?php _e('Mentor(en)', 'periode-actieplan'); ?></th>
                        <th><?php _e('Acties', 'periode-actieplan'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): 
                        $mentor_names = array();
                        if ($student['mentor_1']) {
                            $m1 = get_userdata($student['mentor_1']);
                            if ($m1) $mentor_names[] = $m1->display_name;
                        }
                        if ($student['mentor_2']) {
                            $m2 = get_userdata($student['mentor_2']);
                            if ($m2) $mentor_names[] = $m2->display_name;
                        }
                    ?>
                        <tr data-class="<?php echo esc_attr($student['class']); ?>" 
                            data-mentor-1="<?php echo esc_attr($student['mentor_1'] ?? ''); ?>"
                            data-mentor-2="<?php echo esc_attr($student['mentor_2'] ?? ''); ?>">
                            <td><strong><?php echo esc_html($student['name']); ?></strong></td>
                            <td><?php echo esc_html($student['email']); ?></td>
                            <td><?php echo esc_html($student['class']); ?></td>
                            <td><?php echo esc_html(implode(', ', $mentor_names)); ?></td>
                            <td>
                                <a href="<?php echo admin_url('admin.php?page=pam-students&student=' . $student['id']); ?>" class="button button-small">
                                    <?php _e('Bekijken', 'periode-actieplan'); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
