<?php
/**
 * Frontend form template
 */

if (!defined('ABSPATH')) {
    exit;
}

$periods = get_option('pam_periods', array());
$period_name = isset($periods[$current_period - 1]['name']) ? $periods[$current_period - 1]['name'] : 'Periode ' . $current_period;
$is_closed = ($period_status === 'closed');
?>

<div class="pam-container" id="pam-form-container">
    <div class="pam-header">
        <h2><?php echo esc_html(sprintf(__('Verbeterplan voor %s', 'periode-actieplan'), $period_name)); ?></h2>
        <?php if ($is_closed): ?>
            <div class="pam-status-closed">
                <span class="dashicons dashicons-lock"></span>
                <?php _e('Deze periode is afgesloten', 'periode-actieplan'); ?>
            </div>
        <?php endif; ?>
    </div>
    
    <form id="pam-form" class="pam-form" method="post">
        <input type="hidden" name="period_number" value="<?php echo esc_attr($current_period); ?>">
        
        <?php if (empty($subjects) && $current_period == 1): ?>
            <!-- Subject selection for first period -->
            <div class="pam-section pam-subject-selection">
                <h3><?php _e('Selecteer je vakken', 'periode-actieplan'); ?></h3>
                <p class="pam-description"><?php _e('Vink de vakken aan die je dit jaar volgt:', 'periode-actieplan'); ?></p>
                
                <div class="pam-subjects-grid">
                    <?php foreach ($all_subjects as $subject): ?>
                        <label class="pam-subject-checkbox">
                            <input type="checkbox" 
                                   name="subjects[]" 
                                   value="<?php echo esc_attr($subject); ?>"
                                   <?php echo $is_closed ? 'disabled' : ''; ?>>
                            <span><?php echo esc_html($subject); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                
                <div class="pam-custom-subject">
                    <label>
                        <?php _e('Of voeg een eigen vak toe:', 'periode-actieplan'); ?>
                        <input type="text" 
                               id="pam-custom-subject" 
                               placeholder="<?php esc_attr_e('bijv. Maatschappijleer', 'periode-actieplan'); ?>"
                               <?php echo $is_closed ? 'disabled' : ''; ?>>
                    </label>
                    <button type="button" 
                            id="pam-add-subject" 
                            class="pam-button-secondary"
                            <?php echo $is_closed ? 'disabled' : ''; ?>>
                        <?php _e('Toevoegen', 'periode-actieplan'); ?>
                    </button>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Grades section -->
        <div class="pam-section pam-grades-section" id="pam-grades-section" style="<?php echo empty($subjects) && $current_period == 1 ? 'display:none;' : ''; ?>">
            <h3><?php _e('Cijfers', 'periode-actieplan'); ?></h3>
            <p class="pam-description"><?php _e('Vul je cijfers in (gebruik een komma voor decimalen, bijv. 6,5):', 'periode-actieplan'); ?></p>
            
            <div class="pam-grades-grid">
                <?php 
                $display_subjects = !empty($subjects) ? $subjects : array();
                foreach ($display_subjects as $subject): 
                    $current_grade = isset($grades_by_subject[$subject]) ? $grades_by_subject[$subject]['grade'] : null;
                    $grade_display = $current_grade !== null ? number_format($current_grade, 1, ',', '') : '';
                ?>
                    <div class="pam-grade-row">
                        <label class="pam-grade-label">
                            <span class="pam-subject-name"><?php echo esc_html($subject); ?></span>
                            <input type="text" 
                                   name="grades[<?php echo esc_attr($subject); ?>]" 
                                   value="<?php echo esc_attr($grade_display); ?>"
                                   placeholder="-"
                                   class="pam-grade-input"
                                   data-subject="<?php echo esc_attr($subject); ?>"
                                   <?php echo $is_closed ? 'readonly' : ''; ?>
                                   pattern="^([1-9]|10)([,\.][0-9])?$|^-$">
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (!$is_closed): ?>
                <button type="button" id="pam-save-grades" class="pam-button-primary">
                    <?php _e('Cijfers opslaan', 'periode-actieplan'); ?>
                </button>
            <?php endif; ?>
        </div>
        
        <!-- Improvement plans section (appears after saving grades < 6.0) -->
        <div class="pam-section pam-plans-section" id="pam-plans-section" style="display:none;">
            <h3><?php _e('Verbeterplannen', 'periode-actieplan'); ?></h3>
            <p class="pam-description"><?php _e('Voor de volgende vakken heb je een onvoldoende. Vul het actieplan in:', 'periode-actieplan'); ?></p>
            
            <div id="pam-plans-container"></div>
        </div>
        
        <div class="pam-actions">
            <div id="pam-message" class="pam-message"></div>
            <?php if (!$is_closed): ?>
                <button type="button" id="pam-finalize" class="pam-button-success" style="display:none;">
                    <span class="dashicons dashicons-yes-alt"></span>
                    <?php _e('Plan Opslaan', 'periode-actieplan'); ?>
                </button>
            <?php endif; ?>
        </div>
    </form>
</div>

<script type="text/template" id="pam-plan-template">
    <div class="pam-plan-card" data-subject="{{subject}}">
        <h4 class="pam-plan-subject">
            <span class="pam-subject-icon">📚</span>
            {{subject}} <span class="pam-grade-badge">({{grade}})</span>
        </h4>
        
        {{#questions}}
        <div class="pam-plan-question">
            <label>
                <span class="pam-question-number">{{number}}</span>
                <span class="pam-question-text">{{question}}</span>
            </label>
            <textarea name="plans[{{subject}}][]" 
                      rows="3" 
                      placeholder="{{placeholder}}"
                      class="pam-plan-answer"
                      required>{{answer}}</textarea>
        </div>
        {{/questions}}
    </div>
</script>
