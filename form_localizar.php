<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Proj - Inici</title>
        <script>
            
            function marcar_focus(){
                
                document.form_inici.cerca.focus();
                
            }
            
            function envia_dades(dades){
                
                //alert(dades);
                document.form_inici.submit();
                //location.href='mapa_marcas.php';
                
            }
    
        
    
        </script>
    </head>
    <style>
        body{
            background-color: #D7DF01;
            height: 800px;
            width: 1000px;
        }
        select{
            margin-top: 50px;
            margin: 25px;
            font-size: 65px; 
            background-color:#ccccff
        }
        input[type=submit]{
            margin-top: 25px; 
            height: 100px;
            width: 600px;
            font-size: 40px; 
        }
        
        input{
            margin: 30px;
            height: 100px;
            width: 600px;
            font-size: 40px; 
        }
        
        h1{
            padding-left: 30px;
            padding-top: 30px;
            font-family: Helvetica,font-family;
            font-size: 60px;
        }
        #contenido{
            height: 800px;
            width: 370x;
            background-color: #FACC2E;
        }
    </style>
    <body onload="marcar_focus()">
        <div id="contenido">
            <!--<form  name="form_inici" id="form_inici" action="javascript:envia_dades(document.form_inici.cerca.value);">-->
            <form  name="form_inici" id="form_inici" method="post" action="mapa_marcas.php">
                <h2>Busca establiments</h2>
                <input type="text" name="cerca"/>
                <input type="submit" value="cerca">
            </form>
        </div>
    </body>
</html>
