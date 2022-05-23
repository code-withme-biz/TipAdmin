@php @endphp
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TiP - Payment</title>
    <style>
      body{
        padding:0;
        margin: 0;
        font-family: Arial, Helvetica, sans-serif;
        background: #000;
      }

      p{
        font-size: 18px;
        color: #fff;
        line-height: 1.7;
      }
      /*Huge thanks to @tobiasahlin at http://tobiasahlin.com/spinkit/ */
        .spinner {
          margin: 100px auto 0;
          width: 70px;
          text-align: center;
        }

        .spinner > div {
          width: 18px;
          height: 18px;
          background-color: #8a2ad6;
          border-radius: 100%;
          display: inline-block;
          -webkit-animation: sk-bouncedelay 1.4s infinite ease-in-out both;
          animation: sk-bouncedelay 1.4s infinite ease-in-out both;
        }

        .spinner .bounce1 {
          -webkit-animation-delay: -0.32s;
          animation-delay: -0.32s;
        }

        .spinner .bounce2 {
          -webkit-animation-delay: -0.16s;
          animation-delay: -0.16s;
        }

        @-webkit-keyframes sk-bouncedelay {
          0%, 80%, 100% { -webkit-transform: scale(0) }
          40% { -webkit-transform: scale(1.0) }
        }

        @keyframes sk-bouncedelay {
          0%, 80%, 100% { 
            -webkit-transform: scale(0);
            transform: scale(0);
          } 40% { 
            -webkit-transform: scale(1.0);
            transform: scale(1.0);
          }
        }
      </style>
  </head>
<body>
  <div style="
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;">
    <figure  style="text-align: center;width:100%;margin:0;" >
      <img src="https://www.dapperitmedia.com/public/black/img/icon.png"  style="width: 200px;border-radius: 50%;"/>
      <figure>
        <!-- <a href="javascript:void(0)" id="pay-cus" style="display:none;" >Pay with PayPal</a> -->
      <a href="javascript:void(0)" id="pay-cus" style="display:none;" onClick="paytest();">Pay with PayPal</a>
     <p>Hold on we're getting you to PayPal</p>
      <div class="spinner">
          <div class="bounce1"></div>
          <div class="bounce2"></div>
          <div class="bounce3"></div>
      </div>
<script
  src="https://code.jquery.com/jquery-3.5.1.min.js"
  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  crossorigin="anonymous"></script>
  <script>
    jQuery('#pay-cus').click();
  function paytest(){
    $.post("{{URL::to('/api/payment/create')}}", {
          device_id: '{{$device_id}}',
          artist_id: '{{$artist_id}}',
          amount: '{{$amount}}',
          tranType: '{{$intent}}',
      }).done( res => {
        window.location.href = res;
      }).fail( err => {
        console.log(res);
      })
  }
  </script>
</body>
</html>
