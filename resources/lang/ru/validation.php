<?php

return [

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

    'required' => 'Поле :attribute обязательно для заполнения',

    'custom' => [
        'password' => [
            'confirmed' => 'Подтверждение пароля не совпадает',
            'min' => 'Пароль должен быть не менее :min символов',
        ],
        'email' => [
            'unique' => 'Указанный email уже используется в системе',
            'email' => 'Введен некорректный email!',
        ],
        'phone' => [
            'required' => 'Поле Номер телефона обязательно для заполнения',
            'regex' => 'Формат поля Номер телефона должен быть +7 (XXX) XXX-XX-XX',
            'max' => 'Длина пароля должна быть не более :max символов!'
        ]
    ],
];
