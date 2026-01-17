<?php
namespace App\Config;

class SiteConfig {
    const APP_NAME = 'MIVO';
    const APP_VERSION = 'v1.1.0';
    const APP_FULL_NAME = 'MIVO - Mikrotik Voucher';
    const CREDIT_NAME = 'DyzulkDev';
    const CREDIT_URL = 'https://dyzulk.com';
    const YEAR = '2026';
    const REPO_URL = 'https://github.com/dyzulk/mivo';
    
    // Security Keys
    // Fetched from .env or fallback to default
    public static function getSecretKey() {
        return getenv('APP_KEY') ?: 'mikhmonv3remake_secret_key_32bytes';
    }

    const IS_DEV = true; // Still useful for code logic not relying on env yet, or can be refactored too.
    
    /**
     * Get the formatted page title
     */
    public static function getTitle($page = '') {
        return empty($page) ? self::APP_NAME : $page . ' | ' . self::APP_NAME;
    }
    
    /**
     * Get footer text
     */
    public static function getFooter() {
        $currentYear = date('Y');
        $yearDisplay = (self::YEAR == $currentYear) ? self::YEAR : self::YEAR . ' - ' . $currentYear;
        return self::APP_FULL_NAME . ' &copy; 2026 - ' . $yearDisplay . ' &bull; Created with Love <i data-lucide="heart" class="w-3 h-3 inline text-red-500 fill-red-500 mx-1"></i> Developed by <a href="' . self::CREDIT_URL . '" target="_blank" class="font-medium hover:text-foreground transition-colors">' . self::CREDIT_NAME . '</a>';
    }
}
