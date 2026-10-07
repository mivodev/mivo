<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Libraries\RouterOSAPI;
use App\Models\Config;

class ApiController extends Controller
{
    public function getInterfaces()
    {
        // Only allow POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);

            return;
        }

        // -----------------------------------------------------------------
        // Security: Require authenticated admin session (defense-in-depth).
        // The route-level 'auth' middleware is the primary gate; this check
        // is a secondary safeguard in case the route is ever misconfigured.
        // Fix for: CWE-306 — reported by kta1kri.
        // -----------------------------------------------------------------
        if (! isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);

            return;
        }

        // Get JSON Input
        $input = json_decode(file_get_contents('php://input'), true);

        $ip = $input['ip'] ?? '';
        $user = $input['user'] ?? '';
        $pass = $input['password'] ?? '';
        $id = $input['id'] ?? null;
        $port = $input['port'] ?? 8728; // Default port

        // -----------------------------------------------------------------
        // Security: When using a stored router record (edit mode), bind ALL
        // connection parameters to the database record. Never connect to a
        // caller-supplied IP while reusing a stored decrypted credential.
        // Fix for: CWE-918 (SSRF) / CWE-522 — reported by kta1kri.
        // -----------------------------------------------------------------
        if (! empty($id)) {
            $configModel = new Config;
            $session = $configModel->getSessionById($id);

            if (! $session) {
                http_response_code(404);
                echo json_encode(['error' => 'Router not found']);

                return;
            }

            // Bind destination to the stored record — ignore caller input
            $ip = $session['ip_address'];
            $user = $session['username'];
            $pass = $session['password']; // Already decrypted by getSessionById()
        }

        if (empty($ip) || empty($user)) {
            http_response_code(400);
            echo json_encode(['error' => 'IP Address and Username are required']);

            return;
        }

        $api = new RouterOSAPI;
        // $api->debug = true; // Enable for debugging
        $api->port = (int) $port;

        if ($api->connect($ip, $user, $pass)) {
            $api->write('/interface/print');
            $read = $api->read(false);
            $interfaces = $api->parseResponse($read);
            $api->disconnect();

            $list = [];
            foreach ($interfaces as $iface) {
                if (isset($iface['name'])) {
                    $list[] = $iface['name'];
                }
            }

            // Return success
            echo json_encode([
                'success' => true,
                'interfaces' => $list,
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'error' => 'Connection failed. Check IP, User, Password, or connectivity.',
            ]);
        }
    }
}

