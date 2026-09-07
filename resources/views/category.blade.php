@extends('includes.front')

@section('seo')
@php
    $baseTitle = $settings->meta_title ?? 'Simplytronix';
@endphp
<title>Product Categories | {{ $baseTitle }}</title>
<meta name="title"       content="Product Categories | {{ $baseTitle }}">
<meta name="description" content="Browse 800+ electronic component categories at Simplytronix. Semiconductors, passives, RF, electromechanical and more. Fast global RFQ.">
<meta name="keywords"    content="{{ $settings->meta_keyword }}">
<meta name="language"    content="en">
<meta property="og:url"         content="{{ url()->current() }}">
<meta property="og:site_name"   content="{{ $baseTitle }}">
<meta property="og:type"        content="website">
<meta property="og:title"       content="Product Categories | {{ $baseTitle }}">
<meta property="og:description" content="Browse 800+ electronic component categories at Simplytronix.">
<meta property="og:image"       content="{{ url('public/'.$settings->logo) }}">
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:title"       content="Product Categories | {{ $baseTitle }}">
<meta name="twitter:description" content="Browse 800+ electronic component categories at Simplytronix.">
<meta name="twitter:image"       content="{{ url('public/'.$settings->logo) }}">
@stop

@section('content')

@php
use App\Models\Category;

// ── Icon map — covers all L1 roots + every L2 subcategory ──────────────────
$iconMap = [
    // L1 roots
    1    => 'ti-cpu',
    91   => 'ti-bolt',
    110  => 'ti-circuit-resistor',
    138  => 'ti-radar-2',
    152  => 'ti-antenna',
    161  => 'ti-plug',
    5107 => 'ti-bulb',
    5111 => 'ti-settings-2',
    5113 => 'ti-code',
    5116 => 'ti-tool',

    // Semiconductors L2
    2    => 'ti-arrow-right',
    12   => 'ti-transistor',
    22   => 'ti-circuit-board',
    29   => 'ti-logic-buffer',
    35   => 'ti-database',
    46   => 'ti-wavesaw-mono',
    57   => 'ti-switch-horizontal',
    62   => 'ti-exchange',
    73   => 'ti-logic-and',
    81   => 'ti-clock',
    4430 => 'ti-current-dc',
    4431 => 'ti-current-ac',
    4448 => 'ti-shield-lock',
    4553 => 'ti-chip',
    4575 => 'ti-device-tv',
    5026 => 'ti-ripple',
    5051 => 'ti-laser',

    // Power & Power Management L2
    93   => 'ti-transfer-in',
    94   => 'ti-adjustments-horizontal',
    95   => 'ti-line',
    96   => 'ti-settings-automation',
    97   => 'ti-circuit-capacitor',
    98   => 'ti-temperature-plus',
    99   => 'ti-toggle-right',
    100  => 'ti-steering-wheel',
    101  => 'ti-battery',
    102  => 'ti-adjustments',
    103  => 'ti-wave-square',
    104  => 'ti-plug-connected',
    105  => 'ti-arrows-exchange',
    106  => 'ti-server',
    107  => 'ti-plug',
    108  => 'ti-server-2',
    109  => 'ti-battery-eco',
    4358 => 'ti-bulb',
    4359 => 'ti-engine',
    4360 => 'ti-arrows-exchange-2',
    4361 => 'ti-eye-check',
    4387 => 'ti-battery-1',
    4390 => 'ti-git-compare',
    4399 => 'ti-adjustments-alt',
    4417 => 'ti-solar-panel',
    4423 => 'ti-battery-charging',
    4426 => 'ti-plug-x',
    4441 => 'ti-wifi',
    4447 => 'ti-wall',
    4456 => 'ti-battery-charging-2',
    4476 => 'ti-device-desktop',
    4478 => 'ti-shield-bolt',
    4489 => 'ti-shield-check',
    4493 => 'ti-device-floppy',
    4503 => 'ti-switch-2',
    4535 => 'ti-shield',
    4548 => 'ti-wifi-2',
    4562 => 'ti-battery-2',
    4574 => 'ti-bulb',
    4577 => 'ti-adjustments-bolt',
    4635 => 'ti-barrier-block',
    4640 => 'ti-circuit-diode',
    4641 => 'ti-circuit-diode',
    4652 => 'ti-arrows-up-down',
    4698 => 'ti-bulb',
    4715 => 'ti-plug-connected',
    4724 => 'ti-network',
    4745 => 'ti-gauge',
    4757 => 'ti-box',
    4770 => 'ti-laser',
    4811 => 'ti-server-bolt',
    4812 => 'ti-server-cog',
    4819 => 'ti-circuit-pushbutton',
    4823 => 'ti-battery-eco',
    4866 => 'ti-plug',
    4868 => 'ti-server',
    4882 => 'ti-device-desktop-analytics',
    4884 => 'ti-server-cog',
    4885 => 'ti-network',
    4888 => 'ti-refresh',
    4898 => 'ti-network',
    4899 => 'ti-share',
    4902 => 'ti-temperature',
    4927 => 'ti-circuit-ground',
    4941 => 'ti-plug',
    4948 => 'ti-plug-connected',
    4950 => 'ti-barrier-block',
    4956 => 'ti-battery-eco',
    4959 => 'ti-server',
    4961 => 'ti-plug',
    4970 => 'ti-arrows-exchange',
    4971 => 'ti-arrows-exchange',
    4972 => 'ti-battery-eco',
    4978 => 'ti-components',
    4979 => 'ti-server',
    5000 => 'ti-transfer',
    5072 => 'ti-plug',
    5088 => 'ti-plug-x',
    5100 => 'ti-bulb',
    5106 => 'ti-adjustments',
    5109 => 'ti-circuit-capacitor',
    5110 => 'ti-plug-connected',
    5115 => 'ti-battery',
    5118 => 'ti-adjustments-horizontal',
    5126 => 'ti-arrows-exchange',

    // Passive Components L2
    111  => 'ti-circuit-capacitor',
    122  => 'ti-circuit-resistor',
    131  => 'ti-circuit-inductor',
    4398 => 'ti-filter',
    4400 => 'ti-shield-half',
    4401 => 'ti-ring',
    4434 => 'ti-ring',
    4435 => 'ti-filter',
    4466 => 'ti-dial',
    4501 => 'ti-shield-bolt',
    4518 => 'ti-slider-horizontal',
    4647 => 'ti-circuit-resistor',
    4708 => 'ti-square-x',
    4711 => 'ti-device-speaker',
    4716 => 'ti-circuit-resistor',
    4717 => 'ti-circuit-inductor',
    4744 => 'ti-circuit-inductor',
    4756 => 'ti-current-ac',
    4772 => 'ti-circuit-resistor',
    4777 => 'ti-circuit-resistor',
    4813 => 'ti-circuit-capacitor',
    4815 => 'ti-circuit-resistor',
    4824 => 'ti-circuit-capacitor',
    4849 => 'ti-circuit-resistor',
    4850 => 'ti-circuit-resistor',
    4855 => 'ti-circuit-resistor',
    4869 => 'ti-filter',
    4871 => 'ti-circuit-inductor',
    4883 => 'ti-circuit-resistor',
    4895 => 'ti-transformer',
    4914 => 'ti-circuit-resistor',
    4915 => 'ti-circuit-resistor',
    4916 => 'ti-circuit-resistor',
    4917 => 'ti-circuit-capacitor',
    4919 => 'ti-circuit-capacitor',
    4920 => 'ti-ring',
    4921 => 'ti-circuit-resistor',
    4928 => 'ti-circuit-capacitor',
    4929 => 'ti-circuit-capacitor',
    4930 => 'ti-circuit-capacitor',
    4931 => 'ti-circuit-capacitor',
    4932 => 'ti-circuit-capacitor',
    4933 => 'ti-circuit-capacitor',
    4934 => 'ti-circuit-capacitor',
    4935 => 'ti-dial',
    4936 => 'ti-circuit-capacitor',
    4937 => 'ti-circuit-capacitor',
    4938 => 'ti-circuit-capacitor',
    4939 => 'ti-circuit-capacitor',
    4940 => 'ti-engine',
    4949 => 'ti-circuit-inductor',
    4952 => 'ti-sun',
    4958 => 'ti-package',
    4965 => 'ti-circuit-capacitor',
    4967 => 'ti-package',
    4968 => 'ti-dial',
    4982 => 'ti-circuit-resistor',
    4986 => 'ti-circuit-resistor',
    4989 => 'ti-dial',
    4991 => 'ti-transformer',
    4995 => 'ti-transformer',
    4998 => 'ti-circuit-capacitor',
    5074 => 'ti-transformer',
    5080 => 'ti-circuit-capacitor',
    5147 => 'ti-package',

    // Sensors & Measurement L2
    4410 => 'ti-device-gamepad',
    4470 => 'ti-rotate-clockwise',
    4490 => 'ti-wave-sine',
    4579 => 'ti-microphone',
    4667 => 'ti-hand-finger',
    4668 => 'ti-chart-dots',
    4671 => 'ti-fingerprint',
    4686 => 'ti-user-scan',
    4695 => 'ti-temperature',
    4701 => 'ti-separator-horizontal',
    4713 => 'ti-temperature',
    4720 => 'ti-photo-sensor',
    4721 => 'ti-gauge',
    4723 => 'ti-gauge',
    4735 => 'ti-plug-connected',
    4749 => 'ti-temperature',
    4750 => 'ti-tool',
    4751 => 'ti-magnet',
    4761 => 'ti-cone',
    4763 => 'ti-hand-finger',
    4799 => 'ti-flame',
    4800 => 'ti-eye',
    4802 => 'ti-ruler-measure',
    4803 => 'ti-cable',
    4804 => 'ti-separator-horizontal',
    4806 => 'ti-temperature-plus',
    4816 => 'ti-run',
    4821 => 'ti-arrows-horizontal',
    4822 => 'ti-radar',
    4840 => 'ti-run',
    4922 => 'ti-current-ac',
    4923 => 'ti-gyroscope',
    4924 => 'ti-droplet',
    4953 => 'ti-magnet',
    4957 => 'ti-chart-dots-3',
    4977 => 'ti-ruler',
    4984 => 'ti-ruler-measure',
    4987 => 'ti-plug-connected',
    5021 => 'ti-thermometer',
    5084 => 'ti-droplets',
    5136 => 'ti-thermometer',
    5156 => 'ti-dashboard',
    5157 => 'ti-current-ac',

    // RF & Wireless L2
    92   => 'ti-wave-square',
    139  => 'ti-chart-dots',
    153  => 'ti-antenna',
    4393 => 'ti-wave-sine',
    4411 => 'ti-device-speaker',
    4461 => 'ti-cone',
    4482 => 'ti-wifi',
    4505 => 'ti-cone',
    4506 => 'ti-arrows-exchange',
    4547 => 'ti-antenna',
    4642 => 'ti-wifi',
    4646 => 'ti-wifi',
    4649 => 'ti-antenna',
    4692 => 'ti-circuit-board',
    4704 => 'ti-cable',
    4705 => 'ti-plug',
    4722 => 'ti-cone',
    4743 => 'ti-arrows-exchange',
    4748 => 'ti-arrows-left-right',
    4752 => 'ti-blend-mode',
    4797 => 'ti-antenna',
    4818 => 'ti-nfc',
    4829 => 'ti-wave-sine',
    4832 => 'ti-adjustments-horizontal',
    4838 => 'ti-arrows-exchange',
    4839 => 'ti-antenna-bars-4',
    4848 => 'ti-send',
    4887 => 'ti-cone',
    4926 => 'ti-plug-x',
    4943 => 'ti-arrows-exchange',
    4981 => 'ti-antenna',
    4983 => 'ti-antenna',
    5005 => 'ti-wifi',
    5079 => 'ti-cone',
    5138 => 'ti-nfc',
    5144 => 'ti-plug',

    // Electromechanical L2
    162  => 'ti-toggle-left',
    168  => 'ti-circuit-switch-closed',
    169  => 'ti-circuit-switch-closed',
    170  => 'ti-plug',
    171  => 'ti-plug',
    172  => 'ti-shield-bolt',
    173  => 'ti-shield-bolt',
    4375 => 'ti-square-rounded',
    4392 => 'ti-arrow-bar-right',
    4396 => 'ti-circuit-breaker',
    4407 => 'ti-shoe',
    4408 => 'ti-toggle-left',
    4412 => 'ti-device-speaker',
    4416 => 'ti-layout-board',
    4419 => 'ti-plug-connected',
    4425 => 'ti-dial',
    4429 => 'ti-circle',
    4454 => 'ti-square-rounded',
    4455 => 'ti-bulb',
    4463 => 'ti-toggle-left',
    4464 => 'ti-screwdriver',
    4469 => 'ti-package',
    4471 => 'ti-shield',
    4472 => 'ti-hand-click',
    4473 => 'ti-keyboard',
    4479 => 'ti-car',
    4486 => 'ti-paperclip',
    4488 => 'ti-plug',
    4494 => 'ti-lock',
    4508 => 'ti-refresh',
    4509 => 'ti-engine',
    4512 => 'ti-plug-connected',
    4517 => 'ti-network',
    4537 => 'ti-toggle-right',
    4540 => 'ti-circuit-switch-closed',
    4541 => 'ti-magnet',
    4544 => 'ti-plug',
    4546 => 'ti-settings',
    4550 => 'ti-plug',
    4551 => 'ti-test-pipe',
    4555 => 'ti-joystick',
    4556 => 'ti-network',
    4563 => 'ti-bulb',
    4564 => 'ti-shield-bolt',
    4567 => 'ti-hand-click',
    4568 => 'ti-toggle-left',
    4569 => 'ti-circuit-switch-open',
    4636 => 'ti-circuit-switch-closed',
    4685 => 'ti-plug',
    4691 => 'ti-terminal-2',
    4700 => 'ti-network',
    4709 => 'ti-terminal',
    4710 => 'ti-engine',
    4728 => 'ti-usb',
    4729 => 'ti-car',
    4731 => 'ti-plug',
    4732 => 'ti-plug',
    4734 => 'ti-hand-grab',
    4762 => 'ti-car',
    4774 => 'ti-circuit-switch-closed',
    4780 => 'ti-memory-2',
    4783 => 'ti-credit-card',
    4786 => 'ti-plug',
    4787 => 'ti-memory-2',
    4788 => 'ti-clock',
    4789 => 'ti-circuit-switch-closed',
    4791 => 'ti-layout-rows',
    4795 => 'ti-industry',
    4808 => 'ti-plug',
    4826 => 'ti-circuit-board',
    4846 => 'ti-droplet',
    4867 => 'ti-circuit-switch-closed',
    4876 => 'ti-tool',
    4879 => 'ti-plug',
    4881 => 'ti-layout-board-split',
    4908 => 'ti-columns-2',
    4944 => 'ti-plug',
    4945 => 'ti-terminal-2',
    4946 => 'ti-plug-connected',
    4947 => 'ti-plug',
    4985 => 'ti-plug',
    4990 => 'ti-terminal',
    4993 => 'ti-rotate-clockwise',
    5018 => 'ti-plug',
    5023 => 'ti-circuit-board',
    5024 => 'ti-tool',
    5027 => 'ti-plug',
    5028 => 'ti-square-x',
    5029 => 'ti-square-rounded',
    5049 => 'ti-plug',
    5075 => 'ti-test-pipe',
    5077 => 'ti-bulb',
    5078 => 'ti-plug',
    5082 => 'ti-plug',
    5083 => 'ti-plug',
    5087 => 'ti-dial',
    5089 => 'ti-engine',
    5090 => 'ti-plug',
    5134 => 'ti-urgent',

    // Displays & Optoelectronics L2
    4382 => 'ti-volume',
    4383 => 'ti-volume-2',
    4384 => 'ti-indicator',
    4420 => 'ti-bulb',
    4436 => 'ti-shield-half',
    4439 => 'ti-camera',
    4467 => 'ti-lens',
    4532 => 'ti-device-tablet',
    4573 => 'ti-traffic-lights',
    5013 => 'ti-news',
    5045 => 'ti-line',
    5046 => 'ti-alarm',
    5057 => 'ti-sun',
    5060 => 'ti-bulb',
    5062 => 'ti-desk-lamp',
    5064 => 'ti-circle-dashed',
    5099 => 'ti-traffic-lights',
    5103 => 'ti-bulb',

    // Industrial & Mechanical L2
    4402 => 'ti-pipe',
    4424 => 'ti-box',
    4442 => 'ti-chart-line',
    4449 => 'ti-robot',
    4457 => 'ti-device-desktop',
    4462 => 'ti-bolt',
    4474 => 'ti-printer',
    4477 => 'ti-server',
    4487 => 'ti-plug',
    4491 => 'ti-fence',
    4495 => 'ti-hand',
    4499 => 'ti-camera',
    4524 => 'ti-key',
    4525 => 'ti-usb',
    4531 => 'ti-settings-automation',
    4536 => 'ti-layers-subtract',
    4554 => 'ti-router',
    4557 => 'ti-circle',
    4559 => 'ti-cut',
    4561 => 'ti-settings',
    4572 => 'ti-cpu',
    4576 => 'ti-flask',
    4585 => 'ti-device-gamepad-2',
    4586 => 'ti-engine',
    4589 => 'ti-screw',
    4590 => 'ti-battery-charging',
    4805 => 'ti-switch-horizontal',
    4901 => 'ti-snowflake',
    4903 => 'ti-temperature',
    5022 => 'ti-server',
    5031 => 'ti-tool',
    5032 => 'ti-gauge',
    5038 => 'ti-tool',
    5058 => 'ti-ear',
    5070 => 'ti-code',
    5086 => 'ti-box',

    // Development & Embedded L2
    4380 => 'ti-cpu',
    4386 => 'ti-device-floppy',
    4428 => 'ti-cpu',
    4445 => 'ti-printer',
    4504 => 'ti-brand-open-source',
    4511 => 'ti-brand-raspberry-pi',
    4526 => 'ti-robot',
    4534 => 'ti-brand-open-source',
    4543 => 'ti-device-floppy',
    4565 => 'ti-brand-open-source',
    4587 => 'ti-router',
    4648 => 'ti-exchange',
    4654 => 'ti-wavesaw-mono',
    4670 => 'ti-eye',
    4697 => 'ti-plug',
    4703 => 'ti-bolt',
    4760 => 'ti-database',
    4781 => 'ti-logic-and',
    4831 => 'ti-magnet',
    4836 => 'ti-toggle-left',
    4889 => 'ti-brand-open-source',
    4890 => 'ti-robot',
    4892 => 'ti-device-desktop',
    4911 => 'ti-circuit-board',
    4997 => 'ti-circuit-board',
    5010 => 'ti-device-speaker',
    5014 => 'ti-robot',
    5017 => 'ti-switch-horizontal',
    5040 => 'ti-device-tv',
    5041 => 'ti-server',
    5050 => 'ti-lock',
    5076 => 'ti-brand-open-source',
    5098 => 'ti-package',

    // Tools & Equipment L2
    4389 => 'ti-tool',
    4395 => 'ti-box',
    4415 => 'ti-plug',
    4444 => 'ti-wavesaw-mono',
    4484 => 'ti-droplet',
    4485 => 'ti-square',
    4496 => 'ti-tool',
    4510 => 'ti-cable',
    4521 => 'ti-eraser',
    4523 => 'ti-package',
    4528 => 'ti-certificate',
    4529 => 'ti-flame',
    4530 => 'ti-temperature',
    4582 => 'ti-paperclip',
    4591 => 'ti-screwdriver',
    4942 => 'ti-tool',
    4960 => 'ti-gauge',
    4963 => 'ti-ruler-measure',
    4976 => 'ti-wave-sine',
    5034 => 'ti-wave-square',
    5036 => 'ti-gauge',
    5044 => 'ti-chart-bar',
    5054 => 'ti-cut',
    5056 => 'ti-tool',
    5067 => 'ti-dial',
    5069 => 'ti-plug-connected',
    5094 => 'ti-device-floppy',
    5095 => 'ti-bug',
    5105 => 'ti-droplet',
    5117 => 'ti-flame',
    5123 => 'ti-plug',
    5127 => 'ti-ruler-measure',
];

// ── Color map for L1 roots ─────────────────────────────────────────────────
$colorMap = [
    1    => '#f05d21',
    91   => '#1a73e8',
    110  => '#0f9d58',
    138  => '#9c27b0',
    152  => '#e91e63',
    161  => '#00bcd4',
    5107 => '#ff9800',
    5111 => '#607d8b',
    5113 => '#795548',
    5116 => '#2196f3',
];

// ── Build full 3-level tree ────────────────────────────────────────────────
$rootCategories = Category::whereNull('parent_id')
    ->orderBy('name')
    ->get();

$tree = $rootCategories->map(function ($root) {
    $subs = Category::where('parent_id', $root->id)->orderBy('name')->get();
    $subs = $subs->map(function ($sub) {
        $sub->children = Category::where('parent_id', $sub->id)->orderBy('name')->get();
        return $sub;
    });
    $root->subs = $subs;
    return $root;
});

// ── Build JS search index ─────────────────────────────────────────────────
$searchIndex = [];
foreach ($tree as $root) {
    foreach ($root->subs as $sub) {
        $searchIndex[] = [
            'name'   => $sub->name,
            'slug'   => $sub->slug,
            'parent' => $root->name,
            'color'  => $colorMap[$root->id] ?? '#f05d21',
            'url'    => url('/category/' . $sub->slug),
        ];
        foreach ($sub->children as $leaf) {
            $searchIndex[] = [
                'name'   => $leaf->name,
                'slug'   => $leaf->slug,
                'parent' => $sub->name,
                'color'  => $colorMap[$root->id] ?? '#f05d21',
                'url'    => url('/category/' . $leaf->slug),
            ];
        }
    }
}

$totalCats = Category::count();
@endphp

<style>
.smt-cat-wrap *{box-sizing:border-box;margin:0;padding:0}
.smt-cat-wrap{display:flex;min-height:75vh;background:#f5f6fa}

/* ── Sidebar ── */
.smt-sidebar{width:272px;flex-shrink:0;background:#fff;border-right:1px solid #eee;padding:12px 0;position:sticky;top:0;align-self:flex-start;max-height:90vh;overflow-y:auto}
.smt-sidebar-title{font-size:11px;font-weight:600;color:#bbb;text-transform:uppercase;letter-spacing:.8px;padding:0 18px 10px}
.smt-l1{display:flex;align-items:center;gap:10px;padding:9px 18px;cursor:pointer;border-left:3px solid transparent;transition:all .15s}
.smt-l1:hover{background:#fafafa}
.smt-l1.active{border-left-color:var(--clr);background:#fff8f5}
.smt-l1.active .smt-l1-name{color:var(--clr);font-weight:600}
.smt-l1-icon{width:30px;height:30px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0}
.smt-l1-name{font-size:13px;color:#444;flex:1;line-height:1.3}
.smt-l1-count{font-size:11px;color:#bbb;background:#f5f5f5;border-radius:8px;padding:1px 7px;white-space:nowrap}

/* ── Main ── */
.smt-main{flex:1;padding:24px 28px;min-width:0}

/* ── Filter bar ── */
.smt-filterbar{display:flex;align-items:center;gap:12px;margin-bottom:22px;flex-wrap:wrap}
.smt-filter-wrap{position:relative;flex:1;max-width:400px}
.smt-filter-wrap i{position:absolute;left:11px;top:50%;transform:translateY(-50%);font-size:15px;color:#bbb;pointer-events:none}
#smtSearch{width:100%;height:38px;border:1px solid #e0e0e0;border-radius:9px;padding:0 12px 0 35px;font-size:14px;outline:none;transition:border-color .15s;background:#fff}
#smtSearch:focus{border-color:#f05d21}
.smt-result-count{font-size:13px;color:#aaa;margin-left:auto}

/* ── Search results ── */
.smt-search-results{display:none;margin-bottom:4px}
.smt-search-results h4{font-size:13px;color:#999;margin-bottom:12px}
.smt-search-tags{display:flex;flex-wrap:wrap;gap:8px}
.smt-stag{display:flex;align-items:baseline;gap:6px;background:#fff;border:1px solid #eee;border-radius:8px;padding:7px 12px;font-size:13px;color:#333;text-decoration:none;transition:all .15s;border-left-width:3px}
.smt-stag:hover{background:#fff8f5;text-decoration:none;color:#f05d21}
.smt-stag-parent{font-size:11px;color:#bbb}
.smt-no-results{display:none;text-align:center;padding:60px 24px;color:#ccc}
.smt-no-results i{font-size:40px;display:block;margin-bottom:10px}

/* ── Landing grid ── */
.smt-landing{display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:14px}
.smt-landing-card{background:#fff;border:1px solid #eee;border-radius:12px;padding:20px;cursor:pointer;transition:border-color .2s,transform .2s}
.smt-landing-card:hover{border-color:var(--clr);transform:translateY(-2px)}
.smt-lc-icon{width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;margin-bottom:12px}
.smt-lc-name{font-size:14px;font-weight:600;color:#1a1a2e;margin-bottom:3px}
.smt-lc-sub{font-size:12px;color:#aaa}

/* ── Category panel ── */
.smt-panel{display:none}
.smt-panel.active{display:block}

/* Panel hero */
.smt-hero{display:flex;align-items:center;gap:14px;padding:18px 20px;border-radius:12px;margin-bottom:22px}
.smt-hero-icon{width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;flex-shrink:0}
.smt-hero-title{font-size:18px;font-weight:700;color:#1a1a2e;margin-bottom:2px}
.smt-hero-sub{font-size:13px;color:#888}

/* L2 section headers */
.smt-l2-section{margin-bottom:24px}
.smt-l2-head{display:flex;align-items:center;gap:8px;margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid #eee}
.smt-l2-icon{width:24px;height:24px;border-radius:5px;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0}
.smt-l2-label{font-size:14px;font-weight:600;color:#1a1a2e}
.smt-l2-badge{font-size:11px;color:#999;background:#f0f0f0;border-radius:7px;padding:2px 8px;margin-left:auto}
.smt-l2-link{font-size:12px;color:var(--clr);text-decoration:none;margin-left:auto}
.smt-l2-link:hover{text-decoration:underline}

/* L3 subcategory cards */
.smt-sub-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(195px,1fr));gap:8px}
.smt-sub-card{display:flex;align-items:center;gap:8px;padding:9px 12px;background:#fff;border:1px solid #eee;border-radius:9px;text-decoration:none;color:#444;font-size:13px;font-weight:500;line-height:1.3;transition:all .15s}
.smt-sub-card:hover{border-color:var(--clr);color:var(--clr);background:#fff8f5;text-decoration:none}
.smt-sub-dot{width:7px;height:7px;border-radius:50%;background:var(--clr);flex-shrink:0;opacity:.4;transition:opacity .15s}
.smt-sub-card:hover .smt-sub-dot{opacity:1}

/* Flat grid (no L3 children) */
.smt-flat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:8px}
.smt-flat-card{display:flex;align-items:center;gap:10px;padding:11px 14px;background:#fff;border:1px solid #eee;border-radius:9px;text-decoration:none;color:#444;font-size:13px;font-weight:500;transition:all .15s}
.smt-flat-card:hover{border-color:var(--clr);background:#fff8f5;color:var(--clr);text-decoration:none}
.smt-flat-card .smt-flat-icon{width:26px;height:26px;border-radius:5px;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0}

@media(max-width:768px){
    .smt-sidebar{display:none}
    .smt-main{padding:16px 14px}
    .smt-landing{grid-template-columns:repeat(auto-fill,minmax(140px,1fr))}
}
</style>

<main class="main__content_wrapper">

{{-- Breadcrumb --}}
<div style="padding:12px 24px;border-bottom:1px solid #eee;background:#fff;font-size:13px;color:#888;display:flex;align-items:center;gap:6px">
    <a href="{{ url('/') }}" style="color:#f05d21;text-decoration:none">Home</a>
    <span>›</span>
    <span>Product Categories</span>
</div>

<div class="smt-cat-wrap">

    {{-- ── Sidebar ── --}}
    <nav class="smt-sidebar">
        <div class="smt-sidebar-title">Product Groups</div>

        @foreach($tree as $root)
        @php
            $clr   = $colorMap[$root->id] ?? '#f05d21';
            $icon  = $iconMap[$root->id]  ?? 'ti-folder';
            $total = $root->subs->count() + $root->subs->sum(fn($s) => $s->children->count());
        @endphp
        <div class="smt-l1" style="--clr:{{ $clr }}" onclick="smtShow({{ $root->id }})" id="smt-nav-{{ $root->id }}">
            <div class="smt-l1-icon" style="background:{{ $clr }}22;color:{{ $clr }}">
                <i class="ti {{ $icon }}"></i>
            </div>
            <span class="smt-l1-name">{{ $root->name }}</span>
            <span class="smt-l1-count">{{ $total }}</span>
        </div>
        @endforeach
    </nav>

    {{-- ── Main area ── --}}
    <div class="smt-main">

        {{-- Filter bar --}}
        <div class="smt-filterbar">
            <div class="smt-filter-wrap">
                <i class="ti ti-search"></i>
                <input id="smtSearch" type="search" placeholder="Search all categories…" autocomplete="off">
            </div>
            <span class="smt-result-count" id="smtCount">{{ $totalCats }} categories</span>
        </div>

        {{-- Search results --}}
        <div class="smt-search-results" id="smtSearchResults">
            <h4 id="smtSearchLabel"></h4>
            <div class="smt-search-tags" id="smtSearchTags"></div>
        </div>
        <div class="smt-no-results" id="smtNoResults">
            <i class="ti ti-search-off"></i>No categories found
        </div>

        {{-- Landing: all groups as cards --}}
        <div class="smt-landing" id="smtLanding">
            @foreach($tree as $root)
            @php
                $clr  = $colorMap[$root->id] ?? '#f05d21';
                $icon = $iconMap[$root->id]  ?? 'ti-folder';
                $c2   = $root->subs->count();
            @endphp
            <div class="smt-landing-card" style="--clr:{{ $clr }}" onclick="smtShow({{ $root->id }})">
                <div class="smt-lc-icon" style="background:{{ $clr }}22;color:{{ $clr }}">
                    <i class="ti {{ $icon }}"></i>
                </div>
                <div class="smt-lc-name">{{ $root->name }}</div>
                <div class="smt-lc-sub">{{ $c2 }} subcategories</div>
            </div>
            @endforeach
        </div>

        {{-- Category panels --}}
        @foreach($tree as $root)
        @php
            $clr  = $colorMap[$root->id] ?? '#f05d21';
            $icon = $iconMap[$root->id]  ?? 'ti-folder';
            $c2   = $root->subs->count();
            $c3   = $root->subs->sum(fn($s) => $s->children->count());
            $hasSubcategories = $root->subs->some(fn($s) => $s->children->count() > 0);
        @endphp
        <div class="smt-panel" id="smt-panel-{{ $root->id }}" style="--clr:{{ $clr }}">

            {{-- Hero bar --}}
            <div class="smt-hero" style="background:{{ $clr }}11">
                <div class="smt-hero-icon" style="background:{{ $clr }}">
                    <i class="ti {{ $icon }}"></i>
                </div>
                <div>
                    <div class="smt-hero-title">{{ $root->name }}</div>
                    <div class="smt-hero-sub">
                        {{ $c2 }} subcategories
                        @if($c3 > 0) · {{ $c3 }} sub-subcategories @endif
                    </div>
                </div>
            </div>

            @if($hasSubcategories)
                {{-- Structured: L2 headers + L3 cards --}}
                @foreach($root->subs as $sub)
                @php
                    $subIcon = $iconMap[$sub->id] ?? 'ti-folder';
                @endphp
                <div class="smt-l2-section">
                    <div class="smt-l2-head">
                        <div class="smt-l2-icon" style="background:{{ $clr }}22;color:{{ $clr }}">
                            <i class="ti {{ $subIcon }}"></i>
                        </div>
                        <span class="smt-l2-label">{{ $sub->name }}</span>
                        @if($sub->children->count() > 0)
                            <span class="smt-l2-badge">{{ $sub->children->count() }} subcategories</span>
                        @else
                            <a class="smt-l2-link" href="{{ url('/category/'.$sub->slug) }}">Browse →</a>
                        @endif
                    </div>

                    @if($sub->children->count() > 0)
                    <div class="smt-sub-grid">
                        @foreach($sub->children as $leaf)
                        <a class="smt-sub-card" href="{{ url('/category/'.$leaf->slug) }}">
                            <span class="smt-sub-dot"></span>
                            {{ $leaf->name }}
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach

            @else
                {{-- Flat: subcategories with icon --}}
                <div class="smt-flat-grid">
                    @foreach($root->subs as $sub)
                    @php
                        $subIcon = $iconMap[$sub->id] ?? 'ti-chevron-right';
                    @endphp
                    <a class="smt-flat-card" href="{{ url('/category/'.$sub->slug) }}">
                        <div class="smt-flat-icon" style="background:{{ $clr }}22;color:{{ $clr }}">
                            <i class="ti {{ $subIcon }}"></i>
                        </div>
                        {{ $sub->name }}
                    </a>
                    @endforeach
                </div>
            @endif

        </div>
        @endforeach

    </div>{{-- /smt-main --}}
</div>{{-- /smt-cat-wrap --}}

</main>

<script>
const SMT_INDEX = @json($searchIndex);

function smtShow(id) {
    document.querySelectorAll('.smt-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.smt-l1').forEach(n => n.classList.remove('active'));
    const panel = document.getElementById('smt-panel-' + id);
    const nav   = document.getElementById('smt-nav-' + id);
    if (panel) panel.classList.add('active');
    if (nav)   nav.classList.add('active');
    document.getElementById('smtLanding').style.display       = 'none';
    document.getElementById('smtSearchResults').style.display = 'none';
    document.getElementById('smtNoResults').style.display     = 'none';
    document.getElementById('smtSearch').value = '';
    document.getElementById('smtCount').textContent = '';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

document.getElementById('smtSearch').addEventListener('input', function () {
    const q = this.value.trim().toLowerCase();

    if (!q) {
        document.getElementById('smtSearchResults').style.display = 'none';
        document.getElementById('smtNoResults').style.display     = 'none';
        document.getElementById('smtLanding').style.display       = '';
        document.getElementById('smtCount').textContent           = '{{ $totalCats }} categories';
        document.querySelectorAll('.smt-panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.smt-l1').forEach(n => n.classList.remove('active'));
        return;
    }

    const hits = SMT_INDEX.filter(c => c.name.toLowerCase().includes(q)).slice(0, 48);

    document.getElementById('smtLanding').style.display = 'none';
    document.querySelectorAll('.smt-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.smt-l1').forEach(n => n.classList.remove('active'));

    if (hits.length === 0) {
        document.getElementById('smtSearchResults').style.display = 'none';
        document.getElementById('smtNoResults').style.display     = 'block';
        document.getElementById('smtCount').textContent           = '0 results';
        return;
    }

    document.getElementById('smtNoResults').style.display     = 'none';
    document.getElementById('smtSearchResults').style.display = 'block';
    document.getElementById('smtSearchLabel').textContent     = hits.length + ' result' + (hits.length !== 1 ? 's' : '') + ' for "' + this.value.trim() + '"';
    document.getElementById('smtCount').textContent           = hits.length + ' results';

    document.getElementById('smtSearchTags').innerHTML = hits.map(h =>
        `<a class="smt-stag" href="${h.url}" style="border-left-color:${h.color}">
            ${h.name} <span class="smt-stag-parent">in ${h.parent}</span>
        </a>`
    ).join('');
});
</script>

@stop

@section('footer')
@stop