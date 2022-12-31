<?php

namespace App\Models;

use CodeIgniter\Model;

class ConfiguracionModel extends Model
{
    protected $table      = 'configuracion';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    protected $useSoftUpdates = false;
    protected $useSoftCreates = false;

    protected $allowedFields = ['nombre', 'valor'];

    protected $useTimestamps = false;

    public function getConfig($nombre)
    {
        return $this->select('valor')->where('nombre', $nombre)->get()->getRow()->valor;
    }

    public function folioActual($tipo)
    {
        if ($tipo == 'E') {
            return $this->select('valor')->where('nombre', 'folio_mov_entrada')->get()->getRow()->valor;
        } elseif ($tipo == 'A') {
            return $this->select('valor')->where('nombre', 'folio_mov_ajuste')->get()->getRow()->valor;
        } else {
            return $this->select('valor')->where('nombre', 'folio_mov')->get()->getRow()->valor;
        }
    }

    public function siguienteFolio($tipo)
    {
        $this->set('valor', "valor + 1", FALSE);
        if ($tipo == 'E') {
            $this->where('nombre', 'folio_mov_entrada');
        } elseif ($tipo == 'A') {
            $this->where('nombre', 'folio_mov_ajuste');
        } else {
            $this->where('nombre', 'folio_mov');
        }
        $this->update();
    }

    public static function MiConfig()
    {
        $configuracion = new ConfiguracionModel();
        $datos = $configuracion->findAll();
        $config = array();

        foreach ($datos as $fila) {
            $config[$fila['nombre']] = $fila['valor'];
        }

        return $config;
    }
}
