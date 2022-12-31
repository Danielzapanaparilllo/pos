<?php
namespace App\ThirdParty\Fpdf;
use App\ThirdParty\Fpdf\Fpdf;

	class PlantillaKardex extends Fpdf
	{
		var $widths;
		var $aligns;
		
		function __construct($orientacion, $medida, $tamanio, $datos)
		{
			$GLOBALS['datos'] = $datos;
			parent::__construct($orientacion, $medida, $tamanio);
		}
		
		function Header()
		{
			$dato = $GLOBALS['datos'];

			if ($dato['producto']->tipo_venta == 'P') {
                $existencias = number_format($dato['producto']->existencias, 0);
            } else {
                $existencias = number_format($dato['producto']->existencias, 2, '.', '');
            }
			
			$this->Image($dato['logo'], 10, 5, 15, 0, 'PNG');
			$this->SetFont('Arial','B',10);
			$y = $this->GetY();
			$this->Cell(30);
			$this->Multicell(135, 5, utf8_decode($dato['titulo']), 0, 'C');

			$this->SetXY(180, $y);
			$this->SetFont('Arial','',8);
			$this->Multicell(30, 4,'Fecha y hora: '.date('d/m/Y h:i'), 0, 'L');

			$this->SetFont('Arial', 'B', 9);
            $this->Cell(0, 5, 'Kardex', 0, 1, 'C');

			$this->Ln(1);

			$this->SetFont('Arial', 'B', 8);
            $this->Cell(20, 8, 'Producto: ', 0, 0, 'R');
            $this->SetFont('Arial', '', 8);
            $this->Cell(100, 8, utf8_decode($dato['producto']->nombre), 0, 0, 'L');
            $this->SetFont('Arial', 'B', 8);
            $this->Cell(30, 8, 'Existencia actual: ', 0, 0, 'R');
            $this->SetFont('Arial', '', 8);
            $this->Cell(30, 8, utf8_decode($existencias), 0, 1, 'L');

			$this->SetFont('Arial', 'B', 8);

            $this->Ln(3);
			
		}
		
		function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial','I',8);
			$this->SetTextColor(128);
			$this->Cell(0,10, utf8_decode('Página ').$this->PageNo().'/{nb}' ,0,0,'C');
		}
		
		function SetWidths($w)
		{
			$this->widths=$w;
		}
		
		function SetAligns($a)
		{
			$this->aligns=$a;
		}
		
		function Row($data)
		{
			//Calculate the height of the row
			$nb=0;
			for($i=0;$i<count($data);$i++)
			$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
			$h=5*$nb;
			//Issue a page break first if needed
			$this->CheckPageBreak($h);
			//Draw the cells of the row
			for($i=0;$i<count($data);$i++)
			{
				$w=$this->widths[$i];
				$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
				//Save the current position
				$x=$this->GetX();
				$y=$this->GetY();
				//Draw the border
				$this->Rect($x,$y,$w,$h);
				//Print the text
				$this->MultiCell($w,5,$data[$i],0,$a);
				//Put the position to the right of the cell
				$this->SetXY($x+$w,$y);
			}
			//Go to the next line
			$this->Ln($h);
		}
		
		function CheckPageBreak($h)
		{
			//If the height h would cause an overflow, add a new page immediately
			if($this->GetY()+10>$this->PageBreakTrigger){
				$this->AddPage($this->CurOrientation);
				$this->SetFont('Arial','B',8);
				$this->Row(array('Fecha','Movimiento','Entrada','Salida', 'Existencia'));
				$this->SetFont('Arial','',8);
			}
		}
		
		function NbLines($w,$txt)
		{
			//Computes the number of lines a MultiCell of width w will take
			$cw=&$this->CurrentFont['cw'];
			if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
			$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
			$s=str_replace("\r",'',$txt);
			$nb=strlen($s);
			if($nb>0 and $s[$nb-1]=="\n")
			$nb--;
			$sep=-1;
			$i=0;
			$j=0;
			$l=0;
			$nl=1;
			while($i<$nb)
			{
				$c=$s[$i];
				if($c=="\n")
				{
					$i++;
					$sep=-1;
					$j=$i;
					$l=0;
					$nl++;
					continue;
				}
				if($c==' ')
				$sep=$i;
				$l+=$cw[$c];
				if($l>$wmax)
				{
					if($sep==-1)
					{
						if($i==$j)
						$i++;
					}
					else
					$i=$sep+1;
					$sep=-1;
					$j=$i;
					$l=0;
					$nl++;
				}
				else
				$i++;
			}
			return $nl;
		}
	}
