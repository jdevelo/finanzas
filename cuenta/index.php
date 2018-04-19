<?php require '../config/config.php'; $title ="Hola Munco"; ?>
<?php require DIRECTORIO_ROOT.'inc/header.php'; ?>
    <!-- Angular -->
    <div ng-app="finanzasApp">
      
      <hr>

        <div ng-controller="cursoAngular">

<!-- 
          <input type="text" ng-model="nombre">

          Hola <span ng-bind="nombre"></span>

          <hr>

          Comentarios:

          <input type="text" ng-model="nuevoComentario.comentario">
          <br>
          <input type="text" ng-model="nuevoComentario.usuario">
          <button ng-click="agregarComentario()">Agregar comentario</button>


          <ul>
            <li ng-repeat="comentario in comentarios">
              {{comentario.comentario + ' by ' + comentario.usuario}}
            </li>
          </ul>

       aplicando -->
<!-- 
          <input type="text" name="" ng-model="nuevaNota.materia">
          <input type="text" name="" ng-model="nuevaNota.nota">
          <button ng-click="agregarNota()" >Agregar nota</button>

          <ul>
            <li ng-repeat="nota in notas">
              {{nota.materia + ': ' + nota.nota}}
            </li>
          </ul>
          
        </div>
 -->
      <hr>


      <!-- $http -->
      <div ng-controller="httpController">
          <input type="text" ng-model="newPost.title">
          <input type="text" ng-model="newPost.body">
          <button ng-click="addPost()">NUEVO POST</button>

          <div ng-show="prueba">Cargando Prueba...</div>
          {{nombres}}
          <br>
          {{$parent.nombres}}
          <div ng-show="loading">Cargando...</div>
          <div ng-show="!loading && posts.length <= 0">No se han encontrado datos</div>
          <ul ng-show="posts.length == 0" class="animated">
            <li ng-repeat="post in posts">
                <h2>{{post.title}}</h2>
                <p>{{post.body}}</p>
            </li>
          </ul>
      </div>

      <hr>

      <!-- toDoList -->
      <h3>To Do List</h3>
      <div ng-controller="toDoList">
        
        <form ng-submit="addActv()">
          <input type="text" ng-model="newActv.descripcion"> 
          <input type="datetime-local" ng-model="newActv.fecha">
          <input type="submit" value="Guardar Tarea">
        </form>
          <button ng-click="clean()">Limpiar</button>

        <ul>
          <li ng-repeat="actv in todo">
            {{actv.descripcion}}
            {{actv.fecha | date: 'short'}}
          </li>
        </ul>

      </div> 
      <!-- toDoList -->


      <hr>


      <!-- Filter -->
        <h3>filtro</h3>

        <div ng-controller="filtersController">
          {{ mi_html | removeHtml }}
        </div>
      <!-- Filter -->

      <hr>

      <!-- Whatch - Diggest - aplly -->

      <div ng-controller="diggest">
        
        <h3>{{name}}</h3>
        <button ng-click="name = 'Juanito'">Cluck</button>
      </div>

      <!-- Whatch - Diggest - aplly -->


    </div>
    <!-- #Angular -->


  


<?php require DIRECTORIO_ROOT.'inc/footer.php'; ?>