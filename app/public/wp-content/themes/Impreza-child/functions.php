<?php
/* Custom functions code goes here. */


// ── Design system assets ──────────────────────────────────────────
add_action('wp_enqueue_scripts', 'pa_enqueue_design_assets', 20);
function pa_enqueue_design_assets() {
    $dir = get_stylesheet_directory();
    wp_enqueue_style(
        'pa-design',
        get_stylesheet_directory_uri() . '/pa-design.css',
        [],
        filemtime( $dir . '/pa-design.css' )
    );
    wp_enqueue_style(
        'pa-uikit',
        get_stylesheet_directory_uri() . '/ui-kit.css',
        ['pa-design'],
        filemtime( $dir . '/ui-kit.css' )
    );
    wp_enqueue_style(
        'pa-pdp',
        get_stylesheet_directory_uri() . '/product-page.css',
        ['pa-uikit'],
        filemtime( $dir . '/product-page.css' )
    );
}

// Add pa-storefront class to every page so our CSS applies globally
add_filter('body_class', function ($classes) {
    $classes[] = 'pa-storefront';
    return $classes;
});

// Bypass Impreza's page builder for single product pages
add_action('template_redirect', function() {
    if ( function_exists('is_product') && is_product() ) {
        $custom = get_stylesheet_directory() . '/single-product.php';
        if ( file_exists( $custom ) ) {
            include $custom;
            exit;
        }
    }
}, 1);

// Bypass Impreza's page builder for order received page
add_action('template_redirect', function() {
    if ( function_exists('is_order_received_page') && is_order_received_page() ) {
        $custom = get_stylesheet_directory() . '/woocommerce/checkout/thankyou.php';
        if ( file_exists( $custom ) ) {
            include $custom;
            exit;
        }
    }
}, 1);

// ── Admin: Bewertung löschen mit Begründung per E-Mail ───────────
add_action('wp_ajax_pa_delete_review', function() {
    if ( ! current_user_can('manage_options') ) {
        wp_send_json_error(['message' => 'Keine Berechtigung.'], 403);
    }
    check_ajax_referer('pa_delete_review', 'nonce');

    $comment_id = absint( $_POST['comment_id'] ?? 0 );
    $reason     = sanitize_textarea_field( $_POST['reason'] ?? '' );

    if ( ! $comment_id || ! $reason ) {
        wp_send_json_error(['message' => 'Fehlende Daten.']);
    }

    $comment = get_comment( $comment_id );
    if ( ! $comment ) {
        wp_send_json_error(['message' => 'Bewertung nicht gefunden.']);
    }

    if ( $comment->comment_author_email ) {
        $product_title = get_the_title( $comment->comment_post_ID );
        $body = sprintf(
            "Hallo %s,\n\nIhre Bewertung zum Produkt \"%s\" wurde von unserem Team entfernt.\n\nBegründung:\n%s\n\nBei Fragen können Sie uns jederzeit kontaktieren.\n\nMit freundlichen Grüßen\nDas Plantaphilia-Team",
            $comment->comment_author,
            $product_title,
            $reason
        );
        wp_mail(
            $comment->comment_author_email,
            'Ihre Bewertung bei Plantaphilia wurde entfernt',
            $body,
            ['Content-Type: text/plain; charset=UTF-8']
        );
    }

    if ( wp_delete_comment( $comment_id, true ) ) {
        wp_send_json_success(['message' => 'Bewertung gelöscht.']);
    } else {
        wp_send_json_error(['message' => 'Fehler beim Löschen.']);
    }
});

// ── Doppelbewertung verhindern ────────────────────────────────────
add_filter('preprocess_comment', function( $data ) {
    $user_id    = get_current_user_id();
    $product_id = (int) ( $data['comment_post_ID'] ?? 0 );
    if ( ! $user_id || ! $product_id ) return $data;
    if ( get_post_type( $product_id ) !== 'product' ) return $data;
    $existing = get_comments([
        'post_id' => $product_id,
        'user_id' => $user_id,
        'status'  => 'any',
        'count'   => true,
    ]);
    if ( $existing > 0 ) {
        wp_die(
            esc_html__( 'Sie haben dieses Produkt bereits bewertet.', 'woocommerce' ),
            esc_html__( 'Fehler', 'woocommerce' ),
            [ 'back_link' => true, 'response' => 403 ]
        );
    }
    return $data;
});

// ══════════════════════════════════════════════════════════════════
// SHOP-FRONTEND SECURITY
// ══════════════════════════════════════════════════════════════════

// ── WordPress-Version aus HTML-Quelltext entfernen ────────────────
remove_action('wp_head', 'wp_generator');
add_filter('the_generator', '__return_empty_string');
add_filter('get_the_generator_html',  '__return_empty_string');
add_filter('get_the_generator_xhtml', '__return_empty_string');

// WooCommerce-Version aus dem <head> entfernen
add_action('woocommerce_init', function () {
    remove_action('wp_head', array(WC(), 'generator'));
});

// ── Versionsnummern aus Asset-URLs entfernen ──────────────────────
add_filter('style_loader_src',  'pa_strip_ver_query', 9999);
add_filter('script_loader_src', 'pa_strip_ver_query', 9999);
function pa_strip_ver_query($src) {
    // Keep ver= on our own design stylesheets so cache-busting works in dev
    if ( $src && strpos($src, '/Impreza-child/') !== false ) return $src;
    return $src && strpos($src, 'ver=') !== false ? remove_query_arg('ver', $src) : $src;
}

// ── Unnötige <head>-Tags entfernen ───────────────────────────────
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'feed_links',        2);
remove_action('wp_head', 'feed_links_extra',  3);
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles',  'print_emoji_styles');

// ── XML-RPC vollständig deaktivieren ─────────────────────────────
add_filter('xmlrpc_enabled', '__return_false');
add_filter('xmlrpc_methods', function () { return []; });
add_action('wp_head', function () {
    remove_action('wp_head', 'rsd_link');
});

// ── User-Enumeration via URL-Parameter verhindern ─────────────────
add_action('init', function () {
    if (!is_admin() && !empty($_REQUEST['author'])) {
        wp_redirect(home_url('/'), 301);
        exit;
    }
});

// REST-API: Benutzerliste für Nicht-Admins sperren
add_filter('rest_endpoints', function ($endpoints) {
    if (!current_user_can('manage_options')) {
        unset($endpoints['/wp/v2/users']);
        unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
    }
    return $endpoints;
});

// ── Datei-Editor im Admin deaktivieren ───────────────────────────
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}

// ── Checkout: Honeypot gegen Bots ────────────────────────────────
add_action('woocommerce_after_checkout_billing_form', function () {
    // Unsichtbares Feld – echte Nutzer lassen es leer, Bots füllen es aus
    echo '<div style="display:none!important;visibility:hidden!important;position:absolute;left:-9999px;" aria-hidden="true">';
    echo '<label for="pa_hp">Leer lassen</label>';
    echo '<input type="text" name="pa_hp" id="pa_hp" autocomplete="off" tabindex="-1" value="">';
    echo '</div>';
});

add_action('woocommerce_checkout_process', function () {
    if (!empty($_POST['pa_hp'])) {
        wc_add_notice(__('Bestellung konnte nicht verarbeitet werden. Bitte versuche es erneut.', 'woocommerce'), 'error');
    }
}, 5);

// ── Checkout: Rate-Limiting (max. 5 Versuche / 10 min pro IP) ─────
add_action('woocommerce_checkout_process', function () {
    $ip      = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'] ?? ''));
    $key     = 'pa_co_' . md5($ip);
    $count   = (int) get_transient($key);

    if ($count >= 5) {
        wc_add_notice(
            'Zu viele Bestellversuche von deiner IP-Adresse. Bitte warte 10 Minuten.',
            'error'
        );
        return;
    }

    set_transient($key, $count + 1, 10 * MINUTE_IN_SECONDS);
}, 10);

// Counter zurücksetzen nach erfolgreicher Bestellung
add_action('woocommerce_checkout_order_created', function () {
    $ip  = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'] ?? ''));
    delete_transient('pa_co_' . md5($ip));
});

// ── Login: Brute-Force-Schutz (max. 5 Fehlversuche / 15 min) ─────
add_filter('authenticate', function ($user, $username, $password) {
    if (empty($username) || empty($password)) {
        return $user;
    }
    $ip    = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'] ?? ''));
    $key   = 'pa_li_' . md5($ip);
    $count = (int) get_transient($key);

    if ($count >= 5) {
        return new WP_Error(
            'too_many_attempts',
            'Zu viele Anmeldeversuche. Bitte warte 15 Minuten und versuche es erneut.'
        );
    }
    return $user;
}, 30, 3);

add_action('wp_login_failed', function () {
    $ip  = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'] ?? ''));
    $key = 'pa_li_' . md5($ip);
    set_transient($key, (int) get_transient($key) + 1, 15 * MINUTE_IN_SECONDS);
});

add_action('wp_login', function () {
    $ip = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'] ?? ''));
    delete_transient('pa_li_' . md5($ip));
});

// ── WooCommerce: Bestellnummern-Präfix (verschleiert Bestellvolumen) ─
add_filter('woocommerce_order_number', function ($order_id) {
    return 'PA-' . str_pad((int) $order_id + 10000, 7, '0', STR_PAD_LEFT);
});

// ══════════════════════════════════════════════════════════════════
// ADMIN-SEITEN SECURITY
// ══════════════════════════════════════════════════════════════════

// ── Generische Login-Fehlermeldung (kein Hinweis ob User oder PW falsch) ──
add_filter('login_errors', function () {
    return 'Benutzername oder Passwort ist falsch.';
});

// Login-Hints komplett entfernen ("Passwort vergessen?" Text nach Fehler)
add_filter('login_messages', '__return_empty_string');

// ── wp-admin: Nicht-Admins sofort umleiten ────────────────────────
add_action('admin_init', function () {
    // AJAX-Requests und Admin-eigene Actions immer erlauben
    if (wp_doing_ajax()) return;
    if (!current_user_can('manage_options') && is_user_logged_in()) {
        wp_redirect(home_url('/'));
        exit;
    }
});

// ── wp-admin: Nicht eingeloggte Nutzer auf Login weiterleiten ─────
// (WordPress macht das by default, aber wir erzwingen es explizit)
add_action('admin_init', function () {
    if (!is_user_logged_in() && !wp_doing_ajax()) {
        auth_redirect();
    }
});

// ── Admin-Bar: Nur für Admins sichtbar ───────────────────────────
add_action('after_setup_theme', function () {
    if (!current_user_can('manage_options')) {
        show_admin_bar(false);
    }
});

// ── Session-Timeout: Nach 2h Inaktivität automatisch ausloggen ────
// (Für Admin-Nutzer: Session-Gültigkeit auf 2h begrenzen)
add_filter('auth_cookie_expiration', function ($seconds, $user_id) {
    if (user_can($user_id, 'manage_options')) {
        return 2 * HOUR_IN_SECONDS;
    }
    return $seconds;
}, 10, 2);

// Letzte Aktivität beim Login initialisieren
add_action('wp_login', function ($user_login, $user) {
    if (user_can($user, 'manage_options')) {
        update_user_meta($user->ID, '_pa_last_seen', time());
    }
}, 10, 2);

// Letzte Aktivität tracken und bei Ablauf ausloggen
add_action('init', function () {
    if (!is_user_logged_in() || wp_doing_ajax()) return;
    if (!current_user_can('manage_options')) return;

    $timeout    = 2 * HOUR_IN_SECONDS;
    $user_id    = get_current_user_id();
    $last_seen  = get_user_meta($user_id, '_pa_last_seen', true);
    $now        = time();

    // Nur ausloggen, wenn ein gültiger Zeitstempel existiert und Timeout überschritten
    if ($last_seen && is_numeric($last_seen) && ($now - (int)$last_seen) > $timeout) {
        wp_logout();
        wp_redirect(wp_login_url(home_url('/')) . '&pa_timeout=1');
        exit;
    }

    // Zeitstempel nur aktualisieren, wenn Seite nicht Admin-Login ist
    if (!isset($_GET['pa_timeout'])) {
        update_user_meta($user_id, '_pa_last_seen', $now);
    }
});

// Timeout-Hinweis auf der Login-Seite anzeigen
add_filter('login_message', function ($message) {
    if (!empty($_GET['pa_timeout'])) {
        $message .= '<p class="message" style="background:#fff3cd;border-left:4px solid #f0ad4e;padding:8px 12px;">'
            . 'Du wurdest wegen Inaktivität automatisch ausgeloggt.'
            . '</p>';
    }
    return $message;
});

// ── Login-Benachrichtigung per E-Mail ────────────────────────────
add_action('wp_login', function ($user_login, $user) {
    if (!user_can($user, 'manage_options')) return;

    $ip        = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'] ?? 'unbekannt'));
    $agent     = sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'] ?? 'unbekannt'));
    $time      = wp_date('d.m.Y H:i:s');
    $site_name = get_bloginfo('name');

    wp_mail(
        get_option('admin_email'),
        "[{$site_name}] Admin-Login: {$user_login}",
        "Ein Administrator hat sich eingeloggt.\n\n"
            . "Benutzer:  {$user_login}\n"
            . "Zeit:      {$time}\n"
            . "IP:        {$ip}\n"
            . "Browser:   {$agent}\n\n"
            . "Falls du das nicht warst, ändere sofort dein Passwort:\n"
            . wp_lostpassword_url()
    );
}, 10, 2);

// ── Admin-Bereich: Zusätzliche Security-Header ────────────────────
add_action('admin_init', function () {
    if (headers_sent()) return;
    header('X-Frame-Options: DENY');            // Im Admin strikter als SAMEORIGIN
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
});

// ── Custom Admin Pages: Redirect statt "Zugriff verweigert" ──────
// Ersetzt die generischen Fehlermeldungen in page-produkt-*.php mit
// einem sauberen Redirect – verhindert Informationslecks über Seitenstruktur
add_action('template_redirect', function () {
    if (!is_page()) return;

    $protected_templates = [
        'page-produkt-hinzufuegen.php',
        'page-produkt-liste.php',
        'page-bestellungen.php',
    ];

    $template = basename(get_page_template());
    if (!in_array($template, $protected_templates, true)) return;

    if (!is_user_logged_in()) {
        wp_redirect(wp_login_url(get_permalink()), 302);
        exit;
    }

    if (!current_user_can('manage_options')) {
        wp_redirect(home_url('/'), 302);
        exit;
    }
}, 1);

// ══════════════════════════════════════════════════════════════════
// AI CHATBOT — Sektion 7
// ══════════════════════════════════════════════════════════════════

add_action('wp_ajax_pa_chatbot_stream',        'pa_chatbot_stream_handler');
add_action('wp_ajax_nopriv_pa_chatbot_stream', 'pa_chatbot_stream_handler');

// ── Key rotation (max 40 req/min per key) ────────────────────────
function pa_chatbot_get_api_key(): string {
    $keys = defined('PLANTAPHILIA_NVAPI_KEYS') ? PLANTAPHILIA_NVAPI_KEYS : [];
    if (empty($keys)) {
        error_log('[Plantaphilia] PLANTAPHILIA_NVAPI_KEYS nicht gesetzt – app/.env prüfen.');
        return '';
    }
    $minute = floor(time() / 60);
    foreach ($keys as $i => $key) {
        $transient = 'pa_nvapi_rpm_' . $i . '_' . $minute;
        $count = (int) get_transient($transient);
        if ($count < 40) {
            set_transient($transient, $count + 1, 90);
            return $key;
        }
    }
    return $keys[0]; // alle Keys erschöpft – Fallback auf ersten
}

// ── SSE helper ───────────────────────────────────────────────────
function pa_sse(string $type, array $payload = []) {
    echo 'data: ' . wp_json_encode(array_merge(['type' => $type], $payload)) . "\n\n";
    if (ob_get_level()) ob_flush();
    flush();
}

// ── Streaming API call via cURL ──────────────────────────────────
// Calls NVIDIA NIM with stream:true. Fires $on_chunk(array $event)
// for each SSE token. Returns accumulated ['content','reasoning','tool_calls'].
function pa_chatbot_stream_api(array $messages, array $tools, callable $on_chunk): array {
    $key      = pa_chatbot_get_api_key();
    $endpoint = defined('PLANTAPHILIA_NVAPI_ENDPOINT') ? PLANTAPHILIA_NVAPI_ENDPOINT : '';
    $model    = defined('PLANTAPHILIA_NVAPI_MODEL')    ? PLANTAPHILIA_NVAPI_MODEL    : 'z-ai/glm-5.1';

    $body = [
        'model'                 => $model,
        'messages'              => $messages,
        'max_tokens'            => 16384,
        'temperature'           => 1,
        'top_p'                 => 1,
        'stream'                => true,
        'chat_template_kwargs'  => ['enable_thinking' => true, 'clear_thinking' => false],
    ];
    if (!empty($tools)) {
        $body['tools']       = $tools;
        $body['tool_choice'] = 'auto';
    }

    $acc_content   = '';
    $acc_reasoning = '';
    $tool_calls    = [];   // indexed by tool_call index
    $line_buf      = '';

    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => wp_json_encode($body),
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $key,
            'Content-Type: application/json',
            'Accept: text/event-stream',
        ],
        CURLOPT_RETURNTRANSFER => false,
        CURLOPT_TIMEOUT        => 120,
        CURLOPT_WRITEFUNCTION  => function ($ch, $raw) use (
            &$line_buf, &$acc_content, &$acc_reasoning, &$tool_calls, $on_chunk
        ) {
            $line_buf .= $raw;
            // Process complete lines
            while (($pos = strpos($line_buf, "\n")) !== false) {
                $line     = substr($line_buf, 0, $pos);
                $line_buf = substr($line_buf, $pos + 1);
                $line     = rtrim($line, "\r");

                if (!str_starts_with($line, 'data: ')) continue;
                $json = substr($line, 6);
                if ($json === '[DONE]') continue;

                $chunk = json_decode($json, true);
                if (!$chunk) continue;

                $choice = $chunk['choices'][0] ?? null;
                if (!$choice) continue;
                $delta = $choice['delta'] ?? [];

                // Reasoning
                if (!empty($delta['reasoning_content'])) {
                    $acc_reasoning .= $delta['reasoning_content'];
                    $on_chunk(['type' => 'reasoning', 'chunk' => $delta['reasoning_content']]);
                }
                // Content
                if (!empty($delta['content'])) {
                    $acc_content .= $delta['content'];
                    $on_chunk(['type' => 'content', 'chunk' => $delta['content']]);
                }
                // Tool calls (accumulated across chunks)
                if (!empty($delta['tool_calls'])) {
                    foreach ($delta['tool_calls'] as $tc) {
                        $idx = $tc['index'] ?? 0;
                        if (!isset($tool_calls[$idx])) {
                            $tool_calls[$idx] = ['id' => '', 'function' => ['name' => '', 'arguments' => '']];
                        }
                        if (!empty($tc['id']))                          $tool_calls[$idx]['id']                        .= $tc['id'];
                        if (!empty($tc['function']['name']))            $tool_calls[$idx]['function']['name']           .= $tc['function']['name'];
                        if (isset($tc['function']['arguments']))       $tool_calls[$idx]['function']['arguments']      .= $tc['function']['arguments'];
                    }
                }
            }
            return strlen($raw);
        },
    ]);
    curl_exec($ch);
    curl_close($ch);

    return [
        'content'     => $acc_content,
        'reasoning'   => $acc_reasoning,
        'tool_calls'  => array_values($tool_calls),
    ];
}

// ── Recursive streaming conversation loop ────────────────────────
function pa_chatbot_stream_loop(array &$messages, int $depth = 0) {
    if ($depth >= 4) return;
    $tools = pa_chatbot_tools();

    $result = pa_chatbot_stream_api($messages, $tools, function (array $event) {
        pa_sse($event['type'], ['chunk' => $event['chunk']]);
    });

    // If tool calls — execute and recurse
    if (!empty($result['tool_calls'])) {
        // Add assistant turn
        $assistant_turn = ['role' => 'assistant', 'content' => $result['content']];
        $assistant_turn['tool_calls'] = array_map(fn($tc) => [
            'id'       => $tc['id'],
            'type'     => 'function',
            'function' => ['name' => $tc['function']['name'], 'arguments' => $tc['function']['arguments']],
        ], $result['tool_calls']);
        $messages[] = $assistant_turn;

        foreach ($result['tool_calls'] as $tc) {
            $fn_name = $tc['function']['name'];
            $fn_args = json_decode($tc['function']['arguments'], true) ?: [];

            pa_sse('tool_start', ['name' => $fn_name]);
            $tool_result = pa_chatbot_execute_tool($fn_name, $fn_args);
            pa_sse('tool_done',  ['name' => $fn_name]);

            $messages[] = [
                'role'         => 'tool',
                'tool_call_id' => $tc['id'],
                'content'      => $tool_result,
            ];
        }

        pa_sse('new_turn', []);
        pa_chatbot_stream_loop($messages, $depth + 1);
    }
}

// ── Main streaming AJAX handler ──────────────────────────────────
function pa_chatbot_stream_handler() {
    if (!check_ajax_referer('pa_chatbot_nonce', 'nonce', false)) {
        http_response_code(403); exit;
    }

    // ── Rate-Limiting: max. 20 Anfragen/Stunde, 60/Tag pro IP ────
    $ip       = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'] ?? ''));
    $ip_hash  = md5($ip);
    $key_h    = 'pa_cb_h_' . $ip_hash;
    $key_d    = 'pa_cb_d_' . $ip_hash;
    $count_h  = (int) get_transient($key_h);
    $count_d  = (int) get_transient($key_d);

    if ($count_h >= 20 || $count_d >= 60) {
        while (ob_get_level()) ob_end_clean();
        header('Content-Type: text/event-stream; charset=utf-8');
        pa_sse('error', ['message' => 'Zu viele Anfragen. Bitte versuche es später erneut.']);
        pa_sse('done', []);
        exit;
    }

    set_transient($key_h, $count_h + 1, HOUR_IN_SECONDS);
    if (!$count_d) set_transient($key_d, 1, DAY_IN_SECONDS);
    else           set_transient($key_d, $count_d + 1, DAY_IN_SECONDS);

    // Kill all output buffers
    while (ob_get_level()) ob_end_clean();
    @ini_set('zlib.output_compression', '0');

    header('Content-Type: text/event-stream; charset=utf-8');
    header('Cache-Control: no-cache');
    header('X-Accel-Buffering: no');

    // ── Payload-Größe prüfen (max. 50 KB) ────────────────────────
    $raw = $_POST['history'] ?? '[]';
    if (strlen($raw) > 51200) {
        pa_sse('error', ['message' => 'Nachrichtenverlauf zu groß.']);
        pa_sse('done', []);
        exit;
    }

    $history = json_decode(wp_unslash($raw), true);
    if (!is_array($history)) $history = [];

    // Max. 20 Nachrichten, je max. 2000 Zeichen
    $history = array_slice(array_map(function ($m) {
        return [
            'role'    => in_array($m['role'] ?? '', ['user', 'assistant']) ? $m['role'] : 'user',
            'content' => mb_substr(sanitize_textarea_field($m['content'] ?? ''), 0, 2000),
        ];
    }, $history), -20);

    $messages = array_merge(
        [['role' => 'system', 'content' => pa_chatbot_system_prompt()]],
        $history
    );

    pa_chatbot_stream_loop($messages);

    pa_sse('done', []);
    exit;
}

// ── Tool definitions sent to the model ──────────────────────────
function pa_chatbot_tools() {
    return [
        [
            'type' => 'function',
            'function' => [
                'name'        => 'send_email_to_admin',
                'description' => 'Sendet eine Email an den Chef/die Chefin von Plantaphilia bei komplexen Problemen oder Beschwerden, die du nicht selbst lösen kannst.',
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [
                        'subject'        => ['type' => 'string', 'description' => 'Betreff der Email'],
                        'message'        => ['type' => 'string', 'description' => 'Nachricht an den Chef (Deutsch, vollständig)'],
                        'customer_email' => ['type' => 'string', 'description' => 'Email-Adresse des Kunden (falls bekannt)'],
                    ],
                    'required' => ['subject', 'message'],
                ],
            ],
        ],
        [
            'type' => 'function',
            'function' => [
                'name'        => 'get_order_history',
                'description' => 'Ruft die Bestellhistorie eines Kunden ab. Nur aufrufen wenn der Kunde ausdrücklich nach seinen Bestellungen fragt UND dabei sowohl seine E-Mail-Adresse als auch eine Bestellnummer (z.B. PA-0010042) nennt.',
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [
                        'customer_email' => ['type' => 'string', 'description' => 'E-Mail-Adresse des Kunden'],
                        'order_number'   => ['type' => 'string', 'description' => 'Eine Bestellnummer des Kunden zur Verifikation (z.B. PA-0010042)'],
                    ],
                    'required' => ['customer_email', 'order_number'],
                ],
            ],
        ],
        [
            'type' => 'function',
            'function' => [
                'name'        => 'get_product_info',
                'description' => 'Sucht Produktinformationen zu einem Pelargonium oder einem anderen Produkt im Shop.',
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [
                        'search_term' => ['type' => 'string', 'description' => 'Produktname oder Suchbegriff'],
                    ],
                    'required' => ['search_term'],
                ],
            ],
        ],
    ];
}

// ── Tool execution ───────────────────────────────────────────────
function pa_chatbot_execute_tool(string $name, array $args): string {
    switch ($name) {

        case 'send_email_to_admin':
            // Rate-Limit: max. 3 Chatbot-E-Mails pro IP pro Tag
            $ip      = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'] ?? ''));
            $mail_key = 'pa_cb_mail_' . md5($ip);
            $mail_count = (int) get_transient($mail_key);
            if ($mail_count >= 3) {
                return 'E-Mail-Limit erreicht. Bitte kontaktiere uns direkt unter kontakt@plantaphilia.eu.';
            }

            $subject = mb_substr(sanitize_text_field($args['subject'] ?? 'Kundenanfrage via Chatbot'), 0, 150);
            $message = mb_substr(sanitize_textarea_field($args['message'] ?? ''), 0, 3000);
            $from    = sanitize_email($args['customer_email'] ?? '');
            $to      = get_option('admin_email');
            $headers = $from ? ['Reply-To: ' . $from] : [];
            $body    = $message . ($from ? "\n\nKunden-Email: " . $from : '');
            $sent    = wp_mail($to, '[Plantaphilia Chatbot] ' . $subject, $body, $headers);

            if ($sent) {
                set_transient($mail_key, $mail_count + 1, DAY_IN_SECONDS);
                return 'Email wurde erfolgreich an den Chef gesendet. Der Kunde wird in Kürze kontaktiert.';
            }
            return 'Email konnte nicht gesendet werden. Bitte den Kunden an kontakt@plantaphilia.eu verweisen.';

        case 'get_order_history':
            if (!function_exists('wc_get_orders')) return 'WooCommerce nicht verfügbar.';

            $email        = sanitize_email($args['customer_email'] ?? '');
            $order_number = sanitize_text_field($args['order_number'] ?? '');

            if (!$email || !$order_number) {
                return 'Bitte nenne mir deine E-Mail-Adresse und eine Bestellnummer zur Verifikation.';
            }

            // Verifikation: Bestellnummer muss zur E-Mail gehören
            // PA-XXXXXXX → post_id = (int ohne Präfix) - 10000
            $numeric = preg_replace('/[^0-9]/', '', $order_number);
            $post_id = (int) $numeric - 10000;
            $verify_order = ($post_id > 0) ? wc_get_order($post_id) : null;

            $verified = false;
            if ($verify_order && $verify_order->get_billing_email() === $email) {
                $verified = true;
            }

            // Fallback: direkte Suche nach Bestellnummer in allen Bestellungen dieser E-Mail
            if (!$verified) {
                $check_orders = wc_get_orders(['billing_email' => $email, 'limit' => 20]);
                foreach ($check_orders as $co) {
                    if (strcasecmp($co->get_order_number(), $order_number) === 0) {
                        $verified = true;
                        break;
                    }
                }
            }

            if (!$verified) {
                return 'Die angegebene Bestellnummer konnte nicht verifiziert werden. Bitte prüfe E-Mail-Adresse und Bestellnummer.';
            }

            $orders = wc_get_orders([
                'billing_email' => $email,
                'limit'         => 10,
                'orderby'       => 'date',
                'order'         => 'DESC',
            ]);
            if (empty($orders)) return "Keine Bestellungen für {$email} gefunden.";

            $lines = [];
            foreach ($orders as $order) {
                $items = [];
                foreach ($order->get_items() as $item) {
                    $items[] = $item->get_quantity() . 'x ' . $item->get_name();
                }
                $lines[] = sprintf(
                    'Bestellung %s (%s) — Status: %s — %s — Summe: %s',
                    $order->get_order_number(),
                    $order->get_date_created()->date('d.m.Y'),
                    wc_get_order_status_name($order->get_status()),
                    implode(', ', $items),
                    $order->get_formatted_order_total()
                );
            }
            return implode("\n", $lines);

        case 'get_product_info':
            $term = sanitize_text_field($args['search_term'] ?? '');
            if (!$term) return 'Kein Suchbegriff angegeben.';
            $results = new WP_Query([
                'post_type'      => 'product',
                'posts_per_page' => 5,
                's'              => $term,
                'post_status'    => 'publish',
            ]);
            if (!$results->have_posts()) return "Keine Produkte für '{$term}' gefunden.";
            $lines = [];
            while ($results->have_posts()) {
                $results->the_post();
                $p      = wc_get_product(get_the_ID());
                $stock  = $p->is_in_stock() ? 'verfügbar' : 'ausverkauft';
                $price  = $p->get_price() ? '€ ' . number_format((float)$p->get_price(), 2, ',', '.') : 'Preis auf Anfrage';
                $lines[] = get_the_title() . ' — ' . $price . ' — ' . $stock;
            }
            wp_reset_postdata();
            return implode("\n", $lines);

        default:
            return 'Unbekanntes Tool.';
    }
}

// ── System prompt ────────────────────────────────────────────────
function pa_chatbot_system_prompt(): string {
    $logged_in = is_user_logged_in();
    $user_info = '';
    if ($logged_in) {
        $user = wp_get_current_user();
        $user_info = "\nDer Kunde ist eingeloggt als: " . $user->display_name . " (" . $user->user_email . ").";
    }
    return "Du bist ein freundlicher und kompetenter Kundenservice-Assistent für Plantaphilia — eine kleine Gärtnerei aus Forstinning, die sich auf seltene Pelargonien und exotische Raritäten spezialisiert hat.

Du hilfst Kunden bei:
- Fragen zu Produkten, Pelargonien-Sorten und Pflanzenpflege
- Bestellstatus und Bestellhistorie (Tool verwenden)
- Versand- und Zahlungsinformationen
- Allgemeinen Fragen zum Shop

Bei komplexen Problemen oder Beschwerden, die du nicht selbst lösen kannst, sendest du eine Email an den Chef (Tool verwenden).

Antworte IMMER auf Deutsch. Sei warm, kompetent und leidenschaftlich — wie jemand der Pelargonien wirklich liebt. Halte Antworten präzise (max. 3 Absätze). Keine Aufzählungen es sei denn wirklich nötig." . $user_info;
}


// ── AJAX Handler für Produktliste
add_action('wp_ajax_get_product_list_data', 'get_product_list_data');

// Funktion zum Berechnen von in_progress basierend auf offenen Bestellungen
function calculate_in_progress_from_orders() {
    // Array zum Speichern der Summen pro Produkt
    $product_quantities = array();
    
    // pending, on-hold und processing Bestellungen zählen
    // Bestand wird erst bei completed reduziert
    $order_statuses = array('pending', 'on-hold', 'processing');
    
    // Alle Bestellungen mit offenen Statusse abrufen
    $args = array(
        'status' => $order_statuses,
        'limit' => -1,
        'return' => 'ids'
    );
    
    $order_ids = wc_get_orders($args);
    
    foreach ($order_ids as $order_id) {
        $order = wc_get_order($order_id);
        if (!$order) continue;
        
        // Alle Items der Bestellung durchgehen
        $items = $order->get_items();
        
        foreach ($items as $item) {
            $product_id = $item->get_product_id();
            $quantity = $item->get_quantity();
            
            if (!isset($product_quantities[$product_id])) {
                $product_quantities[$product_id] = 0;
            }
            
            $product_quantities[$product_id] += $quantity;
        }
    }
    
    // Ergebnisse in _in_progress speichern
    foreach ($product_quantities as $product_id => $quantity) {
        update_post_meta($product_id, '_in_progress', $quantity);
    }
    
    // Für Produkte ohne offene Bestellungen, in_progress auf 0 setzen
    $all_product_ids = get_posts(array(
        'post_type' => 'product',
        'numberposts' => -1,
        'fields' => 'ids'
    ));
    
    foreach ($all_product_ids as $product_id) {
        if (!isset($product_quantities[$product_id])) {
            update_post_meta($product_id, '_in_progress', 0);
        }
    }
}

// Hooks für automatische Aktualisierung bei Bestellungsänderungen
add_action('woocommerce_order_status_changed', 'update_in_progress_on_order_status_change', 10, 4);
add_action('woocommerce_new_order', 'update_in_progress_on_new_order', 10, 1);
add_action('woocommerce_delete_order_item', 'update_in_progress_on_order_item_change', 10, 1);
add_action('woocommerce_add_order_item', 'update_in_progress_on_order_item_change', 10, 1);
add_filter('woocommerce_can_reduce_order_stock', 'prevent_stock_reduction_for_non_pending', 10, 2);

function prevent_stock_reduction_for_non_pending($can_reduce, $order) {
    $status = $order->get_status();
    // Bestand NICHT für pending, on-hold und processing reduzieren, nur für completed
    // pending, on-hold und processing werden in "In Bearbeitung" gezählt
    if ($status === 'pending' || $status === 'on-hold' || $status === 'processing') {
        return false;
    }
    return $can_reduce;
}

function update_in_progress_on_order_status_change($order_id, $old_status, $new_status, $order) {
    // Wenn Status zu completed wechselt, Bestand reduzieren
    if ($new_status === 'completed' && $old_status !== 'completed') {
        wc_reduce_stock_levels($order_id);
    }
    
    // Berechnung neu ausführen, da sich der Status geändert hat
    calculate_in_progress_from_orders();
}

function update_in_progress_on_new_order($order_id) {
    // Berechnung neu ausführen, da eine neue Bestellung erstellt wurde
    calculate_in_progress_from_orders();
}

function update_in_progress_on_order_item_change($item_id) {
    // Berechnung neu ausführen, da ein Item hinzugefügt oder gelöscht wurde
    calculate_in_progress_from_orders();
}

// Filter für verfügbare Menge basierend auf in_progress
add_filter('woocommerce_get_availability', 'adjust_availability_based_on_in_progress', 10, 2);
add_filter('woocommerce_add_to_cart_validation', 'validate_cart_quantity_based_on_in_progress', 10, 4);
add_filter('woocommerce_quantity_input_args', 'adjust_quantity_input_args_based_on_in_progress', 10, 2);
add_filter('woocommerce_cart_item_quantity', 'validate_cart_item_quantity_based_on_in_progress', 10, 3);
add_filter('woocommerce_is_sold_individually', '__return_false', 10, 2);

function validate_cart_quantity_based_on_in_progress($passed, $product_id, $quantity, $variation_id = 0) {
    $product = wc_get_product($product_id);
    if (!$product) return $passed;
    
    $stock = $product->get_stock_quantity();
    $in_progress = get_post_meta($product_id, '_in_progress', true);
    $in_progress = $in_progress ? intval($in_progress) : 0;
    
    $available = $stock - $in_progress;
    
    // Prüfen, ob bereits im Warenkorb
    $cart_quantity = 0;
    foreach (WC()->cart->get_cart() as $cart_item) {
        if ($cart_item['product_id'] == $product_id) {
            $cart_quantity += $cart_item['quantity'];
        }
    }
    
    $total_quantity = $cart_quantity + $quantity;
    
    if ($total_quantity > $available) {
        wc_add_notice(sprintf(__('Du kannst nur %d Stück dieses Produkts bestellen. Aktuell im Warenkorb: %d', 'woocommerce'), $available, $cart_quantity), 'error');
        return false;
    }
    
    return $passed;
}

function validate_cart_item_quantity_based_on_in_progress($quantity, $cart_item_key, $cart_item) {
    $product_id = $cart_item['product_id'];
    $product = wc_get_product($product_id);
    if (!$product) return $quantity;
    
    $stock = $product->get_stock_quantity();
    $in_progress = get_post_meta($product_id, '_in_progress', true);
    $in_progress = $in_progress ? intval($in_progress) : 0;
    
    $available = $stock - $in_progress;
    
    // Wenn der Benutzer versucht, mehr als verfügbar zu wählen, aber nicht blockieren
    // Die Validierung erfolgt beim Checkout
    return $quantity;
}

function adjust_quantity_input_args_based_on_in_progress($args, $product) {
    $stock = $product->get_stock_quantity();
    $in_progress = get_post_meta($product->get_id(), '_in_progress', true);
    $in_progress = $in_progress ? intval($in_progress) : 0;
    
    $available = $stock - $in_progress;
    
    $args['max_value'] = $available > 0 ? $available : 0;
    
    return $args;
}

function adjust_availability_based_on_in_progress($availability, $product) {
    $stock = $product->get_stock_quantity();
    $in_progress = get_post_meta($product->get_id(), '_in_progress', true);
    $in_progress = $in_progress ? intval($in_progress) : 0;
    
    $available = $stock - $in_progress;
    
    if ($product->is_in_stock()) {
        if ($available <= 0) {
            $availability['availability'] = __('Nicht verfügbar', 'woocommerce');
            $availability['class'] = 'out-of-stock';
        } elseif ($available < 5) {
            $availability['availability'] = __('Nur noch ' . $available . ' verfügbar', 'woocommerce');
            $availability['class'] = 'low-stock';
        } else {
            $availability['availability'] = __('Auf Lager', 'woocommerce');
        }
    }
    
    return $availability;
}

function get_product_list_data() {
    error_log('DEBUG: get_product_list_data aufgerufen');
    check_ajax_referer('product_list_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    // Zuerst in_progress für alle Produkte basierend auf offenen Bestellungen berechnen
    calculate_in_progress_from_orders();
    
    $products = wc_get_products(array(
        'limit' => -1,
        'status' => 'publish'
    ));
    
    $product_data = array();
    
    foreach ($products as $product) {
        $image_id = $product->get_image_id();
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';
        
        $stock = $product->get_stock_quantity();
        $in_progress = get_post_meta($product->get_id(), '_in_progress', true);
        $in_progress = $in_progress ? intval($in_progress) : 0;
        
        $regular_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();
        $price = $product->get_price();
        
        // Prüfen, ob Angebot abgelaufen ist und bereinigen
        if (!empty($sale_price)) {
            $date_to = $product->get_date_on_sale_to();
            if ($date_to) {
                $now = current_time('timestamp', true);
                $end_time = $date_to->getTimestamp();
                
                // Wenn das Angebot abgelaufen ist
                if ($end_time < $now) {
                    // Zuerst _original_regular_price prüfen (der Preis VOR dem Angebot)
                    $saved_regular_price = get_post_meta($product->get_id(), '_original_regular_price', true);
                    
                    // Falls nicht vorhanden, _saved_regular_price prüfen
                    if (!$saved_regular_price) {
                        $saved_regular_price = get_post_meta($product->get_id(), '_saved_regular_price', true);
                    }
                    
                    // Fallback: Falls nichts existiert, den aktuellen regular_price verwenden
                    if (!$saved_regular_price) {
                        $saved_regular_price = $product->get_regular_price();
                    }
                    
                    if ($saved_regular_price) {
                        // Angebot im WooCommerce-Produkt löschen
                        $product->set_sale_price('');
                        $product->set_date_on_sale_to('');
                        $product->set_date_on_sale_from('');
                        $product->set_regular_price($saved_regular_price);
                        $product->save();
                        
                        // Angebots-Meta-Daten löschen
                        update_post_meta($product->get_id(), '_sale_price', '');
                        update_post_meta($product->get_id(), '_sale_price_dates_from', '');
                        update_post_meta($product->get_id(), '_sale_price_dates_to', '');
                        
                        // Umfassendes Cache-Leeren
                        wc_delete_product_transients($product->get_id());
                        wp_cache_delete($product->get_id(), 'products');
                        wp_cache_flush();
                        delete_transient('wc_products_onsale');
                        
                        // Werte neu laden
                        $sale_price = $product->get_sale_price();
                        $price = $product->get_price();
                        $regular_price = $product->get_regular_price();
                    }
                }
            }
        }
        
        $has_offer = !empty($sale_price) && $sale_price < $regular_price;
        $offer_end = '';
        
        if ($has_offer) {
            $date_from = $product->get_date_on_sale_from();
            $date_to = $product->get_date_on_sale_to();
            if ($date_to) {
                // Als Unix-Timestamp senden (in Sekunden)
                $offer_end = $date_to->getTimestamp();
            }
        }
        
        // Verkaufen-Status prüfen (catalog_visibility)
        $is_sellable = $product->get_catalog_visibility() !== 'hidden';
        
        // Meta-Daten für Angebot
        $show_old_price = get_post_meta($product->get_id(), '_show_old_price', true);
        $time_limited = get_post_meta($product->get_id(), '_time_limited', true);
        $time_limit_type = get_post_meta($product->get_id(), '_time_limit_type', true);
        $time_limit_duration = get_post_meta($product->get_id(), '_time_limit_duration', true);
        $time_limit_date = get_post_meta($product->get_id(), '_time_limit_date', true);
        $show_end_date = get_post_meta($product->get_id(), '_show_end_date', true);
        $time_limit_days = get_post_meta($product->get_id(), '_time_limit_days', true);
        $time_limit_hours = get_post_meta($product->get_id(), '_time_limit_hours', true);
        $time_limit_minutes = get_post_meta($product->get_id(), '_time_limit_minutes', true);
        $time_limit_time = get_post_meta($product->get_id(), '_time_limit_time', true);
        $time_limit_date_only = get_post_meta($product->get_id(), '_time_limit_date_only', true);
        $offer_start_date = get_post_meta($product->get_id(), '_offer_start_date', true);
        
        // Startdatum aus WooCommerce Produkt abrufen
        $offer_start = 0;
        if ($date_from) {
            $offer_start = $date_from->getTimestamp();
        }
        
        // Meta-Daten für abgelaufene Angebote
        $offer_expired_read = get_post_meta($product->get_id(), '_offer_expired_read', true);
        $offer_expired_since = get_post_meta($product->get_id(), '_offer_expired_since', true);
        
        // Meta-Daten für Restock-Status
        $was_out_of_stock = get_post_meta($product->get_id(), '_was_out_of_stock', true);
        $was_low_stock = get_post_meta($product->get_id(), '_was_low_stock', true);
        $was_restocked = get_post_meta($product->get_id(), '_was_restocked', true);
        $out_of_stock_since = get_post_meta($product->get_id(), '_out_of_stock_since', true);
        $low_stock_since = get_post_meta($product->get_id(), '_low_stock_since', true);
        $restocked_since = get_post_meta($product->get_id(), '_restocked_since', true);
        
        // Meta-Daten für Bulk-Aktionen
        $part_of_sale = get_post_meta($product->get_id(), '_part_of_sale', true);
        $sale_id = get_post_meta($product->get_id(), '_sale_id', true);
        $sale_group_id = get_post_meta($product->get_id(), '_sale_group_id', true);
        $sale_title = '';
        $group_name = '';
        $bulk_sale_start_date = '';
        $bulk_sale_end_date = '';
        
        if ($part_of_sale && $sale_id) {
            $bulk_sales = get_option('_bulk_sales', array());
            if (isset($bulk_sales[$sale_id])) {
                $sale_title = $bulk_sales[$sale_id]['title'];
                $bulk_sale_start_date = isset($bulk_sales[$sale_id]['start_date']) ? $bulk_sales[$sale_id]['start_date'] : '';
                $bulk_sale_end_date = isset($bulk_sales[$sale_id]['end_date']) ? $bulk_sales[$sale_id]['end_date'] : '';
                if (isset($bulk_sales[$sale_id]['groups'][$sale_group_id])) {
                    $group_name = $bulk_sales[$sale_id]['groups'][$sale_group_id]['name'];
                }
            }
        }
        
        // Prüfen, ob Angebot abgelaufen ist
        $is_offer_expired = false;
        if ($has_offer && $time_limited && $offer_end) {
            $now = current_time('timestamp', true);
            if ($offer_end < $now) {
                $is_offer_expired = true;
                // Wenn noch kein abgelaufen_seit gesetzt, setzen
                if (!$offer_expired_since) {
                    $offer_expired_since = $now;
                    update_post_meta($product->get_id(), '_offer_expired_since', $offer_expired_since);
                }
            }
        }
        
        // Auch prüfen, ob das Produkt ein abgelaufenes Angebot hatte (bereinigt)
        if (!$is_offer_expired && $offer_expired_since && !$has_offer) {
            $is_offer_expired = true;
        }
        
        // Prüfen, ob der Artikel neu ist (weniger als 7 Tage alt)
        $is_new = false;
        $date_created = $product->get_date_created();
        if ($date_created) {
            $created_timestamp = $date_created->getTimestamp();
            $now = current_time('timestamp', true);
            $days_since_creation = ($now - $created_timestamp) / (24 * 60 * 60);
            if ($days_since_creation < 7) {
                $is_new = true;
            }
        }
        
        // Rabatttyp und Rabattbetrag berechnen oder aus Meta-Daten abrufen
        $offer_reduction_type = '';
        $offer_reduction_amount = '';
        if ($has_offer && $regular_price && $sale_price && $sale_price < $regular_price) {
            $reduction = $regular_price - $sale_price;
            $percentage = ($reduction / $regular_price) * 100;
            // Prüfen, ob der Rabatttyp in den Meta-Daten gespeichert ist
            $price_type_toggle = get_post_meta($product->get_id(), '_price_type_toggle', true);
            if ($price_type_toggle === '1') {
                // Prozentualer Rabatt - Anzahl der Dezimalstellen basierend auf der Differenz berechnen
                $decimal_places = 0;
                $fractional_part = abs($percentage) - floor(abs($percentage));
                if ($fractional_part > 0) {
                    // Dezimalstellen zählen
                    $decimal_str = number_format($fractional_part, 10, '.', '');
                    $decimal_str = rtrim($decimal_str, '0');
                    $decimal_places = strlen(substr($decimal_str, strpos($decimal_str, '.') + 1));
                    // Maximal 2 Dezimalstellen
                    $decimal_places = min($decimal_places, 2);
                }
                $offer_reduction_type = 'percent';
                $offer_reduction_amount = '-' . number_format($percentage, $decimal_places) . '%';
            } else {
                // Fester Rabatt in Euro
                $offer_reduction_type = 'fixed';
                $offer_reduction_amount = '-' . number_format($reduction, 2) . '€';
            }
        } elseif (!$has_offer && $is_offer_expired) {
            // Wenn Angebot abgelaufen und bereinigt, gespeicherte Werte abrufen
            $offer_reduction_type = get_post_meta($product->get_id(), '_offer_reduction_type', true);
            $offer_reduction_amount = get_post_meta($product->get_id(), '_offer_reduction_amount', true);
        }
        
        error_log('DEBUG: Produkt ' . $product->get_id() . ' - has_offer=' . ($has_offer ? 'yes' : 'no') . ', is_offer_expired=' . ($is_offer_expired ? 'yes' : 'no') . ', offer_expired_since=' . $offer_expired_since);
        
        $product_data[] = array(
            'id' => $product->get_id(),
            'name' => $product->get_name(),
            'sku' => $product->get_sku(),
            'image' => $image_url,
            'stock' => $stock ? $stock : 0,
            'in_progress' => $in_progress,
            'price' => $price,
            'regular_price' => $regular_price,
            'sale_price' => $sale_price,
            'has_offer' => $has_offer,
            'offer_end' => $offer_end,
            'offer_start' => $offer_start,
            'permalink' => $product->get_permalink(),
            'edit_link' => get_edit_post_link($product->get_id()),
            'is_sellable' => $is_sellable,
            'catalog_visibility' => $product->get_catalog_visibility(),
            'show_old_price' => $show_old_price,
            'time_limited' => $time_limited,
            'time_limit_type' => $time_limit_type,
            'time_limit_duration' => $time_limit_duration,
            'time_limit_date' => $time_limit_date,
            'show_end_date' => $show_end_date,
            'is_offer_expired' => $is_offer_expired,
            'offer_expired_read' => $offer_expired_read,
            'offer_expired_since' => $offer_expired_since,
            'offer_reduction_type' => $offer_reduction_type,
            'offer_reduction_amount' => $offer_reduction_amount,
            'time_limit_days' => $time_limit_days,
            'time_limit_hours' => $time_limit_hours,
            'time_limit_minutes' => $time_limit_minutes,
            'time_limit_time' => $time_limit_time,
            'time_limit_date_only' => $time_limit_date_only,
            'offer_start_date' => $offer_start_date,
            'is_new' => $is_new,
            'was_out_of_stock' => $was_out_of_stock,
            'was_low_stock' => $was_low_stock,
            'was_restocked' => $was_restocked,
            'out_of_stock_since' => $out_of_stock_since,
            'low_stock_since' => $low_stock_since,
            'restocked_since' => $restocked_since,
            'part_of_sale' => $part_of_sale,
            'sale_id' => $sale_id,
            'sale_group_id' => $sale_group_id,
            'sale_title' => $sale_title,
            'group_name' => $group_name,
            'offer_start_date' => $part_of_sale ? $bulk_sale_start_date : $offer_start_date,
            'offer_end_date' => $part_of_sale ? $bulk_sale_end_date : ($time_limited && $offer_end ? date('d.m.Y H:i', $offer_end) : ''),
'gattung' => get_post_meta($product->get_id(), '_pa_gattung', true),
'art' => get_post_meta($product->get_id(), '_pa_art', true),
'kultivar' => get_post_meta($product->get_id(), '_pa_kultivar', true),
'care_light' => get_post_meta($product->get_id(), '_pa_care_light', true),
'care_water' => get_post_meta($product->get_id(), '_pa_care_water', true),
'care_winter' => get_post_meta($product->get_id(), '_pa_care_winter', true),
'care_temp_min' => get_post_meta($product->get_id(), '_pa_care_temp_min', true),
'care_temp_max' => get_post_meta($product->get_id(), '_pa_care_temp_max', true),
        );
    }
    
    wp_send_json_success($product_data);
}

// AJAX Handler für Verkaufen-Status
add_action('wp_ajax_update_sell_status', 'update_sell_status');

function update_sell_status() {
    check_ajax_referer('product_list_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $product_id = intval($_POST['product_id']);
    $is_sellable = intval($_POST['is_sellable']);
    
    $product = wc_get_product($product_id);
    if (!$product) {
        wp_send_json_error('Product not found');
    }
    
    // Catalog Visibility setzen
    $visibility = $is_sellable ? 'visible' : 'hidden';
    $product->set_catalog_visibility($visibility);
    
    // Sellable Meta-Wert speichern
    update_post_meta($product_id, 'sellable', $is_sellable ? '1' : '0');
    
    $product->save();
    
    wp_send_json_success(array(
        'product_id' => $product_id,
        'is_sellable' => $is_sellable
    ));
}

// AJAX Handler für Produkt-Details-Update
add_action('wp_ajax_update_product_details', 'update_product_details');

// AJAX Handler für Cleanup abgelaufener Angebote
add_action('wp_ajax_cleanup_expired_sale', 'cleanup_expired_sale');

// AJAX Handler für Markieren als gelesen
add_action('wp_ajax_mark_offer_as_read', 'mark_offer_as_read');

function mark_offer_as_read() {
    $product_id = intval($_POST['product_id']);
    $is_read = intval($_POST['is_read']);
    
    update_post_meta($product_id, '_offer_expired_read', $is_read);
    
    if ($is_read) {
        // Wenn als gelesen markiert, is_offer_expired und offer_expired_since zurücksetzen
        delete_post_meta($product_id, '_offer_expired_since');
    }
    
    wp_send_json_success();
}

function cleanup_expired_sale() {
    $product_id = intval($_POST['product_id']);
    
    $product = wc_get_product($product_id);
    if (!$product) {
        wp_send_json_error('Product not found');
    }
    
    // Prüfen, ob ein gespeicherter Original-Preis existiert, der vom aktuellen regular_price abweicht
    $saved_regular_price = get_post_meta($product_id, '_original_regular_price', true);
    if (!$saved_regular_price) {
        $saved_regular_price = get_post_meta($product_id, '_saved_regular_price', true);
    }
    
    // Prüfen, ob ein sale_price existiert (auch wenn is_on_sale false ist)
    $current_sale_price = $product->get_sale_price();
    $date_to   = $product->get_date_on_sale_to();
    $date_from = $product->get_date_on_sale_from();

    $needs_cleanup = false;
    
    // Wenn ein gespeicherter Preis existiert und vom aktuellen regular_price abweicht, wiederherstellen
    if ($saved_regular_price && $saved_regular_price != $product->get_regular_price()) {
        $product->set_regular_price($saved_regular_price);
        $needs_cleanup = true;
    }
    
    // Wenn ein sale_price existiert, löschen
    if ($current_sale_price) {
        $product->set_sale_price('');
        $needs_cleanup = true;
    }
    
    // Wenn ein date_to existiert, löschen
    if ($date_to) {
        $product->set_date_on_sale_to('');
        $product->set_date_on_sale_from('');
        $needs_cleanup = true;
    }
    
    if ($needs_cleanup) {
        // Angebot in Verlauf speichern, bevor es gelöscht wird
        if ($current_sale_price) {
            $history_entry = [
                'regular_price' => $saved_regular_price ?: $product->get_regular_price(),
                'sale_price'    => $current_sale_price,
                'discount'      => get_post_meta($product_id, '_offer_reduction_amount', true),
                'start_date'    => $date_from ? $date_from->date('Y-m-d H:i:s') : '',
                'end_date'      => $date_to   ? $date_to->date('Y-m-d H:i:s')   : '',
                'archived_at'   => current_time('mysql'),
            ];
            $history = get_post_meta($product_id, '_offer_history', true);
            if (!is_array($history)) $history = [];
            $history[] = $history_entry;
            update_post_meta($product_id, '_offer_history', $history);
        }

        $product->save();

        // Angebots-Meta-Daten löschen
        update_post_meta($product_id, '_sale_price', '');
        update_post_meta($product_id, '_sale_price_dates_from', '');
        update_post_meta($product_id, '_sale_price_dates_to', '');
        
        // offer_expired_since setzen, wenn noch nicht gesetzt
        $offer_expired_since = get_post_meta($product_id, '_offer_expired_since', true);
        if (!$offer_expired_since) {
            $offer_expired_since = current_time('timestamp', true);
            update_post_meta($product_id, '_offer_expired_since', $offer_expired_since);
        }
        
        // Umfassendes Cache-Leeren
        wc_delete_product_transients($product_id);
        wp_cache_delete($product_id, 'products');
        wp_cache_flush();
        delete_transient('wc_products_onsale');
        
        wp_send_json_success(array(
            'regular_price' => $product->get_regular_price(),
            'sale_price' => $product->get_sale_price()
        ));
    }
    
    wp_send_json_error('No price restoration needed');
}

function update_product_details() {
    check_ajax_referer('product_list_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $product_id = intval($_POST['product_id']);
    $stock = intval($_POST['stock']);
    $price = floatval($_POST['price']);
    $has_offer = intval($_POST['has_offer']);
    $sale_price = floatval($_POST['sale_price']);
    $show_old_price = intval($_POST['show_old_price']);
    $time_limited = intval($_POST['time_limited']);
    $time_limit_type = sanitize_text_field($_POST['time_limit_type']);
    $time_limit_duration = intval($_POST['time_limit_duration']);
    $time_limit_date = sanitize_text_field($_POST['time_limit_date']);
    $show_end_date = intval($_POST['show_end_date']);
    $offer_start_date = sanitize_text_field($_POST['offer_start_date']);
    
    $product = wc_get_product($product_id);
    if (!$product) {
        wp_send_json_error('Product not found');
    }
    
    // Aktuellen Stock vor dem Update abrufen
    $current_stock = $product->get_stock_quantity();
    $current_stock = $current_stock ? $current_stock : 0;
    
    // Stock setzen
    $product->set_stock_quantity($stock);
    $product->set_manage_stock(true);
    
    // Restock-Erkennung
    $was_out_of_stock = false;
    $was_low_stock = false;
    $was_restocked = false;
    
    // Prüfen, ob ausverkauft war und jetzt gerestocked wurde
    if ($current_stock == 0 && $stock > 0) {
        $was_out_of_stock = true;
        $was_restocked = true;
        update_post_meta($product_id, '_was_out_of_stock', true);
        update_post_meta($product_id, '_out_of_stock_since', current_time('timestamp', true));
    }
    
    // Prüfen, ob low stock war (weniger als 5) und jetzt gerestocked wurde
    if ($current_stock > 0 && $current_stock < 5 && $stock >= 5) {
        $was_low_stock = true;
        $was_restocked = true;
        update_post_meta($product_id, '_was_low_stock', true);
        update_post_meta($product_id, '_low_stock_since', current_time('timestamp', true));
    }
    
    // Prüfen, ob allgemein gerestocked wurde (Stock erhöht)
    if ($stock > $current_stock && $current_stock > 0) {
        $was_restocked = true;
        update_post_meta($product_id, '_was_restocked', true);
        update_post_meta($product_id, '_restocked_since', current_time('timestamp', true));
    }
    
    // Wenn Stock reduziert wurde, Restock-Flags zurücksetzen
    if ($stock < $current_stock) {
        delete_post_meta($product_id, '_was_out_of_stock');
        delete_post_meta($product_id, '_was_low_stock');
        delete_post_meta($product_id, '_was_restocked');
    }
    
    // Angebot setzen
    if ($has_offer) {
        // VOR dem Setzen des Angebots den aktuellen regular_price speichern
        $current_regular_price = $product->get_regular_price();
        
        if ($current_regular_price && $current_regular_price != $price) {
            // Nur speichern, wenn sich der Preis geändert hat
            update_post_meta($product_id, '_original_regular_price', $current_regular_price);
        }
        
        // offer_expired_since zurücksetzen, da ein neues Angebot gesetzt wird
        delete_post_meta($product_id, '_offer_expired_since');
        delete_post_meta($product_id, '_offer_expired_read');
        
        // offer_start_activated zurücksetzen, da ein neues Angebot gesetzt wird
        delete_post_meta($product_id, '_offer_start_activated');
        
        // Rabatttyp und Rabattbetrag berechnen und speichern
        $reduction = $price - $sale_price;
        $percentage = ($reduction / $price) * 100;
        $price_type_toggle = isset($_POST['price_type_toggle']) ? intval($_POST['price_type_toggle']) : 0;
        if ($price_type_toggle === 1) {
            // Prozentualer Rabatt - Anzahl der Dezimalstellen basierend auf der Differenz berechnen
            $decimal_places = 0;
            $fractional_part = abs($percentage) - floor(abs($percentage));
            if ($fractional_part > 0) {
                // Dezimalstellen zählen
                $decimal_str = number_format($fractional_part, 10, '.', '');
                $decimal_str = rtrim($decimal_str, '0');
                $decimal_places = strlen(substr($decimal_str, strpos($decimal_str, '.') + 1));
                // Maximal 2 Dezimalstellen
                $decimal_places = min($decimal_places, 2);
            }
            update_post_meta($product_id, '_offer_reduction_type', 'percent');
            update_post_meta($product_id, '_offer_reduction_amount', '-' . number_format($percentage, $decimal_places) . '%');
        } else {
            // Fester Rabatt in Euro
            update_post_meta($product_id, '_offer_reduction_type', 'fixed');
            update_post_meta($product_id, '_offer_reduction_amount', '-' . number_format($reduction, 2) . '€');
        }
        
        $product->set_regular_price($price);
        $product->set_sale_price($sale_price);
        
        // Zeitliche Begrenzung berechnen
        if ($time_limited) {
            // Startdatum verwenden oder aktuelle Zeit
            if (!empty($offer_start_date)) {
                // TT.MM.JJJJ HH:MM Format parsen
                $date_parts = explode(' ', $offer_start_date);
                if (count($date_parts) === 2) {
                    $date = $date_parts[0];
                    $time = $date_parts[1];
                    $date_parts = explode('.', $date);
                    if (count($date_parts) === 3) {
                        $day = intval($date_parts[0]);
                        $month = intval($date_parts[1]);
                        $year = intval($date_parts[2]);
                        $start_time = strtotime($year . '-' . $month . '-' . $day . ' ' . $time . ' Europe/Berlin');
                    } else {
                        $start_time = current_time('timestamp', true);
                    }
                } else {
                    $start_time = current_time('timestamp', true);
                }
            } else {
                $start_time = current_time('timestamp', true);
            }
            
            if ($time_limit_type === 'days') {
                $end_time = strtotime('+' . $time_limit_duration . ' days', $start_time);
            } elseif ($time_limit_type === 'hours') {
                $end_time = strtotime('+' . $time_limit_duration . ' hours', $start_time);
            } elseif ($time_limit_type === 'minutes') {
                $end_time = strtotime('+' . $time_limit_duration . ' minutes', $start_time);
            } elseif ($time_limit_type === 'date' && !empty($time_limit_date)) {
                // TT.MM.JJJJ HH:MM Format parsen
                $date_parts = explode(' ', $time_limit_date);
                if (count($date_parts) === 2) {
                    $date = $date_parts[0];
                    $time = $date_parts[1];
                    $date_parts = explode('.', $date);
                    if (count($date_parts) === 3) {
                        $day = intval($date_parts[0]);
                        $month = intval($date_parts[1]);
                        $year = intval($date_parts[2]);
                        $end_time = strtotime($year . '-' . $month . '-' . $day . ' ' . $time . ' Europe/Berlin');
                    } else {
                        $end_time = strtotime('+7 days', $start_time); // Fallback
                    }
                } else {
                    // Nur Datum ohne Uhrzeit
                    $date_parts = explode('.', $time_limit_date);
                    if (count($date_parts) === 3) {
                        $day = intval($date_parts[0]);
                        $month = intval($date_parts[1]);
                        $year = intval($date_parts[2]);
                        $end_time = strtotime($year . '-' . $month . '-' . $day . ' 23:59:59 Europe/Berlin');
                    } else {
                        $end_time = strtotime('+7 days', $start_time); // Fallback
                    }
                }
            } else {
                $end_time = strtotime('+7 days', $start_time); // Default: 7 Tage
            }
            
            $product->set_date_on_sale_to($end_time);
            $product->set_date_on_sale_from($start_time);
        } else {
            // Keine zeitliche Begrenzung - Angebot läuft unendlich
            $product->set_date_on_sale_to('');
            $product->set_date_on_sale_from('');
        }
    } else {
        // Kein Angebot - regulären Preis setzen und Angebot löschen
        $product->set_regular_price($price);
        $product->set_sale_price('');
        $product->set_date_on_sale_to('');
        $product->set_date_on_sale_from('');
    }
    
    // WooCommerce Cache leeren
    wc_delete_product_transients($product_id);
    wp_cache_delete($product_id, 'products');
    
    // Meta-Felder speichern
    update_post_meta($product_id, '_show_old_price', $show_old_price);
    update_post_meta($product_id, '_time_limited', $time_limited);
    update_post_meta($product_id, '_time_limit_type', $time_limit_type);
    update_post_meta($product_id, '_time_limit_duration', $time_limit_duration);
    update_post_meta($product_id, '_time_limit_date', $time_limit_date);
    update_post_meta($product_id, '_show_end_date', $show_end_date);
    update_post_meta($product_id, '_offer_start_date', $offer_start_date);
    
    // Regulären Preis IMMER separat speichern (für Wiederherstellung nach Ablauf)
    update_post_meta($product_id, '_saved_regular_price', $price);
    
    // Uhrzeit speichern
    if ($time_limit_type === 'date' && !empty($time_limit_date)) {
        $date_parts = explode(' ', $time_limit_date);
        if (count($date_parts) === 2) {
            update_post_meta($product_id, '_time_limit_time', $date_parts[1]);
            update_post_meta($product_id, '_time_limit_date_only', $date_parts[0]);
        } else {
            update_post_meta($product_id, '_time_limit_date_only', $time_limit_date);
        }
    }
    
    // Tage, Stunden und Minuten separat speichern
    $time_limit_days = 0;
    $time_limit_hours = 0;
    $time_limit_minutes = 0;
    
    if ($time_limit_type === 'days') {
        $time_limit_days = $time_limit_duration;
    } elseif ($time_limit_type === 'hours') {
        // Stunden in Tage und Stunden aufteilen
        $time_limit_days = floor($time_limit_duration / 24);
        $time_limit_hours = $time_limit_duration % 24;
    } elseif ($time_limit_type === 'minutes') {
        // Minuten in Tage, Stunden und Minuten aufteilen
        $total_minutes = $time_limit_duration;
        $time_limit_days = floor($total_minutes / (24 * 60));
        $remaining_minutes = $total_minutes % (24 * 60);
        $time_limit_hours = floor($remaining_minutes / 60);
        $time_limit_minutes = $remaining_minutes % 60;
    }
    
    update_post_meta($product_id, '_time_limit_days', $time_limit_days);
    update_post_meta($product_id, '_time_limit_hours', $time_limit_hours);
    update_post_meta($product_id, '_time_limit_minutes', $time_limit_minutes);
    
    $product->save();
    
    wp_send_json_success(array(
        'product_id' => $product_id,
        'stock' => $stock,
        'price' => $price,
        'has_offer' => $has_offer,
        'sale_price' => $sale_price
    ));
}

// AJAX Handler für Bulk-Aktionen
add_action('wp_ajax_create_bulk_sale', 'create_bulk_sale');

function create_bulk_sale() {
    check_ajax_referer('product_list_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $title = sanitize_text_field($_POST['title']);
    $start_date = sanitize_text_field($_POST['start_date']);
    $end_date = sanitize_text_field($_POST['end_date']);
    $show_end_date = isset($_POST['show_end_date']) ? intval($_POST['show_end_date']) : 0;
    $groups = isset($_POST['groups']) ? json_decode(stripslashes($_POST['groups']), true) : array();
    
    error_log('DEBUG: create_bulk_sale - Gruppen: ' . print_r($groups, true));
    
    // Bulk-Sales aus Optionen laden
    $bulk_sales = get_option('_bulk_sales', array());
    
    // Neue ID generieren
    $sale_id = uniqid('sale_');
    
    // Neue Bulk-Aktion erstellen
    $bulk_sales[$sale_id] = array(
        'id' => $sale_id,
        'title' => $title,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'show_end_date' => $show_end_date,
        'groups' => array()
    );
    
    // Gruppen verarbeiten und Meta-Daten für Produkte setzen
    foreach ($groups as $group) {
        $group_id = $group['id'];
        $bulk_sales[$sale_id]['groups'][$group_id] = array(
            'id' => $group_id,
            'name' => $group['name'],
            'has_offer' => isset($group['has_offer']) ? $group['has_offer'] : false,
            'discount_type' => isset($group['discount_type']) ? $group['discount_type'] : 'fixed',
            'discount_amount' => isset($group['discount_amount']) ? $group['discount_amount'] : '',
            'show_old_price' => isset($group['show_old_price']) ? $group['show_old_price'] : false,
            'products' => isset($group['products']) ? $group['products'] : array()
        );
        
        // Meta-Daten für Produkte setzen
        if (isset($group['products']) && is_array($group['products'])) {
            foreach ($group['products'] as $product_id) {
                update_post_meta($product_id, '_part_of_sale', true);
                update_post_meta($product_id, '_sale_id', $sale_id);
                update_post_meta($product_id, '_sale_group_id', $group_id);
                
                // Wenn Gruppe ein Angebot hat, WooCommerce Angebotsdaten setzen
                if (isset($group['has_offer']) && $group['has_offer']) {
                    $product = wc_get_product($product_id);
                    if ($product) {
                        $regular_price = $product->get_regular_price();
                        $discount_amount = isset($group['discount_amount']) ? floatval($group['discount_amount']) : 0;
                        $discount_type = isset($group['discount_type']) ? $group['discount_type'] : 'fixed';
                        
                        // Angebotspreis berechnen
                        if ($discount_type === 'percent') {
                            $sale_price = $regular_price * (1 - ($discount_amount / 100));
                        } else {
                            $sale_price = $regular_price - $discount_amount;
                        }
                        
                        // Angebotspreis darf nicht negativ sein
                        if ($sale_price < 0) {
                            $sale_price = 0;
                        }
                        
                        // Originalen Preis speichern
                        update_post_meta($product_id, '_original_regular_price', $regular_price);
                        
                        // WooCommerce Angebotsdaten setzen
                        $product->set_sale_price($sale_price);
                        
                        // Start- und Enddatum setzen
                        if ($start_date) {
                            $date_parts = explode(' ', $start_date);
                            if (count($date_parts) === 2) {
                                $date_parts_date = explode('.', $date_parts[0]);
                                if (count($date_parts_date) === 3) {
                                    $day = $date_parts_date[0];
                                    $month = $date_parts_date[1];
                                    $year = $date_parts_date[2];
                                    $time = $date_parts[1];
                                    $start_time = strtotime($year . '-' . $month . '-' . $day . ' ' . $time . ' Europe/Berlin');
                                    $product->set_date_on_sale_from($start_time);
                                }
                            }
                        }
                        
                        if ($end_date) {
                            $date_parts = explode(' ', $end_date);
                            if (count($date_parts) === 2) {
                                $date_parts_date = explode('.', $date_parts[0]);
                                if (count($date_parts_date) === 3) {
                                    $day = $date_parts_date[0];
                                    $month = $date_parts_date[1];
                                    $year = $date_parts_date[2];
                                    $time = $date_parts[1];
                                    $end_time = strtotime($year . '-' . $month . '-' . $day . ' ' . $time . ' Europe/Berlin');
                                    $product->set_date_on_sale_to($end_time);
                                }
                            }
                        }
                        
                        $product->save();
                        
                        // Meta-Daten für Angebot setzen
                        update_post_meta($product_id, '_show_old_price', isset($group['show_old_price']) ? $group['show_old_price'] : false);
                        update_post_meta($product_id, '_time_limited', !empty($end_date));
                        update_post_meta($product_id, '_time_limit_type', 'date');
                        update_post_meta($product_id, '_time_limit_date_only', $end_date);
                        update_post_meta($product_id, '_time_limit_time', isset(explode(' ', $end_date)[1]) ? explode(' ', $end_date)[1] : '');
                        update_post_meta($product_id, '_offer_start_date', $start_date);
                        update_post_meta($product_id, '_has_offer', '1');
                        update_post_meta($product_id, '_offer_start_activated', '1'); // Da Angebot sofort aktiv ist
                        
                        // Rabatttyp speichern
                        update_post_meta($product_id, '_price_type_toggle', $discount_type === 'percent' ? '1' : '0');
                        
                        // Cache leeren
                        wc_delete_product_transients($product_id);
                        wp_cache_delete($product_id, 'products');
                    }
                }
            }
        }
    }
    
    // Speichern
    update_option('_bulk_sales', $bulk_sales);
    
    wp_send_json_success(array('sale_id' => $sale_id));
}

add_action('wp_ajax_get_bulk_sales', 'get_bulk_sales');

function get_bulk_sales() {
    check_ajax_referer('product_list_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $bulk_sales = get_option('_bulk_sales', array());
    wp_send_json_success($bulk_sales);
}

add_action('wp_ajax_get_bulk_sale_details', 'get_bulk_sale_details');

function get_bulk_sale_details() {
    check_ajax_referer('product_list_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $sale_id = sanitize_text_field($_POST['sale_id']);
    $bulk_sales = get_option('_bulk_sales', array());
    
    if (isset($bulk_sales[$sale_id])) {
        wp_send_json_success($bulk_sales[$sale_id]);
    } else {
        wp_send_json_error('Sale not found');
    }
}

add_action('wp_ajax_update_bulk_sale', 'update_bulk_sale');

function update_bulk_sale() {
    check_ajax_referer('product_list_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $sale_id = sanitize_text_field($_POST['sale_id']);
    $title = sanitize_text_field($_POST['title']);
    $start_date = sanitize_text_field($_POST['start_date']);
    $end_date = sanitize_text_field($_POST['end_date']);
    $show_end_date = isset($_POST['show_end_date']) ? intval($_POST['show_end_date']) : 0;
    $groups = isset($_POST['groups']) ? json_decode(stripslashes($_POST['groups']), true) : array();
    
    $bulk_sales = get_option('_bulk_sales', array());
    
    if (isset($bulk_sales[$sale_id])) {
        $bulk_sales[$sale_id]['title'] = $title;
        $bulk_sales[$sale_id]['start_date'] = $start_date;
        $bulk_sales[$sale_id]['end_date'] = $end_date;
        $bulk_sales[$sale_id]['show_end_date'] = $show_end_date;
        $bulk_sales[$sale_id]['groups'] = $groups;
        
        // Alle Produkte aus dem alten Sale entfernen (um Konflikte zu vermeiden)
        foreach ($bulk_sales[$sale_id]['groups'] as $old_group) {
            if (isset($old_group['products'])) {
                foreach ($old_group['products'] as $product_id) {
                    delete_post_meta($product_id, '_part_of_sale');
                    delete_post_meta($product_id, '_sale_id');
                    delete_post_meta($product_id, '_sale_group_id');
                    
                    // WooCommerce Angebotsdaten entfernen
                    $product = wc_get_product($product_id);
                    if ($product) {
                        $saved_regular_price = get_post_meta($product_id, '_original_regular_price', true);
                        if ($saved_regular_price) {
                            $product->set_sale_price('');
                            $product->set_date_on_sale_from('');
                            $product->set_date_on_sale_to('');
                            $product->set_regular_price($saved_regular_price);
                            $product->save();
                        }
                        
                        // Angebots-Meta-Daten entfernen
                        delete_post_meta($product_id, '_has_offer');
                        delete_post_meta($product_id, '_show_old_price');
                        delete_post_meta($product_id, '_time_limited');
                        delete_post_meta($product_id, '_offer_start_date');
                        
                        // Cache leeren
                        wc_delete_product_transients($product_id);
                        wp_cache_delete($product_id, 'products');
                    }
                }
            }
        }
        
        // Neue Gruppen verarbeiten und Meta-Daten für Produkte setzen
        foreach ($groups as $group) {
            $group_id = $group['id'];
            
            // Meta-Daten für Produkte setzen
            if (isset($group['products']) && is_array($group['products'])) {
                foreach ($group['products'] as $product_id) {
                    update_post_meta($product_id, '_part_of_sale', true);
                    update_post_meta($product_id, '_sale_id', $sale_id);
                    update_post_meta($product_id, '_sale_group_id', $group_id);
                    
                    // Wenn Gruppe ein Angebot hat, WooCommerce Angebotsdaten setzen
                    if (isset($group['has_offer']) && $group['has_offer']) {
                        $product = wc_get_product($product_id);
                        if ($product) {
                            $regular_price = $product->get_regular_price();
                            $discount_amount = isset($group['discount_amount']) ? floatval($group['discount_amount']) : 0;
                            $discount_type = isset($group['discount_type']) ? $group['discount_type'] : 'fixed';
                            
                            // Angebotspreis berechnen
                            if ($discount_type === 'percent') {
                                $sale_price = $regular_price * (1 - ($discount_amount / 100));
                            } else {
                                $sale_price = $regular_price - $discount_amount;
                            }
                            
                            // Angebotspreis darf nicht negativ sein
                            if ($sale_price < 0) {
                                $sale_price = 0;
                            }
                            
                            // Originalen Preis speichern
                            update_post_meta($product_id, '_original_regular_price', $regular_price);
                            
                            // WooCommerce Angebotsdaten setzen
                            $product->set_sale_price($sale_price);
                            
                            // Start- und Enddatum setzen
                            if ($start_date) {
                                $date_parts = explode(' ', $start_date);
                                if (count($date_parts) === 2) {
                                    $date_parts_date = explode('.', $date_parts[0]);
                                    if (count($date_parts_date) === 3) {
                                        $day = $date_parts_date[0];
                                        $month = $date_parts_date[1];
                                        $year = $date_parts_date[2];
                                        $time = $date_parts[1];
                                        $start_time = strtotime($year . '-' . $month . '-' . $day . ' ' . $time . ' Europe/Berlin');
                                        $product->set_date_on_sale_from($start_time);
                                    }
                                }
                            }
                            
                            if ($end_date) {
                                $date_parts = explode(' ', $end_date);
                                if (count($date_parts) === 2) {
                                    $date_parts_date = explode('.', $date_parts[0]);
                                    if (count($date_parts_date) === 3) {
                                        $day = $date_parts_date[0];
                                        $month = $date_parts_date[1];
                                        $year = $date_parts_date[2];
                                        $time = $date_parts[1];
                                        $end_time = strtotime($year . '-' . $month . '-' . $day . ' ' . $time . ' Europe/Berlin');
                                        $product->set_date_on_sale_to($end_time);
                                    }
                                }
                            }
                            
                            $product->save();
                            
                            // Meta-Daten für Angebot setzen
                            update_post_meta($product_id, '_show_old_price', isset($group['show_old_price']) ? $group['show_old_price'] : false);
                            update_post_meta($product_id, '_time_limited', !empty($end_date));
                            update_post_meta($product_id, '_time_limit_type', 'date');
                            update_post_meta($product_id, '_time_limit_date_only', $end_date);
                            update_post_meta($product_id, '_time_limit_time', isset(explode(' ', $end_date)[1]) ? explode(' ', $end_date)[1] : '');
                            update_post_meta($product_id, '_offer_start_date', $start_date);
                            update_post_meta($product_id, '_has_offer', '1');
                            update_post_meta($product_id, '_offer_start_activated', '1');
                            
                            // Rabatttyp speichern
                            update_post_meta($product_id, '_price_type_toggle', $discount_type === 'percent' ? '1' : '0');
                            
                            // Cache leeren
                            wc_delete_product_transients($product_id);
                            wp_cache_delete($product_id, 'products');
                        }
                    }
                }
            }
        }
        
        update_option('_bulk_sales', $bulk_sales);
        wp_send_json_success(array('sale_id' => $sale_id));
    } else {
        wp_send_json_error('Sale not found');
    }
}

add_action('wp_ajax_delete_bulk_sale', 'delete_bulk_sale');

function delete_bulk_sale() {
    check_ajax_referer('product_list_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $sale_id = sanitize_text_field($_POST['sale_id']);
    $bulk_sales = get_option('_bulk_sales', array());
    
    if (isset($bulk_sales[$sale_id])) {
        // Produkte aus der Aktion entfernen
        foreach ($bulk_sales[$sale_id]['groups'] as $group) {
            if (isset($group['products'])) {
                foreach ($group['products'] as $product_id) {
                    delete_post_meta($product_id, '_part_of_sale');
                    delete_post_meta($product_id, '_sale_id');
                    delete_post_meta($product_id, '_sale_group_id');
                    
                    // WooCommerce Angebotsdaten entfernen
                    $product = wc_get_product($product_id);
                    if ($product) {
                        $saved_regular_price = get_post_meta($product_id, '_original_regular_price', true);
                        if ($saved_regular_price) {
                            $product->set_sale_price('');
                            $product->set_date_on_sale_from('');
                            $product->set_date_on_sale_to('');
                            $product->set_regular_price($saved_regular_price);
                            $product->save();
                        }
                        
                        // Angebots-Meta-Daten entfernen
                        delete_post_meta($product_id, '_has_offer');
                        delete_post_meta($product_id, '_show_old_price');
                        delete_post_meta($product_id, '_time_limited');
                        delete_post_meta($product_id, '_offer_start_date');
                        
                        // Cache leeren
                        wc_delete_product_transients($product_id);
                        wp_cache_delete($product_id, 'products');
                    }
                }
            }
        }
        
        unset($bulk_sales[$sale_id]);
        update_option('_bulk_sales', $bulk_sales);
        wp_send_json_success();
    } else {
        wp_send_json_error('Sale not found');
    }
}

add_action('wp_ajax_add_product_to_sale_group', 'add_product_to_sale_group');

function add_product_to_sale_group() {
    check_ajax_referer('product_list_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $sale_id = sanitize_text_field($_POST['sale_id']);
    $group_id = sanitize_text_field($_POST['group_id']);
    $product_id = intval($_POST['product_id']);
    
    // Prüfen, ob Produkt bereits Teil einer Aktion ist
    $existing_sale_id = get_post_meta($product_id, '_sale_id', true);
    if ($existing_sale_id) {
        wp_send_json_error('Produkt ist bereits Teil einer Aktion');
    }
    
    // Meta-Daten setzen
    update_post_meta($product_id, '_part_of_sale', true);
    update_post_meta($product_id, '_sale_id', $sale_id);
    update_post_meta($product_id, '_sale_group_id', $group_id);
    
    wp_send_json_success();
}

add_action('wp_ajax_remove_product_from_sale_group', 'remove_product_from_sale_group');

function remove_product_from_sale_group() {
    check_ajax_referer('product_list_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $product_id = intval($_POST['product_id']);
    
    // Meta-Daten löschen
    delete_post_meta($product_id, '_part_of_sale');
    delete_post_meta($product_id, '_sale_id');
    delete_post_meta($product_id, '_sale_group_id');
    
    wp_send_json_success();
}

// Funktion zum automatischen Aktivieren von Angeboten mit Startzeitpunkt
add_action('wp_ajax_activate_offers_with_start_date', 'activate_offers_with_start_date');

function activate_offers_with_start_date() {
    check_ajax_referer('product_list_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $now = current_time('timestamp', true);
    $activated_count = 0;
    
    // Alle Produkte mit Angeboten und Startdatum durchlaufen
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => '_has_offer',
                'value' => '1',
                'compare' => '='
            ),
            array(
                'key' => '_offer_start_date',
                'compare' => 'EXISTS'
            ),
            array(
                'key' => '_offer_start_activated',
                'compare' => 'NOT EXISTS'
            )
        )
    );
    
    $products = get_posts($args);
    
    foreach ($products as $post) {
        $product = wc_get_product($post->ID);
        if (!$product) {
            continue;
        }
        
        $offer_start_date = get_post_meta($product->get_id(), '_offer_start_date', true);
        
        if ($offer_start_date) {
            // TT.MM.JJJJ HH:MM Format parsen
            $date_parts = explode(' ', $offer_start_date);
            if (count($date_parts) === 2) {
                $date = $date_parts[0];
                $time = $date_parts[1];
                $date_parts = explode('.', $date);
                if (count($date_parts) === 3) {
                    $day = intval($date_parts[0]);
                    $month = intval($date_parts[1]);
                    $year = intval($date_parts[2]);
                    $start_time = strtotime($year . '-' . $month . '-' . $day . ' ' . $time . ' Europe/Berlin');
                    
                    // Prüfen, ob Startzeitpunkt erreicht ist
                    if ($start_time && $start_time <= $now) {
                        // Angebot aktivieren
                        $sale_price = $product->get_sale_price();
                        if ($sale_price) {
                            $product->set_date_on_sale_from($start_time);
                            $product->save();
                            
                            // Als aktiviert markieren
                            update_post_meta($product->get_id(), '_offer_start_activated', true);
                            $activated_count++;
                        }
                    }
                }
            }
        }
    }
    
    wp_send_json_success(array('activated_count' => $activated_count));
}

// Redirect für nicht verkäufliche Produkte
add_action('template_redirect', 'redirect_hidden_products');

// Abgelaufene Angebote beim Laden der Produktseite bereinigen
add_action('template_redirect', 'cleanup_expired_sales_on_product_page');

function cleanup_expired_sales_on_product_page() {
    // Nur für Produkt-Seiten
    if (!is_product()) {
        return;
    }
    
    $product_id = get_queried_object_id();
    if (!$product_id) {
        return;
    }
    
    $product = wc_get_product($product_id);
    if (!$product) {
        return;
    }
    
    // Prüfen, ob Angebot abgelaufen ist und bereinigen
    if ($product->is_on_sale()) {
        $date_to = $product->get_date_on_sale_to();
        if ($date_to) {
            $now = current_time('timestamp', true);
            $end_time = $date_to->getTimestamp();
            
            // Wenn das Angebot abgelaufen ist
            if ($end_time < $now) {
                // Zuerst _original_regular_price prüfen (der Preis VOR dem Angebot)
                $saved_regular_price = get_post_meta($product->get_id(), '_original_regular_price', true);
                
                // Falls nicht vorhanden, _saved_regular_price prüfen
                if (!$saved_regular_price) {
                    $saved_regular_price = get_post_meta($product->get_id(), '_saved_regular_price', true);
                }
                
                // Fallback: Falls nichts existiert, den aktuellen regular_price verwenden
                if (!$saved_regular_price) {
                    $saved_regular_price = $product->get_regular_price();
                }
                
                if ($saved_regular_price) {
                    // Angebot im WooCommerce-Produkt löschen
                    $product->set_sale_price('');
                    $product->set_date_on_sale_to('');
                    $product->set_date_on_sale_from('');
                    $product->set_regular_price($saved_regular_price);
                    $product->save();
                    
                    error_log('DEBUG: Nachher - regular_price=' . $product->get_regular_price() . ', sale_price=' . $product->get_sale_price());
                    
                    // Angebots-Meta-Daten löschen
                    update_post_meta($product->get_id(), '_sale_price', '');
                    update_post_meta($product->get_id(), '_sale_price_dates_from', '');
                    update_post_meta($product->get_id(), '_sale_price_dates_to', '');
                    
                    // Umfassendes Cache-Leeren
                    wc_delete_product_transients($product->get_id());
                    wp_cache_delete($product->get_id(), 'products');
                    wp_cache_flush();
                    delete_transient('wc_products_onsale');
                }
            }
        }
    }
}

// "Vorher X jetzt Y" Anzeige auf Produktseite
add_filter('woocommerce_get_price_html', 'custom_price_html', 10, 2);

function custom_price_html($price, $product) {
    // Prüfen, ob Angebot abgelaufen ist und bereinigen
    if ($product->is_on_sale()) {
        $date_to = $product->get_date_on_sale_to();
        if ($date_to) {
            $now = current_time('timestamp', true);
            $end_time = $date_to->getTimestamp();
            
            // Wenn das Angebot abgelaufen ist
            if ($end_time < $now) {
                // Zuerst _original_regular_price prüfen (der Preis VOR dem Angebot)
                $saved_regular_price = get_post_meta($product->get_id(), '_original_regular_price', true);
                
                // Falls nicht vorhanden, _saved_regular_price prüfen
                if (!$saved_regular_price) {
                    $saved_regular_price = get_post_meta($product->get_id(), '_saved_regular_price', true);
                }
                
                // Fallback: Falls nichts existiert, den aktuellen regular_price verwenden
                if (!$saved_regular_price) {
                    $saved_regular_price = $product->get_regular_price();
                }
                
                if ($saved_regular_price) {
                    // Angebot im WooCommerce-Produkt löschen
                    $product->set_sale_price('');
                    $product->set_date_on_sale_to('');
                    $product->set_date_on_sale_from('');
                    $product->set_regular_price($saved_regular_price);
                    $product->save();
                    
                    // Angebots-Meta-Daten löschen
                    update_post_meta($product->get_id(), '_sale_price', '');
                    update_post_meta($product->get_id(), '_sale_price_dates_from', '');
                    update_post_meta($product->get_id(), '_sale_price_dates_to', '');
                    
                    // Umfassendes Cache-Leeren
                    wc_delete_product_transients($product->get_id());
                    wp_cache_delete($product->get_id(), 'products');
                    wp_cache_flush();
                    delete_transient('wc_products_onsale');
                    
                    // Produkt neu laden
                    $product = wc_get_product($product->get_id());
                }
            }
        }
    }
    
    $show_old_price = get_post_meta($product->get_id(), '_show_old_price', true);
    
    if ($product->is_on_sale() && $show_old_price) {
        $regular_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();
        
        $price = '<del>' . wc_price($regular_price) . '</del> <ins>' . wc_price($sale_price) . '</ins>';
    }
    
    return $price;
}

// Produktnamen durchstreichen, wenn nicht zum Verkauf ausstehend (nur für Admins)
add_action('wp_footer', 'add_strikethrough_script_for_not_sellable_products');

function add_strikethrough_script_for_not_sellable_products() {
    if (!is_product() || !current_user_can('manage_options')) {
        return;
    }
    
    global $product;
    if (!$product) {
        return;
    }
    
    $product_id = $product->get_id();
    $sellable = get_post_meta($product_id, 'sellable', true);
    error_log('DEBUG: Produkt ID: ' . $product_id);
    error_log('DEBUG: sellable Wert: ' . var_export($sellable, true));
    
    // Nur wenn sellable explizit auf '0' gesetzt ist, ist das Produkt nicht zum Verkauf ausstehend
    if ($sellable === '0') {
        error_log('DEBUG: JavaScript wird hinzugefügt');
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var titleElements = document.querySelectorAll('.product_title, .woocommerce-product-title, .entry-title, h1.product_title');
                titleElements.forEach(function(element) {
                    element.style.textDecoration = 'line-through';
                    element.style.color = '#999';
                });
            });
        </script>
        <?php
    } else {
        error_log('DEBUG: JavaScript wird nicht hinzugefügt, sellable ist: ' . $sellable);
    }
}

// Angebotsende auf Produktseite anzeigen
add_action('woocommerce_single_product_summary', 'display_offer_end_date', 25);

function display_offer_end_date() {
    global $product;
    
    if (!$product) {
        return;
    }
    
    // Prüfen, ob Angebot abgelaufen ist und bereinigen
    if ($product->is_on_sale()) {
        $date_to = $product->get_date_on_sale_to();
        if ($date_to) {
            $now = current_time('timestamp', true);
            $end_time = $date_to->getTimestamp();
            
            // Wenn das Angebot abgelaufen ist
            if ($end_time < $now) {
                // Gespeicherten Original-Preis abrufen
                $saved_regular_price = get_post_meta($product->get_id(), '_original_regular_price', true);
                if (!$saved_regular_price) {
                    $saved_regular_price = get_post_meta($product->get_id(), '_saved_regular_price', true);
                }
                
                if (!$saved_regular_price) {
                    // Fallback: aktuellen regular_price verwenden
                    $saved_regular_price = $product->get_regular_price();
                }
                
                if ($saved_regular_price) {
                    // Angebot im WooCommerce-Produkt löschen
                    $product->set_sale_price('');
                    $product->set_date_on_sale_to('');
                    $product->set_date_on_sale_from('');
                    $product->set_regular_price($saved_regular_price);
                    $product->save();
                    
                    // Angebots-Meta-Daten löschen
                    update_post_meta($product->get_id(), '_sale_price', '');
                    update_post_meta($product->get_id(), '_sale_price_dates_from', '');
                    update_post_meta($product->get_id(), '_sale_price_dates_to', '');
                    
                    // Umfassendes Cache-Leeren
                    wc_delete_product_transients($product->get_id());
                    wp_cache_delete($product->get_id(), 'products');
                    wp_cache_flush();
                    delete_transient('wc_products_onsale');
                    
                    // Produkt neu laden
                    $product = wc_get_product($product->get_id());
                }
            }
        }
    }
    
    if (!$product->is_on_sale()) {
        return;
    }
    
    $show_end_date = get_post_meta($product->get_id(), '_show_end_date', true);
    $time_limited = get_post_meta($product->get_id(), '_time_limited', true);
    
    if ($show_end_date && $time_limited) {
        $date_to = $product->get_date_on_sale_to();
        if ($date_to) {
            $now = current_time('timestamp', true);
            $end_time = $date_to->getTimestamp();
            $diff = $end_time - $now;
            
            if ($diff > 0) {
                $days = floor($diff / (24 * 60 * 60));
                
                if ($days >= 1) {
                    // Mehr als 1 Tag: Datum anzeigen (in lokale Zeit konvertieren)
                    $end_date_local = get_date_from_gmt(date('Y-m-d H:i:s', $end_time), 'd.m.Y H:i');
                    echo '<div class="offer-end-display" style="margin-top: 10px; color: #666; font-size: 14px;">';
                    echo '<span style="background: #e74c3c; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Angebot endet am: ' . $end_date_local . '</span>';
                    echo '</div>';
                } else {
                    // Weniger als 1 Tag: Countdown in HH:MM:SS
                    $hours = floor($diff / (60 * 60));
                    $minutes = floor(($diff % (60 * 60)) / 60);
                    $seconds = $diff % 60;
                    
                    $h = str_pad($hours, 2, '0', STR_PAD_LEFT);
                    $m = str_pad($minutes, 2, '0', STR_PAD_LEFT);
                    $s = str_pad($seconds, 2, '0', STR_PAD_LEFT);
                    
                    echo '<div class="offer-end-display" style="margin-top: 10px; color: #666; font-size: 14px;">';
                    echo '<span class="countdown" data-countdown="' . $end_time . '" style="background: #e74c3c; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Angebot endet in ' . $h . ':' . $m . ':' . $s . '</span>';
                    echo '</div>';
                }
            }
        }
    }
}

// JavaScript für Countdown auf Produktseite
add_action('wp_footer', 'add_countdown_script');

function add_countdown_script() {
    if (!is_product()) {
        return;
    }
    
    global $product;
    if (!$product) return;
    
    $product_id = $product->get_id();
    ?>
    <script>
    let isReloading = false;
    
    function updateCountdown() {
        const countdown = document.querySelector('.countdown');
        if (!countdown) return;
        
        const dateTo = parseInt(countdown.dataset.countdown);
        const now = Math.floor(Date.now() / 1000);
        const diff = dateTo - now;
        
        if (diff > 0) {
            const hours = Math.floor(diff / (60 * 60));
            const minutes = Math.floor((diff % (60 * 60)) / 60);
            const seconds = diff % 60;
            
            const h = String(hours).padStart(2, '0');
            const m = String(minutes).padStart(2, '0');
            const s = String(seconds).padStart(2, '0');
            
            countdown.textContent = 'Angebot endet in ' + h + ':' + m + ':' + s;
        } else {
            countdown.textContent = 'Angebot abgelaufen';
            countdown.style.color = '#e74c3c';
            // Zuerst AJAX-Call für Cleanup, dann Seite neu laden (nur einmal)
            if (!isReloading) {
                isReloading = true;
                
                // AJAX-Call zum Bereinigen des abgelaufenen Angebots
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=cleanup_expired_sale&product_id=<?php echo $product_id; ?>'
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Cleanup result:', data);
                    // Seite neu laden nach erfolgreichem Cleanup
                    location.reload();
                })
                .catch(error => {
                    console.error('Cleanup error:', error);
                    // Trotzdem neu laden bei Fehler
                    location.reload();
                });
            }
        }
    }
    
    setInterval(updateCountdown, 1000);
    updateCountdown();
    </script>
    <?php
}

function redirect_hidden_products() {
    // Nur für Produkt-Seiten und nicht für Admins
    if (!is_product() || is_admin() || current_user_can('manage_options')) {
        return;
    }
    
    global $product;
    if (!$product || !is_object($product)) {
        $product_id = get_queried_object_id();
        $product = wc_get_product($product_id);
    }
    
    if ($product && is_object($product)) {
        $visibility = $product->get_catalog_visibility();
        
        if ($visibility === 'hidden') {
            // Redirect zur "Nicht verfügbar" Seite
            $not_available_page = get_page_by_path('nicht-verfuegbar');
            if ($not_available_page) {
                wp_redirect(get_permalink($not_available_page->ID));
                exit;
            }
        }
    }
}

// ══════════════════════════════════════════════════════════════════
// WARTUNGSMODUS
// ══════════════════════════════════════════════════════════════════

// ── Wartungsseite für Nicht-Admins anzeigen ───────────────────────
add_action('template_redirect', function () {
    if (!get_option('_pa_maintenance_active')) return;
    if (current_user_can('manage_options')) return;
    if (wp_doing_ajax()) return;

    http_response_code(503);
    header('Retry-After: 3600');
    header('Content-Type: text/html; charset=utf-8');

    $site_name = get_bloginfo('name');
    $logo_url  = esc_url(content_url('uploads/2022/01/Logo-Plantaphilia-1.svg'));

    echo '<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Wartung – ' . esc_html($site_name) . '</title>
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background:#f4f5f7;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:24px}
  .card{background:#fff;border-radius:8px;box-shadow:0 2px 16px rgba(0,0,0,.10);max-width:480px;width:100%;padding:48px 40px;text-align:center}
  .logo{max-height:56px;margin-bottom:32px}
  .icon{font-size:48px;margin-bottom:20px}
  h1{font-size:22px;font-weight:700;color:#1a1a2e;margin-bottom:12px}
  p{font-size:15px;color:#555;line-height:1.6;margin-bottom:8px}
  .badge{display:inline-block;margin-top:24px;padding:6px 16px;background:#f4f5f7;border-radius:20px;font-size:12px;color:#888;letter-spacing:.4px}
  .dot{width:8px;height:8px;background:#f0ad4e;border-radius:50%;display:inline-block;margin-right:6px;animation:pulse 1.6s ease-in-out infinite}
  @keyframes pulse{0%,100%{opacity:1}50%{opacity:.3}}
</style>
</head>
<body>
<div class="card">
  <img src="' . $logo_url . '" alt="' . esc_attr($site_name) . '" class="logo">
  <div class="icon">🔧</div>
  <h1>Kurze Wartungspause</h1>
  <p>Wir arbeiten gerade an Verbesserungen für dich.<br>Der Shop ist in Kürze wieder erreichbar.</p>
  <p style="margin-top:12px;font-size:13px;color:#888;">Bei dringenden Fragen: <a href="mailto:kontakt@plantaphilia.eu" style="color:#5a8a5e">kontakt@plantaphilia.eu</a></p>
  <div class="badge"><span class="dot"></span>Maintenance Mode aktiv</div>
</div>
</body>
</html>';
    exit;
}, 1);

// ── AJAX: Wartungsmodus umschalten ────────────────────────────────
add_action('wp_ajax_pa_toggle_maintenance', 'pa_toggle_maintenance_ajax');

function pa_toggle_maintenance_ajax() {
    check_ajax_referer('pa_maintenance_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized');

    $active = !get_option('_pa_maintenance_active');
    update_option('_pa_maintenance_active', $active, false);

    wp_send_json_success(['active' => $active]);
}

// ── Admin-Button: Floating Toggle im Frontend ─────────────────────
add_action('wp_footer', function () {
    if (!current_user_can('manage_options')) return;

    $active = (bool) get_option('_pa_maintenance_active');
    $nonce  = wp_create_nonce('pa_maintenance_nonce');
    $ajax   = admin_url('admin-ajax.php');
    ?>
    <style>
    #pa-maintenance-btn {
        position: fixed;
        bottom: 24px;
        left: 24px;
        z-index: 99999;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border: none;
        border-radius: 24px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 3px 12px rgba(0,0,0,.20);
        transition: background .2s, transform .15s;
        outline: none;
        letter-spacing: .2px;
    }
    #pa-maintenance-btn.inactive { background: #1a1a2e; color: #fff; }
    #pa-maintenance-btn.active   { background: #e8530a; color: #fff; }
    #pa-maintenance-btn:hover    { transform: translateY(-2px); }
    #pa-maintenance-btn .pa-mb-dot {
        width: 8px; height: 8px; border-radius: 50%;
        flex-shrink: 0;
    }
    #pa-maintenance-btn.inactive .pa-mb-dot { background: #aaa; }
    #pa-maintenance-btn.active   .pa-mb-dot {
        background: #fff;
        animation: pa-pulse 1.4s ease-in-out infinite;
    }
    @keyframes pa-pulse { 0%,100%{opacity:1} 50%{opacity:.3} }
    </style>

    <button id="pa-maintenance-btn"
            class="<?php echo $active ? 'active' : 'inactive'; ?>"
            title="Wartungsmodus umschalten">
        <span class="pa-mb-dot"></span>
        <span id="pa-mb-label"><?php echo $active ? 'Wartung AN' : 'Wartung AUS'; ?></span>
    </button>

    <script>
    (function () {
        var btn   = document.getElementById('pa-maintenance-btn');
        var label = document.getElementById('pa-mb-label');
        if (!btn) return;

        btn.addEventListener('click', function () {
            var isActive = btn.classList.contains('active');
            var msg = isActive
                ? 'Wartungsmodus deaktivieren?\nBesucher sehen wieder die normale Seite.'
                : 'Wartungsmodus aktivieren?\nBesucher sehen eine Wartungsseite.';
            if (!confirm(msg)) return;

            btn.disabled = true;
            label.textContent = '…';

            var body = new URLSearchParams({
                action: 'pa_toggle_maintenance',
                nonce:  '<?php echo esc_js($nonce); ?>'
            });

            fetch('<?php echo esc_js($ajax); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: body.toString()
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    var on = data.data.active;
                    btn.classList.toggle('active', on);
                    btn.classList.toggle('inactive', !on);
                    label.textContent = on ? 'Wartung AN' : 'Wartung AUS';
                } else {
                    label.textContent = 'Fehler';
                }
                btn.disabled = false;
            })
            .catch(function () {
                label.textContent = 'Fehler';
                btn.disabled = false;
            });
        });
    }());
    </script>
    <?php
}, 100);

// Admin-Buttons nach Abmelden-Link im Footer einfügen
add_action('wp_footer', 'add_admin_buttons_after_logout', 999);

function add_admin_buttons_after_logout() {
    // Nur für Admins
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $add_product_url = home_url('/neues-produkt/');
    $product_list_url = home_url('/produkt-liste/');
    
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Suche nach Abmelden-Link im Footer
        var logoutLinks = document.querySelectorAll('a[href*="logout"]');
        logoutLinks.forEach(function(link) {
            if (link.textContent.includes('Abmelden') || link.textContent.includes('Logout')) {
                // Finde das w-iconbox Element
                var iconbox = link.closest('.w-iconbox');
                if (iconbox) {
                    // Erstelle w-iconbox für Produkt hinzufügen
                    var addIconbox = document.createElement('div');
                    addIconbox.className = 'w-iconbox iconpos_left style_default color_contrast align_left no_text';
                    
                    addIconbox.innerHTML = `
                        <a href='<?php echo esc_url($add_product_url); ?>' class='w-iconbox-link'>
                            <div class='w-iconbox-icon' style='font-size:1.2rem;'>
                                <i class='far fa-plus'></i>
                            </div>
                        </a>
                        <div class='w-iconbox-meta'>
                            <a href='<?php echo esc_url($add_product_url); ?>' class='w-iconbox-link'>
                                <div class='w-iconbox-title'>Produkt hinzufügen</div>
                            </a>
                        </div>
                    `;
                    
                    // Erstelle w-iconbox für Produktliste
                    var listIconbox = document.createElement('div');
                    listIconbox.className = 'w-iconbox iconpos_left style_default color_contrast align_left no_text';
                    
                    listIconbox.innerHTML = `
                        <a href='<?php echo esc_url($product_list_url); ?>' class='w-iconbox-link'>
                            <div class='w-iconbox-icon' style='font-size:1.2rem;'>
                                <i class='far fa-list'></i>
                            </div>
                        </a>
                        <div class='w-iconbox-meta'>
                            <a href='<?php echo esc_url($product_list_url); ?>' class='w-iconbox-link'>
                                <div class='w-iconbox-title'>Produktliste</div>
                            </a>
                        </div>
                    `;
                    
                    // Separatoren hinzufügen
                    var separator1 = document.createElement('div');
                    separator1.className = 'w-separator size_custom';
                    separator1.style.height = '5px';
                    
                    var separator2 = document.createElement('div');
                    separator2.className = 'w-separator size_custom';
                    separator2.style.height = '5px';
                    
                    // Nach dem Abmelden-iconbox einfügen
                    iconbox.parentNode.insertBefore(separator1, iconbox.nextSibling);
                    separator1.parentNode.insertBefore(addIconbox, separator1.nextSibling);
                    addIconbox.parentNode.insertBefore(separator2, addIconbox.nextSibling);
                    separator2.parentNode.insertBefore(listIconbox, separator2.nextSibling);
                    
                    // Erstelle w-iconbox für Bestellungen
                    var ordersIconbox = document.createElement('div');
                    ordersIconbox.className = 'w-iconbox iconpos_left style_default color_contrast align_left no_text';
                    
                    ordersIconbox.innerHTML = `
                        <a href='<?php echo esc_url(home_url('/bestellungen/')); ?>' class='w-iconbox-link'>
                            <div class='w-iconbox-icon' style='font-size:1.2rem;'>
                                <i class='far fa-shopping-cart'></i>
                            </div>
                        </a>
                        <div class='w-iconbox-meta'>
                            <a href='<?php echo esc_url(home_url('/bestellungen/')); ?>' class='w-iconbox-link'>
                                <div class='w-iconbox-title'>Bestellungen</div>
                            </a>
                        </div>
                    `;
                    
                    var separator3 = document.createElement('div');
                    separator3.className = 'w-separator size_custom';
                    separator3.style.height = '5px';
                    
                    // Nach der Produktliste einfügen
                    listIconbox.parentNode.insertBefore(separator3, listIconbox.nextSibling);
                    separator3.parentNode.insertBefore(ordersIconbox, separator3.nextSibling);
                }
            }
        });
    });
    </script>
    <?php
}

// Hook: WooCommerce Status auf processing zurücksetzen, wenn Shipment auf shipped gesetzt wird
// Höhere Priorität (999) um sicherzustellen, dass er NACH Germanized ausgeführt wird
add_action('woocommerce_gzd_shipment_status_changed', 'reset_order_status_after_shipment', 999, 4);

function reset_order_status_after_shipment($shipment_id, $old_status, $new_status, $shipment) {
    if ($new_status === 'shipped') {
        $order_id = $shipment->get_order_id();
        $order = wc_get_order($order_id);
        
        if ($order) {
            $current_status = $order->get_status();
            if ($current_status === 'completed') {
                $order->update_status('processing');
            }
        }
    }
}

// ── Bestellungsübersicht: Hilfsfunktionen ────────────────────────────────────

function orders_check_shipments_plugin() {
    if (class_exists('WooCommerce_Germanized_Shipments') || function_exists('wc_gzd_get_shipment') || defined('WC_GZD_SHIPMENT_VERSION')) return true;
    if (function_exists('is_plugin_active') && (is_plugin_active('woocommerce-germanized/woocommerce-germanized.php') || is_plugin_active('woocommerce-germanized-pro/woocommerce-germanized-pro.php'))) return true;
    return false;
}

function orders_format_address($order, $type) {
    if ($type === 'shipping') {
        $postcode = $order->get_shipping_postcode();
        $city     = $order->get_shipping_city();
        $addr1    = $order->get_shipping_address_1();
        $addr2    = $order->get_shipping_address_2();
        if (!$city && !$addr1) return orders_format_address($order, 'billing');
    } else {
        $postcode = $order->get_billing_postcode();
        $city     = $order->get_billing_city();
        $addr1    = $order->get_billing_address_1();
        $addr2    = $order->get_billing_address_2();
    }
    $parts = array();
    if ($postcode || $city) $parts[] = trim($postcode . ' ' . $city);
    if ($addr1) $parts[] = $addr1;
    if ($addr2) $parts[] = $addr2;
    $phone = $order->get_billing_phone();
    if ($phone) $parts[] = '📞 ' . $phone;
    return implode(', ', array_filter($parts));
}

function orders_classify_orders($raw_orders, $has_shipments) {
    $buckets = array('pending' => array(), 'processing' => array(), 'shipped' => array(), 'completed' => array());

    foreach ($raw_orders as $order) {
        if (method_exists($order, 'get_type') && $order->get_type() === 'shop_order_refund') continue;
        $status   = $order->get_status();
        $order_id = $order->get_id();

        $items = array();
        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            $sku     = $product ? $product->get_sku() : '';
            $items[] = array(
                'quantity' => $item->get_quantity(),
                'sku'      => $sku,
                'name'     => $item->get_name(),
                'total'    => trim(wp_strip_all_tags(html_entity_decode($order->get_formatted_line_subtotal($item), ENT_QUOTES | ENT_HTML5, 'UTF-8'))),
            );
        }

        $taxes = array();
        foreach ($order->get_tax_totals() as $tax) {
            $taxes[] = array(
                'label'  => $tax->label,
                'amount' => number_format((float) $tax->amount, 2, ',', '.') . ' €',
            );
        }

        $date_created = $order->get_date_created() ? $order->get_date_created()->date_i18n('d.m.Y H:i') : '';

        $data = array(
            'id'               => $order_id,
            'number'           => $order->get_order_number(),
            'date_created'     => $date_created,
            'total'            => number_format((float) $order->get_total(), 2, ',', '.') . ' €',
            'total_raw'        => $order->get_total(),
            'taxes'            => $taxes,
            'user_id'          => $order->get_user_id() ?: 0,
            'user_name'        => trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()),
            'comment'          => $order->get_customer_note(),
            'items'            => $items,
            'billing_address'  => orders_format_address($order, 'billing'),
            'shipping_address' => orders_format_address($order, 'shipping'),
            'display_date'     => $date_created,
        );

        if ($status === 'pending' || $status === 'on-hold') {
            $buckets['pending'][] = $data;
        } elseif ($status === 'processing') {
            $is_shipped   = false;
            $shipped_date = '';

            if ($has_shipments && function_exists('wc_gzd_get_shipments_by_order')) {
                foreach (wc_gzd_get_shipments_by_order($order_id) as $shipment) {
                    if (in_array($shipment->get_status(), array('shipped', 'delivered'))) {
                        $is_shipped   = true;
                        $shipped_date = $shipment->get_date_sent() ? $shipment->get_date_sent()->date_i18n('d.m.Y H:i') : $date_created;
                        break;
                    }
                }
            }
            if (!$is_shipped) {
                $is_shipped = get_post_meta($order_id, '_is_shipped', true) === '1';
                $sm = get_post_meta($order_id, '_shipped_date', true);
                if ($sm) $shipped_date = date('d.m.Y H:i', strtotime($sm));
            }

            if ($is_shipped) {
                $data['display_date'] = $shipped_date ?: $date_created;
                $buckets['shipped'][] = $data;
            } else {
                $proc = get_post_meta($order_id, '_processing_date', true);
                $data['display_date'] = $proc ? date('d.m.Y H:i', strtotime($proc)) : $date_created;
                $buckets['processing'][] = $data;
            }
        } elseif ($status === 'completed') {
            $buckets['completed'][] = $data;
        }
    }

    return $buckets;
}

function orders_group_by_user_address($orders, $address_type) {
    usort($orders, function ($a, $b) {
        $c = strcasecmp($a['user_name'], $b['user_name']);
        return $c !== 0 ? $c : strcmp($a['display_date'], $b['display_date']);
    });

    $users   = array();
    $u_index = array();

    foreach ($orders as $order) {
        $u_key = $order['user_id'] ? 'u_' . $order['user_id'] : 'g_' . md5($order['user_name']);

        if (!isset($u_index[$u_key])) {
            $u_index[$u_key] = count($users);
            $users[] = array('user_name' => $order['user_name'], 'user_id' => $order['user_id'], 'address_groups' => array(), 'a_idx' => array());
        }

        $ui    = $u_index[$u_key];
        $addr  = $address_type === 'shipping' ? $order['shipping_address'] : $order['billing_address'];
        $a_key = md5($addr);

        if (!isset($users[$ui]['a_idx'][$a_key])) {
            $users[$ui]['a_idx'][$a_key] = count($users[$ui]['address_groups']);
            $users[$ui]['address_groups'][] = array('address' => $addr, 'orders' => array());
        }

        $users[$ui]['address_groups'][$users[$ui]['a_idx'][$a_key]]['orders'][] = $order;
    }

    foreach ($users as &$u) unset($u['a_idx']);
    return $users;
}

// ── Bestellungsübersicht AJAX ─────────────────────────────────────────────────
add_action('wp_ajax_get_orders_overview', 'get_orders_overview');

function get_orders_overview() {
    check_ajax_referer('orders_overview_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized');

    $has_shipments = orders_check_shipments_plugin();

    $raw = wc_get_orders(array(
        'status'  => array('pending', 'processing', 'on-hold', 'completed'),
        'limit'   => -1,
        'orderby' => 'date',
        'order'   => 'ASC',
    ));

    $b = orders_classify_orders($raw, $has_shipments);

    wp_send_json_success(array(
        'pending'    => orders_group_by_user_address($b['pending'],    'billing'),
        'processing' => orders_group_by_user_address($b['processing'], 'shipping'),
        'shipped'    => orders_group_by_user_address($b['shipped'],    'shipping'),
        'completed'  => orders_group_by_user_address($b['completed'],  'billing'),
    ));
}

// ── Bestellungen CSV-Export ───────────────────────────────────────────────────
add_action('wp_ajax_export_orders_csv', 'export_orders_csv_ajax');

function export_orders_csv_ajax() {
    check_ajax_referer('orders_overview_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized');

    $cols_raw = isset($_POST['columns']) ? (array) $_POST['columns'] : array();
    $allowed  = array('pending', 'processing', 'shipped', 'completed');
    $columns  = array_filter(array_map('sanitize_text_field', $cols_raw), function ($c) use ($allowed) {
        return in_array($c, $allowed);
    });

    if (empty($columns)) wp_send_json_error('Keine Spalten ausgewählt');

    $has_shipments = orders_check_shipments_plugin();

    $raw = wc_get_orders(array(
        'status'  => array('pending', 'processing', 'on-hold', 'completed'),
        'limit'   => -1,
        'orderby' => 'date',
        'order'   => 'DESC',
    ));

    $buckets = orders_classify_orders($raw, $has_shipments);

    $col_labels = array(
        'pending'    => 'In Wartestellung',
        'processing' => 'In Bearbeitung',
        'shipped'    => 'Versandt',
        'completed'  => 'Abgeschlossen',
    );

    $rows = array(array('Bestellnummer', 'Datum', 'Status', 'Kunde', 'PLZ', 'Ort', 'Straße', 'Artikel', 'Gesamtbetrag', 'Kommentar'));

    foreach ($columns as $col) {
        foreach ($buckets[$col] as $o) {
            $items_str = implode('; ', array_map(function ($item) {
                return $item['sku']
                    ? $item['quantity'] . 'x ' . $item['sku'] . '|' . $item['name']
                    : $item['quantity'] . 'x ' . $item['name'];
            }, $o['items']));

            $addr_parts    = explode(', ', $o['billing_address']);
            $postcode_city = isset($addr_parts[0]) ? $addr_parts[0] : '';
            $street        = isset($addr_parts[1]) ? $addr_parts[1] : '';
            $pc_parts      = explode(' ', $postcode_city, 2);
            $postcode      = isset($pc_parts[0]) ? $pc_parts[0] : '';
            $city          = isset($pc_parts[1]) ? $pc_parts[1] : '';

            $rows[] = array(
                '#' . $o['number'],
                $o['display_date'],
                $col_labels[$col],
                $o['user_name'],
                $postcode,
                $city,
                $street,
                $items_str,
                number_format((float) $o['total_raw'], 2, ',', '.') . ' €',
                $o['comment'],
            );
        }
    }

    $csv = "\xEF\xBB\xBF"; // UTF-8 BOM for Excel
    foreach ($rows as $row) {
        $escaped = array_map(function ($v) {
            return '"' . str_replace('"', '""', $v ?? '') . '"';
        }, $row);
        $csv .= implode(';', $escaped) . "\r\n";
    }

    wp_send_json_success(array('csv' => $csv, 'count' => count($rows) - 1));
}

// AJAX Handler für Status-Update
add_action('wp_ajax_update_order_status', 'update_order_status_ajax');

function update_order_status_ajax() {
    ob_start();
    check_ajax_referer('orders_overview_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        ob_end_clean();
        wp_send_json_error('Unauthorized');
    }
    
    $order_id = intval($_POST['order_id']);
    $new_status = sanitize_text_field($_POST['status']);
    
    $order = wc_get_order($order_id);
    if (!$order) {
        ob_end_clean();
        wp_send_json_error('Bestellung nicht gefunden');
    }
    
    // Status-Mapping
    $wc_status = '';
    switch ($new_status) {
        case 'cancelled':
            $wc_status = 'cancelled';
            break;
        case 'pending':
            $wc_status = 'pending';
            break;
        case 'processing':
            // Wenn von shipped zurück zu processing, Shipment-Status zurücksetzen
            if (function_exists('wc_gzd_get_shipments_by_order')) {
                $shipments = wc_gzd_get_shipments_by_order($order_id);
                foreach ($shipments as $shipment) {
                    if ($shipment->get_status() === 'shipped' || $shipment->get_status() === 'delivered') {
                        $shipment->update_status('dispatched');
                        $shipment->save();
                    }
                }
            }
            // Meta-Daten zurücksetzen
            delete_post_meta($order_id, '_is_shipped');
            delete_post_meta($order_id, '_shipped_date');
            if (!get_post_meta($order_id, '_processing_date', true)) {
                update_post_meta($order_id, '_processing_date', current_time('mysql'));
            }
            $wc_status = 'processing';
            break;
        case 'shipped':
            // Immer eigene Meta-Felder setzen (unabhängig von Germanized)
            update_post_meta($order_id, '_is_shipped', '1');
            update_post_meta($order_id, '_shipped_date', current_time('mysql'));
            // Germanized-Integration optional – Fehler werden still ignoriert
            try {
                if (function_exists('wc_gzd_get_shipments_by_order')) {
                    $shipments = wc_gzd_get_shipments_by_order($order_id);
                    if (empty($shipments)) {
                        if (function_exists('wc_gzd_create_shipment')) {
                            $shipment = wc_gzd_create_shipment($order);
                            if ($shipment) {
                                $shipment->update_status('shipped');
                                $shipment->save();
                            }
                        }
                    } else {
                        $shipment = reset($shipments);
                        $shipment->update_status('shipped');
                        $shipment->save();
                    }
                }
            } catch (\Throwable $e) {
                error_log('Germanized shipment error (shipped): ' . $e->getMessage());
            }
            $wc_status = 'processing';
            break;
        case 'completed':
            $wc_status = 'completed';
            
            // Shipment auf delivered setzen, wenn Germanized aktiv ist
            $has_shipments = false;
            if (class_exists('WooCommerce_Germanized_Shipments')) {
                $has_shipments = true;
            } elseif (function_exists('wc_gzd_get_shipment')) {
                $has_shipments = true;
            } elseif (defined('WC_GZD_SHIPMENT_VERSION')) {
                $has_shipments = true;
            }
            
            if (is_plugin_active('woocommerce-germanized/woocommerce-germanized.php') || is_plugin_active('woocommerce-germanized-pro/woocommerce-germanized-pro.php')) {
                $has_shipments = true;
            }
            
            if ($has_shipments && function_exists('wc_gzd_get_shipments_by_order')) {
                $shipments = wc_gzd_get_shipments_by_order($order_id);
                foreach ($shipments as $shipment) {
                    if ($shipment->get_status() === 'shipped') {
                        $shipment->update_status('delivered');
                        $shipment->save();
                    }
                }
            }
            break;
        default:
            ob_end_clean();
            wp_send_json_error('Ungültiger Status');
    }
    
    if ($wc_status && $order->get_status() !== $wc_status) {
        try {
            $order->update_status($wc_status);
        } catch (\Throwable $e) {
            error_log('update_order_status_ajax update_status error: ' . $e->getMessage());
        }
    }

    ob_end_clean();
    wp_send_json_success();
}

// ── Tag Pool ──────────────────────────────────────────────────────────────────
add_action('wp_ajax_pa_get_tag_pool', 'pa_get_tag_pool_ajax');

function pa_get_tag_pool_ajax() {
    if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized');
    $n = $_POST['nonce'] ?? '';
    if (!wp_verify_nonce($n, 'add_product_nonce') && !wp_verify_nonce($n, 'product_list_nonce')) wp_send_json_error('Invalid nonce');

    $all_tags = get_terms(['taxonomy' => 'product_tag', 'hide_empty' => false]);
    $fixed = $variable_types = $variable_values = [];

    foreach ($all_tags as $t) {
        $type = get_term_meta($t->term_id, '_pa_tag_type', true);
        if ($type === 'variable_type') {
            $variable_types[] = [
                'term_id'      => $t->term_id,
                'name'         => $t->name,
                'is_variation' => (bool) get_term_meta($t->term_id, '_pa_is_variation', true),
            ];
        } elseif ($type === 'variable') {
            $prefix = get_term_meta($t->term_id, '_pa_variable_prefix', true);
            if (!isset($variable_values[$prefix])) $variable_values[$prefix] = [];
            $variable_values[$prefix][] = ['term_id' => $t->term_id, 'name' => $t->name];
        } else {
            $fixed[] = ['term_id' => $t->term_id, 'name' => $t->name];
        }
    }

    $cat_terms  = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
    $categories = [];
    foreach ($cat_terms as $t) {
        if (!in_array(strtolower($t->name), ['unkategorisiert', 'uncategorized'])) {
            $categories[] = ['term_id' => $t->term_id, 'name' => $t->name];
        }
    }

    wp_send_json_success([
        'variable_types'  => $variable_types,
        'variable_values' => $variable_values,
        'fixed'           => $fixed,
        'categories'      => $categories,
    ]);
}

add_action('wp_ajax_pa_create_tag', 'pa_create_tag_ajax');

function pa_create_tag_ajax() {
    if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized');
    $n = $_POST['nonce'] ?? '';
    if (!wp_verify_nonce($n, 'add_product_nonce') && !wp_verify_nonce($n, 'product_list_nonce')) wp_send_json_error('Invalid nonce');

    $name = sanitize_text_field($_POST['name'] ?? '');
    $type = sanitize_text_field($_POST['type'] ?? 'fixed');
    if (!$name) wp_send_json_error('Name fehlt');

    if ($type === 'variable') {
        $prefix = sanitize_text_field($_POST['prefix'] ?? '');
        if (!$prefix) wp_send_json_error('Prefix fehlt');
        $existing = get_term_by('name', $name, 'product_tag');
        if ($existing) {
            update_term_meta($existing->term_id, '_pa_tag_type', 'variable');
            update_term_meta($existing->term_id, '_pa_variable_prefix', $prefix);
            wp_send_json_success(['term_id' => $existing->term_id, 'name' => $name, 'type' => 'variable']);
        }
        $result = wp_insert_term($name, 'product_tag');
        if (is_wp_error($result)) wp_send_json_error($result->get_error_message());
        $term_id = $result['term_id'];
        update_term_meta($term_id, '_pa_tag_type', 'variable');
        update_term_meta($term_id, '_pa_variable_prefix', $prefix);
        wp_send_json_success(['term_id' => $term_id, 'name' => $name, 'type' => 'variable']);
    }

    if ($type === 'category') {
        $result = wp_insert_term($name, 'product_cat');
        if (is_wp_error($result)) {
            // If term exists, return it
            $existing = get_term_by('name', $name, 'product_cat');
            if ($existing) wp_send_json_error('Kategorie existiert bereits');
            wp_send_json_error($result->get_error_message());
        }
        wp_send_json_success(['term_id' => $result['term_id'], 'name' => $name, 'type' => 'category']);
    }

    $existing = get_term_by('name', $name, 'product_tag');
    if ($existing) wp_send_json_error('Tag existiert bereits');

    $result = wp_insert_term($name, 'product_tag');
    if (is_wp_error($result)) wp_send_json_error($result->get_error_message());

    $term_id = $result['term_id'];
    update_term_meta($term_id, '_pa_tag_type', $type);

    if ($type === 'variable_type') {
        update_term_meta($term_id, '_pa_is_variation', intval($_POST['is_variation'] ?? 0));
    }

    wp_send_json_success(['term_id' => $term_id, 'name' => $name, 'type' => $type]);
}

// ── Botanischer Produktname zusammensetzen ────────────────────────────────────
function pa_compose_product_name(string $gattung, string $art, string $kultivar): string {
    $gattung  = trim($gattung);
    $art      = trim($art);
    $kultivar = trim($kultivar);
    $parts    = array_filter([$gattung, $art]);
    $name     = implode(' ', $parts);
    if ($kultivar !== '') {
        $name .= ($name ? ' ' : '') . "'" . $kultivar . "'";
    }
    return $name ?: 'Unbenannt';
}

// ── Produkt erstellen ─────────────────────────────────────────────────────────
add_action('wp_ajax_add_product', 'add_product_ajax');

function add_product_ajax() {
    check_ajax_referer('add_product_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }

    $gattung  = sanitize_text_field($_POST['gattung']  ?? '');
    $art      = sanitize_text_field($_POST['art']      ?? '');
    $kultivar = sanitize_text_field($_POST['kultivar'] ?? '');
    $name     = pa_compose_product_name($gattung, $art, $kultivar);
    if ($name === 'Unbenannt' && !$gattung && !$art && !$kultivar) {
        wp_send_json_error('Gattung, Art oder Kultivar fehlt');
    }

    $product = new WC_Product_Simple();
    $product->set_name($name);
    $product->set_status('publish');
    $product->set_catalog_visibility('visible');

    $sku = sanitize_text_field($_POST['product_sku'] ?? '');
    if ($sku) {
        $product->set_sku($sku);
    }

    $price = floatval($_POST['product_price'] ?? 0);
    $product->set_regular_price($price);

    $product->set_manage_stock(true);
    $product->set_stock_quantity(intval($_POST['product_stock'] ?? 0));
    $product->set_backorders('no');

    $tax_class = sanitize_text_field($_POST['tax_class'] ?? 'standard');
    $product->set_tax_class($tax_class === 'standard' ? '' : $tax_class);

    // Versandklasse – per ID (Formular) oder per Name (CSV)
    $shipping_class_id   = intval($_POST['shipping_class'] ?? 0);
    $shipping_class_name = sanitize_text_field($_POST['shipping_class_name'] ?? '');
    if (!$shipping_class_id && $shipping_class_name) {
        $term = get_term_by('name', $shipping_class_name, 'product_shipping_class');
        if ($term) {
            $shipping_class_id = $term->term_id;
        }
    }
    if ($shipping_class_id) {
        $product->set_shipping_class_id($shipping_class_id);
    }

    $weight = sanitize_text_field($_POST['product_weight'] ?? '');
    $length = sanitize_text_field($_POST['product_length'] ?? '');
    $width  = sanitize_text_field($_POST['product_width']  ?? '');
    $height = sanitize_text_field($_POST['product_height'] ?? '');
    if ($weight !== '') $product->set_weight($weight);
    if ($length !== '') $product->set_length($length);
    if ($width  !== '') $product->set_width($width);
    if ($height !== '') $product->set_height($height);

    $product->set_short_description(wp_kses_post($_POST['short_description'] ?? ''));
    $product->set_description(wp_kses_post($_POST['description'] ?? ''));

    $featured_id = intval($_POST['featured_image_id'] ?? 0);
    if ($featured_id) {
        $product->set_image_id($featured_id);
    }

    $gallery_ids_raw = sanitize_text_field($_POST['gallery_image_ids'] ?? '');
    if ($gallery_ids_raw) {
        $gallery_ids = array_filter(array_map('intval', explode(',', $gallery_ids_raw)));
        if (!empty($gallery_ids)) {
            $product->set_gallery_image_ids($gallery_ids);
        }
    }

    $product_id = $product->save();

    if (!$product_id) {
        wp_send_json_error('Produkt konnte nicht gespeichert werden');
    }

    $unit_type    = sanitize_text_field($_POST['unit_type']         ?? 'stueck');
    $product_type = sanitize_text_field($_POST['product_type']      ?? 'pflanze');
    update_post_meta($product_id, '_unit_type',           $unit_type);
    update_post_meta($product_id, '_product_type_custom', $product_type);

    // Versandmaße
    $shipping_dims_same = intval($_POST['shipping_dims_same'] ?? 1);
    if ($shipping_dims_same) {
        update_post_meta($product_id, '_shipping_dims_same', 1);
    } else {
        update_post_meta($product_id, '_shipping_dims_same', 0);
        $sl = sanitize_text_field($_POST['shipping_length'] ?? '');
        $sw = sanitize_text_field($_POST['shipping_width']  ?? '');
        $sh = sanitize_text_field($_POST['shipping_height'] ?? '');
        if ($sl !== '') update_post_meta($product_id, '_shipping_length', $sl);
        if ($sw !== '') update_post_meta($product_id, '_shipping_width',  $sw);
        if ($sh !== '') update_post_meta($product_id, '_shipping_height', $sh);
    }

    if ($unit_type === 'liter') {
        $liters = floatval($_POST['product_liters'] ?? 0);
        update_post_meta($product_id, '_product_liters', $liters);
        if ($liters > 0 && $price > 0) {
            $per_liter  = $price / $liters;
            $base_price = ($product_type === 'substrat') ? $per_liter * 10 : $per_liter;
            $base_unit  = ($product_type === 'substrat') ? '10 L' : '1 L';
            update_post_meta($product_id, '_base_price',      $base_price);
            update_post_meta($product_id, '_base_price_unit', $base_unit);
        }
    }

    update_post_meta($product_id, '_differential_taxation',     intval($_POST['differential_taxation']  ?? 0));
    update_post_meta($product_id, '_delivery_time_days',        intval($_POST['delivery_time']           ?? 7));
    update_post_meta($product_id, '_never_low_stock',           intval($_POST['never_low_stock']         ?? 0));
    update_post_meta($product_id, '_custom_low_stock_threshold',
        intval($_POST['custom_low_stock'] ?? 0) ? intval($_POST['low_stock_threshold'] ?? 5) : 5
    );
    update_post_meta($product_id, '_in_progress', 0);


	// ── Botanische Taxonomie
	update_post_meta($product_id, '_pa_gattung',  $gattung);
	update_post_meta($product_id, '_pa_art',      $art);
	update_post_meta($product_id, '_pa_kultivar', $kultivar);

	// ── Pflege-Parameter
	$care_light = sanitize_text_field($_POST["care_light"] ?? "");
	update_post_meta($product_id, "_pa_care_light", $care_light);

	$care_water = sanitize_text_field($_POST["care_water"] ?? "");
	update_post_meta($product_id, "_pa_care_water", $care_water);

	$care_winter = sanitize_text_field($_POST["care_winter"] ?? "");
	update_post_meta($product_id, "_pa_care_winter", $care_winter);

	$care_temp_min = sanitize_text_field($_POST["care_temp_min"] ?? "");
	update_post_meta($product_id, "_pa_care_temp_min", $care_temp_min);

	$care_temp_max = sanitize_text_field($_POST["care_temp_max"] ?? "");
	update_post_meta($product_id, "_pa_care_temp_max", $care_temp_max);

	$allowed_wh = ['', 'nicht-wh', 'bedingt-wh', 'winterhart', 'sehr-wh', 'voll-wh'];
	$care_winterhaerte = sanitize_text_field($_POST["care_winterhaerte"] ?? "");
	if (!in_array($care_winterhaerte, $allowed_wh, true)) $care_winterhaerte = '';
	update_post_meta($product_id, "_pa_winterhaerte", $care_winterhaerte);

	$species_id = intval($_POST["plant_species_id"] ?? 0);
	if ($species_id) {
		update_post_meta($product_id, "_pa_plant_species_id", $species_id);
	}

    // Kategorien
    $cats_raw = sanitize_text_field($_POST['product_categories'] ?? '');
    if ($cats_raw) {
        $cat_ids = array_filter(array_map('intval', explode(',', $cats_raw)));
        if (!empty($cat_ids)) {
            wp_set_object_terms($product_id, $cat_ids, 'product_cat');
        }
    }

    // Tags (fest + variabel)
    $tag_ids = [];
    $fixed_tags_raw = sanitize_text_field($_POST['product_tags_fixed'] ?? '');
    if ($fixed_tags_raw) {
        $tag_ids = array_filter(array_map('intval', explode(',', $fixed_tags_raw)));
    }
    $variable_tags_raw = wp_unslash($_POST['product_variable_tags'] ?? '[]');
    $variable_tags = json_decode($variable_tags_raw, true);
    if (is_array($variable_tags)) {
        foreach ($variable_tags as $vt) {
            $vt_prefix = sanitize_text_field($vt['prefix'] ?? '');
            $vt_value  = sanitize_text_field($vt['value']  ?? '');
            if (!$vt_prefix || !$vt_value) continue;
            $existing_term = get_term_by('name', $vt_value, 'product_tag');
            if ($existing_term) {
                $vt_term_id = $existing_term->term_id;
            } else {
                $ins = wp_insert_term($vt_value, 'product_tag');
                if (is_wp_error($ins)) continue;
                $vt_term_id = $ins['term_id'];
            }
            update_term_meta($vt_term_id, '_pa_tag_type', 'variable');
            update_term_meta($vt_term_id, '_pa_variable_prefix', $vt_prefix);
            $tag_ids[] = $vt_term_id;
        }
    }
    // tags_string for bulk import: "kategorie:wert,tag2" format
    $tags_string = sanitize_text_field($_POST['tags_string'] ?? '');
    if ($tags_string) {
        foreach (array_filter(array_map('trim', explode(',', $tags_string))) as $entry) {
            if (strpos($entry, ':') !== false) {
                [$pfx, $val] = explode(':', $entry, 2);
                $pfx = trim($pfx); $val = trim($val);
                if (!$pfx || !$val) continue;
                $t = get_term_by('name', $val, 'product_tag');
                if ($t) { $tid = $t->term_id; }
                else { $ins = wp_insert_term($val, 'product_tag'); if (is_wp_error($ins)) continue; $tid = $ins['term_id']; }
                update_term_meta($tid, '_pa_tag_type', 'variable');
                update_term_meta($tid, '_pa_variable_prefix', $pfx);
                $tag_ids[] = $tid;
            } else {
                $t = get_term_by('name', $entry, 'product_tag');
                if ($t) { $tag_ids[] = $t->term_id; }
                else { $ins = wp_insert_term($entry, 'product_tag'); if (!is_wp_error($ins)) { update_term_meta($ins['term_id'], '_pa_tag_type', 'fixed'); $tag_ids[] = $ins['term_id']; } }
            }
        }
    }

    if (!empty($tag_ids)) {
        wp_set_object_terms($product_id, array_values($tag_ids), 'product_tag');
    }

    // Varianten als Meta speichern
    $variants_raw = wp_unslash($_POST['product_variants'] ?? '[]');
    $variants = json_decode($variants_raw, true);
    if (is_array($variants) && !empty($variants)) {
        update_post_meta($product_id, '_pa_variants', $variants);
    }

    wp_send_json_success(['product_id' => $product_id, 'product_name' => $product->get_name()]);
}

// ── Produkt-Detail für Edit-Modal ─────────────────────────────────────────────
add_action('wp_ajax_get_product_detail', 'get_product_detail_ajax');

function get_product_detail_ajax() {
    check_ajax_referer('product_list_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized');

    $product_id = intval($_POST['product_id']);
    $product    = wc_get_product($product_id);
    if (!$product) wp_send_json_error('Product not found');

    // Steuerklassen-Optionen
    $tax_class_options = [['slug' => 'standard', 'name' => 'Standard (19%)']];
    foreach (WC_Tax::get_tax_classes() as $class) {
        $tax_class_options[] = ['slug' => sanitize_title($class), 'name' => $class];
    }

    // Versandklassen-Optionen
    $shipping_class_options = [['id' => 0, 'name' => '— Keine —']];
    $shipping_terms = get_terms(['taxonomy' => 'product_shipping_class', 'hide_empty' => false]);
    if (!is_wp_error($shipping_terms)) {
        foreach ($shipping_terms as $term) {
            $shipping_class_options[] = ['id' => $term->term_id, 'name' => $term->name];
        }
    }

    // Titelbild
    $featured_image = null;
    $image_id = $product->get_image_id();
    if ($image_id) {
        $src = wp_get_attachment_image_src($image_id, 'thumbnail');
        $featured_image = ['id' => $image_id, 'url' => $src ? $src[0] : ''];
    }

    // Galerie
    $gallery = [];
    foreach ($product->get_gallery_image_ids() as $gid) {
        $src = wp_get_attachment_image_src($gid, 'thumbnail');
        $gallery[] = ['id' => $gid, 'url' => $src ? $src[0] : ''];
    }

    $tax_class = $product->get_tax_class();

    // Tags
    $all_product_tags = wp_get_object_terms($product_id, 'product_tag');
    $tags_fixed    = [];
    $tags_variable = [];
    foreach ($all_product_tags as $tag) {
        $tag_type   = get_term_meta($tag->term_id, '_pa_tag_type', true);
        $tag_prefix = get_term_meta($tag->term_id, '_pa_variable_prefix', true);
        if ($tag_type === 'variable') {
            $tags_variable[] = ['term_id' => $tag->term_id, 'name' => $tag->name, 'prefix' => $tag_prefix];
        } elseif ($tag_type === 'fixed') {
            $tags_fixed[] = ['term_id' => $tag->term_id, 'name' => $tag->name];
        }
    }

    wp_send_json_success([
        'id'                    => $product_id,
        'name'                  => $product->get_name(),
        'sku'                   => $product->get_sku(),
        'price'                 => $product->get_regular_price(),
        'stock'                 => (int) $product->get_stock_quantity(),
        'tax_class'             => $tax_class === '' ? 'standard' : $tax_class,
        'product_type'          => get_post_meta($product_id, '_product_type_custom', true) ?: 'pflanze',
        'unit_type'             => get_post_meta($product_id, '_unit_type', true) ?: 'stueck',
        'product_liters'        => get_post_meta($product_id, '_product_liters', true),
        'differential_taxation' => (int) get_post_meta($product_id, '_differential_taxation', true),
        'backorders'            => $product->get_backorders(),
        'low_stock_threshold'   => get_post_meta($product_id, '_custom_low_stock_threshold', true),
        'never_low_stock'       => (int) get_post_meta($product_id, '_never_low_stock', true),
        'weight'                => $product->get_weight(),
        'length'                => $product->get_length(),
        'width'                 => $product->get_width(),
        'height'                => $product->get_height(),
        'shipping_class_id'     => (int) $product->get_shipping_class_id(),
        'delivery_time_days'    => (int) get_post_meta($product_id, '_delivery_time_days', true),
        'gattung'               => get_post_meta($product_id, '_pa_gattung', true),
        'art'                   => get_post_meta($product_id, '_pa_art', true),
        'kultivar'              => get_post_meta($product_id, '_pa_kultivar', true),
        'care_light'            => get_post_meta($product_id, '_pa_care_light', true),
        'care_water'            => get_post_meta($product_id, '_pa_care_water', true),
        'care_winter'           => get_post_meta($product_id, '_pa_care_winter', true),
        'care_winterhaerte'     => get_post_meta($product_id, '_pa_winterhaerte', true),
        'care_temp_min'         => get_post_meta($product_id, '_pa_care_temp_min', true),
        'care_temp_max'         => get_post_meta($product_id, '_pa_care_temp_max', true),
        'description'           => $product->get_description(),
        'short_description'     => $product->get_short_description(),
        'tax_class_options'     => $tax_class_options,
        'shipping_class_options' => $shipping_class_options,
        'featured_image'        => $featured_image,
        'gallery'               => $gallery,
        'tags_fixed'            => $tags_fixed,
        'tags_variable'         => $tags_variable,
    ]);
}

// ── Tag / Kategorie-Term löschen ──────────────────────────────────────────────
add_action('wp_ajax_pa_delete_term', 'pa_delete_term_ajax');

function pa_delete_term_ajax() {
    if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized');
    $n = $_POST['nonce'] ?? '';
    if (!wp_verify_nonce($n, 'add_product_nonce') && !wp_verify_nonce($n, 'product_list_nonce')) wp_send_json_error('Invalid nonce');

    $term_id  = intval($_POST['term_id']);
    $taxonomy = sanitize_key($_POST['taxonomy'] ?? 'product_tag');
    if (!in_array($taxonomy, ['product_tag', 'product_cat'], true)) wp_send_json_error('Invalid taxonomy');

    // For variable_type tags also delete all associated value-terms
    if ($taxonomy === 'product_tag') {
        $type = get_term_meta($term_id, '_pa_tag_type', true);
        if ($type === 'variable_type') {
            $term = get_term($term_id, 'product_tag');
            if ($term && !is_wp_error($term)) {
                global $wpdb;
                $value_ids = $wpdb->get_col($wpdb->prepare(
                    "SELECT t.term_id FROM {$wpdb->termmeta} t
                     INNER JOIN {$wpdb->termmeta} p ON t.term_id = p.term_id
                     WHERE t.meta_key = '_pa_tag_type' AND t.meta_value = 'variable'
                       AND p.meta_key = '_pa_variable_prefix' AND p.meta_value = %s",
                    $term->name
                ));
                foreach ($value_ids as $vid) {
                    wp_delete_term((int) $vid, 'product_tag');
                }
            }
        }
    }

    $result = wp_delete_term($term_id, $taxonomy);
    if (is_wp_error($result)) wp_send_json_error($result->get_error_message());
    wp_send_json_success();
}

// ── Produkt-Tags speichern ────────────────────────────────────────────────────
add_action('wp_ajax_pa_save_product_tags', 'pa_save_product_tags_ajax');

function pa_save_product_tags_ajax() {
    check_ajax_referer('product_list_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized');

    $product_id = intval($_POST['product_id']);
    $product    = wc_get_product($product_id);
    if (!$product) wp_send_json_error('Product not found');

    $fixed_ids_raw = sanitize_text_field($_POST['fixed_ids'] ?? '');
    $variable_raw  = wp_unslash($_POST['variable_tags'] ?? '[]');
    $variable_tags = json_decode($variable_raw, true);

    $tag_ids = [];
    if ($fixed_ids_raw) {
        $tag_ids = array_values(array_filter(array_map('intval', explode(',', $fixed_ids_raw))));
    }
    if (is_array($variable_tags)) {
        foreach ($variable_tags as $vt) {
            $vt_prefix = sanitize_text_field($vt['prefix'] ?? '');
            $vt_value  = sanitize_text_field($vt['value']  ?? '');
            if (!$vt_prefix || !$vt_value) continue;
            $existing_term = get_term_by('name', $vt_value, 'product_tag');
            if ($existing_term) {
                $vt_term_id = $existing_term->term_id;
            } else {
                $ins = wp_insert_term($vt_value, 'product_tag');
                if (is_wp_error($ins)) continue;
                $vt_term_id = $ins['term_id'];
            }
            update_term_meta($vt_term_id, '_pa_tag_type', 'variable');
            update_term_meta($vt_term_id, '_pa_variable_prefix', $vt_prefix);
            $tag_ids[] = $vt_term_id;
        }
    }
    wp_set_object_terms($product_id, $tag_ids, 'product_tag');
    wp_send_json_success();
}

// ── Einzelnes Produktfeld speichern ──────────────────────────────────────────
add_action('wp_ajax_update_product_field', 'update_product_field_ajax');

function update_product_field_ajax() {
    check_ajax_referer('product_list_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized');

    $product_id = intval($_POST['product_id']);
    $field      = sanitize_key($_POST['field'] ?? '');
    $value      = json_decode(wp_unslash($_POST['value'] ?? '""'), true);
    if ($value === null) {
        $value = sanitize_text_field(wp_unslash($_POST['value'] ?? ''));
    }

    $product = wc_get_product($product_id);
    if (!$product) wp_send_json_error('Product not found');

    $needs_wc_save = true;

    switch ($field) {
        case 'gattung':
            update_post_meta($product_id, '_pa_gattung', sanitize_text_field($value));
            $product->set_name(pa_compose_product_name(
                sanitize_text_field($value),
                get_post_meta($product_id, '_pa_art', true),
                get_post_meta($product_id, '_pa_kultivar', true)
            ));
            break;
        case 'art':
            update_post_meta($product_id, '_pa_art', sanitize_text_field($value));
            $product->set_name(pa_compose_product_name(
                get_post_meta($product_id, '_pa_gattung', true),
                sanitize_text_field($value),
                get_post_meta($product_id, '_pa_kultivar', true)
            ));
            break;
        case 'kultivar':
            update_post_meta($product_id, '_pa_kultivar', sanitize_text_field($value));
            $product->set_name(pa_compose_product_name(
                get_post_meta($product_id, '_pa_gattung', true),
                get_post_meta($product_id, '_pa_art', true),
                sanitize_text_field($value)
            ));
            break;
        case 'sku':
            $product->set_sku(sanitize_text_field($value));
            break;
        case 'price':
            $product->set_regular_price(floatval($value));
            break;
        case 'stock':
            $product->set_stock_quantity(intval($value));
            break;
        case 'tax_class':
            $product->set_tax_class($value === 'standard' ? '' : sanitize_text_field($value));
            break;
        case 'backorders':
            $product->set_backorders(sanitize_text_field($value));
            break;
        case 'weight':
            $product->set_weight($value !== '' ? floatval($value) : '');
            break;
        case 'dimensions':
            if (is_array($value)) {
                $product->set_length($value['length'] !== '' ? floatval($value['length']) : '');
                $product->set_width($value['width']   !== '' ? floatval($value['width'])  : '');
                $product->set_height($value['height'] !== '' ? floatval($value['height']) : '');
            }
            break;
        case 'shipping_class':
            $product->set_shipping_class_id(intval($value));
            break;
        case 'product_type':
            update_post_meta($product_id, '_product_type_custom', sanitize_text_field($value));
            $needs_wc_save = false;
            break;
        case 'unit_type':
            update_post_meta($product_id, '_unit_type', sanitize_text_field($value));
            $needs_wc_save = false;
            break;
        case 'product_liters':
            update_post_meta($product_id, '_product_liters', floatval($value));
            $needs_wc_save = false;
            break;
        case 'differential_taxation':
            update_post_meta($product_id, '_differential_taxation', intval($value));
            $needs_wc_save = false;
            break;
        case 'low_stock_threshold':
            update_post_meta($product_id, '_custom_low_stock_threshold', intval($value));
            $needs_wc_save = false;
            break;
        case 'never_low_stock':
            update_post_meta($product_id, '_never_low_stock', intval($value));
            $needs_wc_save = false;
            break;
        case 'delivery_time_days':
            update_post_meta($product_id, '_delivery_time_days', intval($value));
            $needs_wc_save = false;
            break;
        case 'care_light':
            update_post_meta($product_id, '_pa_care_light', sanitize_text_field($value));
            $needs_wc_save = false;
            break;
        case 'care_water':
            update_post_meta($product_id, '_pa_care_water', sanitize_text_field($value));
            $needs_wc_save = false;
            break;
        case 'care_winter':
            update_post_meta($product_id, '_pa_care_winter', sanitize_text_field($value));
            $needs_wc_save = false;
            break;
        case 'care_winterhaerte':
            $allowed_wh2 = ['', 'nicht-wh', 'bedingt-wh', 'winterhart', 'sehr-wh', 'voll-wh'];
            $wh2 = sanitize_text_field($value);
            update_post_meta($product_id, '_pa_winterhaerte', in_array($wh2, $allowed_wh2, true) ? $wh2 : '');
            $needs_wc_save = false;
            break;
        case 'care_temp_min':
            update_post_meta($product_id, '_pa_care_temp_min', sanitize_text_field($value));
            $needs_wc_save = false;
            break;
        case 'care_temp_max':
            update_post_meta($product_id, '_pa_care_temp_max', sanitize_text_field($value));
            $needs_wc_save = false;
            break;
        case 'description':
            $product->set_description(wp_kses_post($value));
            break;
        case 'short_description':
            $product->set_short_description(wp_kses_post($value));
            break;
        default:
            wp_send_json_error('Unbekanntes Feld: ' . $field);
    }

    if ($needs_wc_save) {
        $product->save();
        wc_delete_product_transients($product_id);
    }

    wp_send_json_success();
}

// ══════════════════════════════════════════════════════════════════
// 2.3 RABATTCODE SYSTEM
// ══════════════════════════════════════════════════════════════════

function pa_get_coupons_ajax() {
    check_ajax_referer('product_list_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Forbidden');

    $posts = get_posts([
        'post_type'      => 'shop_coupon',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);

    $coupons = [];
    foreach ($posts as $post) {
        $coupon  = new WC_Coupon($post->ID);
        $expires = $coupon->get_date_expires();
        $coupons[] = [
            'id'                   => $post->ID,
            'code'                 => $coupon->get_code(),
            'discount_type'        => $coupon->get_discount_type(),
            'amount'               => (float) $coupon->get_amount(),
            'minimum_amount'       => (float) $coupon->get_minimum_amount(),
            'maximum_amount'       => (float) $coupon->get_maximum_amount(),
            'product_ids'          => array_map('intval', $coupon->get_product_ids()),
            'product_categories'   => array_map('intval', $coupon->get_product_categories()),
            'usage_limit'          => $coupon->get_usage_limit() ?: 0,
            'usage_limit_per_user' => $coupon->get_usage_limit_per_user() ?: 0,
            'usage_count'          => (int) $coupon->get_usage_count(),
            'date_start'           => get_post_meta($post->ID, '_pa_coupon_date_start', true),
            'date_expires'         => $expires ? $expires->date('Y-m-d') : '',
            'min_cart_quantity'    => (int) get_post_meta($post->ID, '_pa_min_cart_quantity', true),
            'new_customers_only'   => (bool) get_post_meta($post->ID, '_pa_new_customers_only', true),
            'exclude_sale_items'   => (bool) $coupon->get_exclude_sale_items(),
            'free_shipping'        => (bool) $coupon->get_free_shipping(),
            'individual_use'       => (bool) $coupon->get_individual_use(),
        ];
    }
    wp_send_json_success($coupons);
}
add_action('wp_ajax_pa_get_coupons', 'pa_get_coupons_ajax');

function pa_save_coupon_ajax() {
    check_ajax_referer('product_list_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Forbidden');

    $coupon_id = intval($_POST['coupon_id'] ?? 0);
    $code      = strtolower(sanitize_text_field(trim($_POST['code'] ?? '')));
    if (empty($code)) wp_send_json_error('Code darf nicht leer sein.');

    $existing_id = wc_get_coupon_id_by_code($code);
    if ($existing_id && $existing_id !== $coupon_id) {
        wp_send_json_error('Dieser Code existiert bereits.');
    }

    $coupon = $coupon_id ? new WC_Coupon($coupon_id) : new WC_Coupon();
    $coupon->set_code($code);
    $coupon->set_discount_type(sanitize_text_field($_POST['discount_type'] ?? 'percent'));
    $coupon->set_amount(floatval($_POST['amount'] ?? 0));

    $max_amt = floatval($_POST['maximum_amount'] ?? 0);
    $coupon->set_maximum_amount($max_amt > 0 ? $max_amt : '');
    $min_amt = floatval($_POST['minimum_amount'] ?? 0);
    $coupon->set_minimum_amount($min_amt > 0 ? $min_amt : '');

    $coupon->set_individual_use(($_POST['individual_use'] ?? '0') === '1');
    $coupon->set_exclude_sale_items(($_POST['exclude_sale_items'] ?? '0') === '1');
    $coupon->set_free_shipping(($_POST['free_shipping'] ?? '0') === '1');

    $usage_limit = intval($_POST['usage_limit'] ?? 0);
    $coupon->set_usage_limit($usage_limit > 0 ? $usage_limit : '');
    $usage_per   = intval($_POST['usage_limit_per_user'] ?? 0);
    $coupon->set_usage_limit_per_user($usage_per > 0 ? $usage_per : '');

    $product_ids_raw = sanitize_text_field($_POST['product_ids'] ?? '');
    $coupon->set_product_ids(array_values(array_filter(array_map('intval', array_filter(explode(',', $product_ids_raw))))));

    $cat_ids_raw = sanitize_text_field($_POST['product_categories'] ?? '');
    $coupon->set_product_categories(array_values(array_filter(array_map('intval', array_filter(explode(',', $cat_ids_raw))))));

    $date_expires = sanitize_text_field($_POST['date_expires'] ?? '');
    $coupon->set_date_expires($date_expires ? strtotime($date_expires . ' 23:59:59') : '');

    $new_id = $coupon->save();
    if (!$new_id) wp_send_json_error('Speichern fehlgeschlagen.');

    $date_start = sanitize_text_field($_POST['date_start'] ?? '');
    update_post_meta($new_id, '_pa_coupon_date_start', $date_start);
    $min_qty = intval($_POST['min_cart_quantity'] ?? 0);
    update_post_meta($new_id, '_pa_min_cart_quantity', $min_qty > 0 ? $min_qty : '');
    update_post_meta($new_id, '_pa_new_customers_only', ($_POST['new_customers_only'] ?? '0') === '1' ? 1 : 0);

    wp_send_json_success(['coupon_id' => $new_id]);
}
add_action('wp_ajax_pa_save_coupon', 'pa_save_coupon_ajax');

function pa_delete_coupon_ajax() {
    check_ajax_referer('product_list_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Forbidden');

    $coupon_id = intval($_POST['coupon_id'] ?? 0);
    if (!$coupon_id) wp_send_json_error('Ungültige ID.');
    wp_delete_post($coupon_id, true);
    wp_send_json_success();
}
add_action('wp_ajax_pa_delete_coupon', 'pa_delete_coupon_ajax');

// Validate custom coupon rules at checkout
add_filter('woocommerce_coupon_is_valid', 'pa_validate_coupon_custom_rules', 10, 2);
function pa_validate_coupon_custom_rules($valid, $coupon) {
    if (!$valid) return false;
    $coupon_id = $coupon->get_id();

    $date_start = get_post_meta($coupon_id, '_pa_coupon_date_start', true);
    if ($date_start && strtotime($date_start) > current_time('timestamp')) {
        throw new Exception('Dieser Rabattcode ist noch nicht gültig.');
    }

    $min_qty = (int) get_post_meta($coupon_id, '_pa_min_cart_quantity', true);
    if ($min_qty > 0 && WC()->cart) {
        if (WC()->cart->get_cart_contents_count() < $min_qty) {
            throw new Exception(
                sprintf('Dieser Rabattcode erfordert mindestens %d Artikel im Warenkorb.', $min_qty)
            );
        }
    }

    if (get_post_meta($coupon_id, '_pa_new_customers_only', true) && is_user_logged_in()) {
        $orders = wc_get_orders([
            'customer' => get_current_user_id(),
            'limit'    => 1,
            'status'   => ['completed', 'processing'],
        ]);
        if (!empty($orders)) {
            throw new Exception('Dieser Rabattcode ist nur für Neukunden gültig.');
        }
    }

    return $valid;
}

// ══════════════════════════════════════════════════════════════════
// 2.4 SOCIAL MEDIA DEAL SYSTEM
// ══════════════════════════════════════════════════════════════════

add_action('after_setup_theme', 'pa_maybe_create_deals_table');
function pa_maybe_create_deals_table() {
    if (get_option('_pa_deals_table_v') === '1') return;
    global $wpdb;
    $table           = $wpdb->prefix . 'pa_social_deals';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS {$table} (
        id           bigint(20)   NOT NULL AUTO_INCREMENT,
        user_id      bigint(20)   NOT NULL DEFAULT 0,
        order_id     bigint(20)   NOT NULL,
        platform     varchar(50)  NOT NULL,
        handle       varchar(100) NOT NULL DEFAULT '',
        assigned_pct float        DEFAULT NULL,
        coupon_code  varchar(100) DEFAULT NULL,
        coupon_id    bigint(20)   DEFAULT NULL,
        status       varchar(20)  NOT NULL DEFAULT 'pending',
        submitted_at datetime     NOT NULL,
        processed_at datetime     DEFAULT NULL,
        PRIMARY KEY  (id),
        KEY user_id  (user_id),
        KEY order_id (order_id),
        KEY status   (status)
    ) {$charset_collate};";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
    update_option('_pa_deals_table_v', '1', false);
}

function pa_get_sm_config_ajax() {
    check_ajax_referer('product_list_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Forbidden');
    wp_send_json_success(get_option('_pa_sm_platforms', []));
}
add_action('wp_ajax_pa_get_sm_config', 'pa_get_sm_config_ajax');

function pa_save_sm_config_ajax() {
    check_ajax_referer('product_list_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Forbidden');

    $platforms_raw = wp_unslash($_POST['platforms'] ?? '[]');
    $platforms     = json_decode($platforms_raw, true);
    if (!is_array($platforms)) wp_send_json_error('Ungültiges Format.');

    $allowed_keys = ['instagram', 'facebook', 'tiktok', 'pinterest', 'youtube', 'twitter'];
    $clean        = [];
    foreach ($platforms as $p) {
        $key = sanitize_key($p['key'] ?? '');
        if (!in_array($key, $allowed_keys, true)) continue;
        $clean[] = [
            'key'     => $key,
            'label'   => sanitize_text_field($p['label'] ?? ''),
            'handle'  => sanitize_text_field($p['handle'] ?? ''),
            'pct_min' => max(1, min(100, intval($p['pct_min'] ?? 5))),
            'pct_max' => max(1, min(100, intval($p['pct_max'] ?? 20))),
            'active'  => !empty($p['active']),
        ];
    }
    update_option('_pa_sm_platforms', $clean, false);
    wp_send_json_success();
}
add_action('wp_ajax_pa_save_sm_config', 'pa_save_sm_config_ajax');

function pa_get_deals_ajax() {
    check_ajax_referer('product_list_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Forbidden');

    global $wpdb;
    $table  = $wpdb->prefix . 'pa_social_deals';
    $status = sanitize_text_field($_POST['status'] ?? '');
    $where  = $status ? $wpdb->prepare('WHERE d.status = %s', $status) : '';

    $rows = $wpdb->get_results(
        "SELECT d.*, u.display_name, u.user_email
         FROM {$table} d
         LEFT JOIN {$wpdb->users} u ON u.ID = d.user_id
         {$where}
         ORDER BY d.submitted_at DESC"
    );

    $result = [];
    foreach ($rows as $row) {
        $order    = wc_get_order((int) $row->order_id);
        $products = [];
        if ($order) {
            foreach ($order->get_items() as $item) {
                $products[] = $item->get_quantity() . 'x ' . $item->get_name();
            }
        }
        $result[] = [
            'id'           => (int)   $row->id,
            'user_id'      => (int)   $row->user_id,
            'user_name'    => $row->display_name ?: 'Gast',
            'user_email'   => $row->user_email   ?: '',
            'order_id'     => (int)   $row->order_id,
            'order_number' => $order ? $order->get_order_number() : '#' . $row->order_id,
            'products'     => implode(', ', $products),
            'platform'     => $row->platform,
            'handle'       => $row->handle,
            'assigned_pct' => $row->assigned_pct !== null ? (float) $row->assigned_pct : null,
            'coupon_code'  => $row->coupon_code,
            'status'       => $row->status,
            'submitted_at' => $row->submitted_at,
        ];
    }
    wp_send_json_success($result);
}
add_action('wp_ajax_pa_get_deals', 'pa_get_deals_ajax');

function pa_submit_deal_ajax() {
    check_ajax_referer('pa_deal_nonce', 'nonce');
    if (!is_user_logged_in()) wp_send_json_error('Bitte melde dich an.');

    $user_id  = get_current_user_id();
    $order_id = intval($_POST['order_id'] ?? 0);
    $platform = sanitize_key($_POST['platform'] ?? '');
    $handle   = sanitize_text_field($_POST['handle'] ?? '');

    if (!$order_id || !$platform || !$handle) wp_send_json_error('Pflichtfelder fehlen.');
    if (mb_strlen($handle) > 100) wp_send_json_error('Handle zu lang.');

    $order = wc_get_order($order_id);
    if (!$order || (int) $order->get_customer_id() !== $user_id) {
        wp_send_json_error('Ungültige Bestellung.');
    }

    global $wpdb;
    $table    = $wpdb->prefix . 'pa_social_deals';
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM {$table} WHERE order_id = %d AND status != 'rejected'", $order_id
    ));
    if ($existing) wp_send_json_error('Für diese Bestellung wurde bereits ein Deal eingereicht.');

    $platforms = get_option('_pa_sm_platforms', []);
    $valid     = false;
    foreach ($platforms as $p) {
        if ($p['key'] === $platform && !empty($p['active'])) { $valid = true; break; }
    }
    if (!$valid) wp_send_json_error('Ungültige Plattform.');

    $wpdb->insert($table, [
        'user_id'      => $user_id,
        'order_id'     => $order_id,
        'platform'     => $platform,
        'handle'       => $handle,
        'status'       => 'pending',
        'submitted_at' => current_time('mysql'),
    ]);
    wp_send_json_success();
}
add_action('wp_ajax_pa_submit_deal', 'pa_submit_deal_ajax');

function pa_approve_deal_ajax() {
    check_ajax_referer('product_list_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Forbidden');

    $deal_id = intval($_POST['deal_id'] ?? 0);
    $pct     = floatval($_POST['percent'] ?? 0);
    if (!$deal_id || $pct <= 0 || $pct > 100) wp_send_json_error('Ungültige Parameter.');

    global $wpdb;
    $table = $wpdb->prefix . 'pa_social_deals';
    $deal  = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $deal_id));
    if (!$deal)                      wp_send_json_error('Deal nicht gefunden.');
    if ($deal->status !== 'pending') wp_send_json_error('Deal ist nicht mehr offen.');

    $code   = 'SM-' . strtoupper(wp_generate_password(10, false, false));
    $coupon = new WC_Coupon();
    $coupon->set_code($code);
    $coupon->set_discount_type('percent');
    $coupon->set_amount($pct);
    $coupon->set_usage_limit(1);
    $coupon->set_usage_limit_per_user(1);
    $coupon->set_date_expires(strtotime('+90 days'));
    $coupon_id = $coupon->save();
    if (!$coupon_id) wp_send_json_error('Coupon konnte nicht erstellt werden.');

    $wpdb->update($table, [
        'status'       => 'approved',
        'assigned_pct' => $pct,
        'coupon_code'  => $code,
        'coupon_id'    => $coupon_id,
        'processed_at' => current_time('mysql'),
    ], ['id' => $deal_id]);

    $user = get_userdata((int) $deal->user_id);
    if ($user && $user->user_email) {
        $site = get_bloginfo('name');
        $shop = wc_get_page_permalink('shop');
        wp_mail(
            $user->user_email,
            sprintf('Dein %s%% Rabattcode für %s', number_format($pct, 0), $site),
            sprintf(
                "Hallo %s,\n\nvielen Dank für deinen Social-Media-Post!\n\n" .
                "Hier ist dein persönlicher Rabattcode:\n\n    %s\n\n" .
                "Betrag: %s%%  |  Gültig 90 Tage  |  Nur einmal verwendbar\n\n" .
                "Jetzt einlösen: %s\n\nViele Grüße,\n%s",
                $user->display_name, $code, number_format($pct, 0), $shop, $site
            )
        );
    }
    wp_send_json_success(['coupon_code' => $code]);
}
add_action('wp_ajax_pa_approve_deal', 'pa_approve_deal_ajax');

function pa_reject_deal_ajax() {
    check_ajax_referer('product_list_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Forbidden');

    $deal_id = intval($_POST['deal_id'] ?? 0);
    if (!$deal_id) wp_send_json_error('Ungültige ID.');
    global $wpdb;
    $table = $wpdb->prefix . 'pa_social_deals';
    $wpdb->update($table, [
        'status'       => 'rejected',
        'processed_at' => current_time('mysql'),
    ], ['id' => $deal_id]);
    wp_send_json_success();
}
add_action('wp_ajax_pa_reject_deal', 'pa_reject_deal_ajax');

// Shared deal popup HTML used on order-received page and account page
function pa_deal_popup_html(int $order_id, array $platforms, string $nonce, string $ajax): string {
    ob_start();
    ?>
<div class="pa-deal-banner" style="margin-top:28px;padding:18px 22px;background:#f0faf4;border:1px solid #b7dfc4;border-radius:6px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
    <div style="flex:1;min-width:200px;">
        <strong style="display:block;margin-bottom:3px;color:#1a5c30;font-size:15px;">Rabattcode verdienen</strong>
        <span style="font-size:13px;color:#2d6a4f;">Teile deine Bestellung in den sozialen Medien, tagge uns und erhalte einen persönlichen Rabattcode.</span>
    </div>
    <button onclick="paOpenDeal(<?php echo intval($order_id); ?>)" style="padding:10px 20px;background:#2d6a4f;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:13px;font-weight:600;white-space:nowrap;">Rabattcode verdienen</button>
</div>

<div id="pa-deal-overlay" onclick="if(event.target===this)paCloseDeal()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:99999;align-items:center;justify-content:center;">
<div style="background:#fff;border-radius:10px;padding:30px;max-width:480px;width:92%;position:relative;max-height:88vh;overflow-y:auto;">
    <button onclick="paCloseDeal()" style="position:absolute;top:14px;right:18px;background:none;border:none;font-size:24px;cursor:pointer;color:#bbb;line-height:1;">&#215;</button>
    <h3 style="margin:0 0 6px;font-size:19px;color:#1a1a1a;">Rabattcode verdienen</h3>
    <p style="margin:0 0 18px;font-size:13px;color:#666;">Wähle eine Plattform, poste ein Foto und tagge uns. Sobald wir deinen Post geprüft haben, erhältst du per E-Mail deinen persönlichen Rabattcode.</p>
    <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:18px;">
    <?php foreach ($platforms as $p): ?>
        <label class="pa-plat-lbl" style="display:flex;align-items:center;gap:12px;padding:11px 14px;border:2px solid #e5e5e5;border-radius:6px;cursor:pointer;transition:border-color .15s;">
            <input type="radio" name="pa_platform" value="<?php echo esc_attr($p['key']); ?>" style="accent-color:#2d6a4f;width:15px;height:15px;flex-shrink:0;">
            <div>
                <div style="font-weight:600;font-size:14px;color:#1a1a1a;"><?php echo esc_html($p['label']); ?></div>
                <div style="font-size:11px;color:#999;margin-top:1px;">Tag: <?php echo esc_html($p['handle']); ?> &nbsp;&middot;&nbsp; <?php echo intval($p['pct_min']); ?>&#8211;<?php echo intval($p['pct_max']); ?>% Rabatt möglich</div>
            </div>
        </label>
    <?php endforeach; ?>
    </div>
    <label style="display:block;font-size:13px;font-weight:600;color:#333;margin-bottom:6px;">Dein @ Handle</label>
    <input id="pa-deal-handle-inp" type="text" placeholder="z.B. @dein_account" style="width:100%;padding:10px 12px;border:1px solid #ccc;border-radius:4px;font-size:14px;box-sizing:border-box;margin-bottom:5px;">
    <p style="margin:0 0 16px;font-size:11px;color:#aaa;">Wir überprüfen deinen Post und senden dir dann per E-Mail einen persönlichen Rabattcode.</p>
    <div id="pa-deal-err" style="display:none;padding:8px 12px;background:#fff0f0;border-radius:4px;color:#c00;font-size:13px;margin-bottom:12px;"></div>
    <div id="pa-deal-ok" style="display:none;padding:14px;background:#f0fff4;border-radius:6px;text-align:center;color:#1a5c30;font-weight:600;margin-bottom:12px;">Vielen Dank! Wir melden uns per E-Mail sobald wir deinen Post geprüft haben.</div>
    <button id="pa-deal-submit" onclick="paSubmitDeal()" style="width:100%;padding:12px;background:#2d6a4f;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:14px;font-weight:600;">Einreichen</button>
</div>
</div>

<script>
if (typeof _paDealNonce === 'undefined') {
    var _paDealNonce = '<?php echo esc_js($nonce); ?>';
    var _paDealAjax  = '<?php echo esc_js($ajax); ?>';
    var _paDealOrdId = <?php echo intval($order_id); ?>;

    document.querySelectorAll('.pa-plat-lbl').forEach(function(lbl) {
        lbl.querySelector('input').addEventListener('change', function() {
            document.querySelectorAll('.pa-plat-lbl').forEach(function(l) { l.style.borderColor = '#e5e5e5'; });
            lbl.style.borderColor = '#2d6a4f';
        });
    });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') paCloseDeal(); });

    function paOpenDeal(ordId) {
        _paDealOrdId = ordId;
        document.getElementById('pa-deal-err').style.display    = 'none';
        document.getElementById('pa-deal-ok').style.display     = 'none';
        document.getElementById('pa-deal-submit').style.display = 'block';
        document.getElementById('pa-deal-submit').disabled      = false;
        document.getElementById('pa-deal-submit').textContent   = 'Einreichen';
        document.getElementById('pa-deal-handle-inp').value     = '';
        document.querySelectorAll('input[name="pa_platform"]').forEach(function(r) { r.checked = false; });
        document.querySelectorAll('.pa-plat-lbl').forEach(function(l) { l.style.borderColor = '#e5e5e5'; });
        document.getElementById('pa-deal-overlay').style.display = 'flex';
    }
    function paCloseDeal() { document.getElementById('pa-deal-overlay').style.display = 'none'; }
    function paSubmitDeal() {
        var plat = document.querySelector('input[name="pa_platform"]:checked');
        var hdl  = document.getElementById('pa-deal-handle-inp').value.trim();
        var err  = document.getElementById('pa-deal-err');
        if (!plat) { err.textContent = 'Bitte eine Plattform wählen.';  err.style.display = 'block'; return; }
        if (!hdl)  { err.textContent = 'Bitte deinen Handle eingeben.'; err.style.display = 'block'; return; }
        err.style.display = 'none';
        var btn = document.getElementById('pa-deal-submit');
        btn.disabled = true; btn.textContent = 'Wird eingereicht…';
        fetch(_paDealAjax, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=pa_submit_deal&nonce=' + encodeURIComponent(_paDealNonce)
                + '&order_id=' + _paDealOrdId
                + '&platform=' + encodeURIComponent(plat.value)
                + '&handle='   + encodeURIComponent(hdl)
        })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            if (d.success) {
                document.getElementById('pa-deal-ok').style.display = 'block';
                btn.style.display = 'none';
                var banner = document.querySelector('.pa-deal-banner');
                if (banner) banner.innerHTML = '<span style="color:#1a5c30;font-weight:600;">Deal eingereicht! Du erhältst deinen Code per E-Mail sobald wir deinen Post geprüft haben.</span>';
            } else {
                err.textContent = d.data || 'Fehler beim Einreichen.';
                err.style.display = 'block';
                btn.disabled = false; btn.textContent = 'Einreichen';
            }
        })
        .catch(function() {
            err.textContent = 'Netzwerkfehler.';
            err.style.display = 'block';
            btn.disabled = false; btn.textContent = 'Einreichen';
        });
    }
}
</script>
    <?php
    return ob_get_clean();
}

// Show deal section on order-received page
add_action('woocommerce_thankyou', 'pa_thankyou_deal_section', 20);
function pa_thankyou_deal_section(int $order_id) {
    if (!is_user_logged_in()) return;
    $order = wc_get_order($order_id);
    if (!$order || (int) $order->get_customer_id() !== get_current_user_id()) return;

    global $wpdb;
    $table    = $wpdb->prefix . 'pa_social_deals';
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM {$table} WHERE order_id = %d AND status != 'rejected'", $order_id
    ));
    if ($existing) return;

    $platforms = array_values(array_filter(
        get_option('_pa_sm_platforms', []),
        function($p) { return !empty($p['active']); }
    ));
    if (empty($platforms)) return;

    echo pa_deal_popup_html($order_id, $platforms, wp_create_nonce('pa_deal_nonce'), admin_url('admin-ajax.php'));
}

// Add "Rabatt Deal" action button to My Account orders
add_filter('woocommerce_my_account_my_orders_actions', 'pa_account_deal_action', 10, 2);
function pa_account_deal_action(array $actions, $order): array {
    if (!is_user_logged_in()) return $actions;
    if ((int) $order->get_customer_id() !== get_current_user_id()) return $actions;

    $platforms = array_filter(get_option('_pa_sm_platforms', []), function($p) { return !empty($p['active']); });
    if (empty($platforms)) return $actions;

    global $wpdb;
    $table = $wpdb->prefix . 'pa_social_deals';
    $deal  = $wpdb->get_row($wpdb->prepare(
        "SELECT id, status FROM {$table} WHERE order_id = %d AND status != 'rejected' LIMIT 1",
        $order->get_id()
    ));

    if ($deal) {
        $labels = ['pending' => 'Deal: Ausstehend', 'approved' => 'Deal: Genehmigt'];
        $actions['pa_deal'] = [
            'url'    => '#',
            'name'   => $labels[$deal->status] ?? 'Deal: Eingereicht',
            'action' => 'pa-deal-status',
        ];
        return $actions;
    }

    $actions['pa_deal'] = [
        'url'    => '#pa-deal-' . $order->get_id(),
        'name'   => 'Rabatt Deal',
        'action' => 'pa-deal',
    ];
    return $actions;
}

// ═══════════════════════════════════════════════════════════════════════
// 10.2 PRODUKT VARIANTEN
// ═══════════════════════════════════════════════════════════════════════

// Display variant switcher on single product page
add_action('woocommerce_single_product_summary', 'pa_product_variant_switcher', 25);
function pa_product_variant_switcher(): void {
    global $product;
    if (!$product) return;
    $product_id = $product->get_id();
    $parent_id  = (int) get_post_meta($product_id, '_pa_variant_parent', true);

    // Determine group root
    if ($parent_id) {
        $root = $parent_id;
    } else {
        $child_check = get_posts([
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'meta_key'       => '_pa_variant_parent',
            'meta_value'     => $product_id,
            'posts_per_page' => 1,
            'fields'         => 'ids',
        ]);
        if (empty($child_check)) return;
        $root = $product_id;
    }

    // Collect all products in variant group (root + children)
    $siblings = get_posts([
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'meta_key'       => '_pa_variant_parent',
        'meta_value'     => $root,
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]);
    $all_ids = array_unique(array_merge([$root], array_map('intval', $siblings)));
    if (count($all_ids) <= 1) return;

    // Collect variable tags (format "Type: Value") per product
    $variant_data  = [];
    $tag_type_set  = [];
    foreach ($all_ids as $pid) {
        $tags = get_the_terms($pid, 'product_tag');
        if (!$tags || is_wp_error($tags)) { $variant_data[$pid] = []; continue; }
        $vars = [];
        foreach ($tags as $tag) {
            if (strpos($tag->name, ':') !== false) {
                [$type, $val] = explode(':', $tag->name, 2);
                $type = trim($type); $val = trim($val);
                $vars[$type] = $val;
                $tag_type_set[$type] = true;
            }
        }
        $variant_data[$pid] = $vars;
    }
    if (empty($tag_type_set)) return;

    // Build options per type: type => [value => [pids]]
    $opts_by_type = [];
    foreach ($variant_data as $pid => $vars) {
        foreach ($vars as $type => $val) {
            $opts_by_type[$type][$val][] = $pid;
        }
    }

    // Current selections
    $current_vars = $variant_data[$product_id] ?? [];

    echo '<div class="pa-variant-switcher">';
    foreach ($opts_by_type as $type => $opts) {
        if (count($opts) <= 1) continue;
        echo '<div class="pa-variant-group">';
        echo '<div class="pa-variant-label">' . esc_html($type) . '</div>';
        echo '<div class="pa-variant-options">';
        foreach ($opts as $val => $pids) {
            $is_active = isset($current_vars[$type]) && $current_vars[$type] === $val;
            // Find best match: product that has this $val for $type and matches current selections for other types
            $best_pid = null;
            foreach ($pids as $candidate) {
                $candidate_vars = $variant_data[$candidate] ?? [];
                $match = true;
                foreach ($current_vars as $ot => $ov) {
                    if ($ot === $type) continue;
                    if (isset($candidate_vars[$ot]) && $candidate_vars[$ot] !== $ov) {
                        $match = false; break;
                    }
                }
                if ($match) { $best_pid = $candidate; break; }
            }
            $best_pid = $best_pid ?: $pids[0];
            $in_stock = get_post_meta($best_pid, '_stock_status', true) !== 'outofstock';
            $cls = 'pa-variant-btn' . ($is_active ? ' active' : '') . (!$in_stock ? ' sold-out' : '');
            echo '<a href="' . esc_url(get_permalink($best_pid)) . '" class="' . $cls . '">' . esc_html($val) . '</a>';
        }
        echo '</div></div>';
    }
    echo '</div>';
}

// AJAX: product search (for variant parent picker)
add_action('wp_ajax_pa_search_products', 'pa_search_products_ajax');
function pa_search_products_ajax(): void {
    check_ajax_referer('product_list_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Forbidden');
    $q = sanitize_text_field($_POST['q'] ?? '');
    if (strlen($q) < 2) wp_send_json_success([]);
    $posts = get_posts([
        'post_type'      => 'product',
        'post_status'    => 'publish',
        's'              => $q,
        'posts_per_page' => 15,
        'fields'         => 'all',
    ]);
    $results = [];
    foreach ($posts as $post) {
        $p = wc_get_product($post->ID);
        $results[] = [
            'id'   => $post->ID,
            'name' => $post->post_title,
            'sku'  => $p ? $p->get_sku() : '',
        ];
    }
    wp_send_json_success($results);
}

// AJAX: set variant parent from admin edit popup
add_action('wp_ajax_pa_set_variant_parent', 'pa_set_variant_parent_ajax');
function pa_set_variant_parent_ajax(): void {
    check_ajax_referer('product_list_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Forbidden');
    $product_id = (int) ($_POST['product_id'] ?? 0);
    $parent_id  = (int) ($_POST['parent_id']  ?? 0);
    if (!$product_id) wp_send_json_error('Invalid product');
    if ($parent_id) {
        update_post_meta($product_id, '_pa_variant_parent', $parent_id);
    } else {
        delete_post_meta($product_id, '_pa_variant_parent');
    }
    wp_send_json_success();
}

// ═══════════════════════════════════════════════════════════════════════
// 13.1 / 13.2  SHARED CAROUSEL HELPER
// ═══════════════════════════════════════════════════════════════════════

if (!function_exists('pa_render_carousel')) {
    function pa_render_carousel(array $products, string $title): void {
        if (empty($products)) return;
        static $carousel_id = 0;
        $carousel_id++;
        $cid = 'pa-cr-' . $carousel_id;
        ?>
        <section class="pa-carousel-section">
          <div class="pa-carousel-header">
            <h2 class="pa-carousel-title"><?php echo esc_html($title); ?></h2>
            <div class="pa-carousel-nav">
              <button class="pa-carousel-prev" aria-label="Zurück" onclick="paCrScroll('<?php echo $cid; ?>',-1)">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
              </button>
              <button class="pa-carousel-next" aria-label="Vor" onclick="paCrScroll('<?php echo $cid; ?>',1)">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
              </button>
            </div>
          </div>
          <div class="pa-carousel-track" id="<?php echo $cid; ?>">
            <?php foreach ($products as $product) :
                $pid     = $product->get_id();
                $img_url = get_the_post_thumbnail_url($pid, 'woocommerce_single') ?: '';
                $price   = $product->get_price_html();
                $on_sale = $product->is_on_sale();
            ?>
              <div class="pa-carousel-item">
                <a href="<?php echo esc_url($product->get_permalink()); ?>" class="pa-carousel-link">
                  <div class="pa-carousel-img" style="background-image: url('<?php echo esc_url($img_url); ?>')">
                    <?php if ($on_sale) : ?><span class="pa-badge">Sale</span><?php endif; ?>
                  </div>
                  <h3 class="pa-carousel-name"><?php echo esc_html($product->get_name()); ?></h3>
                  <div class="pa-carousel-price"><?php echo $price; ?></div>
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        </section>
        <?php
    }
}

// Enqueue carousel JS once per page
add_action('wp_footer', 'pa_enqueue_carousel_js');
function pa_enqueue_carousel_js(): void {
    if (!did_action('pa_carousel_js_done')) {
        ?>
        <script>
        function paCrScroll(id, dir) {
            var track = document.getElementById(id);
            if (!track) return;
            var item = track.querySelector('.pa-carousel-item');
            var step = item ? item.offsetWidth + 16 : 220;
            track.scrollBy({ left: dir * step * 3, behavior: 'smooth' });
        }
        </script>
        <?php
        do_action('pa_carousel_js_done');
    }
}

// ═══════════════════════════════════════════════════════════════════════
// 7.1 / 8.3 NEWSLETTER BACKEND
// ═══════════════════════════════════════════════════════════════════════

// Recipient count
add_action('wp_ajax_get_newsletter_recipient_count', 'pa_newsletter_recipient_count_ajax');
function pa_newsletter_recipient_count_ajax(): void {
    check_ajax_referer('newsletter_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Forbidden');
    $sub_only = !empty($_POST['subscribers_only']);
    if ($sub_only) {
        $users = get_users(['meta_key' => '_pa_newsletter_pref', 'meta_value' => 'yes']);
    } else {
        // All registered users with a verified email
        $users = get_users(['fields' => ['user_email']]);
    }
    wp_send_json_success(['count' => count($users)]);
}

// Product categorization for sidebar
add_action('wp_ajax_get_newsletter_products', 'pa_newsletter_products_ajax');
function pa_newsletter_products_ajax(): void {
    check_ajax_referer('newsletter_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Forbidden');

    global $wpdb;
    $categories = [];

    // Helper to build product data array
    $build_product_data = function(int $pid): array {
        $p = wc_get_product($pid);
        if (!$p) return [];
        $img  = get_the_post_thumbnail_url($pid, 'thumbnail') ?: '';
        $reg  = (float) $p->get_regular_price();
        $sale = (float) $p->get_sale_price();
        $disc = ($reg > 0 && $sale > 0 && $sale < $reg)
                ? '-' . round((($reg - $sale) / $reg) * 100) . '%' : '';
        return [
            'product_id'  => $pid,
            'name'        => $p->get_name(),
            'excerpt'     => wp_strip_all_tags(wp_trim_words($p->get_short_description() ?: $p->get_description(), 14, '…')),
            'price'       => $reg ?: $sale,
            'sale_price'  => $sale && $sale < $reg ? $sale : null,
            'discount'    => $disc,
            'image_url'   => $img,
            'product_url' => get_permalink($pid),
        ];
    };

    // ── 1. Sale groups ─────────────────────────────────────────────
    $sg_table = $wpdb->prefix . 'pa_sale_groups';
    $sg_exists = $wpdb->get_var("SHOW TABLES LIKE '{$sg_table}'") === $sg_table;
    $sg_items  = [];
    $sg_pids   = []; // track products already in sale groups
    if ($sg_exists) {
        $groups = $wpdb->get_results("SELECT * FROM {$sg_table} WHERE status='active' ORDER BY id DESC", ARRAY_A);
        foreach ($groups as $g) {
            $pids = !empty($g['product_ids']) ? array_filter(array_map('intval', explode(',', $g['product_ids']))) : [];
            $prods = array_values(array_filter(array_map($build_product_data, $pids)));
            if (empty($prods)) continue;
            $sg_pids = array_merge($sg_pids, $pids);
            $disc = '';
            if (!empty($g['discount_pct'])) $disc = '-' . $g['discount_pct'] . '%';
            $sg_items[] = [
                'key'           => 'sg_' . $g['id'],
                'label'         => $g['group_name'] ?? ('Sale-Gruppe #' . $g['id']),
                'sub'           => $disc,
                'type'          => 'sale_group',
                'thumb'         => '',
                'product_count' => count($prods),
                'block_data'    => [
                    'type' => 'sale_group',
                    'data' => [
                        'title'    => $g['group_name'] ?? 'Sale',
                        'discount' => $disc,
                        'products' => $prods,
                    ],
                ],
            ];
        }
    }
    if (!empty($sg_items)) {
        $categories[] = ['key' => 'sale_groups', 'label' => 'Sale Gruppen', 'items' => $sg_items];
    }

    // ── 2. Individual sale products (not in any sale group) ────────
    $on_sale_ids = array_diff(array_filter(array_map('intval', wc_get_product_ids_on_sale())), $sg_pids);
    $sale_items  = [];
    foreach (array_slice($on_sale_ids, 0, 40) as $pid) {
        $d = $build_product_data($pid);
        if (empty($d)) continue;
        $sale_items[] = [
            'key'        => 'sale_' . $pid,
            'label'      => $d['name'],
            'sub'        => $d['discount'] ?: '',
            'type'       => 'product',
            'thumb'      => $d['image_url'],
            'block_data' => ['type' => 'product', 'data' => $d],
        ];
    }
    if (!empty($sale_items)) {
        $categories[] = ['key' => 'angebote', 'label' => 'Angebote', 'items' => $sale_items];
    }

    // ── 3. New products (added in last 60 days) ────────────────────
    $new_products = get_posts([
        'post_type'   => 'product',
        'post_status' => 'publish',
        'orderby'     => 'date',
        'order'       => 'DESC',
        'numberposts' => 30,
        'date_query'  => [['after' => '60 days ago']],
        'fields'      => 'ids',
    ]);
    $new_items = [];
    foreach ($new_products as $pid) {
        $d = $build_product_data((int)$pid);
        if (empty($d)) continue;
        $new_items[] = [
            'key'        => 'new_' . $pid,
            'label'      => $d['name'],
            'sub'        => 'Neu',
            'type'       => 'product',
            'thumb'      => $d['image_url'],
            'block_data' => ['type' => 'product', 'data' => $d],
        ];
    }
    if (!empty($new_items)) {
        $categories[] = ['key' => 'neue_produkte', 'label' => 'Neue Produkte', 'items' => $new_items];
    }

    // ── 4. All products by WooCommerce category ────────────────────
    $wc_cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true, 'orderby' => 'name']);
    if (!is_wp_error($wc_cats)) {
        foreach ($wc_cats as $cat) {
            if ($cat->slug === 'uncategorized') continue;
            $cat_ids = get_posts([
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'tax_query'      => [['taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $cat->term_id]],
                'numberposts'    => 50,
                'orderby'        => 'title',
                'order'          => 'ASC',
                'fields'         => 'ids',
            ]);
            $cat_items = [];
            foreach ($cat_ids as $pid) {
                $d = $build_product_data((int)$pid);
                if (empty($d)) continue;
                $cat_items[] = [
                    'key'        => 'p_' . $pid,
                    'label'      => $d['name'],
                    'sub'        => $d['sale_price'] ? ('Angebot ' . ($d['discount'] ?: '')) : '',
                    'type'       => 'product',
                    'thumb'      => $d['image_url'],
                    'block_data' => ['type' => 'product', 'data' => $d],
                ];
            }
            if (!empty($cat_items)) {
                $categories[] = ['key' => 'cat_' . $cat->term_id, 'label' => $cat->name, 'items' => $cat_items];
            }
        }
    }

    // ── 5. Uncategorized / all remaining products ──────────────────
    $all_ids = get_posts(['post_type' => 'product', 'post_status' => 'publish', 'numberposts' => 100, 'fields' => 'ids', 'orderby' => 'title', 'order' => 'ASC']);
    $shown_pids = [];
    foreach ($categories as $c) {
        foreach ($c['items'] as $it) {
            preg_match('/\d+$/', $it['key'], $m);
            if ($m) $shown_pids[] = (int)$m[0];
        }
    }
    $misc_items = [];
    foreach ($all_ids as $pid) {
        if (in_array((int)$pid, $shown_pids, true)) continue;
        $d = $build_product_data((int)$pid);
        if (empty($d)) continue;
        $misc_items[] = [
            'key'        => 'p_' . $pid,
            'label'      => $d['name'],
            'sub'        => '',
            'type'       => 'product',
            'thumb'      => $d['image_url'],
            'block_data' => ['type' => 'product', 'data' => $d],
        ];
    }
    if (!empty($misc_items)) {
        $categories[] = ['key' => 'alle_produkte', 'label' => 'Alle Produkte', 'items' => $misc_items];
    }

    // ── 6. Restocked categories ────────────────────────────────────
    $restock_cutoff = date('Y-m-d H:i:s', strtotime('-30 days'));

    foreach ([
        ['meta' => '_pa_restocked_from_empty', 'label' => 'Restocked (leer → verfügbar)'],
        ['meta' => '_pa_restocked_from_low',   'label' => 'Restocked (niedrig → normal)'],
        ['meta' => '_pa_restocked_at',         'label' => 'Restocked'],
    ] as $rs) {
        $rs_posts = get_posts([
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => 30,
            'meta_query'     => [[
                'key'     => $rs['meta'],
                'value'   => $restock_cutoff,
                'compare' => '>=',
                'type'    => 'DATETIME',
            ]],
            'fields' => 'ids',
        ]);
        $rs_items = [];
        foreach ($rs_posts as $pid) {
            $d = $build_product_data((int)$pid);
            if (empty($d)) continue;
            $rs_items[] = [
                'key'        => 'rs_' . $pid,
                'label'      => $d['name'],
                'sub'        => 'Restocked ' . date('d.m.', strtotime(get_post_meta((int)$pid, $rs['meta'], true))),
                'type'       => 'product',
                'thumb'      => $d['image_url'],
                'block_data' => ['type' => 'product', 'data' => $d],
            ];
        }
        if (!empty($rs_items)) {
            $categories[] = ['key' => sanitize_key($rs['meta']), 'label' => $rs['label'], 'items' => $rs_items];
        }
    }

    wp_send_json_success($categories);
}

// Newsletter unsubscribe token + handler
function pa_nl_unsub_token(int $user_id, string $email): string {
    return hash_hmac('sha256', $user_id . '|' . $email, wp_salt('auth'));
}

add_action('init', function () {
    $token  = sanitize_text_field($_GET['pa_nl_unsub'] ?? '');
    $uid    = absint($_GET['uid'] ?? 0);
    if (!$token || !$uid) return;

    $user = get_userdata($uid);
    if (!$user) wp_die('Ungültiger Abmeldelink.', '', ['response' => 400]);
    if (!hash_equals(pa_nl_unsub_token($uid, $user->user_email), $token)) {
        wp_die('Ungültiger oder abgelaufener Abmeldelink.', '', ['response' => 403]);
    }

    update_user_meta($uid, '_pa_newsletter_pref', 'no');
    wp_die(
        '<p style="font-family:sans-serif;padding:40px;">Du wurdest erfolgreich vom Newsletter abgemeldet. '
        . '<a href="' . esc_url(home_url('/')) . '">Zurück zum Shop</a></p>',
        'Abgemeldet',
        ['response' => 200]
    );
});

// Send newsletter
add_action('wp_ajax_send_newsletter', 'pa_send_newsletter_ajax');
function pa_send_newsletter_ajax(): void {
    check_ajax_referer('newsletter_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Forbidden');

    $subject        = sanitize_text_field($_POST['subject'] ?? '');
    $sub_only       = !empty($_POST['subscribers_only']);
    $blocks_json    = stripslashes($_POST['blocks'] ?? '[]');
    $blocks         = json_decode($blocks_json, true);

    if (!$subject) wp_send_json_error('Kein Betreff angegeben');
    if (!is_array($blocks)) wp_send_json_error('Ungültige Blöcke');

    // Get recipients
    if ($sub_only) {
        $users = get_users(['meta_key' => '_pa_newsletter_pref', 'meta_value' => 'yes', 'fields' => 'all']);
    } else {
        $users = get_users(['fields' => 'all']);
    }

    $sent = 0; $failed = 0;
    $headers = ['Content-Type: text/html; charset=UTF-8'];
    foreach ($users as $user) {
        if (empty($user->user_email)) continue;
        $unsub_url = add_query_arg([
            'pa_nl_unsub' => pa_nl_unsub_token($user->ID, $user->user_email),
            'uid'         => $user->ID,
        ], home_url('/'));
        $html   = pa_newsletter_blocks_to_html($blocks, $unsub_url);
        $result = wp_mail($user->user_email, $subject, $html, $headers);
        $result ? $sent++ : $failed++;
    }

    wp_send_json_success(['sent' => $sent, 'failed' => $failed]);
}

function pa_newsletter_blocks_to_html(array $blocks, string $unsubscribe_url = ''): string {
    $site_name = get_bloginfo('name');
    $home_url  = home_url('/');
    $logo_url  = content_url('uploads/2022/01/Logo-Plantaphilia-1.svg');

    $body = '';
    foreach ($blocks as $block) {
        $type = $block['type'] ?? '';
        if ($type === 'text') {
            $content = wp_kses_post($block['content'] ?? '');
            $body .= '<div style="padding:0 0 16px;font-family:Arial,sans-serif;font-size:15px;line-height:1.7;color:#333;">' . $content . '</div>';
        } elseif ($type === 'product') {
            $d    = $block['data'] ?? [];
            $name = esc_html($d['name'] ?? '');
            $exc  = esc_html($d['excerpt'] ?? '');
            $url  = esc_url($d['product_url'] ?? '');
            $img  = esc_url($d['image_url'] ?? '');
            $reg  = number_format((float)($d['price'] ?? 0), 2, ',', '.');
            $sale = isset($d['sale_price']) ? number_format((float)$d['sale_price'], 2, ',', '.') : null;
            $price_html = $sale ? "<s style='color:#999;'>{$reg}&nbsp;€</s> <strong style='color:#c03;'>{$sale}&nbsp;€</strong>" : "{$reg}&nbsp;€";
            $img_part = $img ? "<img src='{$img}' width='90' height='90' style='width:90px;height:90px;object-fit:cover;float:left;margin-right:14px;border-radius:4px;' alt=''>" : '';
            $body .= "<table width='100%' cellpadding='0' cellspacing='0' style='margin-bottom:16px;background:#f9f9f9;border-radius:6px;padding:14px;'><tr><td>{$img_part}<strong style='font-size:16px;color:#222;'>{$name}</strong><br><span style='color:#666;font-size:13px;'>{$exc}</span><br><span style='font-size:15px;'>{$price_html}</span><br><a href='{$url}' style='display:inline-block;margin-top:8px;padding:8px 18px;background:#6b2f5c;color:#fff;text-decoration:none;border-radius:4px;font-size:13px;'>Jetzt ansehen →</a></td></tr></table>";
        } elseif ($type === 'sale' || $type === 'sale_group') {
            $d     = $block['data'] ?? [];
            $title = esc_html($d['title'] ?? $d['sale_title'] ?? 'Sale');
            $disc  = esc_html($d['discount'] ?? '');
            $prods = $d['products'] ?? [];
            $cards = '';
            foreach ($prods as $p) {
                $img  = esc_url($p['image_url'] ?? '');
                $pname = esc_html($p['name'] ?? '');
                $preg  = number_format((float)($p['price'] ?? 0), 2, ',', '.');
                $psale = isset($p['sale_price']) ? number_format((float)$p['sale_price'], 2, ',', '.') : null;
                $pprice = $psale ? "<s style='color:#999;font-size:11px;'>{$preg}&nbsp;€</s><br><strong style='color:#c03;'>{$psale}&nbsp;€</strong>" : "{$preg}&nbsp;€";
                $img_part = $img ? "<img src='{$img}' width='130' height='130' style='width:130px;height:130px;object-fit:cover;' alt=''>" : '<div style="width:130px;height:130px;background:#eee;"></div>';
                $cards .= "<td valign='top' style='width:140px;padding:4px;'><table width='130' cellpadding='0' cellspacing='0' style='border:1px solid #e0e0e0;border-radius:4px;overflow:hidden;'><tr><td>{$img_part}</td></tr><tr><td style='padding:8px;font-size:12px;'><strong>{$pname}</strong><br>{$pprice}</td></tr></table></td>";
            }
            $body .= "<div style='margin-bottom:16px;background:#f9f9f9;border-radius:6px;padding:14px;'><strong style='font-size:16px;'>🏷 {$title}</strong>" . ($disc ? " <span style='background:#c03;color:#fff;padding:2px 7px;border-radius:10px;font-size:12px;'>{$disc}</span>" : '') . "</div><table cellpadding='0' cellspacing='0' style='margin-bottom:16px;'><tr>{$cards}</tr></table>";
        }
    }

    $privacy_url  = esc_url(home_url('/datenschutzerklaerung/'));
    $unsub_link   = $unsubscribe_url
        ? "<br><a href='" . esc_url($unsubscribe_url) . "' style='color:#aaa;'>Newsletter abbestellen</a>"
        : '';

    return "<!DOCTYPE html><html><head><meta charset='UTF-8'></head><body style='margin:0;padding:0;background:#f0f0f0;'>"
        . "<table width='100%' cellpadding='0' cellspacing='0' bgcolor='#f0f0f0'><tr><td align='center' style='padding:30px 10px;'>"
        . "<table width='600' cellpadding='0' cellspacing='0' style='background:#fff;border-radius:8px;overflow:hidden;'>"
        . "<tr><td style='background:#0f2419;padding:24px 30px;text-align:center;'>"
        . "<img src='{$logo_url}' height='40' alt='{$site_name}' style='height:40px;'>"
        . "</td></tr>"
        . "<tr><td style='padding:30px;font-family:Arial,sans-serif;'>{$body}</td></tr>"
        . "<tr><td style='background:#143020;padding:20px 30px;text-align:center;font-family:Arial,sans-serif;font-size:12px;color:#ccc;'>"
        . "© " . date('Y') . " {$site_name} · <a href='{$home_url}' style='color:#ccc;'>{$home_url}</a>"
        . "<br><a href='{$privacy_url}' style='color:#aaa;'>Datenschutzerklärung</a>"
        . $unsub_link
        . "</td></tr></table></td></tr></table></body></html>";
}

// ═══════════════════════════════════════════════════════════════════════
// 8.3 EXCEL PARSER (XLSX)
// ═══════════════════════════════════════════════════════════════════════

add_action('wp_ajax_pa_parse_excel', 'pa_parse_excel_ajax');
function pa_parse_excel_ajax(): void {
    check_ajax_referer('add_product_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Forbidden');
    if (empty($_FILES['excel_file'])) wp_send_json_error('Keine Datei hochgeladen');
    $file = $_FILES['excel_file'];
    if ($file['error'] !== UPLOAD_ERR_OK) wp_send_json_error('Upload-Fehler: ' . $file['error']);
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext === 'xls') {
        wp_send_json_error('Altes XLS-Format nicht unterstützt. Bitte als XLSX oder CSV speichern.');
    }
    if ($ext !== 'xlsx') {
        wp_send_json_error('Ungültiges Dateiformat. Nur XLSX wird unterstützt.');
    }
    $result = pa_parse_xlsx($file['tmp_name']);
    if (is_wp_error($result)) {
        wp_send_json_error($result->get_error_message());
    }
    wp_send_json_success($result);
}

function pa_parse_xlsx(string $filepath): array|WP_Error {
    if (!class_exists('ZipArchive')) {
        return new WP_Error('no_zip', 'ZipArchive PHP-Erweiterung nicht verfügbar');
    }
    $zip = new ZipArchive();
    if ($zip->open($filepath) !== true) {
        return new WP_Error('zip_open', 'XLSX-Datei konnte nicht geöffnet werden');
    }
    // Parse shared strings table
    $shared = [];
    $ssRaw  = $zip->getFromName('xl/sharedStrings.xml');
    if ($ssRaw) {
        $ss = @simplexml_load_string($ssRaw);
        if ($ss) {
            foreach ($ss->si as $si) {
                if (isset($si->t)) {
                    $shared[] = (string) $si->t;
                } else {
                    $text = '';
                    foreach ($si->r as $r) { $text .= (string) $r->t; }
                    $shared[] = $text;
                }
            }
        }
    }
    // Parse first worksheet
    $wsRaw = $zip->getFromName('xl/worksheets/sheet1.xml');
    $zip->close();
    if (!$wsRaw) {
        return new WP_Error('no_sheet', 'Kein Arbeitsblatt in der XLSX-Datei gefunden');
    }
    $ws = @simplexml_load_string($wsRaw);
    if (!$ws) {
        return new WP_Error('xml_error', 'Arbeitsblatt-XML konnte nicht geparst werden');
    }
    // Convert column letters to zero-based index (e.g. A=0, Z=25, AA=26)
    $colIndex = function(string $letters): int {
        $n = 0;
        foreach (str_split(strtoupper($letters)) as $ch) {
            $n = $n * 26 + (ord($ch) - 64);
        }
        return $n - 1;
    };
    $rows = [];
    foreach ($ws->sheetData->row as $rowNode) {
        $cells = [];
        foreach ($rowNode->c as $cell) {
            $ref  = (string) ($cell['r'] ?? '');
            $type = (string) ($cell['t'] ?? '');
            preg_match('/^([A-Z]+)/', $ref, $m);
            $col = isset($m[1]) ? $colIndex($m[1]) : 0;
            if ($type === 's') {
                $val = $shared[(int)(string)($cell->v ?? 0)] ?? '';
            } elseif ($type === 'inlineStr') {
                $val = (string) ($cell->is->t ?? '');
            } else {
                $val = (string) ($cell->v ?? '');
            }
            $cells[$col] = $val;
        }
        if (!empty($cells)) {
            $maxCol = max(array_keys($cells));
            $row = [];
            for ($i = 0; $i <= $maxCol; $i++) { $row[] = $cells[$i] ?? ''; }
            $rows[] = $row;
        }
    }
    if (empty($rows)) {
        return new WP_Error('empty_sheet', 'Keine Daten im Arbeitsblatt gefunden');
    }
    $headers = array_map('strval', $rows[0]);
    $data    = [];
    for ($i = 2; $i < count($rows); $i++) { // row[1] = description row in new template → skip
        $obj = [];
        foreach ($headers as $j => $h) { $obj[$h] = $rows[$i][$j] ?? ''; }
        if (!empty(array_filter($obj, 'strlen'))) { $data[] = $obj; }
    }
    return ['headers' => $headers, 'rows' => $data];
}

// ═══════════════════════════════════════════════════════════════════════
// 8.4 XLSX TEMPLATE GENERATOR
// ═══════════════════════════════════════════════════════════════════════

add_action('wp_ajax_pa_download_template_xlsx', 'pa_download_template_xlsx_ajax');
function pa_download_template_xlsx_ajax(): void {
    check_ajax_referer('add_product_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_die('Unauthorized');
    $xlsx = pa_generate_template_xlsx();
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="plantaphilia_produkt_vorlage.xlsx"');
    header('Content-Length: ' . strlen($xlsx));
    header('Cache-Control: no-cache, no-store');
    echo $xlsx;
    exit;
}

function pa_generate_template_xlsx(): string {
    $cols = [
        ['name' => 'Name',                       'desc' => "Produktname (auto-generiert aus Gattung+Art+Kultivar) — nur zur Orientierung",            'ex' => "Pelargonium zonale 'Voodoo'",  'num' => false],
        ['name' => 'SKU',                        'desc' => 'Artikelnummer, z.B. PA-001',                                                               'ex' => 'PA-001',                      'num' => false],
        ['name' => 'Gattung',                    'desc' => 'z.B. Pelargonium — wird angelegt wenn noch nicht vorhanden',                              'ex' => 'Pelargonium',                 'num' => false],
        ['name' => 'Art',                        'desc' => 'z.B. zonale — optional',                                                                   'ex' => 'zonale',                      'num' => false],
        ['name' => 'Kultivar',                   'desc' => 'Sortenname ohne Anführungszeichen, z.B. Voodoo',                                          'ex' => 'Voodoo',                      'num' => false],
        ['name' => 'Preis',                      'desc' => 'Dezimalzahl mit Punkt, z.B. 12.90',                                                       'ex' => '12.90',                       'num' => true],
        ['name' => 'Bestand',                    'desc' => 'Ganzzahl, z.B. 5',                                                                         'ex' => '5',                           'num' => true],
        ['name' => 'Produkttyp',                 'desc' => 'Dropdown: Pflanze oder Substrat',                                                         'ex' => 'Pflanze',                     'num' => false],
        ['name' => 'Einheit',                    'desc' => 'Dropdown: Stueck oder Liter',                                                             'ex' => 'Stueck',                      'num' => false],
        ['name' => 'Liter',                      'desc' => 'Nur bei Einheit=Liter, Dezimalzahl',                                                      'ex' => '',                            'num' => false],
        ['name' => 'Steuerklasse',               'desc' => 'standard oder Steuerklassen-Slug',                                                        'ex' => 'standard',                    'num' => false],
        ['name' => 'Differenzbesteuerung',       'desc' => '0 = Nein, 1 = Ja — nur 0 oder 1 eingeben',                                              'ex' => '0',                           'num' => true],
        ['name' => 'Gewicht_kg',                 'desc' => 'Dezimalzahl',                                                                             'ex' => '0.5',                         'num' => true],
        ['name' => 'Laenge_cm',                  'desc' => 'Dezimalzahl',                                                                             'ex' => '15',                          'num' => true],
        ['name' => 'Breite_cm',                  'desc' => 'Dezimalzahl',                                                                             'ex' => '15',                          'num' => true],
        ['name' => 'Hoehe_cm',                   'desc' => 'Dezimalzahl',                                                                             'ex' => '30',                          'num' => true],
        ['name' => 'Versandlaenge_cm',           'desc' => 'Dezimalzahl — leer = gleich wie Produktmaß',                                             'ex' => '',                            'num' => false],
        ['name' => 'Versandbreite_cm',           'desc' => 'Dezimalzahl — leer = gleich wie Produktmaß',                                             'ex' => '',                            'num' => false],
        ['name' => 'Versandhoehe_cm',            'desc' => 'Dezimalzahl — leer = gleich wie Produktmaß',                                             'ex' => '',                            'num' => false],
        ['name' => 'Versandklasse',              'desc' => 'Name der Versandklasse, leer = keine',                                                    'ex' => '',                            'num' => false],
        ['name' => 'Lieferzeit_Tage',            'desc' => 'Ganzzahl',                                                                                'ex' => '7',                           'num' => true],
        ['name' => 'Schwellwert_Lagerbestand',   'desc' => 'Ganzzahl, leer = Standard 5',                                                            'ex' => '5',                           'num' => true],
        ['name' => 'Nie_geringer_Lagerbestand',  'desc' => '0 = Nein, 1 = Ja — nur 0 oder 1 eingeben',                                              'ex' => '0',                           'num' => true],
        ['name' => 'Kurzbeschreibung',           'desc' => 'Max. 160 Zeichen. Kein HTML.',                                                            'ex' => 'Leuchtendrote Blüten, buschiger Wuchs', 'num' => false],
        ['name' => 'Beschreibung',               'desc' => 'Fließtext. Zeilenumbruch = @@ — Fettdruck: **text**',                                    'ex' => 'Robuste Sorte mit tiefroten Blüten. @@Ideal für sonnige Standorte.', 'num' => false],
        ['name' => 'Tags',                       'desc' => 'Format: kategorie:wert,tag2 — Trennzeichen: , — Kategorie:Wert oder fester Tag',         'ex' => 'farbe:rot,standort:sonnig,Bestseller', 'num' => false],
        ['name' => 'Pflegelicht',                'desc' => 'z.B. Vollsonne, Halbschatten',                                                            'ex' => 'Vollsonne',                   'num' => false],
        ['name' => 'Pflegewasser',               'desc' => 'z.B. mäßig, regelmäßig',                                                                  'ex' => 'mäßig',                       'num' => false],
        ['name' => 'Pflegewinter',               'desc' => 'z.B. frostfrei, 5–10 °C',                                                                 'ex' => 'frostfrei',                   'num' => false],
        ['name' => 'PflegeTempMin',              'desc' => 'Ganzzahl (°C)',                                                                            'ex' => '5',                           'num' => true],
        ['name' => 'PflegeTempMax',              'desc' => 'Ganzzahl (°C)',                                                                            'ex' => '30',                          'num' => true],
    ];

    $colLtr = function(int $i): string {
        $s = ''; $i++;
        while ($i > 0) { $s = chr(65 + ($i-1) % 26) . $s; $i = (int)(($i-1) / 26); }
        return $s;
    };

    // Shared strings
    $strings = []; $strIdx = [];
    $addS = function(string $s) use (&$strings, &$strIdx): int {
        if (!isset($strIdx[$s])) { $strIdx[$s] = count($strings); $strings[] = $s; }
        return $strIdx[$s];
    };
    $hIdx = $dIdx = $eIdx = [];
    foreach ($cols as $c) { $hIdx[] = $addS($c['name']); $dIdx[] = $addS($c['desc']); $eIdx[] = ($c['ex'] !== '' && !$c['num']) ? $addS($c['ex']) : null; }

    // sharedStrings.xml
    $ssXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($strings) . '" uniqueCount="' . count($strings) . '">';
    foreach ($strings as $s) { $ssXml .= '<si><t xml:space="preserve">' . htmlspecialchars($s, ENT_XML1, 'UTF-8') . '</t></si>'; }
    $ssXml .= '</sst>';

    // styles.xml — xf[0]=default, xf[1]=header(bold+darkbg), xf[2]=desc(italic+gray)
    $stylesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><fonts count="3"><font><sz val="10"/><name val="Calibri"/></font><font><b/><sz val="10"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font><font><i/><sz val="9"/><color rgb="FF999999"/><name val="Calibri"/></font></fonts><fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF2D4739"/></patternFill></fill></fills><borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders><cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs><cellXfs count="3"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/><xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1"/></cellXfs></styleSheet>';

    // sheet1.xml
    $shXml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><cols>';
    foreach ($cols as $i => $c) {
        $w = max(12, min(36, strlen($c['name']) * 1.6));
        $shXml .= '<col min="' . ($i+1) . '" max="' . ($i+1) . '" width="' . $w . '" customWidth="1"/>';
    }
    $shXml .= '</cols><sheetData>';
    // Row 1: headers (style 1)
    $shXml .= '<row r="1">';
    foreach ($cols as $i => $c) { $shXml .= '<c r="' . $colLtr($i) . '1" t="s" s="1"><v>' . $hIdx[$i] . '</v></c>'; }
    $shXml .= '</row>';
    // Row 2: descriptions (style 2)
    $shXml .= '<row r="2">';
    foreach ($cols as $i => $c) { $shXml .= '<c r="' . $colLtr($i) . '2" t="s" s="2"><v>' . $dIdx[$i] . '</v></c>'; }
    $shXml .= '</row>';
    // Row 3: example data (style 0)
    $shXml .= '<row r="3">';
    foreach ($cols as $i => $c) {
        if ($c['ex'] === '') continue;
        if ($c['num']) {
            $shXml .= '<c r="' . $colLtr($i) . '3"><v>' . htmlspecialchars($c['ex'], ENT_XML1) . '</v></c>';
        } else {
            $shXml .= '<c r="' . $colLtr($i) . '3" t="s"><v>' . $eIdx[$i] . '</v></c>';
        }
    }
    $shXml .= '</row></sheetData>';
    // Data validations: H=dropdown Pflanze/Substrat, I=dropdown Stueck/Liter, L=0/1, W=0/1
    $h = $colLtr(7); $ic = $colLtr(8); $l = $colLtr(11); $w = $colLtr(22);
    $shXml .= '<dataValidations count="4">';
    $shXml .= '<dataValidation type="list" sqref="' . $h . '3:' . $h . '1048576" showDropDown="0" showErrorMessage="1" errorStyle="stop" errorTitle="Ungültig" error="Nur Pflanze oder Substrat"><formula1>&quot;Pflanze,Substrat&quot;</formula1></dataValidation>';
    $shXml .= '<dataValidation type="list" sqref="' . $ic . '3:' . $ic . '1048576" showDropDown="0" showErrorMessage="1" errorStyle="stop" errorTitle="Ungültig" error="Nur Stueck oder Liter"><formula1>&quot;Stueck,Liter&quot;</formula1></dataValidation>';
    $shXml .= '<dataValidation type="whole" operator="between" sqref="' . $l . '3:' . $l . '1048576" showErrorMessage="1" errorStyle="stop" errorTitle="Ungültig" error="Nur 0 oder 1 erlaubt"><formula1>0</formula1><formula2>1</formula2></dataValidation>';
    $shXml .= '<dataValidation type="whole" operator="between" sqref="' . $w . '3:' . $w . '1048576" showErrorMessage="1" errorStyle="stop" errorTitle="Ungültig" error="Nur 0 oder 1 erlaubt"><formula1>0</formula1><formula2>1</formula2></dataValidation>';
    $shXml .= '</dataValidations></worksheet>';

    $wbXml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Produkte" sheetId="1" r:id="rId1"/></sheets></workbook>';
    $relsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>';
    $wbRels  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/><Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>';
    $ctXml   = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/></Types>';

    $tmp = tempnam(sys_get_temp_dir(), 'pa_xlsx_');
    $zip = new ZipArchive();
    $zip->open($tmp, ZipArchive::OVERWRITE);
    $zip->addFromString('[Content_Types].xml',        $ctXml);
    $zip->addFromString('_rels/.rels',                $relsXml);
    $zip->addFromString('xl/workbook.xml',            $wbXml);
    $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);
    $zip->addFromString('xl/styles.xml',              $stylesXml);
    $zip->addFromString('xl/sharedStrings.xml',       $ssXml);
    $zip->addFromString('xl/worksheets/sheet1.xml',   $shXml);
    $zip->close();
    $content = file_get_contents($tmp);
    unlink($tmp);
    return $content;
}

// ═══════════════════════════════════════════════════════════════════════
// 2.1 PRODUKTBEWERTUNGEN
// ═══════════════════════════════════════════════════════════════════════

// Enforce: reviews on, rating required, verified purchasers only
function pa_return_yes(): string { return 'yes'; }
add_filter('pre_option_woocommerce_enable_reviews',                  'pa_return_yes');
add_filter('pre_option_woocommerce_review_rating_required',          'pa_return_yes');
add_filter('pre_option_woocommerce_enable_review_rating',            'pa_return_yes');
add_filter('pre_option_woocommerce_reviews_only_verified_purchasers','pa_return_yes');

// Customize review form defaults
add_filter('comment_form_defaults', 'pa_review_form_defaults');
function pa_review_form_defaults(array $defaults): array {
    if (!is_product()) return $defaults;
    $defaults['title_reply']          = 'Bewertung schreiben';
    $defaults['title_reply_before']   = '<h3 class="pa-review-form-title">';
    $defaults['title_reply_after']    = '</h3>';
    $defaults['label_submit']         = 'Bewertung absenden';
    $defaults['submit_button']        = '<button type="submit" name="%1$s" id="%2$s" class="pa-btn-filled %3$s">%4$s</button>';
    $defaults['comment_notes_before'] = '';
    $defaults['comment_notes_after']  = '';
    return $defaults;
}

// Remove website URL field from reviews
add_filter('comment_form_fields', 'pa_review_remove_url_field');
function pa_review_remove_url_field(array $fields): array {
    if (is_product()) unset($fields['url']);
    return $fields;
}

// Show login prompt instead of form for guests on product pages
add_filter('woocommerce_product_review_comment_form_args', 'pa_review_form_args');
function pa_review_form_args(array $args): array {
    if (!is_user_logged_in()) {
        $args['must_log_in'] = '<p class="pa-review-login-notice">Bitte <a href="' . esc_url(wc_get_page_permalink('myaccount')) . '">anmelden</a>, um eine Bewertung zu schreiben. Nur verifizierte Käufer können Bewertungen abgeben.</p>';
    }
    return $args;
}

// ═══════════════════════════════════════════════════════════════════════
// 2.2 NEWSLETTER POPUP BEI ANMELDUNG
// ═══════════════════════════════════════════════════════════════════════

// Set transient on login so footer knows to show popup this session
add_action('wp_login', 'pa_newsletter_popup_on_login', 10, 2);
function pa_newsletter_popup_on_login(string $user_login, WP_User $user): void {
    $pref = get_user_meta($user->ID, '_pa_newsletter_pref', true);
    // Only show if user hasn't made a choice yet
    if ($pref === '') {
        set_transient('pa_nl_popup_' . $user->ID, '1', 30 * MINUTE_IN_SECONDS);
    }
}

// Output popup in footer; fire JS to show it if transient is active
add_action('wp_footer', 'pa_newsletter_popup_footer');
function pa_newsletter_popup_footer(): void {
    if (!is_user_logged_in()) return;
    $user_id = get_current_user_id();
    $pref    = get_user_meta($user_id, '_pa_newsletter_pref', true);
    if ($pref !== '') return; // already decided
    $transient = get_transient('pa_nl_popup_' . $user_id);
    if (!$transient) return;
    $nonce = wp_create_nonce('pa_newsletter_pref_nonce');
    ?>
    <div id="pa-nl-overlay" style="display:none;position:fixed;inset:0;background:rgba(7,21,13,.75);z-index:9000;align-items:center;justify-content:center;">
      <div style="background:var(--bg-surface);border:1px solid var(--border-thin);max-width:420px;width:90%;padding:40px 36px;position:relative;">
        <button onclick="paNlClose()" aria-label="Schließen" style="position:absolute;top:14px;right:14px;background:none;border:none;color:var(--creme-muted);cursor:pointer;font-size:20px;line-height:1;">✕</button>
        <div style="font-family:var(--serif-display);font-size:22px;color:var(--creme);font-weight:400;margin-bottom:10px;">Newsletter abonnieren?</div>
        <p style="color:var(--creme-dim);font-size:14px;line-height:1.6;margin:0 0 28px;">Erhalten Sie Neuigkeiten zu Pflanzen, Angeboten und saisonalen Kollektionen direkt in Ihr Postfach.</p>
        <div style="display:flex;gap:12px;">
          <button onclick="paNlRespond('yes')" class="pa-btn-filled" style="flex:1;">Ja, gerne</button>
          <button onclick="paNlRespond('no')" class="pa-btn-outline" style="flex:1;">Nein, danke</button>
        </div>
      </div>
    </div>
    <script>
    (function() {
      var overlay = document.getElementById('pa-nl-overlay');
      if (overlay) overlay.style.display = 'flex';
      function paNlClose() {
        if (overlay) overlay.style.display = 'none';
      }
      function paNlRespond(choice) {
        paNlClose();
        var fd = new FormData();
        fd.append('action', 'pa_save_newsletter_pref');
        fd.append('nonce',  <?php echo json_encode($nonce); ?>);
        fd.append('choice', choice);
        fetch(<?php echo json_encode(admin_url('admin-ajax.php')); ?>, { method:'POST', body:fd });
      }
      window.paNlClose    = paNlClose;
      window.paNlRespond  = paNlRespond;
    }());
    </script>
    <?php
}

// AJAX: save newsletter preference
add_action('wp_ajax_pa_save_newsletter_pref', 'pa_save_newsletter_pref_ajax');
function pa_save_newsletter_pref_ajax(): void {
    check_ajax_referer('pa_newsletter_pref_nonce', 'nonce');
    $user_id = get_current_user_id();
    if (!$user_id) wp_send_json_error('Not logged in');
    $choice  = sanitize_text_field($_POST['choice'] ?? '');
    if (!in_array($choice, ['yes', 'no'], true)) wp_send_json_error('Invalid choice');
    update_user_meta($user_id, '_pa_newsletter_pref', $choice);
    delete_transient('pa_nl_popup_' . $user_id);
    wp_send_json_success();
}

// ═══════════════════════════════════════════════════════════════════════

// Inject deal popup once in footer on account page
add_action('wp_footer', 'pa_account_deal_popup_footer');
function pa_account_deal_popup_footer() {
    if (!is_account_page() || !is_user_logged_in()) return;
    $platforms = array_values(array_filter(
        get_option('_pa_sm_platforms', []),
        function($p) { return !empty($p['active']); }
    ));
    if (empty($platforms)) return;
    echo pa_deal_popup_html(0, $platforms, wp_create_nonce('pa_deal_nonce'), admin_url('admin-ajax.php'));
    ?>
    <script>
    document.querySelectorAll('a.pa-deal[href^="#pa-deal-"]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var ordId = parseInt(this.getAttribute('href').replace('#pa-deal-', '')) || 0;
            if (ordId && typeof paOpenDeal === 'function') paOpenDeal(ordId);
        });
    });
    </script>
    <?php
}

// ═══════════════════════════════════════════════════════════════════════
// 7.1 RESTOCKED TRACKING
// ═══════════════════════════════════════════════════════════════════════

// Track stock changes to power "restocked" newsletter categories
add_action('woocommerce_product_set_stock', 'pa_track_stock_change');
function pa_track_stock_change(WC_Product $product): void {
    $pid       = $product->get_id();
    $new_stock = (int) $product->get_stock_quantity();
    $old_stock = (int) get_post_meta($pid, '_pa_prev_stock', true);
    $threshold = (int) (get_option('woocommerce_notify_low_stock_amount', 2));
    $now       = current_time('mysql');

    if ($new_stock > 0 && $old_stock <= 0) {
        // Restocked after being completely empty
        update_post_meta($pid, '_pa_restocked_from_empty', $now);
        update_post_meta($pid, '_pa_restocked_at',        $now);
    } elseif ($new_stock > $threshold && $old_stock <= $threshold && $old_stock > 0) {
        // Restocked after being at low-stock
        update_post_meta($pid, '_pa_restocked_from_low', $now);
        update_post_meta($pid, '_pa_restocked_at',       $now);
    } elseif ($new_stock > $old_stock) {
        // Any stock increase
        update_post_meta($pid, '_pa_restocked_at', $now);
    }

    update_post_meta($pid, '_pa_prev_stock', $new_stock);
}

// ═══════════════════════════════════════════════════════════════════════
// 8.3 NEWSLETTER SPEICHERN / LADEN API
// ═══════════════════════════════════════════════════════════════════════

// Save newsletter draft
add_action('wp_ajax_save_newsletter_draft', 'pa_save_newsletter_draft_ajax');
function pa_save_newsletter_draft_ajax(): void {
    check_ajax_referer('newsletter_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Forbidden');

    $subject = sanitize_text_field($_POST['subject'] ?? '');
    $blocks  = stripslashes($_POST['blocks'] ?? '[]');

    // Validate JSON
    $decoded = json_decode($blocks, true);
    if (!is_array($decoded)) wp_send_json_error('Ungültige Blöcke');

    update_option('_pa_newsletter_draft', [
        'subject'  => $subject,
        'blocks'   => $decoded,
        'saved_at' => current_time('mysql'),
    ], false);

    wp_send_json_success(['saved_at' => current_time('mysql')]);
}

// Load newsletter draft
add_action('wp_ajax_load_newsletter_draft', 'pa_load_newsletter_draft_ajax');
function pa_load_newsletter_draft_ajax(): void {
    check_ajax_referer('newsletter_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Forbidden');

    $draft = get_option('_pa_newsletter_draft', null);
    if (!$draft) wp_send_json_error('Kein Entwurf vorhanden');
    wp_send_json_success($draft);
}

// ══════════════════════════════════════════════════════════════════
// DESIGN — Cart / Checkout Schritt-Anzeige & Seitenköpfe
// ══════════════════════════════════════════════════════════════════

// Hilfs-Funktion: Schritt-Anzeige HTML
function pa_steps_html( string $current ): string {
    $steps = [
        'shop'    => 'Schaubeet',
        'cart'    => 'Warenkorb',
        'checkout'=> 'Kasse',
        'confirm' => 'Bestätigung',
    ];
    $order = array_keys( $steps );
    $current_idx = array_search( $current, $order );

    $html = '<div class="pa-steps">';
    foreach ( $steps as $key => $label ) {
        $idx = array_search( $key, $order );
        if ( $idx < $current_idx ) {
            $state = 'done';
            $num   = '✓';
        } elseif ( $idx === $current_idx ) {
            $state = 'active';
            $num   = '<em>' . ( $idx + 1 ) . '</em>';
        } else {
            $state = '';
            $num   = '<em>' . ( $idx + 1 ) . '</em>';
        }
        $html .= '<div class="pa-step ' . $state . '">';
        $html .= '<span class="pa-step-num">' . $num . '</span>';
        $html .= '<span class="pa-step-label">' . esc_html( $label ) . '</span>';
        $html .= '</div>';
        if ( $idx < count( $steps ) - 1 ) {
            $html .= '<div class="pa-step-rule"></div>';
        }
    }
    $html .= '</div>';
    return $html;
}

// Cart & Checkout page headers are now embedded in the template overrides
// woocommerce/cart/cart.php and woocommerce/checkout/form-checkout.php.
// The old action hooks below are intentionally removed to prevent duplication.

// ═══════════════════════════════════════════════════════════════════════
// TAXONOMY VALUE POOL — alle vorhandenen Gattung/Art-Werte für Dropdowns
// ═══════════════════════════════════════════════════════════════════════
add_action('wp_ajax_pa_get_taxonomy_values', 'pa_get_taxonomy_values_ajax');
function pa_get_taxonomy_values_ajax() {
    if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized');
    global $wpdb;

    $gattungen = $wpdb->get_col(
        "SELECT DISTINCT meta_value FROM {$wpdb->postmeta}
         WHERE meta_key = '_pa_gattung' AND meta_value != ''
         ORDER BY meta_value"
    );

    $arts_raw = $wpdb->get_results(
        "SELECT DISTINCT g.meta_value AS gattung, a.meta_value AS art
         FROM {$wpdb->postmeta} g
         INNER JOIN {$wpdb->postmeta} a ON g.post_id = a.post_id
         WHERE g.meta_key = '_pa_gattung' AND a.meta_key = '_pa_art'
           AND g.meta_value != '' AND a.meta_value != ''
         ORDER BY g.meta_value, a.meta_value"
    );

    $arts = [];
    foreach ($arts_raw as $row) {
        if (!isset($arts[$row->gattung])) $arts[$row->gattung] = [];
        $arts[$row->gattung][] = $row->art;
    }

    wp_send_json_success(['gattungen' => array_values($gattungen), 'arts' => $arts]);
}

// ═══════════════════════════════════════════════════════════════════════
// SHOP TEMPLATE — force our archive-product.php despite Impreza's
// wc-templates/ path override (us-core plugin switches the path when a
// custom shop layout is set, bypassing the normal woocommerce/ lookup)
//
// NOTE: Cart, checkout, and thankyou are NOT forced here — they are
// WooCommerce template PARTIALS (no get_header/get_footer) and must be
// picked up by WooCommerce's own wc_locate_template() which checks
// woocommerce/ in the child theme automatically. Returning them from
// template_include renders them as bare HTML with no <head> and no CSS.
// ═══════════════════════════════════════════════════════════════════════
add_filter('template_include', function($template) {
    if (!function_exists('is_shop')) return $template;
    $dir = get_stylesheet_directory();

    // Shop archive / category / tag — Impreza changes woocommerce_template_path
    // to wc-templates/ when a custom layout is set, so we must force our file.
    if (is_shop() || is_product_category() || is_product_tag()) {
        $custom = $dir . '/woocommerce/archive-product.php';
        if (file_exists($custom)) return $custom;
    }

    return $template;
}, 999);

// ═══════════════════════════════════════════════════════════════════════
// SHOP FILTER & SORT — /shop/ page with pa_g[] / pa_a[] URL params
// ═══════════════════════════════════════════════════════════════════════

// Filter products by Gattung / Art URL params
add_action('woocommerce_product_query', function($q) {
    $sel_g = array_values(array_filter(array_map('sanitize_text_field', (array) ($_GET['pa_g'] ?? []))));
    $sel_a = array_values(array_filter(array_map('sanitize_text_field', (array) ($_GET['pa_a'] ?? []))));
    if (empty($sel_g) && empty($sel_a)) return;
    $meta_query = ['relation' => 'OR'];
    if (!empty($sel_g)) $meta_query[] = ['key' => '_pa_gattung', 'value' => $sel_g, 'compare' => 'IN'];
    if (!empty($sel_a)) $meta_query[] = ['key' => '_pa_art',     'value' => $sel_a, 'compare' => 'IN'];
    $existing = $q->get('meta_query') ?: [];
    $q->set('meta_query', empty($existing)
        ? $meta_query
        : ['relation' => 'AND', $existing, $meta_query]
    );
});

// Custom sort: name A→Z and Z→A
add_filter('woocommerce_get_catalog_ordering_args', function($args) {
    $orderby = sanitize_text_field($_GET['orderby'] ?? '');
    if ($orderby === 'name') {
        $args['orderby'] = 'title';
        $args['order']   = 'ASC';
    } elseif ($orderby === 'name-desc') {
        $args['orderby'] = 'title';
        $args['order']   = 'DESC';
    }
    return $args;
});

// Register name / name-desc so WooCommerce doesn't strip the orderby param
add_filter('woocommerce_catalog_orderby', function($options) {
    $options['name']      = 'Name A→Z';
    $options['name-desc'] = 'Name Z→A';
    return $options;
});

// ══════════════════════════════════════════════════════════════════
// Warenkorb — Beilagen (add-ons via cart fees)
// ══════════════════════════════════════════════════════════════════

add_action('wp_ajax_pa_toggle_beilage',       'pa_ajax_toggle_beilage');
add_action('wp_ajax_nopriv_pa_toggle_beilage','pa_ajax_toggle_beilage');
function pa_ajax_toggle_beilage(): void {
    check_ajax_referer('pa-beilage-nonce', 'nonce');
    $allowed = ['gift', 'care', 'fert'];
    $key     = sanitize_key((string)($_POST['key'] ?? ''));
    $on      = !empty($_POST['on']) && $_POST['on'] === '1';
    if (!in_array($key, $allowed, true)) { wp_send_json_error('bad key'); }
    $sel = (array) WC()->session->get('pa_beilagen', ['care' => 1]);
    if ($on) {
        $sel[$key] = 1;
    } else {
        unset($sel[$key]);
    }
    WC()->session->set('pa_beilagen', $sel);
    if (isset($_POST['note'])) {
        WC()->session->set('pa_cart_note', sanitize_textarea_field((string)$_POST['note']));
    }
    WC()->cart->calculate_totals();
    wp_send_json_success(['selected' => array_keys($sel)]);
}

add_action('wp_ajax_pa_save_cart_note',       'pa_ajax_save_cart_note');
add_action('wp_ajax_nopriv_pa_save_cart_note','pa_ajax_save_cart_note');
function pa_ajax_save_cart_note(): void {
    check_ajax_referer('pa-beilage-nonce', 'nonce');
    WC()->session->set('pa_cart_note', sanitize_textarea_field((string)($_POST['note'] ?? '')));
    wp_send_json_success();
}

add_action('woocommerce_cart_calculate_fees', 'pa_beilagen_cart_fees');
function pa_beilagen_cart_fees(\WC_Cart $cart): void {
    if (is_admin() && !defined('DOING_AJAX')) return;
    $sel = (array) WC()->session->get('pa_beilagen', ['care' => 1]);
    if (!empty($sel['gift'])) {
        $cart->add_fee(__('Pflanzenpass · Geschenkverpackung', 'woocommerce'), 4.50, true);
    }
    if (!empty($sel['fert'])) {
        $cart->add_fee(__('Bio-Pelargonien-Dünger · 250 ml', 'woocommerce'), 8.90, true);
    }
}

// ═══════════════════════════════════════════════════════════════════════
// MIGRATION — EXPORT & IMPORT
// ═══════════════════════════════════════════════════════════════════════

function pa_encrypt_payload(string|false $data, string $password): string {
    if ($data === false) $data = '';
    $key = hash('sha256', $password, true);
    $iv  = openssl_random_pseudo_bytes(16);
    $enc = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $enc);
}

function pa_decrypt_payload(string $enc, string $password): string|false {
    $raw = base64_decode($enc, true);
    if (!$raw || strlen($raw) < 17) return false;
    $key = hash('sha256', $password, true);
    return openssl_decrypt(substr($raw, 16), 'AES-256-CBC', $key, OPENSSL_RAW_DATA, substr($raw, 0, 16));
}

// ── Export ────────────────────────────────────────────────────────────────────
add_action('wp_ajax_pa_migration_export', 'pa_migration_export_ajax');
function pa_migration_export_ajax(): void {
    check_ajax_referer('product_list_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_die('Unauthorized');

    $password = $_POST['password'] ?? '';
    if (strlen($password) < 8) { wp_die('Passwort zu kurz', '', ['response' => 400]); }

    @ini_set('memory_limit', '-1');
    set_time_limit(0);

    $att_meta    = pa_mig_export_media();
    $upload_base = wp_upload_dir()['basedir'];

    $tmp = tempnam(sys_get_temp_dir(), 'pa_mig_');
    $zip = new ZipArchive();
    if ($zip->open($tmp, ZipArchive::OVERWRITE) !== true) {
        wp_die('ZipArchive konnte nicht erstellt werden', '', ['response' => 500]);
    }

    $zip->addFromString('migration_products.enc',  pa_encrypt_payload(wp_json_encode(pa_mig_export_products()),  $password));
    $zip->addFromString('migration_orders.enc',    pa_encrypt_payload(wp_json_encode(pa_mig_export_orders()),    $password));
    $zip->addFromString('migration_users.enc',     pa_encrypt_payload(wp_json_encode(pa_mig_export_users()),     $password));
    $zip->addFromString('migration_settings.enc',  pa_encrypt_payload(wp_json_encode(pa_mig_export_settings()),  $password));
    $zip->addFromString('migration_media_ids.enc', pa_encrypt_payload(wp_json_encode($att_meta),                 $password));

    // Encrypt each image to a temp file to avoid loading entire library into RAM
    $enc_tmp_files = [];
    foreach ($att_meta as $item) {
        if (empty($item['rel_path'])) continue;
        $filepath = $upload_base . '/' . ltrim($item['rel_path'], '/\\');
        if (!is_file($filepath)) continue;
        $filedata = file_get_contents($filepath);
        if ($filedata === false) continue;
        $enc_tmp = tempnam(sys_get_temp_dir(), 'pa_enc_');
        file_put_contents($enc_tmp, pa_encrypt_payload($filedata, $password));
        unset($filedata);
        $zip->addFile($enc_tmp, 'media/' . $item['id'] . '.enc');
        $enc_tmp_files[] = $enc_tmp;
    }

    $zip->close();
    foreach ($enc_tmp_files as $f) @unlink($f);

    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="plantaphilia_migration_' . date('Y-m-d') . '.zip"');
    header('Content-Length: ' . filesize($tmp));
    header('Cache-Control: no-cache, no-store');
    readfile($tmp);
    unlink($tmp);
    exit;
}

function pa_mig_export_products(): array {
    $products = wc_get_products(['limit' => -1, 'status' => 'any']);
    return array_map(function($p) {
        $id = $p->get_id();
        $meta = get_post_meta($id);
        $tax = [];
        foreach (['product_cat', 'product_tag', 'product_shipping_class'] as $t) {
            $terms = wp_get_object_terms($id, $t, ['fields' => 'all']);
            $tax[$t] = array_map(fn($x) => ['slug' => $x->slug, 'name' => $x->name], $terms);
        }
        return [
            'sku'               => $p->get_sku(),
            'name'              => $p->get_name(),
            'status'            => $p->get_status(),
            'description'       => $p->get_description(),
            'short_description' => $p->get_short_description(),
            'price'             => $p->get_regular_price(),
            'sale_price'        => $p->get_sale_price(),
            'stock'             => $p->get_stock_quantity(),
            'image_id'          => $p->get_image_id(),
            'gallery_ids'       => $p->get_gallery_image_ids(),
            'meta'              => array_map(fn($v) => $v[0] ?? '', $meta),
            'taxonomy'          => $tax,
        ];
    }, $products);
}

function pa_mig_export_orders(): array {
    $orders = wc_get_orders(['limit' => -1, 'status' => 'any', 'type' => 'shop_order']);
    return array_map(function($o) {
        $id = $o->get_id();
        $items = [];
        foreach ($o->get_items() as $it) {
            $prod = $it->get_product();
            $items[] = ['name' => $it->get_name(), 'qty' => $it->get_quantity(), 'total' => $it->get_total(), 'sku' => $prod ? $prod->get_sku() : ''];
        }
        return [
            'order_number' => $o->get_order_number(),
            'status'       => $o->get_status(),
            'date_created' => $o->get_date_created() ? $o->get_date_created()->format('Y-m-d H:i:s') : '',
            'total'        => $o->get_total(),
            'billing'      => $o->get_address('billing'),
            'shipping'     => $o->get_address('shipping'),
            'items'        => $items,
            'meta'         => array_map(fn($v) => $v[0] ?? '', get_post_meta($id)),
        ];
    }, $orders);
}

function pa_mig_export_users(): array {
    $users = get_users(['role__in' => ['customer', 'subscriber', 'administrator']]);
    return array_map(function($u) {
        return [
            'email'        => $u->user_email,
            'login'        => $u->user_login,
            'display_name' => $u->display_name,
            'registered'   => $u->user_registered,
            'password'     => $u->user_pass,
            'meta'         => array_map(fn($v) => $v[0] ?? '', get_user_meta($u->ID)),
        ];
    }, $users);
}

function pa_mig_export_settings(): array {
    global $wpdb;
    $wc = $wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'woocommerce_%'", ARRAY_A);
    $pa = $wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'pa_%' OR option_name LIKE '_pa_%' OR option_name IN ('_bulk_sales','pa_newsletter_list')", ARRAY_A);
    return [
        'woocommerce' => array_column($wc, 'option_value', 'option_name'),
        'pa'          => array_column($pa, 'option_value', 'option_name'),
    ];
}

function pa_mig_export_media(): array {
    $upload_base = wp_upload_dir()['basedir'];
    return array_map(function($att) use ($upload_base) {
        $filepath = get_attached_file($att->ID) ?: '';
        $rel = $filepath ? str_replace('\\', '/', ltrim(str_replace($upload_base, '', $filepath), '/\\')) : '';
        return [
            'id'       => $att->ID,
            'filename' => basename($filepath),
            'guid'     => $att->guid,
            'rel_path' => $rel,
            'mime'     => get_post_mime_type($att->ID) ?: 'application/octet-stream',
        ];
    }, get_posts(['post_type' => 'attachment', 'posts_per_page' => -1, 'post_status' => 'any']));
}

// ── Import ────────────────────────────────────────────────────────────────────
add_action('wp_ajax_pa_migration_import', 'pa_migration_import_ajax');
function pa_migration_import_ajax(): void {
    check_ajax_referer('product_list_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_die('Unauthorized');

    if (empty($_FILES['migration_zip']) || $_FILES['migration_zip']['error'] !== UPLOAD_ERR_OK) {
        wp_send_json_error('Upload-Fehler');
    }

    $password = $_POST['password'] ?? '';
    $dry_run  = intval($_POST['dry_run'] ?? 1);
    $step     = sanitize_text_field($_POST['step'] ?? 'all');

    $zip = new ZipArchive();
    if ($zip->open($_FILES['migration_zip']['tmp_name']) !== true) {
        wp_send_json_error('ZIP konnte nicht geöffnet werden');
    }

    $keys = ['products' => 'migration_products.enc', 'orders' => 'migration_orders.enc', 'users' => 'migration_users.enc', 'settings' => 'migration_settings.enc', 'media' => 'migration_media_ids.enc'];
    $data = [];
    foreach ($keys as $k => $fn) {
        $enc = $zip->getFromName($fn);
        if ($enc === false) { $zip->close(); wp_send_json_error('Datei fehlt: ' . $fn); }
        $json = pa_decrypt_payload($enc, $password);
        if ($json === false) { $zip->close(); wp_send_json_error('Entschlüsselung fehlgeschlagen — falsches Passwort?'); }
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) { $zip->close(); wp_send_json_error('JSON-Fehler: ' . $fn); }
        $data[$k] = $decoded;
    }

    // Count media binary files in ZIP (media/{id}.enc entries)
    $media_file_count = 0;
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $zn = $zip->getNameIndex($i);
        if ($zn !== false && strpos($zn, 'media/') === 0 && substr($zn, -4) === '.enc') $media_file_count++;
    }

    if ($dry_run) {
        $zip->close();
        $diff = pa_mig_dry_run($data);
        $diff['media']['files'] = $media_file_count;
        wp_send_json_success(['dry_run' => true, 'diff' => $diff]);
    }

    set_time_limit(600);
    $results = [];
    $id_map = [];
    if (in_array($step, ['all','media'], true)) {
        $mr = pa_mig_import_media_files($zip, $password, $data['media']);
        $results['media_files'] = ['restored' => $mr['restored'], 'skipped' => $mr['skipped'], 'errors' => $mr['errors']];
        $id_map = $mr['id_map'];
    }
    $zip->close();

    if (in_array($step, ['all','products'],  true)) $results['products']     = pa_mig_import_products($data['products']);
    if (in_array($step, ['all','orders'],    true)) $results['orders']       = pa_mig_import_orders($data['orders']);
    if (in_array($step, ['all','users'],     true)) $results['users']        = pa_mig_import_users($data['users']);
    if (in_array($step, ['all','settings'],  true)) $results['settings']     = pa_mig_import_settings($data['settings']);
    if (in_array($step, ['all','media'],     true)) $results['media_relink'] = pa_mig_relink_media($data['media'], $id_map);
    wp_send_json_success(['dry_run' => false, 'results' => $results]);
}

function pa_mig_dry_run(array $data): array {
    $diff = [];
    $new_p = $upd_p = 0;
    foreach ($data['products'] as $p) {
        $sku = $p['sku'] ?? '';
        ($sku && wc_get_product_id_by_sku($sku)) ? $upd_p++ : $new_p++;
    }
    $diff['products'] = ['new' => $new_p, 'update' => $upd_p, 'total' => count($data['products'])];

    $new_o = $skip_o = 0;
    foreach ($data['orders'] as $o) {
        $num = preg_replace('/[^0-9]/', '', $o['order_number'] ?? '');
        $pid = intval($num) > 10000 ? intval($num) - 10000 : 0;
        ($pid && get_post($pid) && get_post($pid)->post_type === 'shop_order') ? $skip_o++ : $new_o++;
    }
    $diff['orders'] = ['new' => $new_o, 'skip' => $skip_o, 'total' => count($data['orders'])];

    $new_u = $upd_u = 0;
    foreach ($data['users'] as $u) { email_exists($u['email'] ?? '') ? $upd_u++ : $new_u++; }
    $diff['users'] = ['new' => $new_u, 'update' => $upd_u, 'total' => count($data['users'])];

    $diff['settings'] = ['total' => count($data['settings']['pa'] ?? []) + count($data['settings']['woocommerce'] ?? [])];
    $diff['media']    = ['total' => count($data['media'] ?? [])];
    return $diff;
}

function pa_mig_import_products(array $products): array {
    $created = $updated = 0; $errors = [];
    foreach ($products as $p) {
        try {
            $sku = $p['sku'] ?? '';
            $eid = $sku ? wc_get_product_id_by_sku($sku) : 0;
            $prod = $eid ? wc_get_product($eid) : new WC_Product_Simple();
            $prod->set_name($p['name'] ?? '');
            $prod->set_status($p['status'] ?? 'publish');
            $prod->set_description($p['description'] ?? '');
            $prod->set_short_description($p['short_description'] ?? '');
            $prod->set_regular_price($p['price'] ?? '');
            $prod->set_manage_stock(true);
            $prod->set_stock_quantity(intval($p['stock'] ?? 0));
            if ($sku) $prod->set_sku($sku);
            $nid = $prod->save();
            foreach (($p['meta'] ?? []) as $k => $v) {
                if (in_array($k, ['_thumbnail_id','_product_image_gallery'], true)) continue;
                update_post_meta($nid, $k, $v);
            }
            foreach (($p['taxonomy'] ?? []) as $tax => $terms) {
                $tids = [];
                foreach ($terms as $t) {
                    $tx = get_term_by('slug', $t['slug'], $tax) ?: (function() use ($t, $tax) { $ins = wp_insert_term($t['name'], $tax, ['slug' => $t['slug']]); return is_wp_error($ins) ? null : get_term($ins['term_id']); })();
                    if ($tx) $tids[] = $tx->term_id;
                }
                if ($tids) wp_set_object_terms($nid, $tids, $tax);
            }
            $eid ? $updated++ : $created++;
        } catch (\Throwable $e) { $errors[] = ['sku' => $p['sku'] ?? '?', 'error' => $e->getMessage()]; }
    }
    return ['created' => $created, 'updated' => $updated, 'errors' => $errors];
}

function pa_mig_import_orders(array $orders): array {
    $created = $skipped = 0; $errors = [];
    foreach ($orders as $o) {
        $num = preg_replace('/[^0-9]/', '', $o['order_number'] ?? '');
        $pid = intval($num) > 10000 ? intval($num) - 10000 : 0;
        if ($pid && get_post($pid) && get_post($pid)->post_type === 'shop_order') { $skipped++; continue; }
        try {
            $order = wc_create_order(['status' => $o['status'] ?? 'pending']);
            if (is_wp_error($order)) { $errors[] = ['order' => $o['order_number'] ?? '?', 'error' => $order->get_error_message()]; continue; }
            $order->set_address($o['billing'] ?? [], 'billing');
            $order->set_address($o['shipping'] ?? [], 'shipping');
            foreach (($o['items'] ?? []) as $it) {
                $oi = new WC_Order_Item_Product();
                $oi->set_name($it['name'] ?? '');
                $oi->set_quantity($it['qty'] ?? 1);
                $oi->set_total($it['total'] ?? 0);
                $pid2 = !empty($it['sku']) ? wc_get_product_id_by_sku($it['sku']) : 0;
                if ($pid2) $oi->set_product_id($pid2);
                $order->add_item($oi);
            }
            foreach (($o['meta'] ?? []) as $k => $v) { update_post_meta($order->get_id(), $k, $v); }
            $order->save();
            $created++;
        } catch (\Throwable $e) { $errors[] = ['order' => $o['order_number'] ?? '?', 'error' => $e->getMessage()]; }
    }
    return ['created' => $created, 'skipped' => $skipped, 'errors' => $errors];
}

function pa_mig_import_users(array $users): array {
    $created = $updated = 0; $errors = [];
    foreach ($users as $u) {
        $email = $u['email'] ?? '';
        if (!$email) continue;
        $eid = email_exists($email);
        if ($eid) {
            foreach (($u['meta'] ?? []) as $k => $v) { update_user_meta($eid, $k, $v); }
            $updated++;
        } else {
            $uid = wp_insert_user(['user_email' => $email, 'user_login' => $u['login'] ?? $email, 'display_name' => $u['display_name'] ?? '', 'user_pass' => $u['password'] ?? wp_generate_password(), 'user_registered' => $u['registered'] ?? current_time('mysql'), 'role' => 'customer']);
            if (!is_wp_error($uid)) { foreach (($u['meta'] ?? []) as $k => $v) { update_user_meta($uid, $k, $v); } $created++; }
            else { $errors[] = ['email' => $email, 'error' => $uid->get_error_message()]; }
        }
    }
    return ['created' => $created, 'updated' => $updated, 'errors' => $errors];
}

function pa_mig_import_settings(array $settings): array {
    $count = 0;
    foreach (($settings['woocommerce'] ?? []) as $k => $v) { update_option($k, $v); $count++; }
    foreach (($settings['pa'] ?? []) as $k => $v) { update_option($k, maybe_unserialize($v)); $count++; }
    return ['restored' => $count];
}

function pa_mig_import_media_files(ZipArchive $zip, string $password, array $media_meta): array {
    $upload_dir  = wp_upload_dir();
    $upload_base = $upload_dir['basedir'];
    $upload_url  = $upload_dir['baseurl'];
    $restored = $skipped = 0;
    $errors = []; $id_map = [];
    $meta_by_id = [];
    foreach ($media_meta as $item) {
        if (!empty($item['id'])) $meta_by_id[(int)$item['id']] = $item;
    }
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $zip_name = $zip->getNameIndex($i);
        if ($zip_name === false || strpos($zip_name, 'media/') !== 0 || substr($zip_name, -4) !== '.enc') continue;
        $old_id = (int) basename($zip_name, '.enc');
        if (!$old_id) continue;
        $meta = $meta_by_id[$old_id] ?? null;
        if (!$meta) continue;
        $rel_path = $meta['rel_path'] ?? '';
        $filename  = $meta['filename'] ?? ($rel_path ? basename($rel_path) : '');
        if (!$filename) continue;
        $enc = $zip->getFromIndex($i);
        if ($enc === false) { $errors[] = "Lesen fehlgeschlagen: $zip_name"; continue; }
        $filedata = pa_decrypt_payload($enc, $password);
        if ($filedata === false) { $errors[] = "Entschlüsselung fehlgeschlagen: $zip_name"; continue; }
        $target = $rel_path ? $upload_base . '/' . ltrim($rel_path, '/') : $upload_base . '/' . date('Y/m') . '/' . $filename;
        $dir = dirname($target);
        if (!file_exists($dir)) wp_mkdir_p($dir);
        // If file already exists and size matches, try to find existing attachment
        if (file_exists($target) && filesize($target) === strlen($filedata)) {
            $target_url = str_replace($upload_base, $upload_url, $target);
            $existing_id = attachment_url_to_postid($target_url);
            if ($existing_id) { $id_map[$old_id] = $existing_id; $skipped++; continue; }
        }
        if (file_put_contents($target, $filedata) === false) { $errors[] = "Schreiben fehlgeschlagen: $target"; continue; }
        $mime = $meta['mime'] ?? (wp_check_filetype($filename)['type'] ?: 'application/octet-stream');
        $new_id = wp_insert_attachment([
            'post_mime_type' => $mime,
            'post_title'     => sanitize_file_name(pathinfo($filename, PATHINFO_FILENAME)),
            'post_content'   => '',
            'post_status'    => 'inherit',
        ], $target);
        if (is_wp_error($new_id)) { $errors[] = $new_id->get_error_message(); continue; }
        wp_update_attachment_metadata($new_id, wp_generate_attachment_metadata($new_id, $target));
        $id_map[$old_id] = $new_id;
        $restored++;
    }
    return ['restored' => $restored, 'skipped' => $skipped, 'id_map' => $id_map, 'errors' => $errors];
}

function pa_mig_relink_media(array $media, array $id_map = []): array {
    if (empty($id_map)) {
        // Fallback: filename matching against current library
        $current = [];
        foreach (get_posts(['post_type' => 'attachment', 'posts_per_page' => -1, 'post_status' => 'any']) as $att) {
            $fn = basename(get_attached_file($att->ID));
            if ($fn) $current[$fn] = $att->ID;
        }
        foreach ($media as $m) {
            if (!empty($m['filename']) && isset($current[$m['filename']]) && !empty($m['id'])) {
                $id_map[(int)$m['id']] = $current[$m['filename']];
            }
        }
    }
    if (empty($id_map)) return ['relinked' => 0, 'mapped' => 0];
    $relinked = 0;
    foreach (get_posts(['post_type' => 'product', 'posts_per_page' => -1]) as $pr) {
        $thumb = (int) get_post_meta($pr->ID, '_thumbnail_id', true);
        if ($thumb && isset($id_map[$thumb])) { update_post_meta($pr->ID, '_thumbnail_id', $id_map[$thumb]); $relinked++; }
        $gallery = get_post_meta($pr->ID, '_product_image_gallery', true);
        if ($gallery) {
            $ids = array_filter(explode(',', $gallery));
            $new_gallery = implode(',', array_map(fn($id) => $id_map[(int)$id] ?? $id, $ids));
            if ($new_gallery !== $gallery) update_post_meta($pr->ID, '_product_image_gallery', $new_gallery);
        }
    }
    return ['relinked' => $relinked, 'mapped' => count($id_map)];
}
