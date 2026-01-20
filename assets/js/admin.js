jQuery(document).ready(function($) {
    'use strict';
    
    const PAM_Admin = {
        init: function() {
            this.bindEvents();
            this.initTabs();
            this.initFilters();
        },
        
        bindEvents: function() {
            // Settings forms
            $('.pam-settings-form').on('submit', this.handleSettingsSubmit.bind(this));
            
            // Student meta form
            $('.pam-student-meta-form').on('submit', this.handleStudentMetaSubmit.bind(this));
            
            // Mentor notes form
            $('.pam-mentor-notes-form').on('submit', this.handleMentorNotesSubmit.bind(this));
            
            // Toggle period status
            $('.pam-toggle-period').on('click', this.handleTogglePeriod.bind(this));
            
            // Subjects management
            $('#pam-add-subject-btn').on('click', this.addSubjectRow.bind(this));
            $(document).on('click', '.pam-remove-subject', this.removeSubjectRow.bind(this));
            
            // Test email
            $('#pam-test-email').on('click', this.sendTestEmail.bind(this));
            
            // Period radio buttons
            $('input[name="active_period"]').on('change', this.handlePeriodChange.bind(this));
        },
        
        initTabs: function() {
            $('.pam-tab-button').on('click', function() {
                const period = $(this).data('period');
                
                $('.pam-tab-button').removeClass('active');
                $(this).addClass('active');
                
                $('.pam-tab-panel').removeClass('active');
                $('.pam-tab-panel[data-period="' + period + '"]').addClass('active');
            });
        },
        
        initFilters: function() {
            const self = this;
            
            // Search filter
            $('#pam-search-student').on('input', function() {
                self.filterStudents();
            });
            
            // Class filter
            $('#pam-filter-class').on('change', function() {
                self.filterStudents();
            });
            
            // Mentor filter
            $('#pam-filter-mentor').on('change', function() {
                self.filterStudents();
            });
        },
        
        filterStudents: function() {
            const searchTerm = $('#pam-search-student').val().toLowerCase();
            const selectedClass = $('#pam-filter-class').val();
            const selectedMentor = $('#pam-filter-mentor').val();
            
            $('.pam-students-table tbody tr').each(function() {
                const $row = $(this);
                const studentName = $row.find('td:first').text().toLowerCase();
                const studentClass = $row.data('class');
                const mentor1 = String($row.data('mentor-1'));
                const mentor2 = String($row.data('mentor-2'));
                
                let show = true;
                
                // Search filter
                if (searchTerm && !studentName.includes(searchTerm)) {
                    show = false;
                }
                
                // Class filter
                if (selectedClass && studentClass !== selectedClass) {
                    show = false;
                }
                
                // Mentor filter
                if (selectedMentor) {
                    if (mentor1 !== selectedMentor && mentor2 !== selectedMentor) {
                        show = false;
                    }
                }
                
                $row.toggle(show);
            });
        },
        
        handleSettingsSubmit: function(e) {
            e.preventDefault();
            
            const $form = $(e.currentTarget);
            const $message = $form.find('.pam-save-message');
            const settingType = $form.find('input[name="setting_type"]').val();
            
            let formData = new FormData();
            formData.append('action', 'pam_save_settings');
            formData.append('nonce', pamAdminData.nonce);
            formData.append('setting_type', settingType);
            
            // Collect form data based on type
            if (settingType === 'subjects') {
                const subjects = [];
                $form.find('input[name="subjects[]"]').each(function() {
                    const value = $(this).val().trim();
                    if (value) {
                        subjects.push(value);
                    }
                });
                subjects.forEach(s => formData.append('subjects[]', s));
                
            } else if (settingType === 'periods') {
                const periods = [];
                for (let i = 0; i < 4; i++) {
                    const name = $form.find('input[name="periods[' + i + '][name]"]').val();
                    const isActive = $('input[name="active_period"]:checked').val() == i;
                    periods.push({
                        name: name,
                        active: isActive
                    });
                }
                formData.append('periods', JSON.stringify(periods));
                
            } else if (settingType === 'mentor_role') {
                formData.append('mentor_role', $form.find('select[name="mentor_role"]').val());
                
            } else if (settingType === 'email') {
                formData.append('email_template', $form.find('textarea[name="email_template"]').val());
                
            } else if (settingType === 'pdro') {
                $form.find('textarea[name="questions[]"]').each(function() {
                    formData.append('questions[]', $(this).val());
                });
            }
            
            this.showMessage($message, pamAdminData.texts.saving, 'info');
            
            $.ajax({
                url: pamAdminData.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        PAM_Admin.showMessage($message, pamAdminData.texts.saved, 'success');
                    } else {
                        PAM_Admin.showMessage($message, response.data.message, 'error');
                    }
                },
                error: function() {
                    PAM_Admin.showMessage($message, 'Er is een fout opgetreden', 'error');
                }
            });
        },
        
        handleStudentMetaSubmit: function(e) {
            e.preventDefault();
            
            const $form = $(e.currentTarget);
            const $message = $form.find('.pam-save-message');
            const studentId = $form.data('student-id');
            
            const formData = new FormData();
            formData.append('action', 'pam_save_student_meta');
            formData.append('nonce', pamAdminData.nonce);
            formData.append('user_id', studentId);
            formData.append('mentor_1', $form.find('select[name="mentor_1"]').val());
            formData.append('mentor_2', $form.find('select[name="mentor_2"]').val());
            formData.append('class_name', $form.find('input[name="class_name"]').val());
            
            this.showMessage($message, pamAdminData.texts.saving, 'info');
            
            $.ajax({
                url: pamAdminData.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        PAM_Admin.showMessage($message, pamAdminData.texts.saved, 'success');
                    } else {
                        PAM_Admin.showMessage($message, response.data.message, 'error');
                    }
                },
                error: function() {
                    PAM_Admin.showMessage($message, 'Er is een fout opgetreden', 'error');
                }
            });
        },
        
        handleMentorNotesSubmit: function(e) {
            e.preventDefault();
            
            const $form = $(e.currentTarget);
            const $message = $form.find('.pam-save-message');
            const studentId = $form.data('student-id');
            const period = $form.data('period');
            
            const formData = new FormData();
            formData.append('action', 'pam_save_mentor_notes');
            formData.append('nonce', pamAdminData.nonce);
            formData.append('user_id', studentId);
            formData.append('period_number', period);
            formData.append('notes', $form.find('textarea[name="mentor_notes"]').val());
            
            this.showMessage($message, pamAdminData.texts.saving, 'info');
            
            $.ajax({
                url: pamAdminData.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        PAM_Admin.showMessage($message, pamAdminData.texts.saved, 'success');
                    } else {
                        PAM_Admin.showMessage($message, response.data.message, 'error');
                    }
                },
                error: function() {
                    PAM_Admin.showMessage($message, 'Er is een fout opgetreden', 'error');
                }
            });
        },
        
        handleTogglePeriod: function(e) {
            e.preventDefault();
            
            const $button = $(e.currentTarget);
            const studentId = $button.data('student-id');
            const period = $button.data('period');
            const action = $button.data('action');
            
            if (!confirm('Weet je zeker dat je deze periode wilt ' + (action === 'open' ? 'heropenen' : 'afsluiten') + '?')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'pam_toggle_period_status');
            formData.append('nonce', pamAdminData.nonce);
            formData.append('user_id', studentId);
            formData.append('period_number', period);
            formData.append('action_type', action);
            
            $.ajax({
                url: pamAdminData.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message);
                    }
                },
                error: function() {
                    alert('Er is een fout opgetreden');
                }
            });
        },
        
        addSubjectRow: function() {
            const html = '<div class="pam-subject-item">' +
                '<span class="dashicons dashicons-menu pam-drag-handle"></span>' +
                '<input type="text" name="subjects[]" value="" placeholder="Vaknaam">' +
                '<button type="button" class="button pam-remove-subject">' +
                '<span class="dashicons dashicons-trash"></span>' +
                '</button>' +
                '</div>';
            
            $('#pam-subjects-list').append(html);
        },
        
        removeSubjectRow: function(e) {
            if (confirm(pamAdminData.texts.confirm_delete)) {
                $(e.currentTarget).closest('.pam-subject-item').remove();
            }
        },
        
        sendTestEmail: function() {
            const email = prompt('Voer een e-mailadres in om de test e-mail naar te versturen:');
            
            if (!email) {
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'pam_send_test_email');
            formData.append('nonce', pamAdminData.nonce);
            formData.append('email', email);
            
            $.ajax({
                url: pamAdminData.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        alert('Test e-mail verstuurd naar ' + email);
                    } else {
                        alert('Fout bij versturen: ' + response.data.message);
                    }
                },
                error: function() {
                    alert('Er is een fout opgetreden bij het versturen van de test e-mail');
                }
            });
        },
        
        handlePeriodChange: function(e) {
            // Visual feedback when changing active period
            $('.pam-period-card').removeClass('pam-period-active');
            $(e.target).closest('.pam-period-card').addClass('pam-period-active');
        },
        
        showMessage: function($element, message, type) {
            $element.removeClass('success error info')
                    .addClass(type + ' show')
                    .text(message);
            
            if (type === 'success' || type === 'info') {
                setTimeout(function() {
                    $element.removeClass('show');
                }, 3000);
            }
        }
    };
    
    // Initialize
    PAM_Admin.init();
    
    // Make subjects list sortable if jQuery UI is available
    if ($.fn.sortable) {
        $('#pam-subjects-list').sortable({
            handle: '.pam-drag-handle',
            placeholder: 'pam-subject-placeholder',
            axis: 'y'
        });
    }
});
