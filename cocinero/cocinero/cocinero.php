<?php
session_start();
error_reporting(0);
$varsesion = $_SESSION['usuario'];
if ($varsesion == null || $varsesion = '') {
  header("location: ../index.php");
  die();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />

  <title>HOTELEASE</title>

  <!-- slider stylesheet -->
  <link rel="stylesheet" type="text/css"
    href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.1.3/assets/owl.carousel.min.css" />

  <!-- fonts style -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:400,700|Raleway:400,500,700&display=swap"
    rel="stylesheet">
  <!-- bootstrap core css -->
  <link rel="stylesheet" type="text/css" href="../css2/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="../css2/footer.css" />
  <!-- Custom styles for this template -->
  <link href="../css2/style.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="../css2/responsive.css" rel="stylesheet" />

</head>

<body class="sub_page">
  <div class="hero_area">
    <?php include '../administrador/menuadmin.php'; ?> <!-- Incluir el menú -->
    <section class=" slider_section position-relative">
      <a href="" class="btn-img">
        <img src="../images/btn-img.png" alt="">
      </a>

      <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
          <div class="carousel-item active">
            <div class="container-fluid">
              <div class="row">
                <div class="col-lg-4 offset-lg-2 col-md-5 offset-md-1">
                  <div class="detail_box">
                    <h1>
                      Bienvenido <br>
                      Cocinero al <br>
                      sistema virtual
                    </h1>
                    <p>
                      Acceso limitado para personal de cocina
                    </p>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="img-box">
                    <img src="../images/slider-img.png" alt="">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <script type="text/javascript" src="../js2/jquery-3.4.1.min.js"></script>
  <script type="text/javascript" src="../js2/bootstrap.js"></script>
  <?php include '../footer/footer.php'; ?>
</body>

</html>
