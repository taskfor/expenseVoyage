@extends('layouts.user_dashboard')

@section('content')
<link href="https://cdn.datatables.net/v/dt/dt-2.1.6/datatables.min.css" rel="stylesheet">

@if (session('warning'))
    <div class="alert alert-warning">
        {{ session('warning') }}
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="container mt-3">
    <a href="{{ route('itineraries.create') }}" class="btn btn-primary mb-3">Add New Itinerary</a>
    <a href="{{ route('itineraries.report') }}" class="btn btn-secondary mb-3">Download Report</a> <!-- Button for downloading the report -->

    <!-- Currency Selection -->
    <div class="mb-3">
        <select class="userCurrency" placeholder="Select User Currency">
            <option value="{{ $userCurrency }}">{{ $userCurrency }}</option>
        </select>

        <select class="destinationCurrency" placeholder="Select Destination Currency">
            <option value="">Select Destination Currency</option>
            @foreach ($destinations as $destination)
                <option value="{{ $destination->currency }}">{{ $destination->name }} ({{ $destination->currency }})</option>
            @endforeach
        </select>

        <button class="btn btn-primary convert">Convert</button>
    </div>

    <!-- Search Bar -->
    <input type="text" id="search" placeholder="Search Itineraries" class="form-control mb-3">

    <!-- Itineraries Table -->
    <div class="card">
        <div class="card-body">
            <h4>Itineraries</h4>
            <div class="table-responsive">
                <table id="itinerariesTable" class="table table-sm">
                    <thead>
                        <tr>
                            <th>Trip Name</th>
                            <th>Amount</th>
                            <th>Converted Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="itineraryTableBody">
                        @foreach ($itineraries as $itinerary)
                            <tr data-amount="{{ $itinerary->amount }}">
                                <td>{{ $itinerary->trip->name }}</td>
                                <td>{{ $itinerary->amount }}</td>
                                <td class="converted-amount"></td>
                                <td>
                                    <a href="{{ route('itineraries.edit', $itinerary->id) }}" class="btn btn-success btn-sm">Edit</a>
                                    <form action="{{ route('itineraries.destroy', $itinerary->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this itinerary?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- jQuery for search and conversion functionality -->
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

<script>
    $(document).ready(function() {
        // Search functionality
        $('#search').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#itineraryTableBody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        // Currency conversion on button click
        $(".convert").on("click", function() {
            let userCurrency = $(".userCurrency").val();
            let destinationCurrency = $(".destinationCurrency").val();

            if (!userCurrency || !destinationCurrency) {
                alert("Please select both user and destination currencies.");
                return;
            }

            // Fetch conversion rates from API
            const api = `https://api.exchangerate-api.com/v4/latest/${destinationCurrency}`;

            fetch(api)
                .then(response => response.json())
                .then(data => {
                    if (data.rates[userCurrency]) {
                        let conversionRate = data.rates[userCurrency];

                        $('#itineraryTableBody tr').each(function() {
                            let amount = $(this).data('amount'); // Get amount from data attribute
                            let convertedAmount = (amount * conversionRate).toFixed(2); // Multiply by conversion rate
                            $(this).find('.converted-amount').text(`${convertedAmount} ${userCurrency}`); // Display in new column
                        });
                    } else {
                        alert("User currency not found in exchange rates.");
                    }
                })
                .catch(error => {
                    console.error("Error fetching data:", error);
                    alert("There was an error fetching the exchange rates. Please try again later.");
                });
        });
    });
</script>
@endsection
