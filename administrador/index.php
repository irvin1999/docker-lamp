<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <title>Login-Admin</title>
   <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css'>
   <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'>
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="css2/style.css">
<style>
/* Fonts Form Google Font ::- https://fonts.google.com/  -:: */
@import url('https://fonts.googleapis.com/css?family=Abel|Abril+Fatface|Alegreya|Arima+Madurai|Dancing+Script|Dosis|Merriweather|Oleo+Script|Overlock|PT+Serif|Pacifico|Playball|Playfair+Display|Share|Unica+One|Vibur');
/* End Fonts */
/* Start Global rules */
* {
    padding: 0;
    margin: 0;
    box-sizing: border-box;
}
/* End Global rules */


/* Start body rules */
body {
    background-image: linear-gradient(-225deg, #E3FDF5 0%, #FFE6FA 100%);
background-image: linear-gradient(to top, #a8edea 0%, #fdfed6 100%);
background-attachment: fixed;
  background-repeat: no-repeat;

    font-family: 'Vibur', cursive;
/*   the main font */
    font-family: 'Abel', sans-serif;
opacity: .95;
/* background-image: linear-gradient(to top, #d9afd9 0%, #97d9e1 100%); */
}



/* |||||||||||||||||||||||||||||||||||||||||||||*/
/* //////////////////////////////////////////// */




/* End body rules */

/* Start form  attributes */

form {
    width: 450px;
    min-height: 500px;
    height: auto;
    border-radius: 5px;
    margin: 2% auto;
    box-shadow: 0 9px 50px hsla(20, 67%, 75%, 0.31);
    padding: 2%;
    background-image: linear-gradient(-225deg, #E3FDF5 50%, #FFE6FA 50%);
}
/* form Container */
form .con {
    display: -webkit-flex;
    display: flex;

    -webkit-justify-content: space-around;
    justify-content: space-around;

    -webkit-flex-wrap: wrap;
    flex-wrap: wrap;

      margin: 0 auto;
}

/* the header form form */
header {
    margin: 2% auto 10% auto;
    text-align: center;
}
/* Login title form form */
header h2 {
    font-size: 250%;
    font-family: 'Playfair Display', serif;
    color: #3e403f;
}
/*  A welcome message or an explanation of the login form */
header p {letter-spacing: 0.05em;}



/* //////////////////////////////////////////// */
/* //////////////////////////////////////////// */


.input-item {
    background: #fff;
    color: #333;
    padding: 14.5px 0px 15px 9px;
    border-radius: 5px 0px 0px 5px;
}



/* Show/hide password Font Icon */
#eye {
    background: #fff;
    color: #333;

    margin: 5.9px 0 0 0;
    margin-left: -20px;
    padding: 15px 9px 19px 0px;
    border-radius: 0px 5px 5px 0px;

    float: right;
    position: relative;
    right: 1%;
    top: -.2%;
    z-index: 5;

    cursor: pointer;
}
/* inputs form  */
input[class="form-input"]{
    width: 240px;
    height: 50px;

    margin-top: 2%;
    padding: 15px;

    font-size: 16px;
    font-family: 'Abel', sans-serif;
    color: #5E6472;

    outline: none;
    border: none;

    border-radius: 0px 5px 5px 0px;
    transition: 0.2s linear;

}
input[id="txt-input"] {width: 250px;}
/* focus  */
input:focus {
    transform: translateX(-2px);
    border-radius: 5px;
}

/* //////////////////////////////////////////// */
/* //////////////////////////////////////////// */

/* input[type="text"] {min-width: 250px;} */
/* buttons  */
button {
    display: inline-block;
    color: #252537;

    width: 280px;
    height: 50px;

    padding: 0 20px;
    background: #fff;
    border-radius: 5px;

    outline: none;
    border: none;

    cursor: pointer;
    text-align: center;
    transition: all 0.2s linear;

    margin: 7% auto;
    letter-spacing: 0.05em;
}
/* Submits */
.submits {
    width: 100%;
    display: inline-block;
    float: left;
    margin-left: 10%;
}

/*       Forgot Password button FAF3DD  */
.frgt-pass {background: transparent;}

/*     Sign Up button  */
.sign-up {background: #B8F2E6;}


/* buttons hover */
button:hover {
    transform: translatey(3px);
    box-shadow: none;
}

/* buttons hover Animation */
button:hover {
    animation: ani9 0.4s ease-in-out infinite alternate;
}
@keyframes ani9 {
    0% {
        transform: translateY(3px);
    }
    100% {
        transform: translateY(5px);
    }
}
</style>
</head>

<body>
   <div class="overlay">
      <form action="php/compruebaUsuarios.php" method="POST">
         <div class="con">
            <header class="head-form">
               <h2>Iniciar Sesion</h2>
               <p>ADMINITRADOR</p>
               <p>Inicie sesión aquí usando su nombre de usuario y contraseña</p>
            </header>
            <br>
            <div class="field-set">
               <span class="input-item">
                  <i class="fa fa-user-circle"></i>
               </span>
               <input class="form-input" name="dni" type="text" placeholder="DNI o NIE" required>
               <br>
               <span class="input-item">
                  <i class="fa fa-key"></i>
               </span>
               <input class="form-input" name="passw" type="password" placeholder="Contraseña" id="pwd" required>
               <span>
                  <i class="fa fa-eye" aria-hidden="true" type="button" id="eye"></i>
               </span>
               <br>
               <button class="log-in"> Log In <i class="fa fa-user-plus" aria-hidden="true"></i> </button>
            </div>
            <div id="aviso-inactivo" class="aviso-inactivo">
               El usuario está inactivo. Por favor, contacte al administrador.
            </div>
            <?php
            // Aquí se integra el código PHP para mostrar la alerta si es necesario
            if (isset ($_GET['error']) && $_GET['error'] == 'permisos') {
               echo '<div id="alerta" class="alert alert-primary" role="alert">
                          Servidor solo para administradores, comunicate con tu administrador predeterminado.
                      </div>';
               // Redirigir a esta misma página después de 3 segundos para eliminar el parámetro de la URL
               echo '<meta http-equiv="refresh" content="4;url=./">';
            }
            ?>
            <?php
            // Aquí se integra el código PHP para mostrar el aviso si es necesario
            if (isset ($_GET['error']) && $_GET['error'] == 'inactivo') {
               echo '<div class="alert alert-primary" role="alert">
                          Tu cuenta está inactiva. Por favor, comunícate con el administrador.
                      </div>';
               // Redirigir a esta misma página después de 5 segundos para eliminar el parámetro de la URL
               echo '<meta http-equiv="refresh" content="4;url=./">';
            }
            ?>
            <?php
            // Aquí se integra el código PHP para mostrar el aviso si es necesario
            if (isset ($_GET['error']) && $_GET['error'] == 'contrasena') {
               echo '<div class="alert alert-primary" role="alert">
                          Ingreso de contraseña incorrecto, vuelve a intentarlo.
                      </div>';
               // Redirigir a esta misma página después de 5 segundos para eliminar el parámetro de la URL
               echo '<meta http-equiv="refresh" content="3;url=./">';
            }
            ?>
            <?php
            // Aquí se integra el código PHP para mostrar el aviso si es necesario
            if (isset ($_GET['error']) && $_GET['error'] == 'usuario') {
               echo '<div class="alert alert-primary" role="alert">
                          usuario no registrado, pruebe de nuevo.
                      </div>';
               // Redirigir a esta misma página después de 5 segundos para eliminar el parámetro de la URL
               echo '<meta http-equiv="refresh" content="3;url=./">';
            }
            ?>
         </div>
      </form>
   </div>
   <!-- partial -->
   <script src="js/script.js"></script>
</body>

</html>
