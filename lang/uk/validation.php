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

    'accepted' => 'Поле :attribute має бути прийняте.',
    'accepted_if' => 'Поле :attribute має бути прийняте, коли :other дорівнює :value.',
    'active_url' => 'Поле :attribute має бути дійсною URL-адресою.',
    'after' => 'Поле :attribute має бути датою, пізнішою за :date.',
    'after_or_equal' => 'Поле :attribute має бути датою, пізнішою або рівною :date.',
    'alpha' => 'Поле :attribute може містити лише літери.',
    'alpha_dash' => 'Поле :attribute може містити лише літери, цифри, дефіси та підкреслення.',
    'alpha_num' => 'Поле :attribute може містити лише літери та цифри.',
    'array' => 'Поле :attribute має бути масивом.',
    'ascii' => 'Поле :attribute може містити лише однобайтові символи та цифри.',
    'before' => 'Поле :attribute має бути датою, раніше за :date.',
    'before_or_equal' => 'Поле :attribute має бути датою, раніше або рівною :date.',
    'between' => [
        'array' => 'Поле :attribute має містити від :min до :max елементів.',
        'file' => 'Розмір файлу :attribute має бути від :min до :max кілобайт.',
        'numeric' => 'Значення :attribute має бути від :min до :max.',
        'string' => 'Довжина :attribute має бути від :min до :max символів.',
    ],
    'boolean' => 'Поле :attribute має бути істина або хиба.',
    'can' => 'Поле :attribute містить неприпустиме значення.',
    'confirmed' => 'Підтвердження поля :attribute не збігається.',
    'current_password' => 'Пароль невірний.',
    'date' => 'Поле :attribute має бути дійсною датою.',
    'date_equals' => 'Поле :attribute має бути датою, рівною :date.',
    'date_format' => 'Поле :attribute не відповідає формату :format.',
    'decimal' => 'Поле :attribute має містити :decimal десяткових знаків.',
    'declined' => 'Поле :attribute має бути відхилене.',
    'declined_if' => 'Поле :attribute має бути відхилене, коли :other дорівнює :value.',
    'different' => 'Поля :attribute та :other мають відрізнятися.',
    'digits' => 'Поле :attribute має складатися з :digits цифр.',
    'digits_between' => 'Поле :attribute має складатися від :min до :max цифр.',
    'dimensions' => 'Поле :attribute має недопустимі розміри зображення.',
    'distinct' => 'Поле :attribute містить значення, що повторюється.',
    'doesnt_end_with' => 'Поле :attribute не має закінчуватися одним із наступних значень: :values.',
    'doesnt_start_with' => 'Поле :attribute не має починатися з одного із наступних значень: :values.',
    'email' => 'Поле :attribute має бути дійсною електронною адресою.',
    'ends_with' => 'Поле :attribute має закінчуватися одним із наступних значень: :values.',
    'enum' => 'Обране значення :attribute недійсне.',
    'exists' => 'Обране значення :attribute недійсне.',
    'extensions' => 'Поле :attribute має мати одне з наступних розширень: :values.',
    'file' => 'Поле :attribute має бути файлом.',
    'filled' => 'Поле :attribute має бути заповнене.',
    'gt' => [
        'array' => 'Поле :attribute має містити більше :value елементів.',
        'file' => 'Розмір файлу :attribute має бути більше :value кілобайт.',
        'numeric' => 'Значення :attribute має бути більше :value.',
        'string' => 'Довжина :attribute має бути більше :value символів.',
    ],
    'gte' => [
        'array' => 'Поле :attribute має містити :value елементів або більше.',
        'file' => 'Розмір файлу :attribute має бути більше або дорівнювати :value кілобайт.',
        'numeric' => 'Значення :attribute має бути більше або дорівнювати :value.',
        'string' => 'Довжина :attribute має бути більше або дорівнювати :value символів.',
    ],
    'hex_color' => 'Поле :attribute має бути дійсним шістнадцятковим кольором.',
    'image' => 'Поле :attribute має бути зображенням.',
    'in' => 'Обране значення :attribute недійсне.',
    'in_array' => 'Поле :attribute має існувати в :other.',
    'integer' => 'Поле :attribute має бути цілим числом.',
    'ip' => 'Поле :attribute має бути дійсною IP-адресою.',
    'ipv4' => 'Поле :attribute має бути дійсною IPv4-адресою.',
    'ipv6' => 'Поле :attribute має бути дійсною IPv6-адресою.',
    'json' => 'Поле :attribute має бути дійсним рядком JSON.',
    'list' => 'Поле :attribute має бути списком.',
    'lowercase' => 'Поле :attribute має бути написане малими літерами.',
    'lt' => [
        'array' => 'Поле :attribute має містити менше :value елементів.',
        'file' => 'Розмір файлу :attribute має бути менше :value кілобайт.',
        'numeric' => 'Значення :attribute має бути менше :value.',
        'string' => 'Довжина :attribute має бути менше :value символів.',
    ],
    'lte' => [
        'array' => 'Поле :attribute не має містити більше :value елементів.',
        'file' => 'Розмір файлу :attribute має бути менше або дорівнювати :value кілобайт.',
        'numeric' => 'Значення :attribute має бути менше або дорівнювати :value.',
        'string' => 'Довжина :attribute має бути менше або дорівнювати :value символів.',
    ],
    'mac_address' => 'Поле :attribute має бути дійсною MAC-адресою.',
    'max' => [
        'array' => 'Поле :attribute не може містити більше :max елементів.',
        'file' => 'Розмір файлу :attribute не може перевищувати :max кілобайт.',
        'numeric' => 'Значення :attribute не може перевищувати :max.',
        'string' => 'Довжина :attribute не може перевищувати :max символів.',
    ],
    'max_digits' => 'Поле :attribute не може містити більше :max цифр.',
    'mimes' => 'Поле :attribute має бути файлом одного з типів: :values.',
    'mimetypes' => 'Поле :attribute має бути файлом одного з типів: :values.',
    'min' => [
        'array' => 'Поле :attribute має містити щонайменше :min елементів.',
        'file' => 'Розмір файлу :attribute має бути щонайменше :min кілобайт.',
        'numeric' => 'Значення :attribute має бути щонайменше :min.',
        'string' => 'Довжина :attribute має бути щонайменше :min символів.',
    ],
    'min_digits' => 'Поле :attribute має містити щонайменше :min цифр.',
    'missing' => 'Поле :attribute має бути відсутнім.',
    'missing_if' => 'Поле :attribute має бути відсутнім, коли :other дорівнює :value.',
    'missing_unless' => 'Поле :attribute має бути відсутнім, якщо :other не дорівнює :value.',
    'missing_with' => 'Поле :attribute має бути відсутнім, коли присутнє :values.',
    'missing_with_all' => 'Поле :attribute має бути відсутнім, коли присутні :values.',
    'multiple_of' => 'Поле :attribute має бути кратним :value.',
    'not_in' => 'Обране значення :attribute недійсне.',
    'not_regex' => 'Формат поля :attribute недійсний.',
    'numeric' => 'Поле :attribute має бути числом.',
    'password' => [
        'letters' => 'Поле :attribute має містити щонайменше одну літеру.',
        'mixed' => 'Поле :attribute має містити щонайменше одну велику та одну малу літеру.',
        'numbers' => 'Поле :attribute має містити щонайменше одну цифру.',
        'symbols' => 'Поле :attribute має містити щонайменше один символ.',
        'uncompromised' => 'Вказаний :attribute був знайдений у витоку даних. Будь ласка, оберіть інший :attribute.',
    ],
    'present' => 'Поле :attribute має бути присутнім.',
    'present_if' => 'Поле :attribute має бути присутнім, коли :other дорівнює :value.',
    'present_unless' => 'Поле :attribute має бути присутнім, якщо :other не дорівнює :value.',
    'present_with' => 'Поле :attribute має бути присутнім, коли присутнє :values.',
    'present_with_all' => 'Поле :attribute має бути присутнім, коли присутні :values.',
    'prohibited' => 'Поле :attribute заборонене.',
    'prohibited_if' => 'Поле :attribute заборонене, коли :other дорівнює :value.',
    'prohibited_unless' => 'Поле :attribute заборонене, якщо :other не входить у :values.',
    'prohibits' => 'Поле :attribute забороняє присутність :other.',
    'regex' => 'Формат поля :attribute недійсний.',
    'required' => 'Поле :attribute обов\'язкове.',
    'required_array_keys' => 'Поле :attribute має містити записи для: :values.',
    'required_if' => 'Поле :attribute обов\'язкове, коли :other дорівнює :value.',
    'required_if_accepted' => 'Поле :attribute обов\'язкове, коли :other прийнято.',
    'required_unless' => 'Поле :attribute обов\'язкове, якщо :other не входить у :values.',
    'required_with' => 'Поле :attribute обов\'язкове, коли присутнє :values.',
    'required_with_all' => 'Поле :attribute обов\'язкове, коли присутні :values.',
    'required_without' => 'Поле :attribute обов\'язкове, коли відсутнє :values.',
    'required_without_all' => 'Поле :attribute обов\'язкове, коли відсутні всі :values.',
    'same' => 'Поле :attribute має збігатися з :other.',
    'size' => [
        'array' => 'Поле :attribute має містити :size елементів.',
        'file' => 'Розмір файлу :attribute має бути :size кілобайт.',
        'numeric' => 'Значення :attribute має бути :size.',
        'string' => 'Довжина :attribute має бути :size символів.',
    ],
    'starts_with' => 'Поле :attribute має починатися з одного із наступних значень: :values.',
    'string' => 'Поле :attribute має бути рядком.',
    'timezone' => 'Поле :attribute має бути дійсним часовим поясом.',
    'unique' => 'Таке значення :attribute вже використовується.',
    'uploaded' => 'Не вдалося завантажити :attribute.',
    'uppercase' => 'Поле :attribute має бути написане великими літерами.',
    'url' => 'Поле :attribute має бути дійсною URL-адресою.',
    'ulid' => 'Поле :attribute має бути дійсним ULID.',
    'uuid' => 'Поле :attribute має бути дійсним UUID.',

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
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => "ім'я",
        'surname' => 'прізвище',
        'email' => 'email',
        'phone' => 'телефон',
        'comment' => 'коментар',
        'password' => 'пароль',
        'password_confirmation' => 'підтвердження пароля',
        'current_password' => 'поточний пароль',
        'title' => 'назва',
        'content' => 'зміст',
        'description' => 'опис',
    ],

];
