<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VoiceController extends Controller
{
    /**
     * Validate required Twilio credentials.
     */
    private function validateCredentials(): bool
    {
        return !empty(env('TWILIO_ACCOUNT_SID'))
            && !empty(env('TWILIO_API_KEY'))
            && !empty(env('TWILIO_API_SECRET'))
            && !empty(env('TWILIO_TWIML_APP_SID'));
    }

    /**
     * Encode data using Base64URL encoding.
     *
     * JWT Base64URL rules:
     * + => -
     * / => _
     * Remove trailing =
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(
            strtr(
                base64_encode($data),
                '+/',
                '-_'
            ),
            '='
        );
    }

    /**
     * Generate a Twilio Voice Access Token.
     *
     * GET /admin/voice/token
     */
    public function token(Request $request)
    {
        if (!$this->validateCredentials()) {
            Log::error('Twilio token request failed: missing credentials', [
                'account_sid_present' => !empty(env('TWILIO_ACCOUNT_SID')),
                'api_key_present' => !empty(env('TWILIO_API_KEY')),
                'api_secret_present' => !empty(env('TWILIO_API_SECRET')),
                'twiml_app_sid_present' => !empty(env('TWILIO_TWIML_APP_SID')),
            ]);

            return response()->json([
                'error' => 'Twilio credentials are not configured properly.',
            ], 500);
        }

        try {
            /*
             * Twilio credentials.
             */
            $accountSid = trim(env('TWILIO_ACCOUNT_SID'));
            $apiKey = trim(env('TWILIO_API_KEY'));
            $apiSecret = trim(env('TWILIO_API_SECRET'));
            $appSid = trim(env('TWILIO_TWIML_APP_SID'));

            /*
             * Browser/client identity.
             */
            $identity = 'pratz';

            /*
             * Token lifetime.
             */
            $now = time();
            $ttl = 3600;
            $exp = $now + $ttl;

            /*
             * Unique JWT ID.
             */
            $jti = $apiKey
                . '-'
                . $now
                . '-'
                . bin2hex(random_bytes(8));

            /*
             * JWT HEADER
             *
             * Twilio Voice Access Token.
             */
            $header = json_encode([
                'typ' => 'JWT',
                'alg' => 'HS256',
                'cty' => 'twilio-fpa;v=1',
            ], JSON_UNESCAPED_SLASHES);

            if ($header === false) {
                throw new \RuntimeException(
                    'Unable to encode JWT header.'
                );
            }

            $headerEncoded = $this->base64UrlEncode($header);

            /*
             * JWT PAYLOAD
             */
            $payload = json_encode([
                'jti' => $jti,

                /*
                 * API Key SID.
                 */
                'iss' => $apiKey,

                /*
                 * Twilio Account SID.
                 */
                'sub' => $accountSid,

                /*
                 * Issued-at timestamp.
                 */
                'iat' => $now,

                /*
                 * Expiration timestamp.
                 */
                'exp' => $exp,

                /*
                 * Twilio grants.
                 */
                'grants' => [
                    /*
                     * Browser/client identity.
                     */
                    'identity' => $identity,

                    /*
                     * Voice permissions.
                     */
                    'voice' => [

                        /*
                         * Outgoing calls.
                         */
                        'outgoing' => [
                            'application_sid' => $appSid,
                        ],

                        /*
                         * Incoming calls.
                         */
                        'incoming' => [
                            'allow' => true,
                        ],
                    ],
                ],
            ], JSON_UNESCAPED_SLASHES);

            if ($payload === false) {
                throw new \RuntimeException(
                    'Unable to encode JWT payload.'
                );
            }

            $payloadEncoded = $this->base64UrlEncode($payload);

            /*
             * JWT signing input.
             */
            $signatureInput =
                $headerEncoded . '.' . $payloadEncoded;

            /*
             * HS256 signature.
             *
             * IMPORTANT:
             * This must use the Twilio API KEY SECRET,
             * NOT the Twilio Auth Token.
             */
            $rawSignature = hash_hmac(
                'sha256',
                $signatureInput,
                $apiSecret,
                true
            );

            $signatureEncoded =
                $this->base64UrlEncode($rawSignature);

            /*
             * Final JWT.
             */
            $token =
                $signatureInput . '.' . $signatureEncoded;

            /*
             * Log metadata only.
             *
             * DO NOT log the complete JWT or API secret.
             */
            Log::info('Twilio JWT generated successfully', [
                'identity' => $identity,
                'issued_at' => $now,
                'expires_at' => $exp,
                'expires_in' => $ttl,
                'account_sid_prefix' => substr(
                    $accountSid,
                    0,
                    8
                ),
                'api_key_prefix' => substr(
                    $apiKey,
                    0,
                    8
                ),
                'twiml_app_sid_prefix' => substr(
                    $appSid,
                    0,
                    8
                ),
            ]);

            return response()->json([
                'token' => $token,
                'identity' => $identity,
                'expires_in' => $ttl,
            ], 200);

        } catch (\Throwable $e) {

            Log::error('Twilio JWT generation failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'error' => 'Unable to generate Twilio access token.',
            ], 500);
        }
    }

    /**
     * Generate TwiML for incoming/outgoing calls.
     *
     * GET/POST /voice/twiml
     */
    public function twiml(Request $request)
    {
        $to = trim(
            (string) $request->input('To', '')
        );

        /*
         * Twilio phone number used as caller ID.
         *
         * Example:
         * TWILIO_PHONE_NUMBER=+1234567890
         */
        $callerId = trim(
            (string) env('TWILIO_PHONE_NUMBER')
        );

        if (empty($callerId)) {

            Log::error(
                'Twilio TwiML failed: TWILIO_PHONE_NUMBER missing'
            );

            $xml =
                '<?xml version="1.0" encoding="UTF-8"?>' .
                "\n" .
                '<Response>' .
                '<Say>Calling service is not configured.</Say>' .
                '</Response>';

            return response($xml, 500)
                ->header('Content-Type', 'text/xml');
        }

        $xml =
            '<?xml version="1.0" encoding="UTF-8"?>' .
            "\n";

        $xml .= '<Response>' . "\n";

        /*
         * OUTGOING CALL
         */
        if (!empty($to)) {

            $safeCallerId = htmlspecialchars(
                $callerId,
                ENT_XML1 | ENT_QUOTES,
                'UTF-8'
            );

            $safeTo = htmlspecialchars(
                $to,
                ENT_XML1 | ENT_QUOTES,
                'UTF-8'
            );

            $xml .=
                '  <Dial callerId="' .
                $safeCallerId .
                '">' .
                "\n";

            /*
             * Determine whether destination is:
             *
             * 1. Phone number
             * 2. Twilio Client identity
             */
            if (preg_match(
                '/^[\d\+\-\(\) ]+$/',
                $to
            )) {

                /*
                 * Phone number.
                 */
                $xml .=
                    '    <Number>' .
                    $safeTo .
                    '</Number>' .
                    "\n";

                Log::info(
                    'Twilio dialing phone number',
                    [
                        'number' => $to,
                    ]
                );

            } else {

                /*
                 * Client identity.
                 */
                $xml .=
                    '    <Client>' .
                    $safeTo .
                    '</Client>' .
                    "\n";

                Log::info(
                    'Twilio dialing client',
                    [
                        'client' => $to,
                    ]
                );
            }

            $xml .=
                '  </Dial>' .
                "\n";

        } else {

            /*
             * INCOMING CALL
             *
             * Ring browser identity "pratz".
             */
            $xml .=
                '  <Dial timeout="20" action="/voice/voicemail">' .
                "\n";

            $xml .=
                '    <Client>pratz</Client>' .
                "\n";

            $xml .=
                '  </Dial>' .
                "\n";

            Log::info(
                'Incoming Twilio call routed to pratz client'
            );
        }

        $xml .= '</Response>';

        return response($xml, 200)
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Voicemail fallback.
     *
     * POST /voice/voicemail
     */
    public function voicemail(Request $request)
    {
        $dialStatus = $request->input(
            'DialCallStatus',
            'unknown'
        );

        Log::info(
            'Twilio voicemail prompt triggered',
            [
                'DialCallStatus' => $dialStatus,
                'From' => $request->input('From'),
                'CallSid' => $request->input('CallSid'),
            ]
        );

        $xml =
            '<?xml version="1.0" encoding="UTF-8"?>' .
            "\n";

        $xml .= '<Response>' . "\n";

        /*
         * Call failed / busy / unanswered.
         */
        if (in_array(
            $dialStatus,
            [
                'no-answer',
                'busy',
                'failed',
            ],
            true
        )) {

            $xml .=
                '  <Say>' .
                'Sorry, we could not take your call. ' .
                'Please leave a message after the beep.' .
                '</Say>' .
                "\n";

            $xml .=
                '  <Record ' .
                'maxLength="120" ' .
                'playBeep="true" ' .
                'recordingStatusCallback="/voice/voicemail-saved" ' .
                'recordingStatusCallbackMethod="POST" ' .
                '/>' .
                "\n";

            $xml .=
                '  <Say>' .
                'No message received. Goodbye.' .
                '</Say>' .
                "\n";

        } else {

            $xml .=
                '  <Say>' .
                'Thank you for calling. Goodbye.' .
                '</Say>' .
                "\n";
        }

        $xml .= '</Response>';

        return response($xml, 200)
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Callback when voicemail recording is saved.
     *
     * POST /voice/voicemail-saved
     */
    public function voicemailSaved(Request $request)
    {
        $recordingUrl =
            $request->input('RecordingUrl');

        $recordingDuration =
            $request->input('RecordingDuration');

        $from =
            $request->input('From');

        $callSid =
            $request->input('CallSid');

        Log::info(
            'Twilio voicemail saved',
            [
                'from' => $from,

                'url' => $recordingUrl
                    ? $recordingUrl . '.mp3'
                    : null,

                'duration' => $recordingDuration,

                'call_sid' => $callSid,
            ]
        );

        /*
         * TODO:
         *
         * Save voicemail metadata to database.
         */

        return response('', 200);
    }

    /**
     * Render the browser dialer.
     *
     * GET /admin/voice/app
     */
    public function app()
    {
        return view(
            'admin.voice.dialer'
        );
    }
}