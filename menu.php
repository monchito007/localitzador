    <div class="navbar navbar-inverse navbar-fixed-top ">
      <div class="navbar-inner">
        <div class="container .span12">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="#">
              Localitzador WEB
          </a>
          <div class="nav-collapse collapse">
            <ul class="nav">
                <?php
                echo "<li class='active'><a href='home.php?lat=".$_SESSION['lat_origen']."&lng=".$_SESSION['lng_origen']."'>Home</a></li>";
                ?>
              <!--<li><a href="#contact">Contact</a></li>-->
              <!--
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="#">Action</a></li>
                  <li><a href="#">Another action</a></li>
                  <li><a href="#">Something else here</a></li>
                  <li class="divider"></li>
                  <li class="nav-header">Nav header</li>
                  <li><a href="#">Separated link</a></li>
                  <li><a href="#">One more separated link</a></li>
                </ul>
                </li>
                --!>
                <li><a>
                        
                <?php
                    echo "<p>";
                    echo $classe_location_origen->localitat;
                    echo "<center>";
                    //Si hem trobat dades de previsió meteorologica mostrem la informació
                    if($weather->actcode){

                        echo "<a href='previsio_meteo.php'><img id='img_weather' name='img_weather' src='images/weather/".$weather->actcode.".png'> ".$weather->acttemp." º".$weather->unit_temperature."</a>";

                    }
                    echo "</center>";
                    echo "<div id='div_hora' class='center'></div>";
                    echo "</p>";

                ?>

                </a>
                </li>
                <li>
                <a> 
                <?php
                    include("form_inicia_sessio.php");
                ?>
                </a></li>
              
            </ul>
              <!--
            <form class="navbar-form pull-right">
              <input class="span2" type="text" placeholder="Usuari">
              <input class="span2" type="password" placeholder="Clau">
              <button type="submit" class="btn">Accedeix</button>
            </form>
            -->
            
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
<script type="text/javascript">
    function mostrahora(){
                
                var ahora = new Date();
                var hora = ahora.getHours();
                var minuts = ahora.getMinutes();
                var segons = ahora.getSeconds();
                
                var hora_formatejada = "" + hora + ":" + ((minuts<10)?"0":"")+minuts+":"+((segons<10)?"0":"")+segons; 
                
                document.getElementById('div_hora').innerHTML = hora_formatejada;
                
                relojid = setTimeout("mostrahora()",1000);
                
    }
    mostrahora();
</script>