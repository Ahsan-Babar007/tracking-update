@extends('layouts.admin')

@section('content')

<div class="row">
    <div class="card bg-blueGray-100 w-full">
        <div class="card-header">
            <div class="card-row">
                <h6 class="card-title">
                    Tracking Dashboard jkdfghjkdfhgjk
                </h6>
            </div>
        </div>

        <div class="card-body">
            <form id="trackingForm" type="POST">
                @csrf
                <!-- Form Fields -->
                <div class="row">
                    <!-- Order Date -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="orderDate">Order Date</label>
                            <input type="date" id="orderDate" name="orderDate" class="form-control" required>
                        </div>
                    </div>

                    <!-- Order Time -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="orderTime">Order Time</label>
                            <input type="time" id="orderTime" name="orderTime" class="form-control" required>
                        </div>
                    </div>

                    <!-- Delivery Date -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="deliveryDate">Delivery Date</label>
                            <input type="date" id="deliveryDate" name="deliveryDate" class="form-control" required>
                        </div>
                    </div>

                    <!-- Recipient ZIP -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="recipientZip">Recipient ZIP</label>
                            <input type="text" id="recipientZip" name="recipientZip" class="form-control" placeholder="Enter Recipient ZIP" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
            </form>

            <!-- Show API Response Below the Button -->
            <div id="trackingResults" class="mt-4" style="display:none;">
                <h3>Tracking Results</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Shipped Date</th>
                            <th>Delivery Date</th>
                            <th>Recipient ZIP</th>
                            <th>Service ID</th>
                        </tr>
                    </thead>
                    <tbody id="resultsBody">
                        <!-- Tracking results will be dynamically inserted here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#trackingForm').on('submit', function(event) {
            alert()
            event.preventDefault();  // Prevent the form from submitting normally
            
            var formData = $(this).serialize();  // Serialize the form data
            console.log(formData)
            $.ajax({
                url: '{{ route("getmoretrack") }}',  // Use the route to submit the form
                type: 'POST',
                data: formData,
                success: function(response) {
                    // Check if tracks data is returned
                    if (response.tracks) {
                        var tracks = response.tracks;
                        var resultsBody = $('#resultsBody');
                        resultsBody.empty();  // Clear any previous results

                        // Iterate over the tracks and create table rows dynamically
                        $.each(tracks, function(index, track) {
                            resultsBody.append(
                                '<tr>' +
                                    '<td>' + track.shipped_date + '</td>' +
                                    '<td>' + track.delivery_date + '</td>' +
                                    '<td>' + track.recipient_zip + '</td>' +
                                    '<td>' + track.service_id + '</td>' +
                                '</tr>'
                            );
                        });

                        // Show the results table
                        $('#trackingResults').show();
                    } else if (response.error) {
                        alert(response.error);  // Display error if any
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error: ", error);
                    alert("An error occurred while fetching tracking data.");
                }
            });
        });
    });
</script>
@endsection
