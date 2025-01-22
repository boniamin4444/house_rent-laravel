@extends('layouts.app');

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document</title>
</head>
<body>
    @include('allpage.postmodal') 
    @section('content')

    @auth
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <button class="form-control" data-bs-toggle="modal" data-bs-target="#addHouseRentModal" style="border: 1px solid #ccc; background-color: #f7f7f7; font-size: 1.2rem;">
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
                                <a href="#" class="btn btn-danger btn-block" data-bs-toggle="modal" data-bs-target="#houseRentModal">
                                    <i class="fa fa-film"></i> Rent Advertisement
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
    @else
    @endauth


        <div class="row">
            @foreach($houseRents as $houseRent)
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <!-- Loop through gallery images and display them -->
                            <div class="row">
                                @foreach($houseRent->gallery as $index => $image)
                                    @if($index < 4)
                                        <!-- Show the first 4 images -->
                                        <div class="col-4 mb-2">
                                            <img src="{{ asset('storage/' . $image) }}" class="img-fluid" alt="House Image" data-bs-toggle="modal" data-bs-target="#imageModal{{ $houseRent->id }}">
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <h5 class="card-title">{{ $houseRent->district }}, {{ $houseRent->police_station }}</h5>
                            <p class="card-text">Price: ৳{{ number_format($houseRent->price, 2) }}</p>
                            <p class="card-text">Square Feet: {{ $houseRent->square_feet }} sq ft</p>
                            <p class="card-text">Bedrooms: {{ $houseRent->bedrooms }}</p>

                            <!-- Description (Show 100 words and show more on click) -->
                            <p class="card-text" id="shortDescription{{ $houseRent->id }}">
                                {{ Str::limit($houseRent->description, 100) }}
                                <span data-bs-toggle="collapse" data-bs-target="#description{{ $houseRent->id }}" aria-expanded="false" aria-controls="description{{ $houseRent->id }}" class="text-primary" style="cursor: pointer;">+ Show More</span>
                            </p>

                            <!-- Full Description for Collapsing -->
                            <div class="collapse" id="description{{ $houseRent->id }}">
                                <p class="card-text mt-2">{{ $houseRent->description }}</p>
                            </div>

                           <!-- Like, Comment, and Message Buttons in 4 Columns -->
<div class="row mt-3">
    @auth
        <div class="col-4">
            <button class="btn btn-success w-100">Like</button>
        </div>
        <div class="col-4">
            <button class="btn btn-warning w-100">Comment</button>
        </div>
        <div class="col-4">
          <a class="btn btn-primary w-100" href="{{ route('messages.index', ['receiver_id' => $houseRent->user_id]) }}">Message</a>    
        </div>
    @endauth

    @guest
        <!-- Optional: You can show a message to guests, if desired -->
        <p class="text-center">Please log in to like, comment, or send a message.</p>
    @endguest
</div>


                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal to show all images when clicked -->
    @foreach($houseRents as $houseRent)
        <div class="modal fade" id="imageModal{{ $houseRent->id }}" tabindex="-1" aria-labelledby="imageModalLabel{{ $houseRent->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imageModalLabel{{ $houseRent->id }}">House Rent Images</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Loop through all gallery images and display them in the modal -->
                        <div class="row">
                            @foreach($houseRent->gallery as $image)
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

   
    
</body>
</html>