<?php
Load::models('testipocambios','tessalidas','tesdatos','aclempresas','tessalidasinternas','prorollos','sc_totalcajas','tesdetalleingresos','tesingresos','tesaplicacionfacturasadelantos','tesfacturasadelantos','tesletrasingresos','tesaplicacionletraingresos','tesreportes');

if(Session::get('EMPRESAS_ID')==1)View::template('spatricia/default');
if(Session::get('EMPRESAS_ID')==2)View::template('scarmela/default');

class RepararController extends AdminController {

protected function before_filter() {
		
		if ( Input::isAjax() ){
			View::response('view');
            //View::select(NULL, NULL);
        }
    }
public function index()
{
	
}
public function reparaeltipodecambio()
{
	$n=0;
	$INGRESOS= Load::model('tesingresos');
	$cambios=new Testipocambios();
	$mensaje=' !!! se cambiaron los siguentes ids:';
	if(Input::post('estado'))$estado=Input::post('estado');else $estado='Pendiente';
	$c=0;
	if(isset($_POST['c'])){$c=$_POST['c'];}
	if($c==1)
	{
		$ingresos=$INGRESOS->find('conditions: estadoingreso="'.$estado.'"');
		$n=0;
		foreach($ingresos as $ingreso)
		{
			$a=$this->getCambioFecha($ingreso->femision);
			if($a!=0)
			{
				$n++;
				$INGRESOS->find_first($ingreso->id);
				$z=$INGRESOS->testipocambios_id;
				$INGRESOS->testipocambios_id=$a;
				if($INGRESOS->save())
				{
					if($z!=$a)$mensaje.=' ('.$z.' por '.$a;
				}else
				{
					$mensaje.=' No se Cambio NAda';
				}
			}
		}
	}
	$this->c=$c;
	
    if($n==0){Flash::error('No se modifico ningun elemento'.$mensaje);}else{
            Flash::valid('Se modifico '.$n.' Elementos'.$mensaje);}
	$this->estado=$estado;
	$this->ingresos=$INGRESOS->find('conditions: tesdocumentos_id!=15 AND estadoingreso="'.$estado.'"');
	$estados=$INGRESOS->find('columns: estadoingreso','conditions: tesdocumentos_id!=15','group: estadoingreso');
	$this->estados=$estados;
	
}
	
public function reparaeltipodecambio_salidas()
{
	View::select('reparaeltipodecambio');
	$n=0;
	$SALIDAS= Load::model('tessalidas');
	$cambios=new Testipocambios();
	$mensaje=' !!! se cambiaron los siguentes ids:';
	if(Input::post('estado'))$estado=Input::post('estado');else $estado='Pendiente';
	$c=0;
	if(isset($_POST['c'])){$c=$_POST['c'];}
	if($c==1)
	{
		$salidas=$SALIDAS->find('conditions: tesdocumentos_id=7 AND estadosalida="'.$estado.'"');
		$n=0;
		foreach($salidas as $salida)
		{
			$a=$this->getCambioFecha($salida->femision);
			if($a!=0)
			{
				$n++;
				$SALIDAS->find_first($salida->id);
				$z=$SALIDAS->testipocambios_id;
				$SALIDAS->testipocambios_id=$a;
				if($SALIDAS->update())
				{
					$mensaje.=' ('.$z.' por '.$a;
				}else
				{
					$mensaje.=' No se Cambio Nada ';
				}
			}
		}
	}
	$this->c=$c;
	
    if($n==0){Flash::error('No se modifico ningun elemento'.$mensaje);}else{
            Flash::valid('Se modifico '.$n.' Elementos'.$mensaje);}
	$this->estado=$estado;
	$this->ingresos=$SALIDAS->find('conditions: estadosalida="'.$estado.'"');
	$estados=$SALIDAS->find('conditions: tesdocumentos_id=7','columns: estadosalida','group: estadosalida');
	$this->estados=$estados;
	
}
public function reparaeltipodecambio_salidas_internas()
{
	View::select('reparaeltipodecambio');
	$n=0;
	$SALIDAS= Load::model('tessalidasinternas');
	$cambios=new Testipocambios();
	$mensaje=' !!! se cambiaron los siguentes ids:';
	if(Input::post('estado'))$estado=Input::post('estado');else $estado='Pendiente';
	$c=0;
	if(isset($_POST['c'])){$c=$_POST['c'];}
	if($c==1)
	{
		$salidas=$SALIDAS->find('conditions: estadosalida="'.$estado.'"');
		$n=0;
		foreach($salidas as $salida)
		{
			$a=$this->getCambioFecha($salida->femision);
			if($a!=0)
			{
				$n++;
				$SALIDAS->find_first($salida->id);
				$z=$SALIDAS->testipocambios_id;
				$SALIDAS->testipocambios_id=$a;
				if($SALIDAS->save())
				{
					$mensaje.=' ('.$z.' por '.$a;
				}else
				{
					$mensaje.=' No se Cambio NAda';
				}
			}
		}
	}
	$this->c=$c;
	
    if($n==0){Flash::error('No se modifico ningun elemento'.$mensaje);}else{
            Flash::valid('Se modifico '.$n.' Elementos'.$mensaje);}
	$this->estado=$estado;
	$this->ingresos=$SALIDAS->find('conditions: estadosalida="'.$estado.'"');
	$estados=$SALIDAS->find('columns: estadosalida','group: estadosalida');
	$this->estados=$estados;
	
}	
public function getCambioFecha($fecha='')
{
	$cambios=new Testipocambios();
	if($cambios->exists('fecha="'.$fecha.'"'))
	{
		$val=$cambios->find_first('conditions: fecha="'.$fecha.'"');
		return $val->id;
	}else
	{
		return 0;
	}	
}
/*Para las aplicaciones */
public function actualizarsalidas()
{
	$mensaje='';
	$salidas = new Tessalidas();
	$pendientes=$salidas->find_all_by_sql("SELECT s.id as id from tessalidas as s INNER JOIN tesaplicacionfacturasadelantos as a ON s.id=a.tessalidas_id WHERE (s.totalconigv=0 OR isnull(s.totalconigv)) AND (s.estadosalida='Pendiente' or s.estadosalida='ADELANTADO')");
	$n=0;
	foreach($pendientes as $item):
	$s=$salidas->find_first($item->id);
	$s->estadosalida='PAGADO';
	$s->save();
	$n++;
	endforeach;
	if($n==0)
	{
		Flash::error('No se modifico ningun elemento'.$mensaje);
	}else{
		Flash::valid('Se modifico '.$n.' Elementos'.$mensaje);
	}	
	return Router::toAction('');
}

/* Optimizar para las dos empresas en cuestion de abonos */
public function facturasConAbonos($url='',$empresas_id=1,$llave=0)
{
	$mensaje='';
	$this->empresas_id=$empresas_id;
	$salidas = new Tessalidas();
	$this->facturas=$pendientes=$salidas->find_all_by_sql("SELECT CONCAT_WS('-',s.serie,s.numero) as numero,s.tesdatos_id as tesdatos_id, s.totalconigv , sum(d.monto) as total, s.* FROM tessalidas as s 
INNER JOIN tesabonosdetalles as d ON d.tessalidas_id=s.id 
WHERE (s.estadosalida!='PAGADO' AND s.estadosalida!='ANULADO') AND s.tesdocumentos_id=7 AND s.aclempresas_id=".$empresas_id."
group by s.id");
	$n=0;
	$e=0;
	if($llave!=0){
	foreach($pendientes as $item):
	//$resta=number_format($fa->totalconigv,2,'.','')-number_format($fa->total,2,'.','');
	$resta=number_format($item->totalconigv,2,'.','')-number_format($salidas->getAbonos_factura($item->id,'tessalidas_id'),2,'.','');
	$mensaje.=',id : '.$item->id.' => Abono:'.number_format($salidas->getAbonos_factura($item->id,'tessalidas_id'),2,'.','').'== Totalconigv: '.number_format($item->totalconigv,2,'.','').'resta : '.$resta.' \n';
	if($resta<=1){
	echo $resta;
	$s=$salidas->find_first($item->id);
	$s->estadosalida='PAGADO';
	$s->save();
	$n++;
	}$e++;
	endforeach;
	if($n==0)
	{
		Flash::error('No se modifico ningun elemento <br />'.$mensaje.' <br /> de las '.$e.' salidas');
	}else{
		Flash::valid('Se modifico '.$n.' Elementos'.$mensaje.' de las '.$e.' salidas');
	}
	}
	//return Router::toAction($url);
	
}

public function facturasConAbonos_interna($url='',$empresas_id=1)
{
	$mensaje='';
	$salidas = new Tessalidas();
	$pendientes=$salidas->find_all_by_sql("SELECT * FROM tessalidasinternas WHERE estadosalida='ADELANTADO' AND tesdocumentos_id=7");
	$n=0;
	$e=0;
	foreach($pendientes as $item):
	if(number_format($salidas->getAbonos_factura($item->id,'tessalidasinternas_id'),2,'.','')==number_format($item->totalconigv,2,'.','')){
	
	$s=$salidas->find_first($item->id);
	$s->estadosalida='PAGADO';
	$s->save();
	$n++;
	}$e++;
	endforeach;
	if($n==0)
	{
		Flash::error('No se modifico ningun elemento'.$mensaje.' de las '.$e.' salidas');
	}else{
		Flash::valid('Se modifico '.$n.' Elementos'.$mensaje.' de las '.$e.' salidas');
	}	
	return Router::toAction($url);
	
}
public function actualizarinternas()
{
	$mensaje='';
	$salidas = new Tessalidasinternas();
	$pendientes=$salidas->find_all_by_sql("SELECT s.id as id FROM tessalidasinternas AS s INNER JOIN tesaplicacionletrainterna AS a ON s.id=a.tessalidasinternas_id WHERE (s.total =0 OR ISNULL(s.total)) AND (s.estadosalida='Pendiente' OR s.estadosalida='ADELANTADO' )");
	$n=0;
	foreach($pendientes as $item):
	$s=$salidas->find_first($item->id);
	$s->estadosalida='PAGADO';
	$s->save();
	$n++;
	endforeach;
	if($n==0)
	{
		Flash::error('No se modifico ningun elemento'.$mensaje);
	}else{
		Flash::valid('Se modifico '.$n.' Elementos'.$mensaje);
	}	
	return Router::toAction('');
}
/*Cancelar las facturas que sus abonos esten completos */
public function cambiar_estado($id=0)
{
	$mensaje='NO existe esta salida';
	if($id!=0)
	{
		$SAL=new Tessalidas();
		$salida=$SAL->find_first($id);
		$salida->estadosalida='PAGADO';
		$salida->save();
		$mensaje='Salida pagada !Actualizado';
	}
	Flash::valid($mensaje);
	return Router::toAction('facturasConAbonos');
}
public function cambiar_codigo($em=0,$id=0)
{
	$empresa= new Aclempresas();
	$this->empresas=$empresa->find();
	if($em!=0){
	$datos= new Tesdatos();
		$dats=$datos->find('conditions: testipodatos_id='.$id.' and aclempresas_id='.$em);
		$n=0;
		foreach($dats as $dat):
		
			$n++;
			(string)$val=time();
			$newdat=$datos->find_first((int)$dat->id);
			$maximo=$n;
			switch($maximo)
			{
				case $maximo<10: $maximo="000000".$maximo; break;
				case $maximo<100: $maximo="00000".$maximo; break;
				case $maximo<1000: $maximo="0000".$maximo; break;
				case $maximo<10000: $maximo="000".$maximo; break;
				case $maximo<100000: $maximo="00".$maximo; break;
				case $maximo<1000000: $maximo="0".$maximo; break;
				default: $maximo=$maximo;
			}
			$newdat->codigo=$maximo;
			$newdat->sufijo=mt_rand(10, 99);
			$newdat->save();
		
		Flash::valid("Actualizado!!!");
		endforeach;
	}
	
}
public function reparar_direccion()
{
	/*$tesdatos=new Tesdatos();
	$dats =$tesdatos->find_all_by_sql("SELECT SUBSTRING_INDEX(direccion,'-', 1 ) as ndireccion FROM `tesdatos` WHERE !ISNULL(direccion) limit 0,100");
	$a=0;
	foreach($dats as $dat):
	$datos=new Tesdatos();
	$datos->find_first($dat->id);
	$datos->direccion=$dat->ndireccion;
	if($datos->save()){$a++;}else{}
	endforeach;
	if($a>=1){Flash::valid('Se actualizo los datos... !!!'.$a);}else{Flash::warning('No se Pudieron Guardar los Datos...!!!');}
    //Acciones::add("Agregó Empresa {$em->nombre} al sistema");
    return Router::redirect();*/
}
public function actualizarIngresos()
{
	$ingresos = new Tesingresos();
	$ing=$ingresos->find_all_by_sql("SELECT i.id as ingreso_id,i.numero,i.serie,i.totalconigv as totalconigv,i.estadoingreso,d.id,d.tesingresos_id,d.monto as monto FROM tesingresos as i,tesdetallevauchers as d WHERE i.tesdocumentos_id=7 AND i.id=d.tesingresos_id  AND i.estadoingreso!='PAGADO' AND i.estadoingreso!='Detraccion'
ORDER BY `i`.`id`  DESC");
	$var='';
	foreach($ing as $i):
	$var.=$i->totalconigv.'=='.$i->monto.'<br />';
		if($i->totalconigv==$i->monto)
		{
			$can=$ingresos;
			$can->find_first($i->ingreso_id);
			$can->estadoingreso='PAGADO';
			$can->save();
		}
	endforeach;
	
	Flash::valid($var);
	return Router::toAction('');
	
}



public function rollos($page=1)
{
	$rollos= new Prorollos();
	$this->results=$rollos->paginate("page: $page", 'order: id DESC');
}

public function actualizar_total($url='')
{
	/*buscar los que tengan en cantidad 0*/
	$I= new Tesdetalleingresos();
	$total=new ScTotalcajas();
	$cajas0=$total->find('conditions: cantidad=0');
	foreach($cajas0 as $c)
	{
		/*'SELECT sum(cantidad) FROM `tesdetalleingresos`
WHERE tesingresos_id=5414 AND tesproductos_id=13368'*/
		$cantidad=$I->sum('cantidad','conditions: tesingresos_id='.$c->tesingresos_id.' AND tesproductos_id='.$c->tesproductos_id);
		$total_update=new ScTotalcajas();
		$tt=$total_update->find_first($c->id);
		$tt->cantidad=$cantidad;
		$tt->save();
	}
	return Router::toAction('');
	
}
public function direcciones($id=0,$aclempresas=1)
{
	$salidas= new Tessalidas();
	$this->salidas=$salidas->find('columns: tesdatos_id,id,direccion_entrega,count(id) as total','conditions: aclempresas_id='.$aclempresas.' AND (tesdocumentos_id=7 or tesdocumentos_id=15) AND !isnull(direccion_entrega) GROUP BY direccion_entrega order by tesdatos_id asc');
	
}
public function terminar_fa()
{
	$AP=new tesfacturasadelantos();
	$this->facturas=$AP->getFacturasError();
	if (Input::hasPost('tesaplicacionfacturasadelantos'))
	{
                $aplicaciones = new Tesaplicacionfacturasadelantos(Input::post('tesaplicacionfacturasadelantos'));
				if ($aplicaciones->save()) {
                    Flash::valid('La factura se termino con exito');
					$obj= new Tesfacturasadelantos();
					$obj->find_first($aplicaciones->tesfacturasadelantos_id);
					$obj->estado=9;
					$obj->save();
                    //Acciones::add("Agregó Empresa {$em->nombre} al sistema");
                    return Router::redirect();
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                }
	}
}
public function cambiar_estado_fa($id)
{
	$AP=new tesfacturasadelantos();
	$data=$AP->find_first($id);
	$data->estado=9;
	$data->save();
	return Router::toAction('terminar_fa');
}
public function buscar_fa()
{
	View::response('view');
	$this->data='';
	$q=$_GET['q'];
	$obj= new Tesfacturasadelantos();
	
		$results = $obj->buscar($q);
		$dato='';
		foreach ($results as $value)
		{
			//$dato.="id salida".$value->tessalidas_id."<br />";
			$dato.="Empresa: ".$value->getTessalidas()->getAclempresas()->razonsocial."<br />";
			$dato.="Cliente: ".$value->getTesdatos()->razonsocial." ".$value->getTessalidas()->direccion_entrega." <br />";
			$dato.="Fecha: ".$value->getTessalidas()->femision."<br />";	
			$dato.="Moneda: ".$value->getTesmonedas()->nombre."<br />";			
			$dato.="IGV: ".$value->getTessalidas()->igv."<br />";			
			$dato.="TOTAL: ".$value->getTessalidas()->totalconigv."<br />";
			
			$json = array();
			$json['id'] =$value->id;
			$json['name'] = $value->name;
			$json['montototal'] = $value->montototal;
			$json['moneda'] = $value->tesmonedas_id;
			$json['serie'] = $value->serie;
			$json['numero'] = $value->numero;
			$json['empresa'] = $value->getTessalidas()->aclempresas_id;
			$json['dato']=$dato;
			$this->data[] = $json;
		}
}

public function modificar_codigos_datos($tipo=0,$empresa=1,$cambiar=0)
{
	$empresas= new Aclempresas();
	$this->empresas=$empresas->find();
	$datos= new Tesdatos();
	$this->tipo=$tipo;
	$this->empresa=$empresa;
	if($tipo!=0)
	{
		$this->datos=$datos->find('conditions: testipodatos_id='.$tipo.' AND aclempresas_id='.$empresa,'order: razonsocial DESC');
	}
	if($cambiar==43408841)
	{
		$dats=$datos->find('conditions: testipodatos_id='.$tipo.' AND aclempresas_id='.$empresa);
		foreach($dats as $d)
		{
			$datos= new Tesdatos();
			$dato=$datos->find_first($d->id);
			$dato->codigo="";
			$dato->sufijo="";
			$dato->save();
		}
	}
	
}
public function eliminar_dato($id,$val=0)
{
	$datos= new Tesdatos();
	$d=$datos->find_first($id);
	if($val==4340881){
		
	$datos->delete($id);
	Flash::valid('Dato borrado');
	}else{
	Flash::valid('Val incorrecto');
	}
	return Router::toAction('modificar_codigos_datos/'.$d->testipodatos_id.'/'.$d->aclempresas_id);
	
}


public function letrasaplicacion()
{
	$letras= new Tesletrasingresos();
	$this->letras=$letras->find_all_by_sql("SELECT sum(a.monto)as app,l.* FROM tesletrasingresos as l INNER JOIN tesaplicacionletraingresos as a ON a.tesletrasingresos_id=l.id WHERE l.activo=1 GROUP BY l.id");
	
	/*echo "SELECT id,numerodefactura,monto,(select count(tesdetallevauchers.id) from tesdetallevauchers WHERE  tesdetallevauchers.id=tesaplicacionletraingresos.tesdetallevauchers_id) as t FROM `tesaplicacionletraingresos` WHERE (select count(tesdetallevauchers.id) from tesdetallevauchers WHERE  tesdetallevauchers.id=tesaplicacionletraingresos.tesdetallevauchers_id)=0";*/
}

public function repar_letra_adelanto()
{
	$letras= new Tesletrasingresos();
	/*aplicaciones que no tengan detalles de vauchers*/
	$let=$letras->find_all_by_sql("SELECT id,numerodefactura,monto,(select count(tesdetallevauchers.id) from tesdetallevauchers WHERE  tesdetallevauchers.id=tesaplicacionletraingresos.tesdetallevauchers_id) as t FROM `tesaplicacionletraingresos` WHERE (select count(tesdetallevauchers.id) from tesdetallevauchers WHERE  tesdetallevauchers.id=tesaplicacionletraingresos.tesdetallevauchers_id)=0");
	foreach($let as $l):
	$DE=new Tesaplicacionletraingresos();
	if($DE->exists("(select count(tesdetallevauchers.id) from tesdetallevauchers WHERE  tesdetallevauchers.id=tesaplicacionletraingresos.tesdetallevauchers_id)=0 AND tesaplicacionletraingresos.id=$l->id")){
	$DE->delete($l->id);
	}
	endforeach;
	/*aplicaciones que no que su vauchers esten anulados*/
	$anulados=$letras->find_all_by_sql("SELECT id,numerodefactura,monto,tesdetallevauchers_id,fecha_at,(select count(tesdetallevauchers.id) from tesdetallevauchers,tesvauchers 
WHERE tesvauchers.id=tesdetallevauchers.tesvauchers_id AND tesvauchers.estadov='ANULADO' AND tesdetallevauchers.id=tesaplicacionletraingresos.tesdetallevauchers_id)
as t FROM `tesaplicacionletraingresos` WHERE (select count(tesdetallevauchers.id) from tesdetallevauchers,tesvauchers 
WHERE tesvauchers.id=tesdetallevauchers.tesvauchers_id AND tesvauchers.estadov='ANULADO' AND tesdetallevauchers.id=tesaplicacionletraingresos.tesdetallevauchers_id)=1");
	foreach($anulados as $a):
	$DE=new Tesaplicacionletraingresos();
	if($DE->exists("(select count(tesdetallevauchers.id) from tesdetallevauchers,tesvauchers 
WHERE tesvauchers.id=tesdetallevauchers.tesvauchers_id AND tesvauchers.estadov='ANULADO' AND tesdetallevauchers.id=tesaplicacionletraingresos.tesdetallevauchers_id)=1 AND tesaplicacionletraingresos.id=$a->id")){
	$DE->delete($a->id);
	}
	endforeach;
	
	
	return Router::toAction('letrasaplicacion');
}
public function letra_ingresos_activar($id)
{
	$ingresos= new Tesingresos();
	$i=$ingresos->find_first($id);
	$i->estadoingreso="Pendiente";
	$i->save();
	return Router::toAction('letrasaplicacion');
}
public function letra_activo($id)
{
	$letras= new Tesletrasingresos();
	$lt=$letras->find_first($id);
	$lt->activo="0";
	$lt->save();
	return Router::toAction('letrasaplicacion');
}

public function num_a_letras_salidas()
{
	$salidas=new Tessalidas();
	$sali=$salidas->find_all_by_sql("SELECT s.id as id,s.totalconigv as total,m.nombre as moneda FROM `tessalidas` as s, tesmonedas as m WHERE s.tesmonedas_id=m.id AND (!isnull(s.totalconigv) or s.totalconigv!='')");
	foreach($sali as $sal){
		$ts=$salidas->find_first($sal->id);
		$ts->totalenletras=NumerosLetras::NL($sal->total,$sal->moneda);
		$ts->save();
	}
	Flash::valid('Modificaciones correctas');
	return Router::toAction('');
}


}
?>