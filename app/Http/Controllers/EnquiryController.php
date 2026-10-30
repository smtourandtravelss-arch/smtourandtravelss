<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Setting;
use App\Services\SiteMailer;
use App\Services\SiteRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/** One handler for every enquiry form on the website. */
class EnquiryController extends Controller
{
    public function store(Request $request)
    {
        SiteRenderer::boot();
        $GLOBALS['HC'] = ['settings' => Setting::allSettings(), 'content' => [], 'page' => 'send', 'preview' => false];

        $isAjax = $request->ajax() || $request->input('ajax') === '1';

        $reply = function (bool $ok, string $message, string $redirect = '', array $extra = []) use ($isAjax, $request) {
            if ($isAjax) {
                $res = response()->json(['ok' => $ok, 'message' => $message, 'redirect' => $redirect] + $extra);
                // exact length + close: the browser has the full answer at once, even while the email
                // is still being sent after the response
                $res->headers->set('Content-Length', (string) strlen((string) $res->getContent()));
                $res->headers->set('Connection', 'close');

                return $res;
            }
            if ($ok && $redirect) {
                return redirect()->to($redirect, 303);
            }
            $back = $request->headers->get('referer') ?: hc_url('');
            if (! empty($extra['errors'])) {
                $message .= ' '.implode(' · ', array_unique($extra['errors']));
            }

            return response('<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex"><title>Please check the form</title>'
                .'<style>body{font-family:system-ui,Arial,sans-serif;background:#F3F7FC;margin:0;padding:24px;color:#0F1B33}.b{max-width:520px;margin:60px auto;background:#fff;border-radius:16px;padding:28px;box-shadow:0 10px 30px rgba(0,0,0,.08)}a{display:inline-block;margin:8px 8px 0 0;padding:12px 20px;border-radius:10px;background:#FFC21A;color:#0B1B33;text-decoration:none;font-weight:700}a.c{background:#0B1B33;color:#fff}</style></head><body><div class="b">'
                .'<h1 style="font-size:1.3rem">Please check the form</h1><p>'.hc_e($message).'</p>'
                .'<a href="'.hc_e($back).'">&larr; Go back</a><a class="c" href="'.hc_e(hc_tel()).'">Call '.hc_e(s('mobile')).'</a></div></body></html>', 400);
        };

        $val = function ($k, $max = 1000) use ($request) {
            $v = $request->input($k, '');
            if (is_array($v)) {
                $v = implode(', ', $v);
            }
            $v = trim(strip_tags((string) $v));
            $v = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $v) ?? $v;

            return mb_substr($v, 0, $max, 'UTF-8');
        };

        /* ---------------- SPAM PROTECTION ---------------- */
        if ($val('website') !== '') {
            return $reply(true, 'OK', hc_url('thank-you'));
        }
        $parts = explode('.', $val('hc_ts', 100));
        $validToken = count($parts) === 2 && ctype_digit($parts[0])
            && hash_equals(substr(hash_hmac('sha256', $parts[0], hc_secret()), 0, 24), $parts[1]);
        $age = $validToken ? time() - (int) $parts[0] : -1;
        if (! $validToken || $age > 172800) {
            return $reply(false, 'This form has expired. Please refresh the page and try again, or call us on '.s('mobile').'.');
        }
        if ($age < 3) {
            return $reply(false, 'That was very fast! Please wait a moment and submit again.');
        }

        /* ---------------- VALIDATION ---------------- */
        $data = [];
        $errors = [];
        $short = $request->input('hc_short') === '1';

        // Trip type (One Way / Round Trip / …): decides which fields belong to this booking
        $trip = '';
        if ($tripField = hc_trip_field()) {
            $trip = $val($tripField['name'], 60);
            $match = array_values(array_filter(array_map('strval', hc_select_options($tripField)), fn ($o) => strcasecmp($o, $trip) === 0));
            $trip = $match[0] ?? '';
        }

        foreach (hc_form_fields() as $f) {
            if ($short && isset($f['short_form']) && ! on($f['short_form'])) {
                continue; // field not shown in the popup
            }
            if (! hc_field_applies($f, $trip)) {
                continue; // e.g. "Return date" is not asked for a One Way trip
            }
            [$min, $max] = hc_field_limits($f);
            $v = $f['type'] === 'trip' ? $trip : $val($f['name'], max($max, 200));
            $bad = false;
            if ($f['type'] === 'trip') {
                // already matched against the options above
            } elseif ($f['type'] === 'time' && $v !== '') {
                $bad = ! preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $v);
            } elseif ($f['type'] === 'tel' && $v !== '') {
                $d = hc_digits($v);
                if (strlen($d) === 12 && str_starts_with($d, '91')) {
                    $d = substr($d, 2);
                }
                if (strlen($d) === 11 && $d[0] === '0') {
                    $d = substr($d, 1);
                }
                $bad = ! preg_match('/^[6-9][0-9]{9}$/', $d) || preg_match('/^(\d)\1{9}$/', $d) || $d === '1234567890';
                $v = $d;
            } elseif ($f['type'] === 'email' && $v !== '') {
                $bad = mb_strlen($v) > 100 || ! filter_var($v, FILTER_VALIDATE_EMAIL);
            } elseif ($f['type'] === 'date' && $v !== '') {
                $bad = ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) || $v < date('Y-m-d');
            } elseif ($f['type'] === 'number' && $v !== '') {
                $lo = isset($f['min']) && is_numeric($f['min']) ? (int) $f['min'] : 1;
                $hi = isset($f['max']) && is_numeric($f['max']) ? (int) $f['max'] : 50;
                $bad = ! ctype_digit($v) || (int) $v < $lo || (int) $v > $hi;
            } elseif ($f['type'] === 'select' && $v !== '') {
                if (hc_is_service_field($f)) {
                    $bad = ! hc_find_card($v) && ! hc_find_card(hc_service_label($v));
                    $v = hc_service_label($v);
                } else {
                    $bad = ! in_array($v, array_map('strval', hc_select_options($f)), true);
                }
            } elseif ($v !== '') {
                if ($f['name'] === 'name' && $f['type'] === 'text') {
                    $v = preg_replace('/\s+/', ' ', $v);
                    $bad = ! preg_match('/^[A-Za-z .]{2,50}$/', $v);
                } else {
                    $len = mb_strlen($v);
                    $bad = $len < $min || $len > $max;
                }
            }
            if ($bad || ($f['required'] && $v === '')) {
                $errors[$f['name']] = hc_field_error($f);
            }
            $data[$f['name']] = ['label' => $f['label'], 'value' => $v, 'type' => $f['type']];
        }
        if ($errors) {
            return $reply(false, s('form_error_text', 'Please correct the highlighted fields.'), '', ['errors' => $errors]);
        }

        /* ---------------- DETAILS ---------------- */
        $item = $val('enquiry_item', 150);
        $formName = $val('form_name', 150);
        $pageUrl = $val('page_url', 500) ?: mb_substr((string) $request->headers->get('referer'), 0, 500);
        // The car chosen in the form wins over the button that opened the popup —
        // unless the button was for something else (e.g. "Corporate Taxi"): then both are kept
        foreach (hc_form_fields() as $f) {
            if (hc_is_service_field($f) && ($data[$f['name']]['value'] ?? '') !== '') {
                $car = $data[$f['name']]['value'];
                $item = ($item !== '' && strcasecmp($item, $car) !== 0 && ! hc_find_card($item)) ? $item.' – '.$car : $car;
            }
        }
        $card = hc_find_card($item);
        $details = $card ? hc_card_details($card) : '';

        $name = $data['name']['value'] ?? '';
        $mobile = '';
        $email = '';
        foreach ($data as $d) {
            if ($d['type'] === 'tel' && $mobile === '') {
                $mobile = $d['value'];
            }
            if ($d['type'] === 'email' && $email === '') {
                $email = $d['value'];
            }
        }
        $when = now()->format('d M Y, h:i A');
        $ip = (string) $request->ip();
        $biz = s('business_name', 'Website');

        $subject = $item !== ''
            ? 'New '.s('buttons.email_action_word', 'Enquiry').' – '.$item.' – '.$name
            : 'New Enquiry – '.($formName !== '' ? $formName : 'Website').' – '.$name;

        /* ---------------- SAVE LEAD ---------------- */
        $lead = Lead::create([
            'name' => $name,
            'mobile' => $mobile,
            'email' => $email,
            'item' => $item,
            'form_name' => $formName,
            'page_url' => $pageUrl,
            'data' => collect($data)->map(fn ($d) => ['label' => $d['label'], 'value' => $d['value']])->all(),
            'ip' => $ip,
            'status' => 'new',
        ]);

        /* ---------------- EMAIL ---------------- */
        $rows = [];
        foreach ($data as $d) {
            $rows[] = [$d['label'], $d['value']];
        }
        // "Enquiry For" only when the item is not already one of the form answers (e.g. the chosen car)
        $itemRow = $item !== '' && ! in_array($item, array_column($rows, 1), true);
        if ($itemRow) {
            $rows[] = ['Enquiry For', $item];
        }
        if ($details !== '') {
            $rows[] = ['Service Details', $details];
        }
        $rows[] = ['Form', $formName];
        $rows[] = ['Page', $pageUrl];
        $rows[] = ['Date & Time', $when.' (IST)'];
        $rows[] = ['IP Address', $ip];

        $waNum = '91'.$mobile;
        $waText = rawurlencode('Hi '.$name.', this is '.$biz.'. Thank you for your enquiry'.($item !== '' ? ' for '.$item : '').'.');
        $html = '<div style="font-family:Arial,sans-serif;max-width:620px;margin:auto;border:1px solid #dbe7ea;border-radius:12px;overflow:hidden">'
            .'<div style="background:#0F1B33;color:#fff;padding:18px 22px;font-size:18px;font-weight:bold">'.hc_e($subject).'</div>'
            .'<table cellpadding="10" cellspacing="0" style="width:100%;border-collapse:collapse;font-size:14px">';
        foreach ($rows as $i => $r) {
            $html .= '<tr style="background:'.($i % 2 ? '#ffffff' : '#f4f8fa').'"><td style="width:150px;font-weight:bold;color:#0F1B33;border-bottom:1px solid #e6eef1">'.hc_e($r[0]).'</td><td style="border-bottom:1px solid #e6eef1">'.nl2br(hc_e($r[1])).'</td></tr>';
        }
        $html .= '</table><div style="padding:18px 22px">';
        if ($mobile !== '') {
            $html .= '<a href="tel:+91'.hc_e($mobile).'" style="display:inline-block;background:#1D74C9;color:#fff;padding:12px 20px;border-radius:30px;text-decoration:none;font-weight:bold;margin:0 8px 8px 0">&#128222; Call Customer</a>'
                .'<a href="https://wa.me/'.hc_e($waNum).'?text='.$waText.'" style="display:inline-block;background:#25D366;color:#fff;padding:12px 20px;border-radius:30px;text-decoration:none;font-weight:bold;margin:0 8px 8px 0">WhatsApp Customer</a>';
        }
        $html .= '</div></div>';

        $text = $subject."\n\n";
        foreach ($rows as $r) {
            $text .= $r[0].': '.$r[1]."\n";
        }

        // Emails go out AFTER the visitor already got the answer (a slow SMTP server must never keep the
        // form on "Sending…"). The lead is saved above, so nothing is lost if the email fails.
        $sendMails = function () use ($lead, $email, $name, $item, $subject, $html, $text) {
            @set_time_limit(60);
            $mailOk = false;
            $mailError = '';
            if (on(s('form_action.send_email', true))) {
                try {
                    $m = SiteMailer::make();
                    $count = SiteMailer::addList($m, (string) s('mail_to', ''));
                    SiteMailer::addList($m, (string) s('mail_cc', ''), true);
                    if ($count === 0) {
                        throw new \RuntimeException('No "Send enquiries to" email set in Site Settings.');
                    }
                    if ($email !== '') {
                        $m->addReplyTo($email, $name);
                    }
                    $m->isHTML(true);
                    $m->Subject = $subject;
                    $m->Body = $html;
                    $m->AltBody = $text;
                    $mailOk = $m->send();
                } catch (\Throwable $e) {
                    $mailError = $e->getMessage();
                    Log::warning('[Enquiry] Email failed: '.$mailError);
                }
            }
            $lead->update(['mail_sent' => $mailOk, 'mail_error' => $mailError ?: (on(s('form_action.send_email', true)) ? null : 'Email notifications are turned off in Site Settings')]);

            // Auto-reply to the customer
            if ($mailOk && $email !== '' && on(s('autoreply_on', false))) {
                try {
                    $r = SiteMailer::make();
                    $r->addAddress($email, $name);
                    $itemText = $item !== '' ? $item : 'our services';
                    $r->Subject = str_replace(['{name}', '{item}'], [$name, $itemText], (string) s('autoreply_subject', 'Thank you'));
                    $r->isHTML(false);
                    $r->Body = hc_fill(str_replace(['{name}', '{item}'], [$name, $itemText], (string) s('autoreply_message', '')));
                    $r->send();
                } catch (\Throwable) {
                    // auto-reply failure never blocks the enquiry
                }
            }
        };
        app()->terminating($sendMails);

        /* ---------------- WHAT HAPPENS NEXT ---------------- */
        $q = array_filter(['name' => $name, 'item' => $item]);
        $after = (string) s('form_action.after_submit', 'thankyou');
        $waUrl = $after === 'whatsapp'
            ? hc_wa_details_url(array_slice($rows, 0, count($data) + ($itemRow ? 1 : 0)),['name' => $name, 'mobile' => $mobile, 'item' => $item, 'page' => $pageUrl, 'form' => $formName])
            : '';

        // Thank-you page may offer "Share details on WhatsApp" — it needs to know which enquiry this was
        $q['l'] = $lead->id.'.'.substr(hash_hmac('sha256', (string) $lead->id, hc_secret()), 0, 16);

        $extra = [
            'event' => $waUrl !== '' ? 'whatsapp_form_submit' : 'form_submit',
            'form_name' => $formName,
            'enquiry_item' => $item,
        ];
        if (on(s('form_action.datalayer_data', false))) {
            $extra['fields'] = collect($data)->map(fn ($d) => $d['value'])->all();
        }

        return $reply(true, 'OK', $waUrl !== '' ? $waUrl : hc_url('thank-you').($q ? '?'.http_build_query($q) : ''), $extra);
    }
}
