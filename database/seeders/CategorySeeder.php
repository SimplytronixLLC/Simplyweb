<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $tree = [

            "Semiconductors" => [
                "Diodes" => [
                    "Zener Diodes",
                    "Schottky Diodes & Rectifiers",
                    "SiC Schottky Diodes",
                    "Small Signal Switching Diodes",
                    "Varactor Diodes",
                    "PIN Diodes",
                    "Laser Diodes",
                    "ESD Protection Diodes / TVS Diodes",
                    "Rectifiers"
                ],
                "Transistors" => [
                    "MOSFETs",
                    "SiC MOSFETs",
                    "RF MOSFET Transistors",
                    "Bipolar Transistors - BJT",
                    "Darlington Transistors",
                    "JFETs",
                    "GaN FETs",
                    "IGBTs",
                    "IGBT Modules"
                ],
                "Microcontrollers & Processors" => [
                    "ARM Microcontrollers - MCU",
                    "8-bit Microcontrollers - MCU",
                    "32-bit Microcontrollers - MCU",
                    "RF Microcontrollers - MCU",
                    "Microprocessors - MPU",
                    "CPU - Central Processing Units"
                ],
                "Programmable Logic" => [
                    "FPGA - Field Programmable Gate Array",
                    "CPLD - Complex Programmable Logic Devices",
                    "SoC FPGA",
                    "SPLD",
                    "EEPLD"
                ],
                "Memory" => [
                    "DRAM",
                    "EEPROM",
                    "NAND Flash",
                    "NOR Flash",
                    "eMMC",
                    "Managed NAND",
                    "UFS",
                    "Memory Cards",
                    "Memory Modules",
                    "F-RAM"
                ],
                "Amplifiers" => [
                    "Operational Amplifiers - Op Amps",
                    "Precision Amplifiers",
                    "Differential Amplifiers",
                    "Instrumentation Amplifiers",
                    "High Speed Operational Amplifiers",
                    "Isolation Amplifiers",
                    "RF Amplifier",
                    "Video Amplifiers",
                    "Audio Amplifiers",
                    "Transconductance Amplifiers"
                ],
                "Data Conversion" => [
                    "Analog to Digital Converters - ADC",
                    "Digital to Analog Converters - DAC",
                    "Data Acquisition ADCs/DACs - Specialised",
                    "Analog Front End - AFE"
                ],
                "Interface ICs" => [
                    "USB Interface IC",
                    "RS-232 Interface IC",
                    "RS-422/RS-485 Interface IC",
                    "CAN Interface IC",
                    "UART Interface IC",
                    "LVDS Interface IC",
                    "Ethernet ICs",
                    "I/O Expanders",
                    "Buffers & Line Drivers",
                    "Translation - Voltage Levels"
                ],
                "Logic ICs" => [
                    "Logic Gates",
                    "Flip-Flops",
                    "Encoders, Decoders",
                    "Multiplexers",
                    "Counter Shift Registers",
                    "Latches",
                    "Digital Bus Switch ICs"
                ],
                "Clock & Timing" => [
                    "Crystals",
                    "MEMS Oscillators",
                    "VCXO Oscillators",
                    "VCO Oscillators",
                    "OCXO Oscillators",
                    "Programmable Oscillators",
                    "Clock Drivers",
                    "PLL",
                    "Timers"
                ]
            ],

            "Power & Power Management" => [
                "General" => [
                    "Switching Voltage Regulators",
                    "LDO Voltage Regulators",
                    "Linear Voltage Regulators",
                    "Switching Controllers",
                    "Power Management Specialised - PMIC",
                    "Hot Swap Voltage Controllers",
                    "Power Switch ICs",
                    "Gate Drivers",
                    "Battery Management",
                    "Power Controllers",
                    "Power Factor Correction - PFC",
                    "AC/DC Converters",
                    "DC/DC Converters",
                    "Modular Power Supplies",
                    "Wall Mount AC Adapters",
                    "Power Supplies - DIN Rail Mount",
                    "UPS Systems"
                ]
            ],

            "Passive Components" => [
                "Capacitors" => [
                    "MLCC - SMD/SMT",
                    "Tantalum Capacitors",
                    "Aluminium Electrolytic Capacitors",
                    "Film Capacitors",
                    "Mica Capacitors",
                    "Niobium Oxide Capacitors",
                    "Safety Capacitors",
                    "Supercapacitors",
                    "Capacitor Arrays",
                    "Trimmer Capacitors"
                ],
                "Resistors" => [
                    "Thin Film Resistors",
                    "Thick Film Resistors",
                    "Wirewound Resistors",
                    "High Frequency Resistors",
                    "Current Sense Resistors",
                    "Planar Resistors",
                    "Trimmer Resistors",
                    "Rheostats"
                ],
                "Inductors & Magnetics" => [
                    "Power Inductors",
                    "RF Inductors",
                    "Ferrite Beads",
                    "Ferrite Cores",
                    "Transformers",
                    "Coupled Inductors"
                ]
            ],

            "Sensors & Measurement" => [
                "General" => [
                    "Temperature Sensors",
                    "Hall Effect Sensors",
                    "Proximity Sensors",
                    "IMUs",
                    "Accelerometers",
                    "Gyroscopes",
                    "Image Sensors",
                    "Air Quality Sensors",
                    "Photoelectric Sensors",
                    "Oscilloscopes",
                    "Multimeters",
                    "Test Probes"
                ]
            ],

            "RF & Wireless" => [
                "General" => [
                    "RF System on Chip",
                    "RF Front End",
                    "RF Switch ICs",
                    "RF Detector",
                    "WiFi Modules",
                    "GNSS / GPS Modules",
                    "Antennas"
                ]
            ],

            "Electromechanical" => [
                "Switches" => [
                    "Pushbutton Switches",
                    "Rocker Switches",
                    "Rotary Switches",
                    "Slide Switches",
                    "DIP Switches"
                ],
                "Relays" => [
                    "Relays"
                ],
                "Connectors & Terminal Blocks" => [
                    "Connectors & Terminal Blocks"
                ],
                "Circuit Protection" => [
                    "Circuit Protection"
                ]
            ],

            "Displays & Optoelectronics" => [
                "General" => [
                    "LED Displays",
                    "TFT Displays",
                    "LCD Modules",
                    "LED Drivers",
                    "White LEDs",
                    "LED Backlighting"
                ]
            ],

            "Development & Embedded" => [
                "General" => [
                    "Development Boards & Kits",
                    "Raspberry Pi Accessories",
                    "Single Board Computers",
                    "System-On-Modules"
                ]
            ],

            "Industrial & Mechanical" => [
                "General" => [
                    "Enclosures",
                    "Heat Sinks",
                    "Fans",
                    "DIN Rail",
                    "Mounting Hardware"
                ]
            ],

            "Tools & Equipment" => [
                "General" => [
                    "Soldering Equipment",
                    "Calibration Equipment",
                    "Test Accessories"
                ]
            ]

        ];

        foreach ($tree as $level1Name => $level2Array) {

            $level1 = Category::create([
                'name' => $level1Name,
                'slug' => Str::slug($level1Name),
                'parent_id' => null,
                'level' => 1
            ]);

            foreach ($level2Array as $level2Name => $level3Array) {

                $level2 = Category::create([
                    'name' => $level2Name,
                    'slug' => Str::slug($level1Name . '-' . $level2Name),
                    'parent_id' => $level1->id,
                    'level' => 2
                ]);

                foreach ($level3Array as $level3Name) {

                    Category::create([
                        'name' => $level3Name,
                        'slug' => Str::slug($level1Name . '-' . $level2Name . '-' . $level3Name),
                        'parent_id' => $level2->id,
                        'level' => 3
                    ]);
                }
            }
        }
    }
}