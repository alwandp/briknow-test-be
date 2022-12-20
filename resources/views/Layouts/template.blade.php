<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add In BRIKNOW</title>
    <link rel="stylesheet" href="{{asset('asset/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="py-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-center">
                        <img src="{{asset('asset/images/bri know.png')}}" width="50%" alt="">
                    </div>
                </div>
            </div>
            <div class="row d-flex justify-content-center">
                <div class="col-md-6 mt-4">
                    <div class="input-group mb-3">
                        <span class="input-group-text">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </span>
                        <input type="text" class="form-control" aria-label="Amount (to the nearest dollar)">
                        <button class="input-group-text btn btn-primary">Search</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{asset('asset/js/bootstrap.bundle.min.js')}}"></script>
</body>
</html>