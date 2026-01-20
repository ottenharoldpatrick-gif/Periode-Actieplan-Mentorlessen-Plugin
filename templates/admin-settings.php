<?php
/**
 * Admin Settings Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap pam-admin-wrap">
    <h1 class="pam-admin-title">
        <span class="dashicons dashicons-admin-settings"></span>
        <?php _e('Instellingen', 'periode-actieplan'); ?>
    </h1>
    
    <h2 class="nav-tab-wrapper">
        <a href="?page=pam-settings&tab=subjects" class="nav-tab <?php echo $active_tab === 'subjects' ? 'nav-tab-active' : ''; ?>">
            <?php _e('Vakken', 'periode-actieplan'); ?>
        </a>
        <a href="?page=pam-settings&tab=periods" class="nav-tab <?php echo $active_tab === 'periods' ? 'nav-tab-active' : ''; ?>">
            <?php _e('Periodes', 'periode-actieplan'); ?>
        </a>
        <a href="?page=pam-settings&tab=mentors" class="nav-tab <?php echo $active_tab === 'mentors' ? 'nav-tab-active' : ''; ?>">
            <?php _e('Mentoren', 'periode-actieplan'); ?>
        </a>
        <a href="?page=pam-settings&tab=email" class="nav-tab <?php echo $active_tab === 'email' ? 'nav-tab-active' : ''; ?>">
            <?php _e('E-mail', 'periode-actieplan'); ?>
        </a>
        <a href="?page=pam-settings&tab=pdro" class="nav-tab <?php echo $active_tab === 'pdro' ? 'nav-tab-active' : ''; ?>">
            <?php _e('PDRO Vragen', 'periode-actieplan'); ?>
        </a>
    </h2>
    
    <div class="pam-settings-content">
        
        <!-- Subjects Tab -->
        <?php if ($active_tab === 'subjects'): ?>
            <div class="pam-settings-section">
                <h2><?php _e('Vakken Beheren', 'periode-actieplan'); ?></h2>
                <p class="description">
                    <?php _e('Beheer de lijst met vakken waaruit leerlingen kunnen kiezen. Leerlingen kunnen ook zelf vakken toevoegen.', 'periode-actieplan'); ?>
                </p>
                
                <form id="pam-subjects-form" class="pam-settings-form">
                    <input type="hidden" name="setting_type" value="subjects">
                    
                    <div id="pam-subjects-list">
                        <?php foreach ($subjects as $index => $subject): ?>
                            <div class="pam-subject-item">
                                <span class="dashicons dashicons-menu pam-drag-handle"></span>
                                <input type="text" name="subjects[]" value="<?php echo esc_attr($subject); ?>" placeholder="<?php esc_attr_e('Vaknaam', 'periode-actieplan'); ?>">
                                <button type="button" class="button pam-remove-subject">
                                    <span class="dashicons dashicons-trash"></span>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <button type="button" id="pam-add-subject-btn" class="button">
                        <span class="dashicons dashicons-plus-alt"></span>
                        <?php _e('Vak Toevoegen', 'periode-actieplan'); ?>
                    </button>
                    
                    <div class="pam-form-actions">
                        <button type="submit" class="button button-primary">
                            <?php _e('Vakken Opslaan', 'periode-actieplan'); ?>
                        </button>
                        <span class="pam-save-message"></span>
                    </div>
                </form>
            </div>
        <?php endif; ?>
        
        <!-- Periods Tab -->
        <?php if ($active_tab === 'periods'): ?>
            <div class="pam-settings-section">
                <h2><?php _e('Periodes Configureren', 'periode-actieplan'); ?></h2>
                <p class="description">
                    <?php _e('Stel de namen van de 4 periodes in en activeer de periode waar leerlingen momenteel mee bezig zijn.', 'periode-actieplan'); ?>
                </p>
                
                <form id="pam-periods-form" class="pam-settings-form">
                    <input type="hidden" name="setting_type" value="periods">
                    
                    <div class="pam-periods-grid">
                        <?php foreach ($periods as $index => $period): ?>
                            <div class="pam-period-card">
                                <div class="pam-period-header">
                                    <h3><?php _e('Periode', 'periode-actieplan'); ?> <?php echo ($index + 1); ?></h3>
                                    <label class="pam-switch">
                                        <input type="radio" name="active_period" value="<?php echo $index; ?>" <?php checked($period['active'] ?? false, true); ?>>
                                        <span class="pam-slider"></span>
                                        <span class="pam-switch-label"><?php _e('Actief', 'periode-actieplan'); ?></span>
                                    </label>
                                </div>
                                <input type="text" 
                                       name="periods[<?php echo $index; ?>][name]" 
                                       value="<?php echo esc_attr($period['name']); ?>" 
                                       placeholder="<?php esc_attr_e('bijv. Periode 1', 'periode-actieplan'); ?>"
                                       class="pam-period-name-input">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="pam-form-actions">
                        <button type="submit" class="button button-primary">
                            <?php _e('Periodes Opslaan', 'periode-actieplan'); ?>
                        </button>
                        <span class="pam-save-message"></span>
                    </div>
                </form>
            </div>
        <?php endif; ?>
        
        <!-- Mentors Tab -->
        <?php if ($active_tab === 'mentors'): ?>
            <div class="pam-settings-section">
                <h2><?php _e('Mentor Rol Instellen', 'periode-actieplan'); ?></h2>
                <p class="description">
                    <?php _e('Selecteer welke WordPress rol gebruikt wordt voor mentoren. Gebruikers met deze rol kunnen toegewezen worden aan leerlingen.', 'periode-actieplan'); ?>
                </p>
                
                <form id="pam-mentors-form" class="pam-settings-form">
                    <input type="hidden" name="setting_type" value="mentor_role">
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="mentor_role"><?php _e('Mentor Rol', 'periode-actieplan'); ?></label>
                            </th>
                            <td>
                                <select name="mentor_role" id="mentor_role" class="regular-text">
                                    <?php wp_dropdown_roles($mentor_role); ?>
                                </select>
                                <p class="description">
                                    <?php _e('Voor Sensei LMS is dit meestal "Teacher" of "Course Author".', 'periode-actieplan'); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                    
                    <div class="pam-form-actions">
                        <button type="submit" class="button button-primary">
                            <?php _e('Rol Opslaan', 'periode-actieplan'); ?>
                        </button>
                        <span class="pam-save-message"></span>
                    </div>
                </form>
            </div>
        <?php endif; ?>
        
        <!-- Email Tab -->
        <?php if ($active_tab === 'email'): ?>
            <div class="pam-settings-section">
                <h2><?php _e('E-mail Template', 'periode-actieplan'); ?></h2>
                <p class="description">
                    <?php _e('Pas de e-mail template aan die verstuurd wordt wanneer een leerling een periode afrondt. Gebruik de volgende placeholders:', 'periode-actieplan'); ?>
                </p>
                
                <div class="pam-placeholders">
                    <code>{naam}</code> - <?php _e('Naam van ontvanger (mentor of leerling)', 'periode-actieplan'); ?><br>
                    <code>{leerling_naam}</code> - <?php _e('Naam van de leerling', 'periode-actieplan'); ?><br>
                    <code>{periode_nummer}</code> - <?php _e('Periode naam (bijv. Periode 1)', 'periode-actieplan'); ?><br>
                    <code>{cijfer_overzicht}</code> - <?php _e('Tabel met alle cijfers', 'periode-actieplan'); ?><br>
                    <code>{verbeterplan_overzicht}</code> - <?php _e('Alle verbeterplannen', 'periode-actieplan'); ?><br>
                    <code>{admin_link}</code> - <?php _e('Link naar admin overzicht', 'periode-actieplan'); ?>
                </div>
                
                <form id="pam-email-form" class="pam-settings-form">
                    <input type="hidden" name="setting_type" value="email">
                    
                    <textarea name="email_template" rows="15" class="large-text code"><?php echo esc_textarea($email_template); ?></textarea>
                    
                    <div class="pam-form-actions">
                        <button type="submit" class="button button-primary">
                            <?php _e('Template Opslaan', 'periode-actieplan'); ?>
                        </button>
                        <button type="button" id="pam-test-email" class="button">
                            <span class="dashicons dashicons-email"></span>
                            <?php _e('Test E-mail Versturen', 'periode-actieplan'); ?>
                        </button>
                        <span class="pam-save-message"></span>
                    </div>
                </form>
            </div>
        <?php endif; ?>
        
        <!-- PDRO Tab -->
        <?php if ($active_tab === 'pdro'): ?>
            <div class="pam-settings-section">
                <h2><?php _e('PDRO Vragen Aanpassen', 'periode-actieplan'); ?></h2>
                <p class="description">
                    <?php _e('Pas de 4 vragen aan die leerlingen moeten beantwoorden in hun verbeterplan (PDRO: Plan, Do, Review, Overall).', 'periode-actieplan'); ?>
                </p>
                
                <form id="pam-pdro-form" class="pam-settings-form">
                    <input type="hidden" name="setting_type" value="pdro">
                    
                    <?php foreach ($pdro_questions as $index => $question): ?>
                        <div class="pam-pdro-question">
                            <label>
                                <strong><?php printf(__('Vraag %d', 'periode-actieplan'), $index + 1); ?></strong>
                            </label>
                            <textarea name="questions[]" rows="3" class="large-text"><?php echo esc_textarea($question); ?></textarea>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="pam-form-actions">
                        <button type="submit" class="button button-primary">
                            <?php _e('Vragen Opslaan', 'periode-actieplan'); ?>
                        </button>
                        <span class="pam-save-message"></span>
                    </div>
                </form>
            </div>
        <?php endif; ?>
        
    </div>
</div>
