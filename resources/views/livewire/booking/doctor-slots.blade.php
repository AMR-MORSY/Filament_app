<div class="max-w-2xl mx-auto px-4 sm:px-8 py-8 pb-28">
    <div class="flex items-center gap-4 mb-6">
        <div class="avatar placeholder shrink-0">
            <div class="bg-base-300 text-base-content/40 rounded-full w-14">
                @if ($doctor->photo)
                    <img src="{{ $doctor->photo }}" alt="{{ $doctor->name }}">
                @else
                    <span class="text-lg">{{ strtoupper(substr(preg_replace('/^dr\.?\s*/i', '', $doctor->name), 0, 1)) }}</span>
                @endif
            </div>
        </div>
        <div>
            <div class="font-semibold text-lg">{{ $doctor->name }}</div>
            <div class="text-xs text-base-content/50">{{ $slotDuration }} min &middot; {{ $doctor->clinic->name }}</div>
        </div>
    </div>

    <div class="card border border-base-300 bg-base-100">
        <div class="card-body p-4 sm:p-5 gap-5">
            <div class="flex items-center gap-2">
                <button type="button" wire:click="prevWeek" @if ($weekOffset === 0) disabled @endif
                        class="btn btn-square btn-sm btn-outline">&lsaquo;</button>
                <div class="flex-1 grid grid-cols-7 gap-2">
                    @foreach ($days as $day)
                        <button type="button" wire:click="selectDate('{{ $day->iso }}')" @if ($day->count === 0) disabled @endif
                                class="flex flex-col items-center rounded-lg border px-1 py-2 text-center
                                    {{ $date === $day->iso ? 'bg-primary text-primary-content border-primary' : 'border-base-300' }}
                                    {{ $day->count === 0 ? 'opacity-40' : '' }}">
                            <span class="text-[10px] uppercase {{ $date === $day->iso ? 'opacity-80' : 'text-base-content/50' }}">{{ $day->date->format('D') }}</span>
                            <span class="text-base font-medium">{{ $day->date->format('j') }}</span>
                            <span class="text-[9px] {{ $date === $day->iso ? 'opacity-80' : 'text-base-content/50' }}">
                                {{ $day->count === 0 ? 'closed' : $day->count . ' open' }}
                            </span>
                        </button>
                    @endforeach
                </div>
                <button type="button" wire:click="nextWeek" class="btn btn-square btn-sm btn-outline">&rsaquo;</button>
            </div>

            <div class="flex flex-col gap-4" wire:loading.class="opacity-60" wire:target="selectDate,prevWeek,nextWeek">
                @if ($morning->isEmpty() && $afternoon->isEmpty())
                    <div class="text-center text-sm text-base-content/50 py-6">No open slots this day.</div>
                @endif

                @if ($morning->isNotEmpty())
                    <div>
                        <div class="text-xs font-medium tracking-wide text-base-content/50 uppercase mb-2">Morning</div>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach ($morning as $s)
                                <button type="button" wire:click="selectTime('{{ $s }}')"
                                        class="btn btn-sm {{ $time === $s ? 'btn-primary' : 'btn-outline' }}">
                                    {{ \Carbon\Carbon::parse($s)->format('H:i') }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($afternoon->isNotEmpty())
                    <div>
                        <div class="text-xs font-medium tracking-wide text-base-content/50 uppercase mb-2">Afternoon</div>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach ($afternoon as $s)
                                <button type="button" wire:click="selectTime('{{ $s }}')"
                                        class="btn btn-sm {{ $time === $s ? 'btn-primary' : 'btn-outline' }}">
                                    {{ \Carbon\Carbon::parse($s)->format('H:i') }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="fixed bottom-0 left-0 right-0 bg-base-100 border-t border-base-300 px-4 sm:px-8 py-3">
        <div class="max-w-2xl mx-auto flex items-center justify-between gap-4">
            <div class="text-sm">
                @if ($time)
                    {{ \Carbon\Carbon::parse($date)->format('D j M') }} &middot;
                    {{ \Carbon\Carbon::parse($time)->format('H:i') }} &ndash; {{ \Carbon\Carbon::parse($time)->addMinutes($slotDuration)->format('H:i') }}
                @else
                    <span class="text-base-content/50">Pick a time to continue</span>
                @endif
            </div>
            <button type="button" wire:click="continueToBooking" @if (! $time) disabled @endif
                    class="btn btn-primary rounded-lg px-8">Continue</button>
        </div>
    </div>
</div>
