@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
{{--
    A PNG, not the source SVG: Gmail and most Outlook builds strip <svg> and
    refuse SVG in <img src>, so an SVG logo simply vanishes from the email.
    Rendered at 2x (240px) and displayed at 48px so it stays sharp on retina.

    The alt text carries the brand for anyone whose client blocks images.
--}}
<img src="{{ asset('images/brand/mark-240.png') }}"
     class="logo"
     width="48"
     height="48"
     alt="{{ config('app.name') }}"
     style="width: 48px; height: 48px; max-width: 48px; border: 0; vertical-align: middle;">
<span style="vertical-align: middle; margin-left: 10px; font-size: 19px; font-weight: 700; color: #2b7bb9; text-decoration: none;">{{ config('app.name') }}</span>
</a>
</td>
</tr>
