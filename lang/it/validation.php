<?php

return [
    'required' => 'Il campo :attribute è obbligatorio.',
    'string' => 'Il campo :attribute deve essere una stringa.',
    'integer' => 'Il campo :attribute deve essere un numero intero.',
    'in' => 'Il valore selezionato per :attribute non è valido.',
    'regex' => 'Il formato di :attribute non è valido.',
    'min' => ['numeric' => ':attribute deve essere almeno :min.'],
    'max' => ['numeric' => ':attribute non può superare :max.', 'string' => ':attribute non può superare :max caratteri.'],
    'attributes' => [
        'locale' => 'lingua',
        'description' => 'descrizione',
        'text' => 'testo',
        'format' => 'formato',
        'foreground_color' => 'colore in primo piano',
        'background_color' => 'colore di sfondo',
        'size' => 'dimensione',
    ],
];
