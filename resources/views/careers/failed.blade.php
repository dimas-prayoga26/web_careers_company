<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $brand['name'] }} - Careers</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('assets/svg/svg-icons-animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="icon" href="{{ asset($brand['favicon'] ?? $brand['logo']) }}">
</head>

<body class="snippet-body">
    <div class="container">
        <div class="card">
            <div class="form">
                <div class="left-side">
                    <div class="left-heading">
                        <h3>{{ $brand['name'] }}</h3>
                    </div>
                    <div class="steps-content">
                        <h3>Registration Failed</h3>
                        <p class="step-number-content active">The registration was interrupted.</p>
                    </div>
                    <ul class="progress-bar">
                        <li class="active">Failed Information</li>
                    </ul>
                </div>
                <div class="right-side">
                    <div class="main active">
                        <a href="{{ $brand['website'] }}" target="_blank">
                            <img src="{{ asset($brand['logo']) }}" alt="{{ $brand['name'] }}" width="120">
                        </a>
                        <div class="text congrats">
                            <div class="svg-box">
                                <svg class="circular red-stroke">
                                    <circle class="path" cx="75" cy="75" r="50" fill="none" stroke-width="5" stroke-miterlimit="10" />
                                </svg>
                                <svg class="cross red-stroke">
                                    <g transform="matrix(0.79961,8.65821e-32,8.39584e-32,0.79961,-502.652,-204.518)">
                                        <path class="first-line" d="M634.087,300.805L673.361,261.53" fill="none" />
                                    </g>
                                    <g transform="matrix(-1.28587e-16,-0.79961,0.79961,-1.28587e-16,-204.752,543.031)">
                                        <path class="second-line" d="M634.087,300.805L673.361,261.53" />
                                    </g>
                                </svg>
                            </div>
                            <h2>Registration Failed!</h2>
                            <div class="alert-container">
                                <div class="alert alert-danger" role="alert">{{ $message }}</div>
                            </div>
                            <p>Sorry, the registration was interrupted. Please retry the same flow or reach us if the issue continues.</p>
                            <a href="{{ route('careers.index') }}" class="back-to-home" role="button">Back to Careers</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
