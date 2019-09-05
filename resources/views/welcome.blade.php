<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <link href="https://fonts.googleapis.com/css?family=Montserrat:100,400,700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('front_end/css/style.css')}}">

    <title>Wellcome</title>
</head>

<body>
    <div class="container mt-5">
        <nav class="nav mt-3 ml-4">
            <h4 class="mr-5 logo"><a href="{{ url('/') }}">BudgetinQ</a></h4>
        </nav>
        <div class="card mt-1" style="background-color: #050A27">
            <div class="row main mt-5 ml-2 mb-5">
                <div class="col-lg-8">
                    <p class="">
                        <b>BudgeTinQ</b> adalah aplikasi untuk mengatur keuangan anda sehari-hari.
                    </p>
                </div>
                <div class="col-lg-4">
                    <h1 class="display-5">
                        <button type="button" class="btn btn-primary">Login or Register With Google</button>
                    </h1>
                </div>
            </div>
        </div>
    </div>



    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>

</body>

</html>