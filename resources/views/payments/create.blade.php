<h2 style="text-align:center; color:darkblue;">Create New Payment</h2>

<form action="{{ route('payments.store') }}" method="POST"
      style="width:50%; margin:auto; padding:20px; border:1px solid #ccc; border-radius:10px; background:#f8f9fa;">

    @csrf
    <div class="mb-3">
    <label for="payment_method" class="form-label">
        Payment Method
    </label>

   <div class="mb-3">
    <label class="form-label">
        Payment Method
    </label>

    <select
        name="payment_method"
        id="payment_method"
        class="form-select"
        required
    >
        <option value="">Select payment method</option>
        <option value="mobile_money">Mobile Money</option>
    </select>
</div>
<div
    class="mb-3"
    id="mobile-money-section"
    style="display: none;"
>

    <label class="form-label">
        Mobile Money Network
    </label>

    <select
        name="mobile_operator"
        id="mobile_operator"
        class="form-select"
    >

        <option value="">
            Select network
        </option>

        <option value="MTN">
            MTN Mobile Money
        </option>

        <option value="ORANGE">
            Orange Money
        </option>

    </select>

</div>
<div
    class="mb-3"
    id="phone-section"
    style="display: none;"
>

    <label class="form-label">
        Mobile Money Phone Number
    </label>

    <input
        type="text"
        name="phone_number"
        id="phone_number"
        class="form-control"
        placeholder="Example: 677123456"
    >

    <small class="text-muted">
        Enter the MTN or Orange number that will authorize the payment.
    </small>

</div>

</div>

    <label>Deceased:</label><br>
    <select name="deceased_id" required style="width:100%; padding:10px;">
        @foreach($deceaseds as $deceased)
            <option value="{{ $deceased->id }}">
                {{ $deceased->full_name }}
            </option>
        @endforeach
    </select>

    <br><br>

    <label>Amount:</label><br>
    <input type="number" name="amount" required style="width:100%; padding:10px;">

    <br><br>

    <label>Payment Date:</label><br>
    <input type="date" name="payment_date" required style="width:100%; padding:10px;">

    <br><br>

    <label>Balance:</label><br>
    <input type="number" name="balance" required style="width:100%; padding:10px;">

    <br><br>

    <label>Status:</label><br>
    <select name="status" style="width:100%; padding:10px;">
        <option value="paid">Paid</option>
        <option value="pending">Pending</option>
    </select>

    <br><br>

    <div style="text-align:center;">
        <button type="submit"
            style="background:green; color:white; padding:12px 30px; border:none; border-radius:8px; font-size:16px;">
            Submit Payment
        </button>
    </div>
</form>
<script>

document.addEventListener('DOMContentLoaded', function () {

    const paymentMethod =
        document.getElementById('payment_method');

    const mobileMoneySection =
        document.getElementById('mobile-money-section');

    const phoneSection =
        document.getElementById('phone-section');

    const mobileOperator =
        document.getElementById('mobile_operator');

    const phoneNumber =
        document.getElementById('phone_number');


    paymentMethod.addEventListener('change', function () {

        if (this.value === 'mobile_money') {

            mobileMoneySection.style.display = 'block';
            phoneSection.style.display = 'block';

            mobileOperator.required = true;
            phoneNumber.required = true;

        } else {

            mobileMoneySection.style.display = 'none';
            phoneSection.style.display = 'none';

            mobileOperator.required = false;
            phoneNumber.required = false;

        }

    });

});

</script>