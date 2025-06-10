@extends('layouts.app') {{-- Ensure this correctly extends your main layout --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>House Rents</title>

    {{-- !!! IMPORTANT: Add this CSRF meta tag here or in layouts/app.blade.php head !!! --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Include your CSS stylesheets here, e.g., Bootstrap --}}
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> --}}
</head>
<body>
    @include('allpage.postmodal') {{-- Ensure this path is correct for your modal --}}

    @section('content')
        @auth
            <div class="container mt-4">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <button class="form-control" data-bs-toggle="modal" data-bs-target="#addHouseRentModal"
                                    style="border: 1px solid #ccc; background-color: #f7f7f7; font-size: 1.2rem;">
                                    <span class="text-muted">What's on your mind?</span>
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <a href="#" class="btn btn-primary btn-block">
                                            <i class="fa fa-video-camera"></i> Live video
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="#" class="btn btn-success btn-block">
                                            <i class="fa fa-image"></i> Photo/video
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="#" class="btn btn-danger btn-block" data-bs-toggle="modal"
                                            data-bs-target="#houseRentModal">
                                            <i class="fa fa-film"></i> Rent Advertisement
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- Guest content or message --}}
        @endauth

        <div class="row mt-4"> {{-- Added mt-4 for spacing --}}
            @foreach ($houseRents as $houseRent)
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            {{-- Loop through gallery images and display them --}}
                            <div class="row">
                                @foreach ($houseRent->gallery as $index => $image)
                                    @if ($index < 4)
                                        {{-- Show the first 4 images --}}
                                        <div class="col-4 mb-2">
                                            <img src="{{ asset('storage/' . $image) }}" class="img-fluid"
                                                alt="House Image" data-bs-toggle="modal"
                                                data-bs-target="#imageModal{{ $houseRent->id }}">
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <h5 class="card-title">{{ $houseRent->district }}, {{ $houseRent->police_station }}</h5>
                            <p class="card-text">Price: ৳{{ number_format($houseRent->price, 2) }}</p>
                            <p class="card-text">Square Feet: {{ $houseRent->square_feet }} sq ft</p>
                            <p class="card-text">Bedrooms: {{ $houseRent->bedrooms }}</p>

                            {{-- Description (Show 100 words and show more on click) --}}
                            <p class="card-text" id="shortDescription{{ $houseRent->id }}">
                                {{ Str::limit($houseRent->description, 100) }}
                                @if (strlen($houseRent->description) > 100)
                                    <span data-bs-toggle="collapse" data-bs-target="#description{{ $houseRent->id }}"
                                        aria-expanded="false" aria-controls="description{{ $houseRent->id }}"
                                        class="text-primary show-more-less" style="cursor: pointer;">+ Show More</span>
                                @endif
                            </p>

                            {{-- Full Description for Collapsing --}}
                            <div class="collapse" id="description{{ $houseRent->id }}">
                                <p class="card-text mt-2">{{ $houseRent->description }}</p>
                                <span data-bs-toggle="collapse" data-bs-target="#description{{ $houseRent->id }}"
                                    aria-expanded="true" aria-controls="description{{ $houseRent->id }}"
                                    class="text-primary show-more-less" style="cursor: pointer;">- Show Less</span>
                            </div>

                            {{-- Like, Comment, and Message Buttons in 4 Columns --}}
                            <div class="row mt-3">
                                @auth
                                    <div class="col-4">
                                        {{-- !!! IMPORTANT: data-house-rent-id is correct !!! --}}
                                        {{-- Determine initial button state based on whether the current user has liked it --}}
                                        <button class="btn w-100 like-btn {{ $houseRent->likes->contains('user_id', Auth::id()) ? 'btn-secondary' : 'btn-success' }}"
                                            data-house-rent-id="{{ $houseRent->id }}">
                                            {{ $houseRent->likes->contains('user_id', Auth::id()) ? 'Liked' : 'Like' }}
                                        </button>
                                    </div>
                                    <div class="col-4">
                                        <button class="btn btn-warning w-100">Comment</button>
                                    </div>
                                    <div class="col-4">
                                        <a class="btn btn-primary w-100"
                                            href="{{ route('messages.index', ['receiver_id' => $houseRent->user_id]) }}">Message</a>
                                    </div>
                                @endauth

                                @guest
                                    {{-- Optional: You can show a message to guests, if desired --}}
                                    <div class="col-12 text-center">
                                        <p class="text-muted">Please log in to like, comment, or send a message.</p>
                                    </div>
                                @endguest
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Modals for image gallery --}}
        @foreach ($houseRents as $houseRent)
            <div class="modal fade" id="imageModal{{ $houseRent->id }}" tabindex="-1"
                aria-labelledby="imageModalLabel{{ $houseRent->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="imageModalLabel{{ $houseRent->id }}">House Rent Images</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                @foreach ($houseRent->gallery as $image)
                                    <div class="col-md-4 mb-3">
                                        <img src="{{ asset('storage/' . $image) }}" class="img-fluid" alt="House Image">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endsection

    {{-- !!! IMPORTANT: Scripts section should be AFTER the body, typically in layouts/app.blade.php !!! --}}
    @section('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        {{-- If you're using Bootstrap's JS, include it here as well --}}
        {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}


        <script>
            $(document).ready(function() {
                // Get CSRF token from meta tag
                const csrfToken = $('meta[name="csrf-token"]').attr('content');

                // Add event listener for "Show More/Less" toggle
                $('.show-more-less').on('click', function() {
                    const span = $(this);
                    if (span.text().includes('+ Show More')) {
                        span.text('- Show Less');
                    } else {
                        span.text('+ Show More');
                    }
                });


                // Toggle Like Button
                $(document).on('click', '.like-btn', function() {
                    const button = $(this);
                    const houseRentId = button.data('house-rent-id');

                    $.ajax({
                        url: '/likes/toggle',
                        type: 'POST',
                        data: {
                            house_rent_id: houseRentId,
                            _token: csrfToken // Include CSRF token
                        },
                        success: function(response) {
                           
                            // Toggle button UI
                            if (response.message === 'Liked') {
                                button.text('Liked')
                                    .removeClass('btn-success')
                                    .addClass('btn-secondary');
                            } else {
                                button.text('Like')
                                    .removeClass('btn-secondary')
                                    .addClass('btn-success');
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 401) {
                                alert('Please log in to like this post.');
                                // Optional: redirect to login page
                                // window.location.href = '/login';
                            } else if (xhr.status === 419) { // CSRF token mismatch/session expired
                                alert('Your session has expired. Please refresh the page and try again.');
                                // Optional: reload page to get a new CSRF token
                                // window.location.reload();
                            } else {
                                alert('Something went wrong. Please try again.');
                                console.error('AJAX Error:', xhr.status, xhr.responseText); // Log the error for debugging
                            }
                        }
                    });
                });
            });
        </script>
    @endsection
</body>
</html>