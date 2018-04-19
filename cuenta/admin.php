<?php require '../config/config.php'; $title = 'Finanzas App'; ?>
<?php require DIRECTORIO_ROOT.'inc/header.php'; ?>

<!-- Pruebas de Codigo -->
<?php 
	
 //  var_dump(json_decode(Read::income()));

  Read::nextDebts();


 ?>

<div ng-app="appFinanzas">
	
    <div class="container" ng-controller="finanzasController">

      <h4 class="text-center">
        Dinero Actual: $ <?php echo number_format( Read::currentMoney() ); ?>
      </h4>

    	<!-- Ingresos -->
    	<hr>
    	<h3>Nuevo Ingreso</h3>
      
    	  <form ng-submit="addEntry()">
          Concepto: <input type="text" ng-model="newEntry.concepto">
          Valor: $ <input type="number" ng-model="newEntry.valor" min="1000">
          <input type="hidden" ng-model="newEntry.empt_val">
          <input type="submit" value="Agregar ingreso">
        </form>
        <br>
        
        <table class="table table-bordered table-hover text-center">
        	<thead>
        		<tr colspan="3"><h5>Ingresos (Ultimos 10)</h5></tr>
        		<tr>
        			<th>Concepto</th>
        			<th>Valor</th>
        			<th>Fecha</th>
        		</tr>
        	</thead>
        	<tbody>
        		<tr ng-repeat="entry in income track by $index">
        			<td>{{entry.concepto}}</td>
        			<td>{{entry.valor | currency : $ }}</td>
        			<td>{{entry.fecha | date: 'mediumDate'}}</td>
        		</tr>
        	</tbody>
        </table>
        <!-- #ingresos -->

        <hr>

        <!-- NUEVO GASTO -->

        <h3>Nuevo Gasto</h3>
        <form ng-submit="addSpending()">
          Concepto: <input type="text" ng-model="newSpending.concepto">
          Valor: <input type="number" min="1000" ng-model="newSpending.valor">
          Fecha: <input type="date" ng-model="newSpending.fecha">
          <input type=submit value="Nuevo Gasto">
        </form>

        <!-- #NUEVO GASTO -->


        <hr>

        <!-- Deudas -->
    	<h3>Nueva Deuda</h3>

      Selecciona el tipo de deuda
      <select ng-model="typeDebt" ng-change="selectTypeDebt()">
        <option ng-repeat="x in tipos_deuda" value="{{x.id}}">{{x.tipo_deuda}}</option>
      </select>

    	<form ng-show="dxc" ng-submit="addDebt()">

            <input type="hidden" name="">

          	Concepto: 
          	<select ng-model="newDebt.id_concepto">
          		<option ng-repeat="x in conceptosDeuda" value="{{x.id}}">{{x.concepto}}</option>	
          	</select><br>
          	Detalle Concepto: <input type="text" ng-model="newDebt.concepto"><br>
          	Acreedor: 
          	<select ng-model="newDebt.id_acreedor" ng-change="formCreditor()">
          		<option ng-repeat="x in creditors track by $index" value="{{x.id_acreedor}}">{{x.propietario}} ({{x.banco}})</option>
              <option value="new">Otro</option>
          	</select><br>

          	Valor: $ <input type="number" ng-model="newDebt.valor" min="1000"><br>
          	Tasa Interes: $ <input type="number" ng-model="newDebt.tasa" step="any"><br>
          	Cuotas: $ <input type="number" ng-model="newDebt.cuotas"><br>
          	Fecha de primera cuota: $ <input type="date" ng-model="newDebt.primer_pago"><br>
          	<input type="submit" value="Agregar deuda">
        </form>


        <!-- Cargos Fijos -->
        <form ng-show="cfm" ng-submit="addFixedCharges()">
          <h5>Cargos Fijos</h5>
          Concepto: <input type="text" ng-model="newFixedCharge.concepto">
          Valor: <input type="text" ng-model="newFixedCharge.valor">
          Día Pago<input type="text" ng-model="newFixedCharge.dia_pago">
          <input type="submit" value="Agregar Cargo Fijo">
        </form>
        <!-- #Cargos Fijos -->

            <!-- Acreedores -->
              <div ng-show="creditorForm">
                <form ng-submit="addCreditor()">
                  <h4>Agregar Acreedor</h4>
                  Banco: <input type="text" ng-model="newCreditor.banco">
                  Propietario: <input type="text" ng-model="newCreditor.propietario">
                  <input type="submit" value="Agregar Acreedor">
                </form>
                <span ng-model="resultCreditor"></span>
              </div>
            <!-- Acreedores -->

        <hr>

        



        <hr>



        <table class="table table-bordered table-hover text-center">
          <thead>
            <tr colspan="3"><h5>Acreedores (Ultimos 10)</h5></tr>
            <tr>
              <th>Banco</th>
              <th>Propietario</th>
            </tr>
          </thead>
          <tbody>
            <tr ng-repeat="crd in creditors">
              <td>{{crd.banco}}</td>
              <td>{{crd.propietario}}</td>
            </tr>
          </tbody>
        </table>
      	
	   
       	<!-- #Deudas -->

        <hr>

     

        <hr>
        <h4>Proximos pagos</h4>

        <table class="table table-bordered table-hover text-center">
        	<thead>
        		<tr colspan="3"><h5>Deudas (Ultimos 10)</h5></tr>
        		<tr>
        			<th>Acreedor</th>
        			<th>Valor</th>
        			<th>Fecha</th>
        		</tr>
        	</thead>
        	<tbody>
        		<tr ng-repeat="next in nextDebts">
        			<td>{{next.concepto}}</td>
        			<td>{{next.valor_pagar | currency : $ : 0}}</td>
        			<td>{{ }}</td>
        		</tr>
        	</tbody>
        </table>
        {{nextDebts}}
        <!-- Pagar Deuda -->
        <h4>Pagar</h4>
        <form ng-submit="addDebtPayment()">
          Deuda: 
            <select ng-model="selectedDebt" ng-change="set_ndp()" ng-options="x.concepto for x in nextDebts">
            </select>
          Valor: <input type="text" ng-model="newDebtPayment.pago_total">
          <input type="submit" value="Ingresar Pago">
        </form>
        <!-- #Pagar Deuda -->
    </div>
</div>



<?php require DIRECTORIO_ROOT.'inc/footer.php'; ?>