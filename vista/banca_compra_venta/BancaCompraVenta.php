<?php
/**
*@package pXP
*@file gen-BancaCompraVenta.php
*@author  (admin)
*@date 11-09-2015 14:36:46
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.BancaCompraVenta=Ext.extend(Phx.gridInterfaz,{

tabEnter: true,
tipoBan: 'Compras',
	constructor:function(config){
		
	
		
		var dia = 01;
		var mes = 01;
		var anio = 2015;
		var fecha_fin = '01/01/2016';
		
		this.initButtons=[this.cmbDepto, this.cmbGestion, this.cmbPeriodo];
		
		
		
		
		this.Grupos = [
		            {
		                layout: 'column',
		                border: false,
		                autoHeight : true,
		                defaults: {
		                    border: false,
                            bodyStyle: 'padding:4px'
		                },            
		                items: [
		                              {
		                                xtype: 'fieldset',
		                                columnWidth: 0.5,
		                                defaults: {
								            anchor: '-20' // leave room for error icon
								        },
		                                title: 'Datos del Documento',
		                                items: [],
		                                id_grupo: 0,
		                                flex:1,
		                                autoHeight : true,
		                                margins:'2 2 2 2'
		                             },
		                              
		                            {
		                                xtype: 'fieldset',
		                                columnWidth: 0.5,
		                                title: 'Detalle del Pago',
		                                items: [],
		                                margins:'2 10 2 2',
		                                id_grupo:1,
		                                autoHeight : true,
		                                flex:1
		                            },
		                             {
		                                xtype: 'fieldset',
		                                columnWidth: 0.5,
		                                title: 'Detalle del Pago2',
		                                items: [],
		                                margins:'2 10 2 2',
		                                id_grupo:2,
		                                autoHeight : true,
		                                flex:1
		                             }
		               ]   
		     }];
		
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.BancaCompraVenta.superclass.constructor.call(this,config);
		
		 var fieldset = this.form.items.items[0].items.items[1];
		
		 
		 fieldset.add({
         	xtype:'button',
         	text:'<i class="fa fa-file"></i> Ver Historial Acumulado',
         	scope:this,
         	handler:function(){
         		alert('Ver Historial Acumulado');         	
         	}
         	
         });
         fieldset.doLayout(); 
         
		 console.log(fieldset);
		 
		
		
		this.cmbGestion.on('select', function(combo, record, index){
			this.tmpGestion = record.data.gestion;
		    this.cmbPeriodo.enable();
		    this.cmbPeriodo.reset();
		    this.store.removeAll();
		    this.cmbPeriodo.store.baseParams = Ext.apply(this.cmbPeriodo.store.baseParams, {id_gestion: this.cmbGestion.getValue()});
		    this.cmbPeriodo.modificado = true;
		    
		    /*anio = record.data.gestion;
		    var fecha = new Date(mes+'/'+dia+'/'+anio);
			
			this.getComponente('fecha_documento').setMinValue(fecha);
 			this.getComponente('fecha_documento').setMaxValue(fecha_fin);*/
		    
		    
        },this);
        
        
        this.cmbPeriodo.on('select', function( combo, record, index){
			this.tmpPeriodo = record.data.periodo;
			this.capturaFiltros();
			
			/* mes = record.data.periodo;
			 console.log(mes);
		    var fecha = new Date(mes+'/'+dia+'/'+anio);
		    fecha_fin = new Date(mes+'/'+dia+'/'+anio);
		    fecha_fin.setMonth(fecha_fin.getMonth() + 1);
		    fecha_fin.setDate(fecha_fin.getDate() - 1);
		    console.log(fecha);
		    console.log(fecha_fin);
			
			this.getComponente('fecha_documento').setMinValue(fecha);
 			this.getComponente('fecha_documento').setMaxValue(fecha_fin);*/
			
			
		    
        },this);
        
        this.cmbDepto.on('select', function( combo, record, index){
			this.capturaFiltros();
		    
        },this);
        
        /*this.cmbTipo.on('select', function( combo, record, index){
			if(this.cmbTipo.getValue() == 'Compras'){
				console.log('compras');
				this.Cmp.tipo_transaccion.show()
			}else{
				console.log('ventas');
				this.Cmp.tipo_transaccion.hide();
			}
		    
        },this);
        */
        
      
        
       
        
        
        
        
		this.init();
		this.grid.addListener('cellclick', this.oncellclick,this);
		
		this.iniciarEventos();
		
		 this.construyeVariablesContratos();
		 
		 
		this.addButton('exportar',{argument: {imprimir: 'exportar'},text:'<i class="fa fa-file-text-o fa-2x"></i> Generar TXT',/*iconCls:'' ,*/disabled:false,handler:this.generar_txt});

		this.addButton('Importar',{argument: {imprimir: 'Importar'},text:'<i class="fa fa-file-text-o fa-2x"></i> Importar TXT',/*iconCls:'' ,*/disabled:false,handler:this.importar_txt});

		//this.load({params:{start:0, limit:this.tam_pag}})
	},
	
	
	capturaFiltros:function(combo, record, index){
        this.desbloquearOrdenamientoGrid();
        if(this.validarFiltros()){
        	this.store.baseParams.id_gestion = this.cmbGestion.getValue();
	        this.store.baseParams.id_periodo = this.cmbPeriodo.getValue();
	        this.store.baseParams.id_depto = this.cmbDepto.getValue();
	        this.store.baseParams.tipo = this.tipoBan;
	        this.load(); 
        }
        
    },
    
    validarFiltros:function(){
        if(this.cmbDepto.getValue() && this.cmbGestion.validate() && this.cmbPeriodo.validate()){
            return true;
        }
        else{
            return false;
        }
    },
    onButtonAct:function(){
    	if(!this.validarFiltros()){
            alert('Especifique los filtros antes')
        }
    },
    
    
    
    /*cmbTipo : new Ext.form.ComboBox({
    	
				name: 'tipo',
				fieldLabel: 'tipo',
				allowBlank: true,
				emptyText: 'tipo...',
				typeAhead: true,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'local',
				store: ['Compras', 'Ventas'],
				width: 200,
				type: 'ComboBox',

    }),*/
    cmbDepto: new Ext.form.ComboBox({
                name: 'id_depto',
                fieldLabel: 'Depto',
                blankText: 'Depto',
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
            }),
    
	cmbGestion: new Ext.form.ComboBox({
				fieldLabel: 'Gestion',
				allowBlank: false,
				emptyText:'Gestion...',
				blankText: 'Año',
				store:new Ext.data.JsonStore(
				{
					url: '../../sis_parametros/control/Gestion/listarGestion',
					id: 'id_gestion',
					root: 'datos',
					sortInfo:{
						field: 'gestion',
						direction: 'DESC'
					},
					totalProperty: 'total',
					fields: ['id_gestion','gestion'],
					// turn on remote sorting
					remoteSort: true,
					baseParams:{par_filtro:'gestion'}
				}),
				valueField: 'id_gestion',
				triggerAction: 'all',
				displayField: 'gestion',
			    hiddenName: 'id_gestion',
    			mode:'remote',
				pageSize:50,
				queryDelay:500,
				listWidth:'280',
				width:80
			}),
	
	
     cmbPeriodo: new Ext.form.ComboBox({
				fieldLabel: 'Periodo',
				allowBlank: false,
				blankText : 'Mes',
				emptyText:'Periodo...',
				store:new Ext.data.JsonStore(
				{
					url: '../../sis_parametros/control/Periodo/listarPeriodo',
					id: 'id_periodo',
					root: 'datos',
					sortInfo:{
						field: 'periodo',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_periodo','periodo','id_gestion'],
					// turn on remote sorting
					remoteSort: true,
					baseParams:{par_filtro:'gestion'}
				}),
				valueField: 'id_periodo',
				triggerAction: 'all',
				displayField: 'periodo',
			    hiddenName: 'id_periodo',
    			mode:'remote',
				pageSize:50,
				disabled: true,
				queryDelay:500,
				listWidth:'280',
				width:80
			}),
	
	
	iniciarEventos:function(){
		this.Cmp.tipo_documento_pago.on('select', function(combo, record, index){ 
			console.log(this.Cmp.tipo_documento_pago.getValue());
		}, this);
		
		/*this.Cmp.autorizacion.on('change', function(combo, record, index){ 
			Ext.Ajax.request({
				url: '../../sis_contabilidad/control/BancaCompraVenta/listarBancaCompraVenta',
				params: {'autorizacion': ''+this.Cmp.autorizacion.getValue()+'','start':'0','limit':'1000',"sort":"id_banca_compra_venta","dir":"ASC"},
				success: this.verAutorizacion,
				failure: this.conexionFailure,
				timeout: this.timeout,
				scope: this
			});
		
		}, this);*/
		
		
		this.Cmp.id_proveedor.on('select', function(combo, record, index){ 
			//console.log(record.data.desc_proveedor);
			
			var res = record.data.desc_proveedor.split("(");
			console.log(res[0]);
			this.Cmp.nit_ci.setValue(record.data.nit);
			this.Cmp.razon.setValue(res[0]);
			
			
			
			this.Cmp.id_contrato.enable();
			this.Cmp.id_contrato.reset();
			this.Cmp.id_contrato.store.baseParams.filter = "[{\"type\":\"numeric\",\"comparison\":\"eq\", \"value\":\""+combo.getValue()+"\",\"field\":\"CON.id_proveedor\"}]";
			this.Cmp.id_contrato.modificado = true;
			
			
		}, this);
		
		this.Cmp.tipo_transaccion.on('select', function(combo, record, index){ 
			//console.log(record.data.desc_proveedor);
			if(record.data.digito == 2 || record.data.digito == 3){
				this.Cmp.autorizacion.setValue(4);
			}else{
				this.Cmp.autorizacion.setValue('');
			}
			
		}, this);
		
		
		this.Cmp.id_cuenta_bancaria.on('select', function(combo, record, index){ 
			console.log(record.data);
			
			if(this.Cmp.id_cuenta_bancaria.getValue() == 61){
				this.Cmp.tipo_documento_pago.reset();
				this.Cmp.tipo_documento_pago.store.baseParams.descripcion = "Transferencia de fondos";
				this.Cmp.tipo_documento_pago.modificado = true;
			}else{
				this.Cmp.tipo_documento_pago.reset();
				this.Cmp.tipo_documento_pago.store.baseParams.descripcion = "Cheque de cualquier naturaleza";
				this.Cmp.tipo_documento_pago.modificado = true;
			}
			this.Cmp.num_cuenta_pago.setValue(record.data.nro_cuenta);
			this.Cmp.nit_entidad.setValue(record.data.doc_id);
			
			
			
		}, this);
		
		this.Cmp.id_contrato.on('select', function(combo, record, index){ 
			
			
			console.log(record.data)
			
			this.Cmp.id_contrato.setValue(record.data.id_contrato);
			this.Cmp.num_contrato.setValue(record.data.numero);
			
			
			
		}, this);
		
		
		
		
		
		
	
		
		
		
		
		
		
	},
	
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_banca_compra_venta'
			},
			type:'Field',
			form:true 
		},
		
		 {
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_depto_conta'
			},
			type:'Field',
			form:true 
		},
		
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_periodo'
			},
			type:'Field',
			form:true 
		},
		
		 {
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'tipo'
			},
			type:'Field',
			form:true 
		},
		
		{
			config:{
				name: 'revisado',
				fieldLabel: 'Revisado',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:3,
                renderer: function (value, p, record, rowIndex, colIndex){  
                	     
            	       //check or un check row
            	       var checked = '',
            	       	    momento = 'no';
            	       	    console.log(value);
                	   if(value == 'si'){
                	        	checked = 'checked';;
                	   }
            	       return  String.format('<div style="vertical-align:middle;text-align:center;"><input style="height:37px;width:37px;" type="checkbox"  {0}></div>',checked);
            	        
                 }
			},
			type: 'TextField',
			filters: { pfiltro:'banca.revisado',type:'string'},
			id_grupo: 0,
			grid: true,
			form: false
		},
		
		
		
		{
			config: {
				name: 'modalidad_transaccion',
				fieldLabel: 'Modalidad Transacción ',
				allowBlank: false,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_contabilidad/control/ConfigBanca/listarConfigBanca',
					id: 'id_config_banca',
					root: 'datos',
					sortInfo: {
						field: 'digito',
						direction: 'ASC',
						tipo:'favio'

					},
					totalProperty: 'total',
					fields: ['id_config_banca', 'digito', 'descripcion','tipo'],
					remoteSort: true,
					baseParams: {par_filtro: 'confba.descripcion#confba.tipo',tipo:'Modalidad de transacción'}
				}),
				valueField: 'digito',
				displayField: 'descripcion',
				gdisplayField: 'desc_modalidad_transaccion',
				tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{digito}</b>. {descripcion} </p> </div></tpl>',

				hiddenName: 'id_config_banca',
				forceSelection: true,
				typeAhead: true,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '60%',
				gwidth: 150,
				minChars: 2,
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['desc_modalidad_transaccion']);
				}
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'confba.descripcion',type: 'string'},
			grid: true,
			form: true
		},
		
	
		
		{
			config:{
				name: 'fecha_documento',
				fieldLabel: 'Fecha Factura / Fecha Documento ',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''},
							
						    
			},
				type:'DateField',
				filters:{pfiltro:'banca.fecha_documento',type:'date'},
				id_grupo:0,
				grid:true,
				form:true
		},
		
		{
			config: {
				name: 'tipo_transaccion',
				fieldLabel: 'Tipo de Transacción',
				allowBlank: false,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_contabilidad/control/ConfigBanca/listarConfigBanca',
					id: 'id_config_banca',
					root: 'datos',
					sortInfo: {
						field: 'digito',
						direction: 'ASC',
						tipo:'favio'

					},
					totalProperty: 'total',
					fields: ['id_config_banca', 'digito', 'descripcion','tipo'],
					remoteSort: true,
					baseParams: {par_filtro: 'confba.descripcion#confba.tipo',tipo:'Tipo de transacción'}
				}),
				valueField: 'digito',
				displayField: 'descripcion',
				gdisplayField: 'desc_tipo_transaccion',
				tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{digito} </b>. {descripcion} </p> </div></tpl>',

				hiddenName: 'id_config_banca',
				forceSelection: true,
				typeAhead: true,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '60%',
				gwidth: 150,
				minChars: 2,
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['desc_tipo_transaccion']);
				}
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'confba.descripcion',type: 'string'},
			grid: true,
			form: true
		},
		
		{
			config:{
				name: 'autorizacion',
				fieldLabel: 'Nro Autorización /Factura Documento ',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100
			},
				type:'NumberField',
				filters:{pfiltro:'banca.autorizacion',type:'numeric'},
				id_grupo:0,
				grid:true,
				form:true
		},
		
		{
			config: {
				name: 'id_proveedor',
				fieldLabel: 'Proveedor',
				allowBlank: false,
				forceSelection: false,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_parametros/control/Proveedor/listarProveedorCombos',
					id: 'id_proveedor',
					root: 'datos',
					sortInfo: {
						field: 'id_proveedor',
						direction: 'ASC'

					},
					totalProperty: 'total',
					fields: ['id_proveedor','id_persona','id_institucion','desc_proveedor', 'rotulo_comercial', 'nit'],
					remoteSort: true,
					baseParams: {par_filtro: 'provee.desc_proveedor#provee.nit'}
				}),
				valueField: 'id_proveedor',
				displayField: 'desc_proveedor',
				gdisplayField: 'desc_tipo_transaccion',
				tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{desc_proveedor}</b></p><p>NIT: {nit} </p> </div></tpl>',


				hiddenName: 'id_proveedor',
				forceSelection: true,
				typeAhead: true,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '60%',
				gwidth: 150,
				minChars: 2,
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['desc_tipo_transaccion']);
				}
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'provee.desc_proveedor',type: 'string'},
			grid: false,
			form: true
		},
		
		
		{
			config:{
				name: 'nit_ci',
				fieldLabel: 'NIT / CI Proveedor',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'banca.nit_ci',type:'string'},
				id_grupo:0,
				grid:true,
				form:true
		},
		
		{
			config:{
				name: 'razon',
				fieldLabel: 'Nombre / Razón Social Proveedor',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'banca.razon',type:'string'},
				id_grupo:0,
				grid:true,
				form:true
		},
		
		{
			config:{
				name: 'num_documento',
				fieldLabel: 'Nro de Factura / Nro Documento ',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'banca.num_documento',type:'string'},
				id_grupo:0,
				grid:true,
				form:true
		},
		
		{
			config: {
				name: 'id_contrato',
				hiddenName: 'id_contrato',
				fieldLabel: 'Obj Contrato',
				typeAhead: false,
				forceSelection: false,
				allowBlank: false,
				disabled: true,
				emptyText: 'Contratos...',
				store: new Ext.data.JsonStore({
					url: '../../sis_workflow/control/Tabla/listarTablaCombo',
					id: 'id_contrato',
					root: 'datos',
					sortInfo: {
						field: 'id_contrato',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_contrato', 'numero', 'tipo', 'objeto', 'estado', 'desc_proveedor','monto','moneda','fecha_inicio','fecha_fin'],
					// turn on remote sorting
					remoteSort: true,
					baseParams: {par_filtro:'con.numero#con.tipo#con.monto#prov.desc_proveedor#con.objeto#con.monto', tipo_proceso:"CON",tipo_estado:"finalizado",id_tabla:3}
				}),
				valueField: 'id_contrato',
				displayField: 'objeto',
				gdisplayField: 'desc_contrato',
				triggerAction: 'all',
				lazyRender: true,
				resizable:true,
				mode: 'remote',
				pageSize: 20,
				queryDelay: 200,
				listWidth:380,
				minChars: 2,
				gwidth: 100,
				anchor: '80%',
				renderer: function(value, p, record) {
					if(record.data['desc_contrato']){
						return String.format('{0}', record.data['desc_contrato']);
					}
					return '';
					
				},
				tpl: '<tpl for="."><div class="x-combo-list-item"><p>Nro: {numero} ({tipo})</p><p>Obj: <strong><b>{objeto}</b></strong></p><p>Prov : {desc_proveedor}</p> <p>Monto: {monto} {moneda}</p><p>Rango: {fecha_inicio} al {fecha_fin}</p></div></tpl>'
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {
				pfiltro: 'con.numero',
				type: 'numeric'
			},
			grid: true,
			form: true
		},
		
		
		{
			config:{
				name: 'num_contrato',
				fieldLabel: 'N de contrato ',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'banca.num_contrato',type:'string'},
				id_grupo:0,
				grid:true,
				form:true
		},
		
		{
			config:{
				name: 'importe_documento',
				fieldLabel: 'Importe Factura / Importe Documento',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:655362
			},
				type:'NumberField',
				filters:{pfiltro:'banca.importe_documento',type:'numeric'},
				id_grupo:0,
				grid:true,
				form:true
		},
		
		
		{
			config: {
				name: 'id_cuenta_bancaria',
				fieldLabel: 'Cuenta Bancaria TESORERIA',
				allowBlank: false,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_tesoreria/control/CuentaBancaria/listarCuentaBancaria',
					id: 'id_cuenta_bancaria',
					root: 'datos',
					sortInfo: {
						field: 'id_cuenta_bancaria',
						direction: 'ASC'

					},
					totalProperty: 'total',
					fields: ['id_cuenta_bancaria', 'denominacion', 'nro_cuenta','nombre_institucion','doc_id'],
					remoteSort: true,
					baseParams: {par_filtro: 'ctaban.denominacion#ctaban.nro_cuenta'}
				}),
				valueField: 'id_cuenta_bancaria',
				displayField: 'denominacion',
				gdisplayField: 'desc_tipo_transaccion',
				tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{denominacion}</b></p><p>Nro Cuenta: {nro_cuenta} </p> <p>Institucion: {nombre_institucion} </p><p>nit Institucion: {doc_id} </p></div></tpl>',


				hiddenName: 'id_cuenta_bancaria',
				forceSelection: true,
				typeAhead: true,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '90%',
				gwidth: 150,
				minChars: 2,
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['desc_tipo_transaccion']);
				}
			},
			type: 'ComboBox',
			id_grupo: 1,
			filters: {pfiltro: 'ctaban.denominacion',type: 'string'},
			grid: false,
			form: true
		},
		
		
		{
			config:{
				name: 'num_cuenta_pago',
				fieldLabel: 'Nro de Cuenta del Documento de Pago',
				allowBlank: true,
				anchor: '90%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'banca.num_cuenta_pago',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'monto_pagado',
				fieldLabel: 'Monto Pagado en Documento de Pago',
				allowBlank: true,
				anchor: '90%',
				gwidth: 100,
				maxLength:655362
			},
				type:'NumberField',
				filters:{pfiltro:'banca.monto_pagado',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'monto_acumulado',
				fieldLabel: 'Monto Acumulado',
				allowBlank: true,
				anchor: '90%',
				gwidth: 100,
				maxLength:655362
			},
				type:'NumberField',
				filters:{pfiltro:'banca.monto_acumulado',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		
		{
			config:{
				name: 'nit_entidad',
				fieldLabel: 'NIT Entidad Financiera ',
				allowBlank: true,
				anchor: '90%',
				gwidth: 100
			},
				type:'NumberField',
				filters:{pfiltro:'banca.nit_entidad',type:'numeric'},
				id_grupo:2,
				grid:true,
				form:true
		},
		
		{
			config:{
				name: 'num_documento_pago',
				fieldLabel: 'Nro Documento de Pago (Nro Transacción u Operación) ',
				allowBlank: true,
				anchor: '90%',
				gwidth: 100,
				maxLength:255
			},
				type:'TextField',
				filters:{pfiltro:'banca.num_documento_pago',type:'string'},
				id_grupo:2,
				grid:true,
				form:true
		},
		
		
		
		


		
		
		
		
		
		{
			config: {
				name: 'tipo_documento_pago',
				fieldLabel: 'Tipo de Documento de Pago',
				allowBlank: false,
				emptyText: 'Elija una opción...',
				store: new Ext.data.JsonStore({
					url: '../../sis_contabilidad/control/ConfigBanca/listarConfigBanca',
					id: 'id_config_banca',
					root: 'datos',
					sortInfo: {
						field: 'digito',
						direction: 'ASC',
						tipo:'favio'

					},
					totalProperty: 'total',
					fields: ['id_config_banca', 'digito', 'descripcion','tipo'],
					remoteSort: true,
					baseParams: {par_filtro: 'confba.descripcion#confba.tipo',tipo:'Tipo de documento de pago'}
				}),
				valueField: 'digito',
				displayField: 'descripcion',
				gdisplayField: 'desc_tipo_documento_pago',
				tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{digito} </b>. {descripcion} </p> </div></tpl>',

				hiddenName: 'id_config_banca',
				forceSelection: true,
				typeAhead: true,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 15,
				queryDelay: 1000,
				anchor: '90%',
				gwidth: 150,
				minChars: 2,
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['desc_tipo_documento_pago']);
				}
			},
			type: 'ComboBox',
			id_grupo: 2,
			filters: {pfiltro: 'confba.descripcion',type: 'string'},
			grid: true,
			form: true
		},
		{
			config:{
				name: 'estado_reg',
				fieldLabel: 'Estado Reg.',
				allowBlank: true,
				anchor: '90%',
				gwidth: 100,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'banca.estado_reg',type:'string'},
				id_grupo:2,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'fecha_de_pago',
				fieldLabel: 'Fecha del Documento de Pago  ',
				allowBlank: true,
				anchor: '90%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'banca.fecha_de_pago',type:'date'},
				id_grupo:2,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'fecha_reg',
				fieldLabel: 'Fecha Creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'banca.fecha_reg',type:'date'},
				id_grupo:2,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'usuario_ai',
				fieldLabel: 'Funcionaro AI',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:300
			},
				type:'TextField',
				filters:{pfiltro:'banca.usuario_ai',type:'string'},
				id_grupo:2,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'usr_reg',
				fieldLabel: 'Creado Por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'usu1.cuenta',type:'string'},
				id_grupo:2,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: 'Creado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'banca.id_usuario_ai',type:'numeric'},
				id_grupo:2,
				grid:false,
				form:false
		},
		{
			config:{
				name: 'usr_mod',
				fieldLabel: 'Modificado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'usu2.cuenta',type:'string'},
				id_grupo:2,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'fecha_mod',
				fieldLabel: 'Fecha Modif.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'banca.fecha_mod',type:'date'},
				id_grupo:2,
				grid:true,
				form:false
		}
	],
	tam_pag:50,	
	title:'bancarizacion',
	ActSave:'../../sis_contabilidad/control/BancaCompraVenta/insertarBancaCompraVenta',
	ActDel:'../../sis_contabilidad/control/BancaCompraVenta/eliminarBancaCompraVenta',
	ActList:'../../sis_contabilidad/control/BancaCompraVenta/listarBancaCompraVenta',
	id_store:'id_banca_compra_venta',
	fields: [
		{name:'id_banca_compra_venta', type: 'numeric'},
		{name:'num_cuenta_pago', type: 'string'},
		{name:'tipo_documento_pago', type: 'numeric'},
		{name:'num_documento', type: 'string'},
		{name:'monto_acumulado', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'nit_ci', type: 'string'},
		{name:'importe_documento', type: 'numeric'},
		{name:'fecha_documento', type: 'date',dateFormat:'Y-m-d'},
		{name:'modalidad_transaccion', type: 'numeric'},
		{name:'tipo_transaccion', type: 'numeric'},
		{name:'autorizacion', type: 'numeric'},
		{name:'monto_pagado', type: 'numeric'},
		{name:'fecha_de_pago', type: 'date',dateFormat:'Y-m-d'},
		{name:'razon', type: 'string'},
		{name:'tipo', type: 'string'},
		{name:'num_documento_pago', type: 'string'},
		{name:'num_contrato', type: 'string'},
		{name:'nit_entidad', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		
		{name:'desc_modalidad_transaccion', type: 'string'},
		{name:'desc_tipo_transaccion', type: 'string'},
		{name:'desc_tipo_documento_pago', type: 'string'},
		{name:'revisado', type: 'string'},
	
		
		
	],
	sortInfo:{
		field: 'id_banca_compra_venta',
		direction: 'ASC'
	},
	bdel:true,
	bsave:true,
	
	oncellclick : function(grid, rowIndex, columnIndex, e) {
		
     	var record = this.store.getAt(rowIndex),
	        fieldName = grid.getColumnModel().getDataIndex(columnIndex); // Get field name
     	
     	if(fieldName == 'revisado') {
	       	//if(record.data['revisado'] == 'si'){
	       	   this.cambiarRevision(record);
	       //	}
	    }
     },
     cambiarRevision: function(record){
		Phx.CP.loadingShow();
	    var d = record.data
	    console.log(d)
        Ext.Ajax.request({
            url:'../../sis_contabilidad/control/BancaCompraVenta/cambiarRevision',
            params:{ id_banca_compra_venta: d.id_banca_compra_venta,revisado:d.revisado},
            success: this.successRevision,
            failure: this.conexionFailure,
            timeout: this.timeout,
            scope: this
        }); 
        this.reload();
	},
	successRevision: function(resp){
       Phx.CP.loadingHide();
       var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
       //this.reload();
       /*if(reg.datos = 'correcto'){
         this.reload();
       }*/
    },
   
     
	 onButtonAct:function(){
        if(!this.validarFiltros()){
            alert('Especifique el año y el mes antes')
         }
        else{
            this.store.baseParams.id_gestion=this.cmbGestion.getValue();
            this.store.baseParams.id_periodo = this.cmbPeriodo.getValue();
            this.store.baseParams.id_depto = this.cmbDepto.getValue();
            this.store.baseParams.tipo = this.tipoBan;
            
            Phx.vista.BancaCompraVenta.superclass.onButtonAct.call(this);
        }
    },
    
    onButtonNew:function(){
     	
     	
     	if(!this.validarFiltros()){
            alert('Especifique el año y el mes antes')
        }
        else{
        	this.accionFormulario = 'NEW';
            Phx.vista.BancaCompraVenta.superclass.onButtonNew.call(this);//habilita el boton y se abre
            this.Cmp.id_depto_conta.setValue(this.cmbDepto.getValue()); 
            this.Cmp.id_periodo.setValue(this.cmbPeriodo.getValue()); 
            //this.Cmp.tipo.setValue(this.cmbTipo.getValue()); 
        }
    },
    
     preparaMenu:function(tb){
        Phx.vista.BancaCompraVenta.superclass.preparaMenu.call(this,tb)
        var data = this.getSelectedData();
        if(data['revisado'] ==  'no' ){
            this.getBoton('edit').enable();
            this.getBoton('del').enable();
         
         }
         else{
            this.getBoton('edit').disable();
            this.getBoton('del').disable();
         } 
	        
    },
    
    liberaMenu:function(tb){
        Phx.vista.BancaCompraVenta.superclass.liberaMenu.call(this,tb);
                    
    },
    
    
     construyeVariablesContratos:function(){
    	Phx.CP.loadingShow();
    	Ext.Ajax.request({
                url: '../../sis_workflow/control/Tabla/cargarDatosTablaProceso',
                params: { "tipo_proceso": "CON", "tipo_estado": "finalizado" , "limit":"100","start":"0"},
                success: this.successCotratos,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope:   this
            });
           
    	
    	
    },
    successCotratos:function(resp){
           Phx.CP.loadingHide();
           var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
           if(reg.datos){
                
              this.ID_CONT = reg.datos[0].atributos.id_tabla
              
              this.Cmp.id_contrato.store.baseParams.id_tabla = this.ID_CONT;
             
             }else{
                alert('Error al cargar datos de contratos')
            }
     },
     
    
    
    
    generar_txt:function(){
			var rec = this.cmbPeriodo.getValue();
			var tipo = this.tipoBan;

			console.log(rec);


			
			Ext.Ajax.request({
				url:'../../sis_contabilidad/control/BancaCompraVenta/exporta_txt',
				params:{'id_periodo':rec,'tipo':tipo,'start':0,'limit':100000},
				success: this.successGeneracion_txt,
				failure: this.conexionFailure,
				timeout:this.timeout,
				scope:this
			});
		},
		successGeneracion_txt: function (resp) {
			//Phx.CP.loadingHide();
			console.log('resp' , resp)

			//doc.write(texto);
			var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));

			console.log(objRes.datos)
			//console.log(objRes.ROOT.datos[0].length)

			var texto = objRes.datos;
			  var link = document.createElement("a");
			  link.download = texto;
			  // Construct the uri
			  link.href = "/kerp_capacitacion/reportes_generados/"+texto+".txt";
			  document.body.appendChild(link);
			  link.click();
			  // Cleanup the DOM
			  document.body.removeChild(link);
			  delete link;


		},
		importar_txt:function(){
			
			
			var misdatos = new Object();
			misdatos.id_periodo = this.cmbPeriodo.getValue();
			misdatos.tipo = this.tipoBan;
			
			Phx.CP.loadWindows('../../../sis_contabilidad/vista/banca_compra_venta/subirArchivo.php',
	        'Subir',
	        {
	            modal:true,
	            width:450,
	            height:150
	        },misdatos,this.idContenedor,'SubirArchivo');
	        
		},
		verAutorizacion:function(resp){
			
			var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
			var datos = objRes.datos;
			
			
			if(datos[0] != undefined){
				this.Cmp.nit_ci.setValue(datos[0].nit_ci);
				this.Cmp.razon.setValue(datos[0].razon);
			}
			
		}/*,
		onSubmit : function(o, x, force) {
			alert(this.Cmp.id_contrato.getValue());
		} */
    
    
	}
)
</script>
		
		