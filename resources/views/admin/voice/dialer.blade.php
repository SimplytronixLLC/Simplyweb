<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"
    >

    <title>Simplytronix Dialer</title>

    <script src="/js/twilio.min.js"></script>

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100%;
        }

        body {
            min-height: 100vh;

            background: #f3f4f6;

            font-family:
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                Roboto,
                Helvetica,
                Arial,
                sans-serif;

            color: #111827;
        }

        /* =========================================================
           PAGE
        ========================================================= */

        .dialer-page {
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 15px;
        }

        /* =========================================================
           DIALER
        ========================================================= */

        .dialer {
            width: 100%;
            max-width: 390px;

            background: #ffffff;

            border-radius: 20px;

            box-shadow:
                0 15px 40px rgba(0, 0, 0, 0.10);

            overflow: hidden;
        }

        /* =========================================================
           HEADER - REDUCED
        ========================================================= */

        .dialer-header {
            padding: 12px 20px 10px;

            text-align: center;

            border-bottom: 1px solid #f0f0f0;
        }

        .brand {
            display: flex;

            align-items: center;

            justify-content: center;

            margin-bottom: 2px;
        }

        .brand-logo {
            display: block;

            width: 125px;

            max-width: 75%;

            height: auto;

            max-height: 55px;

            object-fit: contain;
        }

        .brand-subtitle {
            font-size: 11px;

            color: #8b98ab;

            letter-spacing: 0.2px;
        }

        /* =========================================================
           STATUS - REDUCED
        ========================================================= */

        .status {
            margin: 10px 20px 0;

            padding: 8px 10px;

            border-radius: 10px;

            text-align: center;

            font-size: 13px;

            font-weight: 600;
        }

        .status.loading {
            background: #eff6ff;

            color: #1d4ed8;
        }

        .status.ready {
            background: #ecfdf5;

            color: #047857;
        }

        .status.error {
            background: #fef2f2;

            color: #dc2626;
        }

        .status.calling {
            background: #fff7ed;

            color: #c2410c;
        }

        /* =========================================================
           NUMBER DISPLAY - REDUCED
        ========================================================= */

        .number-area {
            padding: 13px 20px 2px;
        }

        .number-display {
            display: block;

            width: 100%;

            height: 38px;

            border: none;

            outline: none;

            background: transparent;

            text-align: center;

            font-size: 29px;

            font-weight: 400;

            letter-spacing: 1px;

            color: #111827;

            padding: 0;
        }

        .number-display::placeholder {
            color: #9ca3af;
        }

        .number-actions {
            height: 25px;

            display: flex;

            align-items: center;

            justify-content: center;
        }

        .delete-btn {
            width: 45px;

            height: 25px;

            border: none;

            background: transparent;

            color: #9ca3af;

            cursor: pointer;

            font-size: 21px;

            padding: 0;

            visibility: hidden;

            touch-action: manipulation;

            -webkit-tap-highlight-color: transparent;
        }

        .delete-btn.visible {
            visibility: visible;
        }

        .delete-btn:hover {
            color: #374151;
        }

        /* =========================================================
           KEYPAD - COMPACT
        ========================================================= */

        .keypad {
            padding: 7px 38px 5px;

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 7px 12px;
        }

        .key {
            width: 62px;

            height: 62px;

            margin: auto;

            padding: 0;

            border: none;

            border-radius: 50%;

            background: #f3f4f6;

            color: #111827;

            cursor: pointer;

            display: flex;

            flex-direction: column;

            align-items: center;

            justify-content: center;

            touch-action: manipulation;

            -webkit-user-select: none;

            user-select: none;

            -webkit-touch-callout: none;

            -webkit-tap-highlight-color: transparent;

            outline: none;

            transition:
                transform 0.08s ease,
                background 0.12s ease;
        }

        .key:hover {
            background: #e5e7eb;
        }

        .key:active {
            transform: scale(0.91);

            background: #d1d5db;
        }

        .key-number {
            font-size: 23px;

            line-height: 25px;

            font-weight: 400;
        }

        .key-letters {
            height: 11px;

            margin-top: 1px;

            font-size: 8px;

            line-height: 10px;

            letter-spacing: 1.4px;

            color: #6b7280;
        }

        /* =========================================================
           CALL AREA - REDUCED
        ========================================================= */

        .call-area {
            display: flex;

            align-items: center;

            justify-content: center;

            padding: 9px 20px 10px;
        }

        .call-btn,
        .hangup-btn {
            width: 62px;

            height: 62px;

            border-radius: 50%;

            border: none;

            color: #ffffff;

            font-size: 24px;

            cursor: pointer;

            transition:
                transform 0.1s ease,
                opacity 0.15s ease;
        }

        .call-btn {
            background: #16a34a;

            box-shadow:
                0 6px 15px rgba(
                    22,
                    163,
                    74,
                    0.25
                );
        }

        .call-btn:hover:not(:disabled) {
            background: #15803d;
        }

        .call-btn:active:not(:disabled) {
            transform: scale(0.94);
        }

        .call-btn:disabled {
            opacity: 0.45;

            cursor: not-allowed;

            box-shadow: none;
        }

        .call-btn.hidden {
            display: none;
        }

        .hangup-btn {
            display: none;

            background: #dc2626;

            box-shadow:
                0 6px 15px rgba(
                    220,
                    38,
                    38,
                    0.25
                );
        }

        .hangup-btn.visible {
            display: block;
        }

        .hangup-btn:hover {
            background: #b91c1c;
        }

        .hangup-btn:active {
            transform: scale(0.94);
        }

        /* =========================================================
           CALL INFO
        ========================================================= */

        .call-info {
            display: none;

            text-align: center;

            padding: 0 20px 10px;
        }

        .call-info.visible {
            display: block;
        }

        .call-label {
            font-size: 11px;

            color: #9ca3af;
        }

        .call-timer {
            margin-top: 2px;

            font-size: 13px;

            font-weight: 600;

            color: #374151;
        }

        /* =========================================================
           INCOMING CALL
        ========================================================= */

        .incoming {
            display: none;

            margin: 0 20px 12px;

            padding: 14px;

            border-radius: 14px;

            background: #fffbeb;

            border: 1px solid #fde68a;

            text-align: center;
        }

        .incoming.visible {
            display: block;
        }

        .incoming-icon {
            font-size: 26px;

            margin-bottom: 3px;
        }

        .incoming-title {
            font-size: 15px;

            font-weight: 700;
        }

        .incoming-number {
            margin-top: 2px;

            font-size: 12px;

            color: #6b7280;
        }

        .incoming-buttons {
            display: flex;

            justify-content: center;

            gap: 10px;

            margin-top: 12px;
        }

        .incoming-buttons button {
            border: none;

            border-radius: 9px;

            padding: 8px 16px;

            color: #ffffff;

            cursor: pointer;

            font-weight: 600;

            touch-action: manipulation;
        }

        .accept-btn {
            background: #16a34a;
        }

        .reject-btn {
            background: #dc2626;
        }

        /* =========================================================
           FOOTER
        ========================================================= */

        .dialer-footer {
            text-align: center;

            padding: 0 20px 10px;

            font-size: 10px;

            color: #9ca3af;
        }

        /* =========================================================
           MOBILE
        ========================================================= */

        @media (max-width: 420px) {

            .dialer-page {
                padding: 0;
            }

            .dialer {
                min-height: auto;

                max-width: none;

                border-radius: 0;

                box-shadow: none;
            }

            .dialer-header {
                padding-top: 10px;
            }

            .keypad {
                padding-left: 28px;

                padding-right: 28px;
            }
        }

        @media (max-width: 350px) {

            .keypad {
                padding-left: 18px;

                padding-right: 18px;

                gap: 6px 8px;
            }

            .key {
                width: 58px;

                height: 58px;
            }
        }
    </style>
</head>

<body>

<div class="dialer-page">

    <div class="dialer">

        <!-- =====================================================
             HEADER
        ====================================================== -->

        <div class="dialer-header">

            <div class="brand">

                <img
                    src="/public/assets/Simplylogo.png"
                    alt="Simplytronix"
                    class="brand-logo"
                >

            </div>

            <div class="brand-subtitle">
                Business Phone
            </div>

        </div>


        <!-- =====================================================
             STATUS
        ====================================================== -->

        <div
            id="status"
            class="status loading"
        >
            Fetching token...
        </div>


        <!-- =====================================================
             NUMBER
        ====================================================== -->

        <div class="number-area">

            <input
                type="tel"
                id="number"
                class="number-display"
                placeholder="+91"
                autocomplete="off"
                inputmode="tel"
                maxlength="20"
            >

            <div class="number-actions">

                <button
                    type="button"
                    id="deleteBtn"
                    class="delete-btn"
                    title="Delete"
                    aria-label="Delete last digit"
                >
                    ⌫
                </button>

            </div>

        </div>


        <!-- =====================================================
             KEYPAD
        ====================================================== -->

        <div class="keypad">

            <button type="button" class="key" data-value="1">
                <span class="key-number">1</span>
                <span class="key-letters">&nbsp;</span>
            </button>

            <button type="button" class="key" data-value="2">
                <span class="key-number">2</span>
                <span class="key-letters">ABC</span>
            </button>

            <button type="button" class="key" data-value="3">
                <span class="key-number">3</span>
                <span class="key-letters">DEF</span>
            </button>

            <button type="button" class="key" data-value="4">
                <span class="key-number">4</span>
                <span class="key-letters">GHI</span>
            </button>

            <button type="button" class="key" data-value="5">
                <span class="key-number">5</span>
                <span class="key-letters">JKL</span>
            </button>

            <button type="button" class="key" data-value="6">
                <span class="key-number">6</span>
                <span class="key-letters">MNO</span>
            </button>

            <button type="button" class="key" data-value="7">
                <span class="key-number">7</span>
                <span class="key-letters">PQRS</span>
            </button>

            <button type="button" class="key" data-value="8">
                <span class="key-number">8</span>
                <span class="key-letters">TUV</span>
            </button>

            <button type="button" class="key" data-value="9">
                <span class="key-number">9</span>
                <span class="key-letters">WXYZ</span>
            </button>

            <button type="button" class="key" data-value="*">
                <span class="key-number">*</span>
                <span class="key-letters">&nbsp;</span>
            </button>

            <!--
                0:
                Tap = 0
                Hold = +
            -->
            <button
                type="button"
                class="key"
                data-value="0"
                data-long-value="+"
                id="zeroKey"
            >
                <span class="key-number">0</span>
                <span class="key-letters">+</span>
            </button>

            <button type="button" class="key" data-value="#">
                <span class="key-number">#</span>
                <span class="key-letters">&nbsp;</span>
            </button>

        </div>


        <!-- =====================================================
             CALL BUTTON
        ====================================================== -->

        <div class="call-area">

            <button
                type="button"
                id="callBtn"
                class="call-btn"
                disabled
                title="Call"
                aria-label="Call"
            >
                📞
            </button>

            <button
                type="button"
                id="hangupBtn"
                class="hangup-btn"
                title="Hang up"
                aria-label="Hang up"
            >
                📞
            </button>

        </div>


        <!-- =====================================================
             CALL TIMER
        ====================================================== -->

        <div
            id="callInfo"
            class="call-info"
        >

            <div class="call-label">
                Call in progress
            </div>

            <div
                id="callTimer"
                class="call-timer"
            >
                00:00
            </div>

        </div>


        <!-- =====================================================
             INCOMING CALL
        ====================================================== -->

        <div
            id="incoming"
            class="incoming"
        >

            <div class="incoming-icon">
                📞
            </div>

            <div class="incoming-title">
                Incoming call
            </div>

            <div
                id="incomingNumber"
                class="incoming-number"
            >
                Unknown
            </div>

            <div class="incoming-buttons">

                <button
                    type="button"
                    id="acceptBtn"
                    class="accept-btn"
                >
                    Accept
                </button>

                <button
                    type="button"
                    id="rejectBtn"
                    class="reject-btn"
                >
                    Reject
                </button>

            </div>

        </div>


        <div class="dialer-footer">
            Simplytronix Business Phone
        </div>

    </div>

</div>


<script>

/*
|--------------------------------------------------------------------------
| Twilio
|--------------------------------------------------------------------------
*/

let device = null;
let activeCall = null;

let callTimerInterval = null;
let callStartTime = null;


/*
|--------------------------------------------------------------------------
| Long press state
|--------------------------------------------------------------------------
*/

let zeroLongPressTimer = null;
let zeroLongPressTriggered = false;


/*
|--------------------------------------------------------------------------
| DOM
|--------------------------------------------------------------------------
*/

const numberInput =
    document.getElementById('number');

const deleteBtn =
    document.getElementById('deleteBtn');

const callBtn =
    document.getElementById('callBtn');

const hangupBtn =
    document.getElementById('hangupBtn');

const statusEl =
    document.getElementById('status');

const callInfo =
    document.getElementById('callInfo');

const callTimer =
    document.getElementById('callTimer');

const incoming =
    document.getElementById('incoming');

const incomingNumber =
    document.getElementById('incomingNumber');

const acceptBtn =
    document.getElementById('acceptBtn');

const rejectBtn =
    document.getElementById('rejectBtn');

const zeroKey =
    document.getElementById('zeroKey');


/*
|--------------------------------------------------------------------------
| Status
|--------------------------------------------------------------------------
*/

function setStatus(
    message,
    type = 'loading'
) {

    statusEl.innerText =
        message;

    statusEl.className =
        'status ' + type;

    console.log(
        '[Status]',
        message,
        '(' + type + ')'
    );
}


/*
|--------------------------------------------------------------------------
| Number UI
|--------------------------------------------------------------------------
*/

function updateNumberUI() {

    if (
        numberInput.value.length > 0
    ) {

        deleteBtn.classList.add(
            'visible'
        );

    } else {

        deleteBtn.classList.remove(
            'visible'
        );

    }

}


/*
|--------------------------------------------------------------------------
| Add digit
|--------------------------------------------------------------------------
*/

function addDigit(value) {

    if (
        numberInput.value.length >= 20
    ) {
        return;
    }

    numberInput.value += value;

    updateNumberUI();

}


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

function deleteLastDigit() {

    if (
        numberInput.value.length === 0
    ) {
        return;
    }

    numberInput.value =
        numberInput.value.slice(
            0,
            -1
        );

    updateNumberUI();

    numberInput.focus();

}


/*
|--------------------------------------------------------------------------
| Normal keypad buttons
|
| IMPORTANT:
| Only pointerup is used.
| No click handler is attached.
|--------------------------------------------------------------------------
*/

document
    .querySelectorAll('.key:not(#zeroKey)')
    .forEach(function(button) {

        button.addEventListener(
            'pointerup',
            function(event) {

                event.preventDefault();
                event.stopPropagation();

                addDigit(
                    button.dataset.value
                );

            },
            {
                passive: false
            }
        );

    });


/*
|--------------------------------------------------------------------------
| ZERO KEY
|
| Tap 0       -> 0
|
| Hold 0      -> +
|
| Long press duration:
| 600 milliseconds
|--------------------------------------------------------------------------
*/

zeroKey.addEventListener(
    'pointerdown',
    function(event) {

        event.preventDefault();
        event.stopPropagation();

        zeroLongPressTriggered =
            false;


        zeroLongPressTimer =
            setTimeout(
                function() {

                    zeroLongPressTriggered =
                        true;

                    addDigit('+');

                    /*
                     * Haptic feedback where
                     * supported.
                     */
                    if (
                        navigator.vibrate
                    ) {

                        navigator.vibrate(
                            30
                        );

                    }

                },
                600
            );

    },
    {
        passive: false
    }
);


zeroKey.addEventListener(
    'pointerup',
    function(event) {

        event.preventDefault();
        event.stopPropagation();


        /*
         * Cancel long press timer.
         */
        if (
            zeroLongPressTimer
        ) {

            clearTimeout(
                zeroLongPressTimer
            );

            zeroLongPressTimer =
                null;

        }


        /*
         * If long press already
         * inserted +, don't insert 0.
         */
        if (
            zeroLongPressTriggered
        ) {

            zeroLongPressTriggered =
                false;

            return;

        }


        /*
         * Normal tap.
         */
        addDigit('0');

    },
    {
        passive: false
    }
);


/*
|--------------------------------------------------------------------------
| Cancel zero long press
|--------------------------------------------------------------------------
*/

zeroKey.addEventListener(
    'pointercancel',
    function(event) {

        if (
            zeroLongPressTimer
        ) {

            clearTimeout(
                zeroLongPressTimer
            );

            zeroLongPressTimer =
                null;

        }

        zeroLongPressTriggered =
            false;

    }
);


/*
|--------------------------------------------------------------------------
| Prevent context menu on long press
|--------------------------------------------------------------------------
*/

zeroKey.addEventListener(
    'contextmenu',
    function(event) {

        event.preventDefault();

    }
);


/*
|--------------------------------------------------------------------------
| Delete button
|--------------------------------------------------------------------------
*/

deleteBtn.addEventListener(
    'click',
    function(event) {

        event.preventDefault();
        event.stopPropagation();

        deleteLastDigit();

    }
);


/*
|--------------------------------------------------------------------------
| Input validation
|--------------------------------------------------------------------------
*/

numberInput.addEventListener(
    'input',
    function() {

        let value =
            numberInput.value;


        /*
         * Only allow:
         *
         * numbers
         * +
         * *
         * #
         */
        value =
            value.replace(
                /[^0-9+*#]/g,
                ''
            );


        /*
         * + can only be first.
         */
        if (
            value.indexOf('+') > 0
        ) {

            value =
                value.replace(
                    /\+/g,
                    ''
                );

        }


        /*
         * Only one +.
         */
        if (
            value.indexOf('+') !==
            value.lastIndexOf('+')
        ) {

            const firstPlus =
                value.indexOf('+');

            value =
                value.substring(
                    0,
                    firstPlus + 1
                ) +
                value.substring(
                    firstPlus + 1
                ).replace(
                    /\+/g,
                    ''
                );

        }


        numberInput.value =
            value.substring(
                0,
                20
            );


        updateNumberUI();

    }
);


/*
|--------------------------------------------------------------------------
| Keyboard support
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'keydown',
    function(event) {

        /*
         * Don't duplicate characters
         * when typing directly in input.
         */
        if (
            event.target === numberInput
        ) {

            return;

        }


        if (
            /^[0-9]$/.test(event.key)
        ) {

            event.preventDefault();

            addDigit(
                event.key
            );

            return;

        }


        if (
            event.key === '*' ||
            event.key === '#'
        ) {

            event.preventDefault();

            addDigit(
                event.key
            );

            return;

        }


        if (
            event.key === '+'
        ) {

            event.preventDefault();

            addDigit('+');

            return;

        }


        if (
            event.key === 'Backspace'
        ) {

            event.preventDefault();

            deleteLastDigit();

            return;

        }


        if (
            event.key === 'Enter' &&
            !callBtn.disabled
        ) {

            event.preventDefault();

            startCall();

        }

    }
);


/*
|--------------------------------------------------------------------------
| Normalize phone number
|--------------------------------------------------------------------------
*/

function normalizePhoneNumber(number) {

    number =
        number.trim();


    /*
     * Remove formatting.
     */
    number =
        number.replace(
            /[\s\-\(\)]/g,
            ''
        );


    /*
     * 9876543210
     *
     * -> +919876543210
     */
    if (
        /^[6-9]\d{9}$/.test(number)
    ) {

        number =
            '+91' + number;

    }


    /*
     * 09876543210
     *
     * -> +919876543210
     */
    if (
        /^0[6-9]\d{9}$/.test(number)
    ) {

        number =
            '+91' +
            number.substring(1);

    }


    return number;

}


/*
|--------------------------------------------------------------------------
| Timer
|--------------------------------------------------------------------------
*/

function startTimer() {

    stopTimer();

    callStartTime =
        Date.now();

    callTimerInterval =
        setInterval(
            updateTimer,
            1000
        );

    updateTimer();

}


function updateTimer() {

    if (!callStartTime) {
        return;
    }

    const totalSeconds =
        Math.floor(
            (
                Date.now() -
                callStartTime
            ) / 1000
        );


    const minutes =
        Math.floor(
            totalSeconds / 60
        );


    const seconds =
        totalSeconds % 60;


    callTimer.innerText =
        String(minutes)
            .padStart(2, '0')
        + ':' +
        String(seconds)
            .padStart(2, '0');

}


function stopTimer() {

    if (
        callTimerInterval
    ) {

        clearInterval(
            callTimerInterval
        );

        callTimerInterval =
            null;

    }

    callStartTime =
        null;

}


/*
|--------------------------------------------------------------------------
| Call UI
|--------------------------------------------------------------------------
*/

function showCallUI() {

    callBtn.classList.add(
        'hidden'
    );

    hangupBtn.classList.add(
        'visible'
    );

    callInfo.classList.add(
        'visible'
    );

}


function hideCallUI() {

    callBtn.classList.remove(
        'hidden'
    );

    hangupBtn.classList.remove(
        'visible'
    );

    callInfo.classList.remove(
        'visible'
    );

    stopTimer();

    callTimer.innerText =
        '00:00';

}


/*
|--------------------------------------------------------------------------
| Start call
|--------------------------------------------------------------------------
*/

async function startCall() {

    if (!device) {

        setStatus(
            'Phone is not ready',
            'error'
        );

        return;

    }


    const rawNumber =
        numberInput.value.trim();


    if (!rawNumber) {

        setStatus(
            'Enter a phone number',
            'error'
        );

        numberInput.focus();

        return;

    }


    const to =
        normalizePhoneNumber(
            rawNumber
        );


    console.log(
        '[Dialer] Calling:',
        to
    );


    try {

        callBtn.disabled =
            true;


        setStatus(
            'Calling ' + to + '...',
            'calling'
        );


        activeCall =
            await device.connect({
                params: {
                    To: to
                }
            });


        showCallUI();


        activeCall.on(
            'ringing',
            function() {

                setStatus(
                    'Ringing...',
                    'calling'
                );

            }
        );


        activeCall.on(
            'accept',
            function() {

                setStatus(
                    'Call connected',
                    'ready'
                );

                startTimer();

            }
        );


        activeCall.on(
            'disconnect',
            function() {

                activeCall =
                    null;

                hideCallUI();

                callBtn.disabled =
                    !device;

                setStatus(
                    'Ready to make calls',
                    'ready'
                );

            }
        );


        activeCall.on(
            'error',
            function(error) {

                console.error(
                    '[Dialer] Call error:',
                    error
                );

                activeCall =
                    null;

                hideCallUI();

                callBtn.disabled =
                    !device;

                setStatus(
                    'Call failed: ' +
                    (
                        error.message ||
                        'Unknown error'
                    ),
                    'error'
                );

            }
        );


    } catch (error) {

        console.error(
            '[Dialer] Connect error:',
            error
        );

        activeCall =
            null;

        hideCallUI();

        callBtn.disabled =
            !device;

        setStatus(
            'Call failed: ' +
            (
                error.message ||
                'Unknown error'
            ),
            'error'
        );

    }

}


/*
|--------------------------------------------------------------------------
| Call button
|--------------------------------------------------------------------------
*/

callBtn.addEventListener(
    'click',
    function(event) {

        event.preventDefault();

        startCall();

    }
);


/*
|--------------------------------------------------------------------------
| Hang up
|--------------------------------------------------------------------------
*/

hangupBtn.addEventListener(
    'click',
    function(event) {

        event.preventDefault();

        if (activeCall) {

            activeCall.disconnect();

            return;

        }


        if (device) {

            device.disconnectAll();

        }


        hideCallUI();

    }
);


/*
|--------------------------------------------------------------------------
| Incoming call
|--------------------------------------------------------------------------
*/

function handleIncomingCall(call) {

    activeCall =
        call;


    const from =
        call.parameters.From ||
        'Unknown';


    incomingNumber.innerText =
        from;


    incoming.classList.add(
        'visible'
    );


    setStatus(
        'Incoming call',
        'calling'
    );


    acceptBtn.onclick =
        function(event) {

            event.preventDefault();

            call.accept();

            incoming.classList.remove(
                'visible'
            );

            showCallUI();

            setStatus(
                'Call connected',
                'ready'
            );

            startTimer();

        };


    rejectBtn.onclick =
        function(event) {

            event.preventDefault();

            call.reject();

            incoming.classList.remove(
                'visible'
            );

            activeCall =
                null;

            setStatus(
                'Ready to make calls',
                'ready'
            );

        };


    call.on(
        'disconnect',
        function() {

            incoming.classList.remove(
                'visible'
            );

            activeCall =
                null;

            hideCallUI();

            callBtn.disabled =
                !device;

            setStatus(
                'Ready to make calls',
                'ready'
            );

        }
    );


    call.on(
        'error',
        function(error) {

            console.error(
                '[Dialer] Incoming error:',
                error
            );

            incoming.classList.remove(
                'visible'
            );

            activeCall =
                null;

            hideCallUI();

            callBtn.disabled =
                !device;

            setStatus(
                'Call error: ' +
                (
                    error.message ||
                    'Unknown error'
                ),
                'error'
            );

        }
    );

}


/*
|--------------------------------------------------------------------------
| Setup Twilio
|--------------------------------------------------------------------------
*/

async function setup() {

    try {

        setStatus(
            'Fetching token...',
            'loading'
        );


        const response =
            await fetch(
                '/admin/voice/token',
                {
                    method: 'GET',

                    headers: {
                        'Accept':
                            'application/json',

                        'X-Requested-With':
                            'XMLHttpRequest'
                    },

                    credentials:
                        'same-origin',

                    cache: 'no-store'
                }
            );


        if (!response.ok) {

            throw new Error(
                'Token endpoint returned HTTP ' +
                response.status
            );

        }


        const data =
            await response.json();


        if (!data.token) {

            throw new Error(
                'No token received from server'
            );

        }


        console.log(
            '[Twilio] Token received:',
            data.identity
        );


        setStatus(
            'Initializing phone...',
            'loading'
        );


        device =
            new Twilio.Device(
                data.token,
                {
                    codecPreferences: [
                        'opus',
                        'pcmu'
                    ],

                    logLevel: 1
                }
            );


        device.on(
            'registered',
            function() {

                console.log(
                    '[Twilio] Device registered'
                );

                setStatus(
                    'Ready to make calls',
                    'ready'
                );

                callBtn.disabled =
                    false;

            }
        );


        device.on(
            'incoming',
            handleIncomingCall
        );


        device.on(
            'error',
            function(error) {

                console.error(
                    '[Twilio] Device error:',
                    error
                );

                callBtn.disabled =
                    true;

                setStatus(
                    'Error: ' +
                    (
                        error.message ||
                        'Twilio error'
                    ),
                    'error'
                );

            }
        );


        device.on(
            'disconnect',
            function() {

                activeCall =
                    null;

                hideCallUI();

                callBtn.disabled =
                    true;

                setStatus(
                    'Phone disconnected',
                    'error'
                );

            }
        );


        await device.register();


    } catch (error) {

        console.error(
            '[Twilio] Setup error:',
            error
        );

        device =
            null;

        callBtn.disabled =
            true;

        setStatus(
            'Setup failed: ' +
            (
                error.message ||
                'Unable to initialize phone'
            ),
            'error'
        );

    }

}


/*
|--------------------------------------------------------------------------
| Initialize
|--------------------------------------------------------------------------
*/

if (
    typeof Twilio !== 'undefined' &&
    Twilio.Device
) {

    setup();

} else {

    window.addEventListener(
        'load',
        function() {

            if (
                typeof Twilio !== 'undefined' &&
                Twilio.Device
            ) {

                setup();

            } else {

                setStatus(
                    'Twilio SDK failed to load',
                    'error'
                );

            }

        }
    );

}

</script>

</body>
</html>