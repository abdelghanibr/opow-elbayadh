@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction: rtl; text-align:right;">

    <h3 class="fw-bold mb-4">🗓️ الأوقات المتاحة للحجز</h3>

    <div id="calendar"></div>

</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.7/index.global.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.7/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        locale: 'ar',
        direction: 'rtl',
        timeZone: 'local',

        initialView: 'timeGridWeek',
        slotMinTime: '08:00:00',
        slotMaxTime: '22:00:00',
        allDaySlot: false,
        nowIndicator: true,
        selectable: true,

        events: @json($calendarData).map(e => ({
            title: e.label,
            start: e.date + " " + e.start,
            end: e.date + " " + e.end,
            color: e.color,
            borderColor: '#fff',
        })),

        select: function(info) {
            if(confirm("هل تريد الحجز في هذا الوقت؟")) {
                window.location.href = `/reservations/form?schedule=${info.startStr}`;
            }
        }
    });

    calendar.render();
});
</script>

@endsection
