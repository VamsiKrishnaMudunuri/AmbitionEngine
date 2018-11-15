<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'The :attribute must be accepted.',
    'active_url'           => 'The :attribute is not a valid URL.',
    'after'                => 'The :attribute must be a date after :date.',
    'after_or_equal'       => 'The :attribute must be a date after or equal to :date.',
    'alpha'                => 'The :attribute may only contain letters.',
    'alpha_dash'           => 'The :attribute may only contain letters, numbers, and dashes.',
    'alpha_num'            => 'The :attribute may only contain letters and numbers.',
    'array'                => 'The :attribute must be an array.',
    'before'               => 'The :attribute must be a date before :date.',
    'before_or_equal'      => 'The :attribute must be a date before or equal to :date.',
    'between'              => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file'    => 'The :attribute must be between :min and :max kilobytes.',
        'string'  => 'The :attribute must be between :min and :max characters.',
        'array'   => 'The :attribute must have between :min and :max items.',
    ],
    'boolean'              => 'The :attribute field must be true or false.',
    'confirmed'            => 'The :attribute confirmation does not match.',
    'date'                 => 'The :attribute is not a valid date.',
    'date_format'          => 'The :attribute does not match the format :format.',
    'different'            => 'The :attribute and :other must be different.',
    'digits'               => 'The :attribute must be :digits digits.',
    'digits_between'       => 'The :attribute must be between :min and :max digits.',
    'dimensions'           => 'The :attribute has invalid image dimensions.',
    'distinct'             => 'The :attribute field has a duplicate value.',
    'email'                => 'The :attribute must be a valid email address.',
    'exists'               => 'The selected :attribute is invalid.',
    'file'                 => 'The :attribute must be a file.',
    'filled'               => 'The :attribute field is required.',
    'image'                => 'The :attribute must be an image.',
    'in'                   => 'The selected :attribute is invalid.',
    'in_array'             => 'The :attribute field does not exist in :other.',
    'integer'              => 'The :attribute must be an integer.',
    'ip'                   => 'The :attribute must be a valid IP address.',
    'json'                 => 'The :attribute must be a valid JSON string.',
    'max'                  => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file'    => 'The :attribute may not be greater than :max kilobytes.',
        'string'  => 'The :attribute may not be greater than :max characters.',
        'array'   => 'The :attribute may not have more than :max items.',
    ],
    'mimes'                => 'The :attribute must be a file of type: :values.',
    'mimetypes'            => 'The :attribute must be a file of type: :values.',
    'min'                  => [
        'numeric' => 'The :attribute must be at least :min.',
        'file'    => 'The :attribute must be at least :min kilobytes.',
        'string'  => 'The :attribute must be at least :min characters.',
        'array'   => 'The :attribute must have at least :min items.',
    ],
    'not_in'               => 'The selected :attribute is invalid.',
    'numeric'              => 'The :attribute must be a number.',
    'present'              => 'The :attribute field must be present.',
    'regex'                => 'The :attribute format is invalid.',
    'required'             => 'The :attribute field is required.',
    'required_if'          => 'The :attribute field is required when :other is :value.',
    'required_unless'      => 'The :attribute field is required unless :other is in :values.',
    'required_with'        => 'The :attribute field is required when :values is present.',
    'required_with_all'    => 'The :attribute field is required when :values is present.',
    'required_without'     => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same'                 => 'The :attribute and :other must match.',
    'size'                 => [
        'numeric' => 'The :attribute must be :size.',
        'file'    => 'The :attribute must be :size kilobytes.',
        'string'  => 'The :attribute must be :size characters.',
        'array'   => 'The :attribute must contain :size items.',
    ],
    'string'               => 'The :attribute must be a string.',
    'timezone'             => 'The :attribute must be a valid zone.',
    'unique'               => 'The :attribute has already been taken.',
    'uploaded'             => 'The :attribute failed to upload.',
    'url'                  => 'The :attribute format is invalid.',
    //'dimensions'           => 'The :attribute has invalid image dimensions.',
    'version' => 'The edit operation was cancelled for the reason of the record was modified by another user. Well, the latest values have been displayed. If you still want to edit/delete this record, submit the form again.',
    'flexible_url'                  => 'The :attribute format is invalid.',
    'length' => 'The length of :attribute must be less than :other.',
    'username' => 'Only use letters, numbers, or - character.',
    'slug' => 'Only use letters, numbers, -, _ or / characters.',
    'price' => 'The :attribute must be in format :symbol and not longer than :digit digits.',
    'signed_price' => 'The :attribute must be in format :symbol and not longer than :digit digits.',
    'coordinate' => 'The :attribute must be in format :symbol and not longer than :digit digits.',
    'greater_than' => 'The :attribute must be greater than :other.',
    'greater_than_equal' => 'The :attribute must be greater than or equal to :other.',
    'greater_than_time' => 'The :attribute cannot be earlier than the :other.',
    'greater_than_equal_time' => 'The :attribute cannot be earlier than the :other.',
    'greater_than_datetime' => 'The ":attribute" cannot be earlier than the ":other".',
    'greater_than_datetime_equal' => 'The ":attribute" cannot be earlier than the ":other".',
    'less_than' => 'The :attribute must be less than :other.',
    'less_than_equal' => 'The :attribute must be less than or equal to :other.',
    'less_than_datetime' => 'The ":attribute" must be earlier than the ":other".',
    'less_than_datetime_equal' => 'The ":attribute" must be earlier than the ":other".',
    'dimensions'           => 'Mininum :widthpx width and :heightpx height is required for the image.',
    'ccn' => 'Invalid card number.',
    'ccd' => "Invalid card's expiry date.",
    'ccdt' => "Invalid card's expiry date.",
    'cvc' => 'Invalid CVC/CVV.',
    'min_if' => 'The :attribute field cannot be more than :other.',
    'max_if' => 'The :attribute field cannot be less than :other.',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'password' => [
            'required' => 'Please enter your password.',
            'regex' => 'Password must be at least 6 to 20 long with combination of number and alphabet characters only.',
        ],
        'password_confirmation' => [
            'required' => 'Please double confirm your password.',
            'same' => 'Password does not match.'
        ],
        'password_existing' => [
            'required' => 'Please enter your current password.',
            'in' => 'You password was incorrect.'
        ],
        'network_username' => [
           'required' => 'Username is required',
            'max' => 'Username may not be greater than :max characters.',
         ],
        'network_password' => [
            'required' => 'Please enter your password.',
            'regex' => 'Password must be at least 6 to 15 long with combination of number and alphabet characters only.',
            'min' => 'Password must be at least 6 to 15 long',
            'max' => 'Password must be at least 6 to 15 long'
        ],
        'printer_username' => [
            'required' => 'Username is required',
            'max' => 'Username may not be greater than :max characters.',
        ],
        'printer_password' => [
            'required' => 'Please enter your password.',
            'regex' => 'Password must be at least 6 to 15 long with combination of number and alphabet characters only.',
            'min' => 'Password must be at least 6 to 15 long',
            'max' => 'Password must be at least 6 to 15 long'
        ],
        'g-recaptcha-response' => [
            'required' => 'Please verify that you are not a robot.',
            'captcha' => 'Invalid captcha.'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'strike_price' => 'Listing Price',
        'spot_price' => 'Selling Price',
        'check_number' => 'Reference Number',
        'salutation' => 'Title'
    ],

];
