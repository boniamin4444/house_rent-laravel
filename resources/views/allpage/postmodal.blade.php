<!-- The Modal -->
<div class="modal fade" id="addHouseRentModal" tabindex="-1" aria-labelledby="addHouseRentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="addHouseRentModalLabel">Advertise your house for rent!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('house_rents.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Row 1 -->
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="district" class="form-label">District:</label>
                            <input type="text" class="form-control border-primary" name="district" id="district" required>
                        </div>
                        <div class="col-md-6">
                            <label for="police_station" class="form-label">Police Station:</label>
                            <input type="text" class="form-control border-primary" name="police_station" id="police_station" required>
                        </div>
                    </div>

                    <!-- Row 2 -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="road" class="form-label">Road:</label>
                            <input type="text" class="form-control border-primary" name="road" id="road" required>
                        </div>
                        <div class="col-md-6">
                            <label for="price" class="form-label">Price:</label>
                            <input type="number" step="0.01" class="form-control border-primary" name="price" id="price" required>
                        </div>
                    </div>

                    <!-- Row 3 -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="square_feet" class="form-label">Square Feet:</label>
                            <input type="number" class="form-control border-primary" name="square_feet" id="square_feet" required>
                        </div>
                        <div class="col-md-6">
                            <label for="bedrooms" class="form-label">Bedrooms:</label>
                            <input type="number" class="form-control border-primary" name="bedrooms" id="bedrooms" required>
                        </div>
                    </div>

                    <!-- Row 4 -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="gallery" class="form-label">Gallery (Upload multiple images):</label>
                            <input type="file" class="form-control border-primary" name="gallery[]" id="gallery" multiple required>
                        </div>
                    </div>

                    <!-- Row 5 -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="description" class="form-label">Full Description:</label>
                            <textarea class="form-control border-primary" name="description" id="description" rows="4" required></textarea>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg w-100">Post</button>
                </form>
            </div>
        </div>
    </div>
</div>
