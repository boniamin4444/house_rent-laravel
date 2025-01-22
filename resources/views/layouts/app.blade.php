

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title','Boni amin') </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .navbar {
            background-color: #4CAF50;
            position: sticky; /* sticky position add kora holo */
        top: 0; /* navbar page er top e thakbe */
        z-index: 1000;
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
        }
        .navbar-brand:hover {
            color: #ddd;
        }
        .nav-link {
            color: white;
        }
        .nav-link:hover {
            color: #ddd;
        }

        footer {
        background-color: #4CAF50; /* সবুজ ব্যাকগ্রাউন্ড */
        color: white; /* টেক্সটের রঙ সাদা */
        padding: 20px 0; /* উপরে এবং নিচে প্যাডিং */
    }

    footer h5 {
        color: #FFD700; /* হেডিং এর রঙ সোনালী */
        font-weight: bold;
    }

    footer a {
        color: #FFD700; /* লিংক সোনালী রঙের হবে */
        text-decoration: none;
    }

    footer a:hover {
        color: white; /* হোভার করলে লিংকের রঙ সাদা হবে */
        text-decoration: underline;
    }

    #main-container{
        min-height: 70vh;
    }

    </style>
</head>
<body>
   
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">MyBrand</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('messages.index') }}">Message</a>
                    </li>



                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                                <li><form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form></li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

   
    


    <main class="container mt-4" id="main-container">


        @yield('content')



    </main>


  <!-- Footer -->
  <footer class="mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <h5>About Us</h5>
                <p>Learn more about our mission, values, and team.</p>
            </div>
            <div class="col-md-3">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ url('/')}}">Home</a></li>
                    <li><a href="#">Services</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h5>Resources</h5>
                <ul class="list-unstyled">
                    <li><a href="#">FAQs</a></li>
                    <li><a href="#">Support</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h5>Contact Us</h5>
                <p>Email: boniamin4444@gmail.com</p>
                <p>Phone: +8801822860232</p>
                <p>Address: Siddhirganj Pool, Chittagong Road, Narayanganj, Dhaka</p>
            </div>
        </div>
    </div>
</footer>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <a href="{{ route('password.request') }}">Forgot your password?</a>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
