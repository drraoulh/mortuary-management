<h2 style="text-align:center; color:darkblue;">Create Schedule</h2>

<form action="{{ route('schedule.store') }}" method="POST"
      style="width:50%; margin:auto; padding:20px;
             border:1px solid #ccc; border-radius:10px; background:#f8f9fa;">

    @csrf

    <label>Deceased:</label><br>
    <select name="deceased_id" required style="width:100%; padding:10px;">
        @foreach($deceaseds as $deceased)
            <option value="{{ $deceased->id }}">
                {{ $deceased->full_name }}
            </option>
        @endforeach
    </select>

    <br><br>

    <label>Pickup Date:</label><br>
    <input type="date" name="pickup_date" required style="width:100%; padding:10px;">
    <div class="mb-3">
    <label>Burial Date</label>
    <input type="date"
           name="burial_date"
           class="form-control"
           required>
</div>

    <br><br>

    <label>Pickup Time:</label><br>
    <input type="time" name="pickup_time" required style="width:100%; padding:10px;">

    <br><br>

    <label>Notes:</label><br>
    <textarea name="notes" style="width:100%; padding:10px;"></textarea>

    <br><br>

    <div style="text-align:center;">
        <button type="submit"
            style="background:green; color:white; padding:12px 25px;
                   border:none; border-radius:8px;">
            Submit Schedule
        </button>
    </div>
</form>