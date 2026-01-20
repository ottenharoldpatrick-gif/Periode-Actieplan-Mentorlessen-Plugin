<?php
/**
 * Student helper class
 * Additional student-related functionality can be added here
 */

if (!defined('ABSPATH')) {
    exit;
}

class PAM_Student {
    
    /**
     * Get student data by user ID
     */
    public static function get_student_data($user_id) {
        $user = get_userdata($user_id);
        if (!$user) {
            return null;
        }
        
        return array(
            'id' => $user_id,
            'name' => $user->display_name,
            'email' => $user->user_email,
            'meta' => PAM_Database::get_student_meta($user_id),
            'subjects' => PAM_Database::get_student_subjects($user_id)
        );
    }
    
    /**
     * Check if user is enrolled in Sensei "Mentor" course
     */
    public static function is_enrolled_in_mentor_course($user_id) {
        // If Sensei is active, check enrollment
        if (function_exists('Sensei')) {
            $mentor_course = self::get_mentor_course_id();
            if ($mentor_course) {
                return Sensei_Utils::user_started_course($mentor_course, $user_id);
            }
        }
        
        // Fallback: check if user has subscriber or student role
        $user = get_userdata($user_id);
        if ($user) {
            return in_array('subscriber', $user->roles) || in_array('student', $user->roles);
        }
        
        return false;
    }
    
    /**
     * Get Sensei "Mentor" course ID
     */
    private static function get_mentor_course_id() {
        $args = array(
            'post_type' => 'course',
            'title' => 'Mentor',
            'posts_per_page' => 1,
            'fields' => 'ids'
        );
        
        $courses = get_posts($args);
        return !empty($courses) ? $courses[0] : null;
    }
    
    /**
     * Get student's current period progress
     */
    public static function get_period_progress($user_id, $period_number) {
        $subjects = PAM_Database::get_student_subjects($user_id);
        $grades = PAM_Database::get_grades($user_id, $period_number);
        
        if (empty($subjects)) {
            return array(
                'total' => 0,
                'completed' => 0,
                'percentage' => 0
            );
        }
        
        $total = count($subjects);
        $completed = 0;
        
        foreach ($grades as $grade) {
            if ($grade['grade'] !== null) {
                $completed++;
            }
        }
        
        return array(
            'total' => $total,
            'completed' => $completed,
            'percentage' => $total > 0 ? round(($completed / $total) * 100) : 0
        );
    }
    
    /**
     * Get student's average grade for a period
     */
    public static function get_period_average($user_id, $period_number) {
        $grades = PAM_Database::get_grades($user_id, $period_number);
        
        if (empty($grades)) {
            return null;
        }
        
        $sum = 0;
        $count = 0;
        
        foreach ($grades as $grade) {
            if ($grade['grade'] !== null) {
                $sum += $grade['grade'];
                $count++;
            }
        }
        
        return $count > 0 ? round($sum / $count, 1) : null;
    }
    
    /**
     * Get number of failures for a period
     */
    public static function get_failure_count($user_id, $period_number) {
        $grades = PAM_Database::get_grades($user_id, $period_number);
        $failures = 0;
        
        foreach ($grades as $grade) {
            if ($grade['grade'] !== null && $grade['grade'] < 6.0) {
                $failures++;
            }
        }
        
        return $failures;
    }
    
    /**
     * Check if student needs improvement plan for a subject
     */
    public static function needs_improvement_plan($user_id, $period_number, $subject_name) {
        $grades = PAM_Database::get_grades($user_id, $period_number);
        
        foreach ($grades as $grade) {
            if ($grade['subject_name'] === $subject_name) {
                return $grade['grade'] !== null && $grade['grade'] < 6.0;
            }
        }
        
        return false;
    }
}
