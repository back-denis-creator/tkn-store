@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
<span style="font-family: 'Cinzel Decorative', Georgia, 'Times New Roman', serif; font-weight: 700; font-size: 30px; letter-spacing: 0.2em;">{{ $slot }}</span>
@endif
</a>
</td>
</tr>
