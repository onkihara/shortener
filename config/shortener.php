<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Hash Configuration
    |--------------------------------------------------------------------------
    |
    | Konfiguration für die automatische Hash-Generierung von Shortlinks
    |
    */

    'hash' => [
        /**
         * Verfügbare Hash-Längen für die Generierung
         * Die Generierung versucht diese Längen nacheinander durch
         */
        'lengths' => [4, 5, 6, 7, 8],

        /**
         * Maximale Anzahl von Versuchen pro Länge
         */
        'retrials' => 10,

        /**
         * Erlaubte Zeichen im Hash
         */
        'characters' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789',

        /**
         * Allowed hash charackter for custom hashes
         */
        'allowed' => '[a-zA-Z0-9_\-]+',

        /**
         * Maximal zulässige Hash-Länge (Datenbankfeld: 16)
         */
        'max_length' => 16,
    ],

    /*
    |--------------------------------------------------------------------------
    | URL Validation
    |--------------------------------------------------------------------------
    |
    | Validierungs-Einstellungen für URLs
    |
    */

    'url' => [
        /**
         * Maximum URL Länge
         */
        'max_length' => 1024,

        /**
         * Erlaubte URL-Schemata
         */
        'allowed_schemes' => ['http', 'https', 'ftp', 'ftps'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Description
    |--------------------------------------------------------------------------
    |
    | Validierungs-Einstellungen für Beschreibungen
    |
    */

    'description' => [
        /**
         * Maximum Beschreibungs-Länge
         */
        'max_length' => 1024,
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    | Paginations-Einstellungen für Shortener-Listen
    |
    */

    'pagination' => [
        /**
         * Items pro Seite
         */
        'per_page' => 15,
    ],
];
