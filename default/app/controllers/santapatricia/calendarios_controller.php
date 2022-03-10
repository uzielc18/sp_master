<?php 
View::template('spatricia/default');
Load::models('eventos','tesingresos','tessalidas','eventostipo');
class CalendariosController extends AdminController
{
	protected function before_filter() {
        if ( Input::isAjax() ){
			View::template(null);
            //View::select(NULL, NULL);
        }
    }
	
	public function index()
	{
		
		Session::delete('tipo_id');
		Session::delete('tipo_nombre');
		$eventostipo=new Eventostipo();
		$this->tipos=$eventostipo->find();
		
	}
	public function cargartipo($id,$url='crear')
	{
		$eventostipo=new Eventostipo();
		$tipo=$eventostipo->find_first($id);
		Session::set('tipo_id',$tipo->id);
		Session::set('tipo_nombre',$tipo->nombre);
		return Redirect::toAction($url);
	}
	public function crear() {
        try {
            
			$this->titulo = 'Crear Evento';
			if (Input::hasPost('eventos')) {
                $ev = new Eventos(Input::post('eventos'));
				$ev->aclusuarios_id=Auth::get('id');
				$ev->activo='1';
                if ($ev->save()) {
                    Flash::valid('EL evento fué agregada Exitosamente...!!!');
                   // Aclauditorias::add("Agregó Memo {$me->nombre} al sistema");
                    return Redirect::toAction('');
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function editar($id) {
        $this->titulo = 'Editar Evento';
        try {
            View::select('crear');

            $ev = new Eventos();

            $this->eventos = $ev->find_first($id);

            if (Input::hasPost('eventos')) {
                if ($ev->update(Input::post('eventos'))) {
                    Flash::valid('El evento fué actualizado Exitosamente...!!!');
                    //Aclauditorias::add("Editó el Memo {$me->id}", 'memos');
                    return Redirect::redirect();
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    unset($this->ev); //para que cargue el $_POST en el form
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
	public function listado()
	{
		 $ev = new Eventos();
		 $this->eventos = $ev->find('conditions: eventostipo_id='.Session::get('tipo_id'));
	}
	public function get_eventos()
	{
		//View::template(null);
		//echo date("Y-m-d",  strtotime($fecha));
		$star=date("Y-m-d",$_GET['start']);
		$end=date("Y-m-d",$_GET['end']);
		$this->hoy=date("Y-m-d",$_GET['_']);
		$ev = new Eventos();
		$eventos=$ev->getEventos($star,$end);
		$n=0;foreach($eventos as $item){
		$n++;
		$json = array();
		$json['id'] =$n;
		$json['title'] = $item->title;
		/*VAlidar si es cumppleaño es true*/
		//$json['allDay'] = FALSE;
		$json['start'] = $item->fecha_inicio;
		$json['end'] = $item->fecha_fin;
		$json['icono']='<i class="icon-bullhorn"></i>';
		//$json['editable'] =TRUE;
		$this->data[] = $json;
		}
		
	}
public function getCumple()
{
	View::template(null);
	View::select('get_eventos');
		//echo date("Y-m-d",  strtotime($fecha));
		$star=date("Y-m-d",$_GET['start']);
		$end=date("Y-m-d",$_GET['end']);
		$this->hoy=date("Y-m-d",$_GET['_']);
		$ev = new Eventos();
		$eventos=$ev->getCumples($star,$end);
		$n=0;
		foreach($eventos as $item){
		$n++;
		$json = array();
		$json['id'] =$item->id;
		$json['title'] = $item->title;
		$json['icono']='<i class="icon-gift"></i>';
		/*VAlidar si es cumppleaño es true*/
		$json['allDay'] = TRUE;
		$json['start'] = $item->fecha_inicio;
		$json['end'] = $item->fecha_fin;
		$json['editable'] =FALSE;
		$this->data[] = $json;
		}
}
public function getDocumentos()
{
	View::template(null);
	View::select('get_eventos');
		//echo date("Y-m-d",  strtotime($fecha));
		$star=date("Y-m-d",$_GET['start']);
		$end=date("Y-m-d",$_GET['end']);
		$this->hoy=date("Y-m-d",$_GET['_']);
		$ev = new Eventos();
		$eventos=$ev->getDoc($star,$end);
		$n=0;
		foreach($eventos as $item){
		$n++;
		$json = array();
		$json['id'] =$n;
		$json['title'] = $item->title;
		$json['icono']='<i class="icon-file"></i>';
		/*VAlidar si es cumppleaño es true*/
		$json['allDay'] = FALSE;
		$json['start'] = $item->fecha_inicio;
		$json['end'] = $item->fecha_fin;
		$json['editable'] =FALSE;
		$this->data[] = $json;
		}
}
/*getEventos*/
public function get_eventos_diarios()
{
	View::template(null);
	View::select('get_eventos');
		//echo date("Y-m-d",  strtotime($fecha));
		$star=date("Y-m-d",$_GET['start']);
		$end=date("Y-m-d",$_GET['end']);
		$this->hoy=date("Y-m-d",$_GET['_']);
		$ev = new Eventos();
		$eventos=$ev->getEventos_diarios($star,$end);
		$n=0;
		$FECHA=explode('-',$star);
		foreach($eventos as $item){
		$n++;
		/*Busca la fecha de incicio para la repeticion del evento*/
		$f_i=$item->fecha_inicio;
		/*Busca la fecha final del evento*/
		$f_f=$end;
		if($item->fecha_fin<=$end && !empty($item->fecha_fin))$f_f=$item->fecha_fin;
		/*obtener numero de dias a mostrar*/
		$numerodedias=$ev->getDias($f_i,$f_f);
			/*Cargar los eventos repetidos*/
			for($i=0;$i<=$numerodedias;$i++)
			{
				$nueva_fecha=$ev->getNuevoDia($item->fecha_inicio,$i);
				$json = array();
				$json['id'] =$n;
				$json['title'] = $item->title;
				$json['icono']='<i class="icon-tags"></i>';
				/*VAlidar si es cumppleaño es true*/
				$json['allDay'] = FALSE;
				$json['start'] =$nueva_fecha;
				$json['end'] ='';
				$json['editable'] =FALSE;
				$this->data[] = $json;
			
			}
		}
}
public function get_eventos_semanales()
{
	View::template(null);
	View::select('get_eventos');
		//echo date("Y-m-d",  strtotime($fecha));
		$star=date("Y-m-d",$_GET['start']);
		$end=date("Y-m-d",$_GET['end']);
		$this->hoy=date("Y-m-d",$_GET['_']);
		$ev = new Eventos();
		$eventos=$ev->getEventos_semanal($star,$end);
		$n=0;
		$FECHA=explode('-',$star);
		foreach($eventos as $item){
		$n++;
		/*Busca la fecha de incicio para la repeticion del evento*/
		//$f_i=$star;
		/*if($item->fecha_inicio>=$star)*/$f_i=$item->fecha_inicio;
		/*Busca la fecha final del evento*/
		$f_f=$end;
		if(!empty($item->fecha_fin))$f_f=$item->fecha_fin;
		/*obtener numero de dias a mostrar*/
		$numerodedias=$ev->getDias($f_i,$f_f);
		$this->numerodedias=$numerodedias;
			/*Cargar los eventos repetidos*/
			for($i=0;$i<=$numerodedias;$i++)
			{
				if(($i%7)==0){
				$nueva_fecha=$ev->getNuevoDia($item->fecha_inicio,$i);
				$json = array();
				$json['id'] =$n;
				$json['title'] = $item->title;
				$json['icono']='<i class="icon-tags"></i>';
				$json['allDay'] = FALSE;
				$json['start'] =$nueva_fecha;
				$json['end'] ='';
				$json['editable'] =FALSE;
				$this->data[] = $json;
				}
			}
		}
}

public function get_eventos_mensuales()
{
	View::template(null);
	View::select('get_eventos');
		//echo date("Y-m-d",  strtotime($fecha));
		$star=date("Y-m-d",$_GET['start']);
		$end=date("Y-m-d",$_GET['end']);
		$this->hoy=date("Y-m-d",$_GET['_']);
		$ev = new Eventos();
		$eventos=$ev->getEventos_mensual($star,$end);
		$n=0;
		$FECHA=explode('-',$star);
		foreach($eventos as $item){
		$n++;
		/*Busca la fecha de incicio para la repeticion del evento*/
		$f_i=$item->fecha_inicio;
		/*Busca la fecha final del evento*/
		$f_f=$end;
		if(!empty($item->fecha_fin))$f_f=$item->fecha_fin;
		/*obtener numero de dias a mostrar*/
		$numerodedias=$ev->getDias($f_i,$f_f);
		$this->numerodedias=$numerodedias;
			/*Cargar los eventos repetidos*/
			for($i=0;$i<=$numerodedias;$i++)
			{
				if(($i%30)==0){
				$nueva_fecha=$ev->getNuevoDia($item->fecha_inicio,$i);
				$json = array();
				$json['id'] =$n;
				$json['title'] = $item->title;
				$json['icono']='<i class="icon-tags"></i>';
				$json['allDay'] = FALSE;
				$json['start'] =$nueva_fecha;
				$json['end'] ='';
				$json['editable'] =FALSE;
				$this->data[] = $json;
				}
			}
		}
}

public function get_eventos_anuales()
{
	View::template(null);
	View::select('get_eventos');
		//echo date("Y-m-d",  strtotime($fecha));
		$star=date("Y-m-d",$_GET['start']);
		$end=date("Y-m-d",$_GET['end']);
		$this->hoy=date("Y-m-d",$_GET['_']);
		$ev = new Eventos();
		$eventos=$ev->getEventos_anual($star,$end);
		$n=0;
		$FECHA=explode('-',$star);
		foreach($eventos as $item){
		$n++;
		/*Busca la fecha de incicio para la repeticion del evento*/
		$f_i=$item->fecha_inicio;
		/*Busca la fecha final del evento*/
		$f_f=$end;
		if(!empty($item->fecha_fin))$f_f=$item->fecha_fin;
		/*obtener numero de dias a mostrar*/
		$numerodedias=$ev->getDias($f_i,$f_f);
		$this->numerodedias=$numerodedias;
			/*Cargar los eventos repetidos*/
			for($i=0;$i<=$numerodedias;$i++)
			{
				if(($i%365)==0){
				$nueva_fecha=$ev->getNuevoDia($item->fecha_inicio,$i);
				$json = array();
				$json['id'] =$n;
				$json['title'] = $item->title;
				$json['icono']='<i class="icon-tags"></i>';
				$json['allDay'] = FALSE;
				$json['start'] =$nueva_fecha;
				$json['end'] ='';
				$json['editable'] =FALSE;
				$this->data[] = $json;
				}
			}
		}
}

/*id 
String/Integer. Optional

Uniquely identifies the given event. Different instances of repeating events should all have the same id.

title	
String. Required.

The text on an event's element

allDay	
true or false. Optional.

Whether an event occurs at a specific time-of-day. This property affects whether an event's time is shown. Also, in the agenda views, determines if it is displayed in the "all-day" section.

Don't include quotes around your true/false. This value is not a string!

When specifying Event Objects for events or eventSources, omitting this property will make it inherit from allDayDefault, which is normally true.

start	
Date. Required.

The date/time an event begins.

When specifying Event Objects for events or eventSources, you may specify a string in IETF format (ex: "Wed, 18 Oct 2009 13:00:00 EST"), a string in ISO8601 format (ex: "2009-11-05T13:15:30Z") or a UNIX timestamp.

end	
Date. Optional.

The date/time an event ends.

As with start, you may specify it in IETF, ISO8601, or UNIX timestamp format.

If an event is all-day...

the end date is inclusive. This means an event with start Nov 10 and end Nov 12 will span 3 days on the calendar.

If an event is NOT all-day...

the end date is exclusive. This is only a gotcha when your end has time 00:00. It means your event ends on midnight, and it will not span through the next day.

url	
String. Optional.

A URL that will be visited when this event is clicked by the user. For more information on controlling this behavior, see the eventClick callback.

className	
String/Array. Optional.

A CSS class (or array of classes) that will be attached to this event's element.

editable	
true or false. Optional.

Overrides the master editable option for this single event.

startEditable	
true or false. Optional.

Overrides the master eventStartEditable option for this single event.

durationEditable	
true or false. Optional.

Overrides the master eventDurationEditable option for this single event.

source	
Event Source Object. Automatically populated.

A reference to the event source that this event came from.

color	
Sets an event's background and border color just like the calendar-wide eventColor option.

backgroundColor	
Sets an event's background color just like the calendar-wide eventBackgroundColor option.

borderColor	
Sets an event's border color just like the the calendar-wide eventBorderColor option.

textColor	
Sets an event's text color just like the calendar-wide eventTextColor option.*/

public function eliminar($id)
{
	$ev = new Eventos();
	$ev->delete($id);
	Flash::valid('El evento fue eliminado!!!');
}



}
?>