@extends('layouts.admin')

@section('content')

<div class="row">
    <div class="card bg-blueGray-100 w-full">
        <div class="card-header">
            <div class="card-row">
                <h6 class="card-title">
                    Tracking Admin Dashboard
                </h6>
            </div>
        </div>

            Admin Dashboard COunt will come here
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
