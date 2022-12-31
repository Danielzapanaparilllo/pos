<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\{ConfiguracionModel, DetalleVentaModel, VentasModel};

class Factura extends BaseController
{
    protected $ventasModel, $detalle_venta, $configuracion;

    public function __construct()
    {
        $this->ventasModel = new VentasModel();
        $this->detalle_venta = new DetalleVentaModel();
        $this->configuracion = new ConfiguracionModel();

        helper(['form']);
    }

    public function facturar($idVenta)
    {

        $datosVenta = $this->ventasModel->where('id', $idVenta)->first();

        if ($datosVenta['timbrado'] == 1) {
            echo 'La venta ya ha sido facturada';
            exit;
        }

        $detalleVenta = $this->detalle_venta->select('*')->where('id_venta', $idVenta)->findAll();
        $nombreTienda = $this->configuracion->select('valor')->where('nombre', 'tienda_nombre')->get()->getRow()->valor;
        $rfcTienda = $this->configuracion->select('valor')->where('nombre', 'tienda_rfc')->get()->getRow()->valor;

        $datosFactura = array();

        $dirCfdi = APPPATH . 'Libraries/cfdi_sat/cfdi/';
        $dir = APPPATH . 'Libraries/cfdi_sat/';

        $nombre = "A3";

        //Datos generales de factura
        $datosFactura["version"] = "3.3";
        $datosFactura["serie"] = "A";
        $datosFactura["folio"] = "3";
        $datosFactura["fecha"] = date('YmdHis');
        $datosFactura["formaPago"] = $datosVenta['forma_pago'];
        $datosFactura["noCertificado"] = "20001000000300022762";
        $datosFactura["subTotal"] = $datosVenta['total'];
        $datosFactura["descuento"] = "0.00";
        $datosFactura["moneda"] = "MXN";
        $datosFactura["total"] = $datosVenta['total'];
        $datosFactura["tipoDeComprobante"] = "I";
        $datosFactura["metodoPago"] = "PUE";
        $datosFactura["lugarExpedicion"] = "01000";

        //Datos del emisor
        $datosFactura['emisor']['rfc'] = $rfcTienda;
        $datosFactura['emisor']['nombre'] =  $nombreTienda;
        $datosFactura['emisor']['regimen'] = '601';

        //Datos del receptor
        $datosFactura['receptor']['rfc'] = 'XAXX010101000';
        $datosFactura['receptor']['nombre'] = 'Publico en general';
        $datosFactura['receptor']['usocfdi'] = 'P01';

        foreach ($detalleVenta as $row) {

            $importe = number_format($row['cantidad'] * $row['precio'], 2, '.', '');

            $datosFactura["conceptos"][] = array("clave" => "01010101", "sku" => "75654123", "descripcion" => $row['nombre'], "cantidad" => $row['cantidad'], "claveUnidad" => "H87", "unidad" => "Pieza", "precio" => $row['precio'], "importe" => $importe, "descuento" => "0.00", "iBase" => $importe, "iImpuesto" => "002", "iTipoFactor" => "Tasa", "iTasaOCuota" => "0.000000", "iImporte" => "0.00");
        }

        $datosFactura['traslados']['impuesto'] = "002";
        $datosFactura['traslados']['tasa'] = "0.000000";
        $datosFactura['traslados']['importe'] = "0.00";

        $xml = new \GeneraXML();
        $xmlBase = $xml->satxmlsv33($datosFactura, '', $dir, '');

        $timbra = new \Pac();
        $cfdi = $timbra->enviar("", "", $rfcTienda, $xmlBase);

        if ($cfdi) {
            file_put_contents($dirCfdi . $nombre . '.xml', base64_decode($cfdi->xml));
            unlink($dir . '/tmp/' . $nombre . '.xml');

            $xml = simplexml_load_file($dirCfdi . $nombre . '.xml');
            $ns = $xml->getNamespaces(true);
            $xml->registerXPathNamespace('c', $ns['cfdi']);
            $xml->registerXPathNamespace('t', $ns['tfd']);

            $uuid = '';
            $fechaTimbrado = '';

            foreach ($xml->xpath('//t:TimbreFiscalDigital') as $tfd) {
                $uuid = $tfd['UUID'];
                $fechaTimbrado = $tfd['FechaTimbrado'];
            }

            $this->ventasModel->update($idVenta, ['uuid' => $uuid, 'fecha_timbrado' => $fechaTimbrado, 'timbrado' => 1]);
        }
    }

    public function generaPdf($folio)
    {
        $dirCfdi = APPPATH . 'Libraries/cfdi_sat/cfdi/';
        $xml = simplexml_load_file($dirCfdi . $folio . '.xml');
        $ns = $xml->getNamespaces(true);
        $xml->registerXPathNamespace('c', $ns['cfdi']);
        $xml->registerXPathNamespace('t', $ns['tfd']);

        foreach ($xml->xpath('//cfdi:Comprobante') as $cfdiComprobante) {
            echo $cfdiComprobante['Version'] . '<br>';
            echo $cfdiComprobante['Total'] . '<br>';
        }

        foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Emisor') as $emisor) {
            echo $emisor['Rfc'] . '<br>';
            echo $emisor['Nombre'] . '<br>';
        }

        foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Conceptos//cfdi:Concepto') as $concepto) {
            echo $concepto['Descripcion'] . '<br>';
            echo $concepto['Importe'] . '<br>';
        }

        foreach ($xml->xpath('//t:TimbreFiscalDigital') as $tfd) {
            echo $tfd['UUID'] . '<br>';
            echo $tfd['FechaTimbrado'] . '<br>';
        }
    }
}
