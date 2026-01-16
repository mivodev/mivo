<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Config;
use App\Libraries\RouterOSAPI;
use App\Helpers\HotspotHelper;

class ReportController extends Controller
{
    public function index($session)
    {
        $configModel = new Config();
        $config = $configModel->getSession($session);
        
        if (!$config) {
            header('Location: /');
            exit;
        }

        $API = new RouterOSAPI();
        $users = [];

        if ($API->connect($config['ip_address'], $config['username'], $config['password'])) {
            // Fetch All Users
            // Optimized print: get .id, name, price, comment
            $users = $API->comm("/ip/hotspot/user/print");
            $API->disconnect();
        }

        // Aggregate Data
        $report = [];
        $totalIncome = 0;
        $totalVouchers = 0;

        foreach ($users as $user) {
            // Skip if no price
            if (empty($user['price']) || $user['price'] == '0') continue;

            // Determine Date from Comment
            // Mikhmon format usually: "mikhmon-MM/DD/YYYY" or just "MM/DD/YYYY" or plain comment
            // We will try to parse a date from the comment, or use "Unknown Date"
            $date = 'Unknown Date';
            $comment = $user['comment'] ?? '';
            
            // Regex for date patterns (d-m-Y or m/d/Y or Y-m-d)
            // Simplify: Group by Comment content itself if it looks like a date/batch
            // Or try to extract M-Y.
            
            // For feature parity, Mikhmon often groups by the exact comment string as the "Batch/Date"
            if (!empty($comment)) {
                $date = $comment;
            }

            if (!isset($report[$date])) {
                $report[$date] = [
                    'date' => $date,
                    'count' => 0,
                    'total' => 0
                ];
            }

            $price = intval($user['price']);
            $report[$date]['count']++;
            $report[$date]['total'] += $price;

            $totalIncome += $price;
            $totalVouchers++;
        }

        // Sort by key (Date/Comment) desc
        krsort($report);

        return $this->view('reports/selling', [
            'session' => $session,
            'report' => $report,
            'totalIncome' => $totalIncome,
            'totalVouchers' => $totalVouchers,
            'currency' => $config['currency'] ?? 'Rp'
        ]);
    }
    public function resume($session)
    {
        $configModel = new Config();
        $config = $configModel->getSession($session);
        
        if (!$config) {
            header('Location: /');
            exit;
        }

        $API = new RouterOSAPI();
        $users = [];

        if ($API->connect($config['ip_address'], $config['username'], $config['password'])) {
            $users = $API->comm("/ip/hotspot/user/print");
            $API->disconnect();
        }

        // Initialize Aggregates
        $daily = [];
        $monthly = [];
        $yearly = [];
        $totalIncome = 0;

        foreach ($users as $user) {
            if (empty($user['price']) || $user['price'] == '0') continue;
            
            // Try to parse Date from Comment (Mikhmon format: mikhmon-10/25/2023 or just 10/25/2023)
            $comment = $user['comment'] ?? '';
            $dateObj = null;

            // Simple parser: try to find MM/DD/YYYY
            if (preg_match('/(\d{1,2})[\/.-](\d{1,2})[\/.-](\d{2,4})/', $comment, $matches)) {
                // Assuming MM/DD/YYYY based on typical Mikhmon, but could be DD-MM-YYYY
                // Let's standardise on checking valid date.
                // Standard Mikhmon V3 is MM/DD/YYYY.
                $m = $matches[1];
                $d = $matches[2];
                $y = $matches[3];
                if (strlen($y) == 2) $y = '20' . $y;
                $dateObj = \DateTime::createFromFormat('m/d/Y', "$m/$d/$y");
            }

            // Fallback: If no date found in comment, maybe created at? 
            // Usually Mikhmon relies strictly on comment.
            if (!$dateObj) continue; 

            $price = intval($user['price']);
            $totalIncome += $price;

            // Formats
            $dayKey = $dateObj->format('Y-m-d');
            $monthKey = $dateObj->format('Y-m');
            $yearKey = $dateObj->format('Y');

            // Daily
            if (!isset($daily[$dayKey])) $daily[$dayKey] = 0;
            $daily[$dayKey] += $price;

            // Monthly
            if (!isset($monthly[$monthKey])) $monthly[$monthKey] = 0;
            $monthly[$monthKey] += $price;

            // Yearly
            if (!isset($yearly[$yearKey])) $yearly[$yearKey] = 0;
            $yearly[$yearKey] += $price;
        }

        // Sort Keys
        ksort($daily);
        ksort($monthly);
        ksort($yearly);

        return $this->view('reports/resume', [
            'session' => $session,
            'daily' => $daily,
            'monthly' => $monthly,
            'yearly' => $yearly,
            'totalIncome' => $totalIncome,
            'currency' => $config['currency'] ?? 'Rp'
        ]);
    }
}
