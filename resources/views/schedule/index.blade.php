<h1 style="text-align:center; color:darkblue;">Schedule Management</h1>

<div style="text-align:center; margin-bottom:20px;">
   <td>

@foreach($schedules as $schedule)

<tr>
    <td>{{ $schedule->id }}</td>
    <td>{{ $schedule->pickup_date }}</td>
    <td>{{ $schedule->pickup_time }}</td>
    <td>{{ $schedule->status }}</td>

    <td>
        @if($schedule->status == 'pending')

            <form action="{{ route('schedule.confirm', $schedule->id) }}"
                  method="POST">

                @csrf

                <button class="btn btn-success">
                    Confirm Schedule
                </button>

            </form>

        @else

            <span class="badge bg-success">
                Confirmed
            </span>

        @endif
    </td>
</tr>

@endforeach
</td>
</div>

@if(session('success'))
    <p style="color:green; text-align:center;">
        {{ session('success') }}
    </p>
@endif

@foreach($schedules as $schedule)
    <div style="width:60%; margin:auto; background:#f5f5f5;
                padding:15px; margin-bottom:15px; border-radius:10px;">
        <p><strong>Pickup Date:</strong> {{ $schedule->pickup_date }}</p>
        <p><strong>Pickup Time:</strong> {{ $schedule->pickup_time }}</p>
        <p><strong>Notes:</strong> {{ $schedule->notes }}</p>
    </div>
@endforeach