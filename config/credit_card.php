<?php

return [

    'rules' => [
        'card_name' => 'required|max:175',
        'card_number' => 'required|ccn|max:19',
        'card_expiry_date' => 'required|ccdt|max:11',
        'card_cvv' => 'required|cvc|max:4',
    ]

];
