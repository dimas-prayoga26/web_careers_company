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
                        <h3>Submitted Successfully</h3>
                        <p class="step-number-content active">Your information has been submitted successfully.</p>
                    </div>
                    <ul class="progress-bar">
                        <li class="active">Success Information</li>
                    </ul>
                </div>
                <div class="right-side">
                    <div class="main active">
                        <a href="{{ $brand['website'] }}" target="_blank">
                            <img src="{{ asset($brand['logo']) }}" alt="{{ $brand['name'] }}" width="120">
                        </a>
                        <div class="text congrats">
                            <div class="svg-box">
                                <svg class="circular green-stroke">
                                    <circle class="path" cx="75" cy="75" r="50" fill="none" stroke-width="5" stroke-miterlimit="10" />
                                </svg>
                                <svg class="checkmark green-stroke">
                                    <g transform="matrix(0.79961,8.65821e-32,8.39584e-32,0.79961,-489.57,-205.679)">
                                        <path class="checkmark__check" fill="none" d="M616.306,283.025L634.087,300.805L673.361,261.53" />
                                    </g>
                                </svg>
                            </div>
                            <h2>Congratulations!</h2>
                            <p>Thanks, your information has been submitted successfully. We will contact you soon.</p>
                            <a href="{{ $brand['website'] }}" class="back-to-home" role="button">Go to {{ $brand['name'] }}</a>
                            <div class="social-icons">
                                <a href="https://www.facebook.com/officialrnb/" class="fa fa-facebook"></a>
                                <a href="#" class="fa fa-twitter"></a>
                                <a href="https://www.instagram.com/official_rnbmanagement/" class="fa fa-instagram"></a>
                                <a href="#" class="fa fa-linkedin"></a>
                                <a href="https://www.youtube.com/@rnbmanagement3859" class="fa fa-youtube"></a>
                                <a href="mailto:{{ $brand['email'] }}" class="fa fa-envelope-o"></a>
                                <a href="{{ $brand['whatsapp_url'] }}" class="fa fa-whatsapp"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
