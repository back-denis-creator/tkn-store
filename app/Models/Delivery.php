<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    const NOVA_POSHTA = 1;
    const SAMOVUVOZ = 2;

    const NAMES = [
        self::NOVA_POSHTA => 'Нова Пошта',
        self::SAMOVUVOZ => 'Самовивіз',
    ];

    const ALL = [
        ['id' => self::NOVA_POSHTA, 'name' => self::NAMES[self::NOVA_POSHTA]],
        ['id' => self::SAMOVUVOZ, 'name' => self::NAMES[self::SAMOVUVOZ]],
    ];
}