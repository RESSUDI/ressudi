urlEliminar='/backend/persona/eliminar';

$(document).ready(function(){
    var value = __obtenerTiempo ();
    $('#mainContent').idle({
       onIdle: function(){
        $.ajax({url: "ingreso/salir", success: function(result){
                        __mensajeSinBoton('#_mensaje-1',  'Cerrando aplicacion por falta de actividad...');
                        __delayRefreshPage(600);
        }});
          },
          idle: value  //10 segundos
        })


	var filtro="/backend/persona/grid";
	if($("#fnombre").val()!="")       filtro+="/nombre/"+$("#fnombre").val();
	if($("#fpaterno").val()!="")       filtro+="/paterno/"+$("#fpaterno").val();
	if($("#fmaterno").val()!="")       filtro+="/materno/"+$("#fmaterno").val();
	
        $("#flexigrid").flexigrid({
		url: filtro,
		dataType: "xml",
		colModel: [
			{display: "Nombre",           name:"p.nombre",       width: 200, sortable: true, align: "center"},
			{display: "Apellido paterno",name:"p.apellido_pat",       width: 150, sortable: true, align: "center"},			
			{display: 'Apellido materno',         name:"p.apellido_mat",     width: 150, sortable : true, align: 'center'},
			{display: 'Correo',         name:"p.correo",     width: 240, sortable : false, align: 'center'},
//			{display: 'Tel&eacute;fono',         name:"p.telefono",     width: 160, sortable : false, align: 'center'},
			{display: 'Estatus',           name:"p.status",       width: 100, sortable : false, align: 'center'},
			{display: 'Editar',           name:"editar",       width: 100, sortable : false, align: 'center'},
            {display: 'Edita zona ',           name:"editar_zona",       width: 100, sortable : false, align: 'center'},
            {display: 'Habilitar/deshabilitar',         name:"eliminar",     width: 150, sortable : false, align: 'center'}
		],
		sortname: "p.nombre"
		,sortorder: "asc"
		,usepager: true
        ,useRp: false
        ,singleSelect: true
        ,resizable: false
        ,showToggleBtn: false
        ,rp: 10
        ,width: 1200
        ,height: 400
	});

});


function agregar(urlDestino, identificador, form, titulo)
{
    $.ajax({
        type: "POST",
        url: urlDestino,
        data: {
            id: identificador
        }
        ,success: function(html)
        {
            $("#_dialogo-1").html(html);        

            $('#fecha_nacimiento').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                maxDate: "+0d"
            });

            $("#_dialogo-1").dialog({
                width: "900",
                height: "auto",
                title: titulo,
                resizable: false,
                draggable: false,
                modal: true
            })//dialog
            
        }//success
        ,error: function(respuesta){
            _mensaje("#_mensaje-1", "Ocurri&oacute; un error inesperado, int&eacute;ntelo de nuevo");
        } //error
    }) //$.ajax
}

function masZonas()
{
	var tipo = "extra";
	var cantidad = 1;
	var zona_id = $("#zona_id").val();
	var persona_id = $("#persona_id").val(); 
	var tipo_id = $("#tipo_id").val();
	
	// console.log(persona_id);

	if (tipo != '') {
		$.ajax({
			url: '/backend/persona/mas-zonas',
			type: 'POST',
			data: {tipo: tipo, cantidad: cantidad, zona_id: zona_id, persona_id: persona_id, tipo_id: tipo_id},
			success: function(res){
				$("#opciones").append(res);
			}
		});
	}
}

function eliminaOpcion(id)
{
	$('#opcion_'+id).remove();
}

function eliminaOpcionA(id)
{
	$.ajax({
		success: function(res){
			$('#opcion_z'+id).remove();
		}
	
});
}

function eliminarOpciones(id)
{
	$.ajax({
		url: '/backend/persona/eliminar-opciones',
		type: 'POST',
		data: {id: id},
		success: function(res)
		{
			if (res == 'El registro fue eliminado') {
				$('#opcion_'+id).remove();
				
			} 
		}
	});
}

function eliminarOpcionesAgregadas(id)
{
	$.ajax({
		url: '/backend/persona/eliminar-opciones-agregadas',
		type: 'POST',
		data: {id: id},
		success: function(res)
		{
			// console.log(id);
			if (res == 'El registro fue eliminado') {
				$('#opcion_z'.id).remove();
				eliminaOpcionA(id);
			} 
		}
	});
}

function updateByEstado(estado_id){
	var filtro2 = '/backend/persona/grid';
// console.log(estado_id);
			filtro2+="/estado_id/"+estado_id;

		$("#flexigrid").flexOptions({
			url: filtro2,
			onSuccess: function(){
			}

		}).flexReload();
	}

function updateByEstadoZona(estado_id,zona_id){
	var filtro2 = '/backend/persona/grid';
	// console.log(estado_id);
	filtro2+="/estado_id/"+estado_id;
	filtro2+="/zona_id/"+zona_id;

	$("#flexigrid").flexOptions({
		url: filtro2,
		onSuccess: function(){
		}

	}).flexReload();
}

function updateByEstadoZonaTipo(estado_id,zona_id,tipo_id){
	var filtro2 = '/backend/persona/grid';
	// console.log(estado_id);
	filtro2+="/estado_id/"+estado_id;
	filtro2+="/zona_id/"+zona_id;
	filtro2+="/tipo_id/"+tipo_id;

	$("#flexigrid").flexOptions({
		url: filtro2,
		onSuccess: function(){
		}

	}).flexReload();
}

function cambiaEstado(estado_id) {
	console.log("estado seleccionado: "+estado_id.value);
	var estado = estado_id.value;
	
	// updateByEstado(estado);

	if (estado != '') {
		$.ajax({
			url: '/backend/persona/on-change-estado',
			type: 'POST',
			data: {estado: estado},
			success: function(res){
				console.log('estado cambiado');
                var objJSON = eval("(function(){return " + res + ";})()");
                // var response = $.parseJSON(res);
                // console.log("sucess " + objJSON[0].nombre);
            var zonas = $('#zona_idS');
            zonas.empty();
            var tipo = $('#tipo_idS');
			tipo.empty();
			$('#zona_idS').append('<option value="">-Selecciona una zona-</option>');
			$('#tipo_idS').append('<option value="">-Selecciona un tipo-</option>');
                for (var zona in objJSON) {
                    console.log(objJSON[zona]['nombre']);
                    zonas.append(
                        $('<option>', {
                        value: objJSON[zona]['id']
                        }).text(objJSON[zona]['nombre'])
                    );
                    // $('#zona_id').append('<option value=' + objJSON[zona]['id'] + '>' + objJSON[zona]['nombre'] + '</option>');
                }
            }
		});
	}
}

function cambiaEstadoAZ(estado_id) {
	console.log("estado seleccionado: "+estado_id.value);
	var estado = estado_id.value;
	
	// updateByEstado(estado);

	if (estado != '') {
		$.ajax({
			url: '/backend/persona/on-change-estado-az',
			type: 'POST',
			data: {estado: estado},
			success: function(res){
				console.log('estado cambiado');
                var objJSON = eval("(function(){return " + res + ";})()");
                // var response = $.parseJSON(res);
                // console.log("sucess " + objJSON[0].nombre);
            var zonas = $('#zona_id');
            zonas.empty();
			$('#zona_id').append('<option value="">-Selecciona una zona-</option>');
                for (var zona in objJSON) {
                    console.log(objJSON[zona]['nombre']);
                    zonas.append(
                        $('<option>', {
                        value: objJSON[zona]['id']
                        }).text(objJSON[zona]['nombre'])
                    );
                    // $('#zona_id').append('<option value=' + objJSON[zona]['id'] + '>' + objJSON[zona]['nombre'] + '</option>');
                }
            }
		});
	}
}

function cambiaZonaAZ(zona_id) {
	console.log("zona seleccionada: "+zona_id.value);
	var zona = zona_id.value;
	
	// updateByEstado(estado);

	if (zona != '') {
		$.ajax({
			url: '/backend/persona/on-change-zona-az',
			type: 'POST',
			data: {zona: zona},
			success: function(res){
				console.log('zona cambiada');
                var objJSON = eval("(function(){return " + res + ";})()");
            var tipos = $('#tipo_id');
            tipos.empty();
			$('#tipo_id').append('<option value="">-Selecciona un tipo-</option>');
                for (var tipo in objJSON) {
                    console.log(objJSON[tipo]['descripcion']);
                    tipos.append(
                        $('<option>', {
                        value: objJSON[tipo]['id']
                        }).text(objJSON[tipo]['descripcion'])
                    );
                    // $('#zona_id').append('<option value=' + objJSON[zona]['id'] + '>' + objJSON[zona]['nombre'] + '</option>');
                }
            }
		});
	}
}


function cambiaZona(zona_id){

	var zona = zona_id.value;
	console.log("zona slelected: "+zona);
		var estado = $('#estado_idS').val();
		console.log("estado pre-seleccionado: "+estado);
		//  updateByEstadoZona(estado,zona);

if (zona != '') {
		$.ajax({
			url: '/backend/pre-registro/on-change-zona',
			type: 'POST',
			data: {zona: zona},
			success: function(res){
                var objJSON = eval("(function(){return " + res + ";})()");
                // var response = $.parseJSON(res);
                // console.log("sucess " + objJSON[0].nombre);
            var tipo = $('#tipo_idS');
			tipo.empty();
			$('#tipo_idS').append('<option value="">-Selecciona un tipo-</option>');
                for (var tipo in objJSON) {
                    console.log("de "+objJSON[tipo]['descripcion']);
                    // tipo.append(
                    //     $('<option>', {
                    //     value: objJSON[tipo]['id']
                    //     }).text(objJSON[tipo]['descripcion'])
                    // );
                     $('#tipo_idS').append('<option value=' + objJSON[tipo]['id'] + '>' + objJSON[tipo]['descripcion'] + '</option>');
                }
            }
		});
	}
}

function cambiaEmpresa(empresa_id) {
	console.log("empresa seleccionada: "+empresa_id.value);
	var empresa = empresa_id.value;
	
	// updateByEstado(estado);

	if (empresa != '') {
		$.ajax({
			url: '/backend/persona/on-change-empresa',
			type: 'POST',
			data: {empresa: empresa},
			success: function(res){
				console.log('empresa cambiada');
                var objJSON = eval("(function(){return " + res + ";})()");
                // var response = $.parseJSON(res);
                // console.log("sucess " + objJSON[0].nombre);
            var tipos = $('#tipo_id');
            tipos.empty();
			$('#tipo_id').append('<option value="">-Selecciona un tipo-</option>');
                for (var zona in objJSON) {
                    console.log(objJSON[zona]['descripcion']);
                    tipos.append(
                        $('<option>', {
                        value: objJSON[zona]['id']
                        }).text(objJSON[zona]['descripcion'])
                    );
                    // $('#zona_id').append('<option value=' + objJSON[zona]['id'] + '>' + objJSON[zona]['nombre'] + '</option>');
                }
            }
		});
	}
}

function cambiaTipo(tipo_id) {
	console.log("tipo seleccionado: "+tipo_id.value);
	var tipo = tipo_id.value;
	var estado = $('#estado_idS').val();
	var zona = $('#zona_idS').val();
	//console.log("tipo seleccionado: "+estado);
	//console.log("tipo seleccionado: "+zona);
	
	//  updateByEstadoZonaTipo(estado,zona,tipo);


}

function shakeEmpys(){
    // var cat = $('#categoria_id').val();

    var tipoF = document.getElementById("tipo_idS");
    var zonaf = document.getElementById("zona_idS");
    var estadoF = document.getElementById("estado_idS");


    if(tipoF.value === "") {
        // console.log("falta " +catF.value);
        tipoF.classList.add("apply-shake");
    }
    if(zonaf.value === "") {
        // console.log("falta " +catF.value);
        zonaf.classList.add("apply-shake");
    }
    if(estadoF.value === "") {
        // console.log("falta " +catF.value);
        estadoF.classList.add("apply-shake");
    }


    tipoF.addEventListener("animationend", (e) => {
        tipoF.classList.remove("apply-shake");
    });
    zonaf.addEventListener("animationend", (e) => {
        zonaf.classList.remove("apply-shake");
    });
    estadoF.addEventListener("animationend", (e) => {
        estadoF.classList.remove("apply-shake");
    });
}