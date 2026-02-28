<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="{{ URL::asset('test/public/css/style.css') }}" />
    <link rel="shortcut icon" href="{{ URL::asset('test/public/img/logo.svg') }}" type="image/x-icon" />
    <title>Coming Soon</title>
  </head>

  <body>
    <section class="nav-section">
      <div class="logo">
        <img src="{{ URL::asset('test/public/img/logo.svg') }}" alt="" class="logo-img" />
      </div>
    </section>
    <section class="coming-soon">
      <div class="coming">Coming</div>
      <span class="soon wordCon">
        <div class="word">Soon</div>
      </span>
      <div class="oops">
        OOPS..You caught us!<br />
        We're just updating our site, it should be back up and running soon. In
        the meantime, check out the cool things on our social media.
      </div>
      <a
        href="https://www.facebook.com/Dawaaalhayatco/"
        target="_blank"
        rel="noopener noreferrer"
        ><img class="w-100" src="{{ URL::asset('test/public/img/facebook.png') }}" alt=""
      /></a>
    </section>
    <div class="motion">
      <img class="background bg1" src="{{ URL::asset('test/public/img/bg.jpg') }}" alt="" />
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- <script src="js/countdown.js"></script> -->
    <script src="js/script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.countdown/2.2.0/jquery.countdown.min.js"></script>
    <script>
      $(document).ready(function () {
        var countdownDate = "2024/08/01 13:00:00";

        $('#clock').countdown(countdownDate, function(event) {
          $(this).html(event.strftime(''
            + '<span class="number days">%-D</span> days '
            + '<span class="number hours">%H</span> hours '
            + '<span class="number minutes">%M</span> minutes '
            + '<span class="number seconds">%S</span> seconds'));
        });
      });
    </script>
  </body>
</html>
