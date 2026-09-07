<?php

return [

    'capacitors' => [
        'match' => ['capacitor', 'mlcc', 'tantalum', 'electrolytic'],
        'patterns' => [
            'Capacitance' => '/(\d+(\.\d+)?\s?(pF|nF|uF|mF))/i',
            'Voltage' => '/(\d+(\.\d+)?\s?V)/i',
            'Tolerance' => '/(±\s?\d+(\.\d+)?%)/i',
            'Dielectric' => '/\b(C0G|NP0|X7R|X5R|Y5V)\b/i',
            'Package' => '/(0402|0603|0805|1206)/i',
            'Type' => '/\b(MLCC|ceramic|tantalum|electrolytic)\b/i'
        ]
    ],

    'resistors' => [
        'match' => ['resistor'],
        'patterns' => [
            'Resistance' => '/(\d+(\.\d+)?\s?(Ohm|kOhm|MOhm))/i',
            'Power' => '/(\d+(\.\d+)?\s?W)/i',
            'Tolerance' => '/(±\s?\d+%)/i',
            'Package' => '/(0402|0603|0805|1206)/i'
        ]
    ],

    'ic' => [
        'match' => ['ic', 'microcontroller', 'eeprom', 'flash', 'processor'],
        'patterns' => [
            'Type' => '/\b(EEPROM|Flash|MCU|Processor|Controller|Driver)\b/i',
            'Memory Size' => '/(\d+\s?x\s?\d+)/i',
            'Voltage' => '/(\d+(\.\d+)?\s?V)/i',
            'Interface' => '/\b(I2C|SPI|UART|CAN)\b/i',
            'Package' => '/\b(SOT-?23|SOIC|QFN|BGA|TSSOP)\b/i',
            'Mounting' => '/\b(SMD|SMT|Through Hole)\b/i'
        ]
    ],

    'diodes' => [
        'match' => ['diode', 'rectifier', 'tvs', 'zener'],
        'patterns' => [
            'Voltage' => '/(\d+(\.\d+)?\s?V)/i',
            'Current' => '/(\d+(\.\d+)?\s?A)/i',
            'Type' => '/\b(Zener|Schottky|TVS|Rectifier)\b/i',
            'Package' => '/\b(SMA|SMB|SOD|DO-41)\b/i'
        ]
    ],

    'transistors' => [
        'match' => ['mosfet', 'bjt', 'igbt', 'transistor'],
        'patterns' => [
            'Type' => '/\b(MOSFET|BJT|IGBT|JFET)\b/i',
            'Voltage' => '/(\d+(\.\d+)?\s?V)/i',
            'Current' => '/(\d+(\.\d+)?\s?A)/i',
            'Package' => '/\b(TO-220|TO-92|SOT-23|DPAK)\b/i'
        ]
    ],

    'connectors' => [
        'match' => ['connector', 'terminal'],
        'patterns' => [
            'Pins' => '/(\d+\s?(pin|position))/i',
            'Pitch' => '/(\d+(\.\d+)?\s?mm)/i',
            'Mounting' => '/\b(SMD|Through Hole)\b/i'
        ]
    ],

    'inductors' => [
        'match' => ['inductor', 'ferrite'],
        'patterns' => [
            'Inductance' => '/(\d+(\.\d+)?\s?(uH|mH))/i',
            'Current' => '/(\d+(\.\d+)?\s?A)/i',
            'Type' => '/\b(Power|RF|Ferrite)\b/i'
        ]
    ],

    'sensors' => [
        'match' => ['sensor'],
        'patterns' => [
            'Type' => '/\b(Temperature|Hall|Proximity|IMU)\b/i',
            'Voltage' => '/(\d+(\.\d+)?\s?V)/i',
            'Interface' => '/\b(I2C|SPI|Analog)\b/i'
        ]
    ],

    // 🔥 fallback for everything else
    'generic' => [
        'match' => [],
        'patterns' => [
            'Voltage' => '/(\d+(\.\d+)?\s?V)/i',
            'Current' => '/(\d+(\.\d+)?\s?A)/i',
            'Power' => '/(\d+(\.\d+)?\s?W)/i',
            'Frequency' => '/(\d+(\.\d+)?\s?(Hz|kHz|MHz))/i',
            'Package' => '/(SOT|QFN|BGA|DIP)/i'
        ]
    ]
];