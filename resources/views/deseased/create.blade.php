<x-app-layout>
<form method="POST" action="{{ route('deceased.store') }}">
    @csrf

    <input type="text" name="full_name" placeholder="Full Name">
    <input type="text" name="gender" placeholder="Gender">
    <input type="date" name="date_of_death">
    <input type="text" name="cause_of_death">
    <input type="date" name="admission_date">

    <button type="submit">Save</button>
</form>
</x-app-layout>