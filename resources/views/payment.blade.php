@include('Navigationbar')
<form action="{{ route('paymongo.create') }}" method="POST">
    @csrf
    <label for="amount">Enter Amount (PHP):</label>
    <input type="number" name="amount" id="amount" min="1" required>
    
    <label for="description">Description:</label>
    <input type="text" name="description" id="description" required>

    <button type="submit">Pay Now</button>
</form>
