<?php
/**
*@package pXP
*@file    SolModPresupuesto.php
*@author  Rensi Arteaga Copari 
*@date    30-01-2014
*@description permites subir archivos a la tabla de documento_sol
*/
header("content-type: text/javascript; charset=UTF-8");
?>

<script>
Phx.vista.FormFiltroBalance=Ext.extend(Phx.frmInterfaz,{
    constructor:function(config)
    {   
    	this.panelResumen = new Ext.Panel({html:'Hola Prueba'});
    	this.Grupos = [{

	                    xtype: 'fieldset',
	                    border: false,
	                    autoScroll: true,
	                    layout: 'form',
	                    items: [],
	                    id_grupo: 0
				               
				    },
				     this.panelResumen
				    ];
				    
        Phx.vista.FormFiltroBalance.superclass.constructor.call(this,config);
        this.init(); 
        this.iniciarEventos();   
       
        
        
    },
    
    Atributos:[
          
	   	   {
				config:{
					name: 'desde',
					fieldLabel: 'Desde',
					allowBlank: true,
					format: 'd/m/Y',
					width: 150
				},
				type: 'DateField',
				id_grupo: 0,
				form: true
		  },
		  {
				config:{
					name: 'hasta',
					fieldLabel: 'Hasta',
					allowBlank: true,
					format: 'd/m/Y',
					width: 150
				},
				type: 'DateField',
				id_grupo: 0,
				form: true
		  },
		  {
   			config:{
                name: 'id_deptos',
                fieldLabel: 'Depto',
                typeAhead: false,
                forceSelection: true,
                allowBlank: false,
                disableSearchButton: true,
                emptyText: 'Depto Contable',
                store: new Ext.data.JsonStore({
                    url: '../../sis_parametros/control/Depto/listarDeptoFiltradoDeptoUsuario',
                    id: 'id_depto',
					root: 'datos',
					sortInfo:{
						field: 'deppto.nombre',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_depto','nombre','codigo'],
					// turn on remote sorting
					remoteSort: true,
					baseParams: { par_filtro:'deppto.nombre#deppto.codigo', estado:'activo', codigo_subsistema: 'CONTA'}
                }),
                valueField: 'id_depto',
   				displayField: 'nombre',
   				hiddenName: 'id_depto',
                enableMultiSelect: true,
                triggerAction: 'all',
                lazyRender: true,
                mode: 'remote',
                pageSize: 20,
                queryDelay: 200,
                anchor: '80%',
                listWidth:'280',
                resizable:true,
                minChars: 2
            },
   			//type:'TrigguerCombo',
   			type:'AwesomeCombo',
   			id_grupo:0,
   			form:true
         },
	     {
	       		config:{
	       			name:'nivel',
	       			fieldLabel:'Nivel',
	       			allowBlank:false,
	       			emptyText:'nivel...',
	       			typeAhead: true,
	       		    triggerAction: 'all',
	       		    lazyRender:true,
	       		    mode: 'local',
	       		    valueField: 'autentificacion',
	       		    store:[1,2,3,4,5,6,7,8]
	       		    
	       		},
	       		type:'ComboBox',
	       		id_grupo:0,
	       		form:true
	      }
    ],
    labelSubmit: '<i class="fa fa-check"></i> Aplicar Filtro',
    title: 'Filtro de mayores',
    // Funcion guardar del formulario
    onSubmit: function(o) {
    	var me = this;
    	if (me.form.getForm().isValid()) {
             var parametros = me.getValForm()
             Phx.CP.loadingShow();
             Ext.Ajax.request({
						url : '../../sis_contabilidad/control/Cuenta/reporteBalanceGeneral',
						params : parametros,
						success : this.successExport,
						failure : this.conexionFailure,
						timeout : this.timeout,
						scope : this
					})
                    
        }

    }
    
    
})    
</script>