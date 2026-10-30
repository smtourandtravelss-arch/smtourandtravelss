<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * After a booking form is sent, the visitor now goes to the Thank-you page (which offers
 * "Send booking details on WhatsApp") instead of straight to WhatsApp.
 * Admin → System & Database → "Update database" runs it; it can be switched back in
 * Admin → Site Settings → Booking Form → "Then take the visitor to".
 */
return new class extends Migration
{
    public function up(): void
    {
        $raw = DB::table('settings')->where('key', 'form_action')->value('value');
        if ($raw === null) {
            return; // not saved yet: the default (thank-you page) applies
        }
        $action = json_decode((string) $raw, true);
        if (! is_array($action) || ($action['after_submit'] ?? '') !== 'whatsapp') {
            return;
        }
        $action['after_submit'] = 'thankyou';
        $action['thankyou_wa_share'] = true;
        $action['share_text'] = trim((string) ($action['share_text'] ?? '')) !== '' ? $action['share_text'] : 'Send booking details on WhatsApp';
        Setting::putMany(['form_action' => $action]);
    }

    public function down(): void
    {
        // setting change — switch it back in Site Settings if needed
    }
};
