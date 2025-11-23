<div class="mb-4">
    <h3 class="text-lg font-semibold">Billed To:</h3>
    <p>{{ $transaction['fullName'] }} ({{ $transaction['role'] }})</p>
    <p>{{ $transaction['address']['street'] }}, {{ $transaction['address']['village'] }}, {{ $transaction['address']['district'] }}</p>
    <p>{{ $transaction['address']['regencie'] }}, {{ $transaction['address']['province'] }}</p>
    <p>Phone: {{ $transaction['phoneNumber'] }}</p>
    <p>Category: {{ $transaction['category'] }}</p>
</div>

<div class="mb-4">
    <h3 class="text-lg font-semibold">Transaction Details:</h3>
    <table class="w-full text-left border border-gray-200">
        <tbody>
            <tr class="border-b border-gray-200">
                <td class="py-2 font-medium">Amount</td>
                <td class="py-2">Rp {{ number_format($transaction['amount'], 0, ',', '.') }}</td>
            </tr>
            <tr class="border-b border-gray-200">
                <td class="py-2 font-medium">Month</td>
                <td class="py-2">{{ $transaction['month'] }}/{{ $transaction['year'] }}</td>
            </tr>
            <tr class="border-b border-gray-200">
                <td class="py-2 font-medium">Status</td>
                <td class="py-2 capitalize">
                    @if($transaction['status'] === 'paid')
                        <span class="px-2 py-1 rounded bg-green-200 text-green-800">Paid</span>
                    @else
                        <span class="px-2 py-1 rounded bg-yellow-200 text-yellow-800">{{ $transaction['status'] }}</span>
                    @endif
                </td>
            </tr>
            <tr class="border-b border-gray-200">
                <td class="py-2 font-medium">Paid At</td>
                <td class="py-2">{{ $transaction['paid_at'] }}</td>
            </tr>
            <tr>
                <td class="py-2 font-medium">Due Date</td>
                <td class="py-2">{{ $transaction['due_date'] }}</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="mt-6 text-right">
    <span class="text-lg font-bold">Total: Rp {{ number_format($transaction['amount'], 0, ',', '.') }}</span>
</div>
