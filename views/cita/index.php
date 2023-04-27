<h1 class="nombre-pagina">Crear Nueva Cita</h1>
<p class="descripcion-pagina">Elige tus Servicios y Coloca tus Datos</p>

<?php include_once __DIR__ . '/../templates/barra.php' ?>

<div class="app">
    <nav class="taps">
        <button class="actual" type="button" data-paso="1">Servicios</button>   
        <button type="button" data-paso="2">Informacion Cita</button>   
        <button type="button" data-paso="3">Resumen</button>   
    </nav>    

    <div id="paso-1" class="seccion mostrar">
        <h2>Servicios</h2>
        <p class="text-center">Elige tus servicios a continuacion</p>
        <div id="servicios" class="listado-servicios"></div>
    </div>
    <div id="paso-2" class="seccion">
        <h2>Tus Datos y Cita</h2>
        <p class="text-center">Coloca tus Datos y fecha de tu cita</p>

        <form class="formulario">
            <div class="campo">
                <label for="nombre">Nombre</label>
                <input 
                    type="text" 
                    id="nombre" 
                    placeholder="Tu Nombre" 
                    value="<?php echo s($_SESSION['nombre']) ?>"
                    disabled
                />
            </div>

            <div class="campo">
                <label for="fecha">Fecha</label>
                <input 
                    type="date" 
                    id="fecha" 
                    min="<?php echo date('Y-m-d') ?>"
                />
            </div>

            <div class="campo">
                <label for="hora">Hora</label>
                <input 
                    type="time" 
                    id="hora" 
                    min="7:00"
                />
            </div>
        </form>
        <input type="hidden" id="id" value="<?php echo $id; ?>">
    </div>
    <div id="paso-3" class="seccion contenido-resumen">
        <h2>Resumen</h2>
        <p class="text-center">Verifica que la Informacion sea Correcta</p>
    </div>

    <div class="paginacion">
        <button class="boton anterior" id="anterior">&laquo; Anterior</button>
        <button class="boton siguiente" id="siguiente"> Siguiente &raquo;</button>
    </div>
</div>



<?php 
    $script = '
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="build/js/app.js"></script>
    ';
?>