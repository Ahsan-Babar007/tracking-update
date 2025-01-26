@extends('layouts.admin')

@section('content')

<div class="row">
    <div class="card bg-blueGray-100 w-full">
        <div class="card-header">
            <div class="card-row">
                <h6 class="card-title">
                    Tracking User Dashboard
                </h6>
            </div>
        </div>
       
        <div class="card-body">
            <!-- Form for API request -->
            {{ $currentSubscription->plan? ' <form id="trackingForm">
                @csrf
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

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>': }}
           

            <!-- Loading spinner (hidden initially) -->
            <div id="loadingSpinner" style="display: none;">
                <img src="path/to/loading.gif" alt="Loading...">
            </div>

            <!-- Show API Response -->
            <div id="resultBox" class="mt-4" style="display: none;">
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
                    <tbody id="trackingResults">
                        <!-- Results will be inserted here dynamically -->
                    </tbody>
                </table>
            </div>

            <!-- Show error message if no data is found -->
            <div id="errorBox" class="alert alert-danger mt-4" style="display: none;">
                No tracking data found.
            </div>

        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#trackingForm').submit(function (e) {
            e.preventDefault();

            // Show the loading spinner
            $('#loadingSpinner').show();

            // Hide result/error box
            $('#resultBox').hide();
            $('#errorBox').hide();

            // Prepare data
            var formData = {
                orderDate: $('#orderDate').val(),
                orderTime: $('#orderTime').val(),
                deliveryDate: $('#deliveryDate').val(),
                recipientZip: $('#recipientZip').val(),
            };

            // Send AJAX request to the server
            $.ajax({
                url: '{{ route("getmoretrack") }}',  // Make sure route is defined in web.php
                type: 'GET',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    // Hide the loading spinner
                    console.log('awais',response)
                    $('#loadingSpinner').hide();

                    // If tracks are found, display the table
                    if (response.tracks && response.tracks.length > 0) {
                        var tableRows = '';
                        response.tracks.forEach(function (track) {
                            tableRows += `<tr>
                                <td>${track.shipped_date}</td>
                                <td>${track.delivery_date}</td>
                                <td>${track.recipient_zip}</td>
                                <td>${track.service_id}</td>
                            </tr>`;
                        });
                        $('#trackingResults').html(tableRows);
                        $('#resultBox').show();
                    } else {
                        // If no tracks found, show error message
                        $('#errorBox').show();
                    }
                },
                error: function () {
                    // Hide the loading spinner and show an error if the request fails
                    $('#loadingSpinner').hide();
                    $('#errorBox').show();
                }
            });
        });
    });
</script>
@endsection
