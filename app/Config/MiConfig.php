<?php
namespace App\Config;

use CodeIgniter\Config\BaseConfig;

class MiConfig extends BaseConfig
{
    public static $registrars = [
        '\App\Models\ConfiguracionModel'
    ];
}
