<?php
namespace PathOfSettings\Core\Services;

class SettingsManager {
    /**
     * Get all settings for a page
     * 
     * @param string $pageId
     * @return array
     */
    public function getSettings(string $pageId): array {
        $optionName = $this->getOptionName($pageId);
        return get_option($optionName, []);
    }
    
    /**
     * Get a specific setting
     * 
     * @param string $pageId
     * @param string $fieldId
     * @param mixed $default
     * @return mixed
     */
    public function getSetting(string $pageId, string $fieldId, $default = null) {
        $settings = $this->getSettings($pageId);
        return $settings[$fieldId] ?? $default;
    }
    
    /**
     * Save settings
     * 
     * @param string $pageId
     * @param array $settings
     * @return bool
     */
    public function saveSettings(string $pageId, array $settings): bool {
        $optionName = $this->getOptionName($pageId);
        
        // Apply filters before saving
        $settings = apply_filters('pos_before_save_settings', $settings, $pageId);
        
        $result = update_option($optionName, $settings);
        
        if ($result) {
            do_action('pos_after_save_settings', $settings, $pageId);
        }
        
        return $result;
    }
    
    /**
     * Get option name for a page
     * 
     * @param string $pageId
     * @return string
     */
    private function getOptionName(string $pageId): string {
        return 'pos_settings_' . $pageId;
    }
}