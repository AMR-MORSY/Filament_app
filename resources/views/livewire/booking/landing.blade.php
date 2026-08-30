<div>
    @php
        $heroImages = [
            asset('images/hero/hero-1-consultation.webp'),
            asset('images/hero/hero-2-waiting-room.webp'),
            asset('images/hero/hero-3-booking-phone.webp'),
            asset('images/hero/hero-4-nurse-tablet.webp'),
            asset('images/hero/hero-5-doctor-child.webp'),
        ];
    @endphp

    <section class="relative h-[80vh] overflow-hidden border-b border-base-300" x-data="{ current: 0, images: @js($heroImages) }"
        x-init="setInterval(() => current = (current + 1) % images.length, 5000)">

        <template x-for="(image, index) in images" :key="index">
            <img :src="image" alt=""
                class="absolute inset-0 h-full w-full object-cover transition-opacity duration-1000 ease-in-out"
                :class="current === index ? 'opacity-100' : 'opacity-0'">
        </template>
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-black/20"></div>

        <div class="relative z-10 flex h-full items-center justify-center px-4 sm:px-8">
            <div class="max-w-4xl text-center">
                <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight">
                    <div class="inline-flex items-center sm:gap-2 text-white">

                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48">
                            <path d="M0 0h48v48H0z" fill="none" />
                            <g fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 23a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2zm4 0v2h-2v-2zm6-2a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2zm2 2h-2v2h2zm4 0a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2zm2 0h2v2h-2zm-16 6a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2zm0 2v2h2v-2zm6 0a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2zm2 0h2v2h-2z"
                                    clip-rule="evenodd" />
                                <path
                                    d="M35 31.5a1 1 0 0 1 1 1v2.086l.707.707a1 1 0 0 1-1.414 1.414L34 35.414V32.5a1 1 0 0 1 1-1" />
                                <path fill-rule="evenodd"
                                    d="M12 7a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0v-1H9a1 1 0 0 0-1 1v4h26v-4a1 1 0 0 0-1-1h-3V9h3a3 3 0 0 1 3 3v16.07A7.001 7.001 0 0 1 35 42a6.99 6.99 0 0 1-5.745-3H9a3 3 0 0 1-3-3V12a3 3 0 0 1 3-3h3zm16 28a7 7 0 0 1 6-6.93V18H8v18a1 1 0 0 0 1 1h19.29a7 7 0 0 1-.29-2m7 5a5 5 0 1 0 0-10a5 5 0 0 0 0 10"
                                    clip-rule="evenodd" />
                                <path d="M27 13a1 1 0 0 1-1-1v-1H16V9h10V7a1 1 0 1 1 2 0v5a1 1 0 0 1-1 1" />
                            </g>
                        </svg>


                        <span class="text-white"> Book a clinic visit in three taps</span>
                    </div>


                </h1>
                <p class="mt-3 text-white/80">Search by specialty, pick a time that works, and you're booked &mdash;
                    no account required.</p>

                <form wire:submit="search"
                    class="mt-8 flex flex-col sm:flex-row gap-0 sm:gap-0 border border-base-300 rounded-2xl overflow-hidden bg-base-100 shadow-lg text-left">
                    <label class="flex-1 px-5 py-3 sm:border-r border-base-300">
                        <span
                            class="block text-[11px] font-medium tracking-wide text-base-content/50 uppercase">Specialty</span>
                        <div class="relative mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15"
                                class="pointer-events-none absolute left-0 top-1/2 z-10 -translate-y-1/2 text-base-content/50">
                                <path d="M0 0h15v15H0z" fill="none" />
                                <path fill="currentColor"
                                    d="M5.5 7A2.5 2.5 0 0 1 3 4.5v-2a.5.5 0 0 1 .5-.5H4a.5.5 0 0 0 0-1h-.5A1.5 1.5 0 0 0 2 2.5v2a3.49 3.49 0 0 0 1.51 2.87A4.4 4.4 0 0 1 5 10.5a3.5 3.5 0 1 0 7 0v-.57a2 2 0 1 0-1 0v.57a2.5 2.5 0 0 1-5 0a4.4 4.4 0 0 1 1.5-3.13A3.49 3.49 0 0 0 9 4.5v-2A1.5 1.5 0 0 0 7.5 1H7a.5.5 0 0 0 0 1h.5a.5.5 0 0 1 .5.5v2A2.5 2.5 0 0 1 5.5 7m6 2a1 1 0 1 1 0-2a1 1 0 0 1 0 2" />
                            </svg>
                            <select wire:model="clinic_id"
                                class="select select-ghost relative z-0 w-full pl-5 pr-0 focus:outline-none open:outline-none">
                                <option value="" selected hidden>Choose specialty</option>
                                <option value="0">Any specialty</option>
                                @foreach ($clinics as $clinic)
                                    <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </label>
                    <label class="flex-1 px-5 py-3 sm:border-r border-base-300 text-primary">


                        <span
                            class="block text-[11px] font-medium tracking-wide text-base-content/50 uppercase">Date</span>


                        <select wire:model="when" class="select select-ghost w-full px-0 mt-1 focus:outline-none">
                            <option value="soonest">Soonest available</option>
                            <option value="today">Today</option>
                            <option value="3days">Next 3 days</option>
                            <option value="week">This week</option>
                        </select>
                    </label>
                    <button type="submit"
                        class="btn btn-primary h-auto rounded-none sm:rounded-r-2xl px-8">Search</button>
                </form>
            </div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-4 sm:px-8 py-12">
        <h2 class="text-lg font-semibold mb-4">Browse by clinic</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach ($clinics as $clinic)
                <a href="{{ route('search', ['clinic' => $clinic->id]) }}" wire:navigate
                    class="card border border-base-300 bg-base-100 hover:border-primary transition-colors">
                    <div class="card-body p-4 gap-2">
                        <div>
                            @switch($clinic->name)
                                @case('Cardiology')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48">
                                        <path d="M0 0h48v48H0z" fill="none" />
                                        <path fill="currentColor" fill-rule="evenodd"
                                            d="M5 9a4 4 0 0 1 4-4h30a4 4 0 0 1 4 4v30a4 4 0 0 1-4 4H9a4 4 0 0 1-4-4zm4-2a2 2 0 0 0-2 2v30a2 2 0 0 0 2 2h30a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zm13.507 4.533a1 1 0 0 1 1-1H27.5a1 1 0 0 1 1 1v1.346l.06.054c.185.17.406.395.642.682c1.685-.297 3.72-.129 6.145.768l.91.337l-.31.92c-.222.663-.468 1.19-.691 1.668l-.006.013c-.227.486-.43.924-.616 1.475l-.36 1.072l-.785-.377c3.256 3.07 2.426 7.696.392 11.214c-1.06 1.833-2.506 3.5-4.09 4.718c-1.568 1.206-3.368 2.044-5.124 2.044c-1.761 0-3.518-.843-5.031-2.044c-1.528-1.213-2.901-2.862-3.911-4.65c-1.007-1.784-1.686-3.77-1.75-5.66c-.037-1.109.139-2.2.594-3.19l-.03-.037a4 4 0 0 0-.454-.461c-.41-.354-.993-.717-1.743-.838a1 1 0 0 1-.825-1.173c.335-1.767.637-2.818.87-3.446a5 5 0 0 1 .312-.695a2 2 0 0 1 .193-.282l.032-.035l.016-.016l.008-.008l.003-.005l.002-.002l-.03.031l1.017-.278h.002l.004.001l.014.005l.049.014l.172.056a15.6 15.6 0 0 1 2.43 1.036a10 10 0 0 1 1.586 1.04l-1.036-1.596a1 1 0 0 1 .272-1.368l3.209-2.21a1 1 0 0 1 1.37.228l.495.667zm-4.911 7.538a3 3 0 0 0-.38-.439c-.41-.4-.963-.765-1.55-1.079c-.53-.284-1.06-.51-1.48-.673c-.138.41-.317 1.043-.521 1.998a5.7 5.7 0 0 1 2.004 1.288a19 19 0 0 1 .486-.306c.417-.34.877-.582 1.357-.748zm-.268 2.417c.285-.17.604-.348.945-.521a4.2 4.2 0 0 1 1.21-.163c.915-.004 1.936.21 2.923.52a20 20 0 0 1 2.692 1.087l-.607 2.179l-2.186.988l-2.579-.607l-.458 1.946l1.784.42l-.567 1.434l1.86.735l.843-2.132l.603-.273l-.045.163l.915 2.114l.176 2.582l1.995-.136l-.123-1.802l1.91-.222l-.231-1.986l-2.119.245l-.407-.94l1.163-4.173l.075-.124l.144-.17l.004-.004l.015-.018l.066-.074q.088-.102.255-.282c.22-.239.532-.563.896-.904c.625-.586 1.32-1.144 1.934-1.462c.246.133.454.234.625.318q.114.055.205.102c.262.132.458.247.741.498c2.354 2.085 2.088 5.552.165 8.878c-.937 1.62-2.213 3.084-3.579 4.134c-1.38 1.062-2.757 1.629-3.904 1.629c-1.14 0-2.467-.563-3.788-1.611c-1.307-1.037-2.518-2.481-3.413-4.067c-.897-1.59-1.442-3.26-1.492-4.744c-.049-1.453.373-2.675 1.354-3.557m8.837-.855l.084-.108a12 12 0 0 1 1.704-1.788c1.228-1.032 3.063-2.044 5.152-1.53q.166-.38.326-.718l.006-.013c.086-.184.168-.36.248-.54c-3.408-1.003-5.539-.198-6.872.905c-1.133.937-1.786 2.17-2.105 3.011a55 55 0 0 1 1.457.781m.764-6.45a1 1 0 0 1-.429-.821v-.829h-1.993v1.21a1 1 0 0 1-.178.57l-.657.946a1 1 0 0 1-1.624.027l-1.066-1.435l-1.613 1.11l.97 1.495a1 1 0 0 1 .03 1.04l-.019.082c-.035.179-.08.524-.1 1.142c.121.017.252.02.507.02c.462 0 1.068.002 2.08.169c.466-1.159 1.226-2.283 2.084-3.176c.578-.603 1.268-1.168 2.008-1.55m-14.005.773l1.016-.278a1 1 0 0 0-.984.246z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @break

                                @case('Dermatology')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24">
                                        <path d="M0 0h24v24H0z" fill="none" />
                                        <path fill="currentColor"
                                            d="M3 20v-7.38q0-.676.475-1.148Q3.949 11 4.615 11H9.5v1q0 1.042.729 1.77q.728.73 1.769.73t1.771-.73T14.5 12v-1h4.885q.666 0 1.14.475t.475 1.14V20zm1-1h16v-6.384q0-.27-.173-.443T19.385 12H15.5q0 1.46-1.024 2.48T12 15.5t-2.476-1.024T8.5 12H4.616q-.27 0-.443.173T4 12.616zm8-6.5q-.214 0-.357-.144T11.5 12q0-2.601.596-5.13t2.562-4.235q.17-.121.369-.113q.198.009.339.17q.14.162.121.37t-.189.329q-1.827 1.551-2.313 3.878Q12.5 9.595 12.5 12q0 .213-.144.356t-.357.144m-5.77 2.734q.194-.19.194-.48t-.19-.484t-.48-.193t-.483.19t-.193.48t.19.483t.48.193t.483-.19m1 2.5q.193-.19.193-.48t-.19-.483t-.48-.193t-.483.19t-.193.48t.19.483t.48.193t.483-.19m11.5-2.5q.193-.19.193-.48t-.19-.483t-.48-.193t-.483.19t-.193.48t.19.483t.48.193t.483-.19M4 19h16z" />
                                    </svg>
                                @break

                                @case('General Surgery')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48">
                                        <path d="M0 0h48v48H0z" fill="none" />
                                        <g fill="currentColor" fill-rule="evenodd" clip-rule="evenodd">
                                            <path
                                                d="M40 8H8v32h32zM8 6a2 2 0 0 0-2 2v32a2 2 0 0 0 2 2h32a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2z" />
                                            <path
                                                d="M10 34h10v2H10zm18 0h4v2h-4zm-6 0h4v2h-4zm12 0h4v2h-4zm-17.172-6h4.724a4 4 0 0 0 2.798-1.142L28 23.284v-.456A4 4 0 0 1 29.172 20l1.689-1.69l-2.606-1.736zM12 30l16-16l6 4l-3.414 3.414A2 2 0 0 0 30 22.828v.456a2 2 0 0 1-.6 1.43l-3.65 3.573A6 6 0 0 1 21.551 30z" />
                                            <path
                                                d="M26.707 21.293a1 1 0 0 1 0 1.414l-3 3a1 1 0 0 1-1.414-1.414l3-3a1 1 0 0 1 1.414 0m10-13.185l-7.6 7.6l-1.414-1.415l7.6-7.6zm1.5 4.285a1 1 0 0 1 0 1.414l-5.5 5.5l-1.414-1.415l5.5-5.5a1 1 0 0 1 1.414 0" />
                                        </g>
                                    </svg>
                                @break

                                @case('Neurology')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 64 64">
                                        <path d="M0 0h64v64H0z" fill="none" />
                                        <path fill="currentColor"
                                            d="M34.427.782c-10.696 0-20.56 8.033-22.61 16.88c-.455 1.966-.969 7.104-.969 7.104L5.936 37.167a1.8 1.8 0 0 0-.146.739c0 1.047.846 1.898 1.897 1.898h3.161v6.588c0 5.14 4.157 9.302 9.29 9.302h3.694v7.216h13.727v-3.22h5.591v3.22h5.838V42.901c5.486-4.314 9.013-11.019 9.013-18.537C58.001 11.338 47.452.783 34.428.783zM16.648 29.464A2.65 2.65 0 0 1 14 26.817a2.646 2.646 0 0 1 2.648-2.64a2.644 2.644 0 0 1 0 5.287m20.91 17.32h5.591v3.024h-5.591zm0-4.303h5.591v3.022h-5.591zm0 8.602h5.591v3.028h-5.591zm0 4.303h5.591v3.028h-5.591zm0-17.206h5.625c-.011.222-.035.44-.035.667v2.357h-5.591zm15.477-17.428c0 1.61-.631 3.071-1.656 4.159a6.08 6.08 0 0 1-3.659 5.109v.021c-2.477 1.442-3.958 3.904-4.408 6.857h-2.29v-6.851c.015-2.136.687-3.503 2.095-4.347a7.3 7.3 0 0 0 1.382-1.011a4.76 4.76 0 0 1 2.557 2.159a.66.66 0 0 0 .558.322a.6.6 0 0 0 .312-.084a.635.635 0 0 0 .25-.871h-.005a6.07 6.07 0 0 0-2.798-2.552c.584-.897.908-1.965.893-3.249c0-.007.008-.013.008-.022a6 6 0 0 0-.24-1.674a6.1 6.1 0 0 0 3.382-1.889a.636.636 0 0 0-.043-.906a.637.637 0 0 0-.899.041a4.84 4.84 0 0 1-2.934 1.538a6.1 6.1 0 0 0-4.693-3.174a4.8 4.8 0 0 1 1.371-2.814a.645.645 0 0 0 0-.902a.644.644 0 0 0-.91 0a6.1 6.1 0 0 0-1.745 3.636a4.83 4.83 0 0 1-4.226-4.789a.644.644 0 0 0-.633-.642a.643.643 0 0 0-.646.642c0 .417.04.813.12 1.204a4.81 4.81 0 0 1-4.794.887l-.015-.004a4.8 4.8 0 0 1-1.758-1.121a.643.643 0 0 0-.906 0a.635.635 0 0 0 0 .904a6.1 6.1 0 0 0 1.756 1.228c-.127 1.595-1.054 2.966-2.35 3.736a6.12 6.12 0 0 0-3.26-2.479a.65.65 0 0 0-.814.416a.645.645 0 0 0 .419.801a4.84 4.84 0 0 1 2.449 1.766a4.8 4.8 0 0 1-1.245.165h-.077a6.1 6.1 0 0 0-4.253 1.726a.625.625 0 0 0-.011.904a.63.63 0 0 0 .896.013a4.8 4.8 0 0 1 3.369-1.363h.078a6.104 6.104 0 0 0 6.044-5.269a6.1 6.1 0 0 0 4.896-1.011a6.05 6.05 0 0 0 1.748 2.262a6.04 6.04 0 0 0-1.324 3.184a6.06 6.06 0 0 0-3.853.637a.64.64 0 0 0-.266.867a.65.65 0 0 0 .57.337q.156.002.292-.077a4.86 4.86 0 0 1 2.262-.567c.339 0 .674.037.994.105c.14 1.296.7 2.565 1.663 3.573a.64.64 0 0 0 .459.197a.66.66 0 0 0 .447-.176a.63.63 0 0 0 .02-.904h-.005a4.86 4.86 0 0 1-1.339-3.219c0-.017.005-.03 0-.049v-.07c0-1.13.39-2.246 1.163-3.143a6.1 6.1 0 0 0 2.732.644h.01a4.83 4.83 0 0 1 4.818 4.812a6 6 0 0 0-1.556-.202c-.762 0-1.534.139-2.279.447a.637.637 0 1 0 .477 1.184a4.9 4.9 0 0 1 1.803-.354c.483 0 .961.073 1.41.213a3.7 3.7 0 0 1-.939 1.803a.5.5 0 0 0-.099.112a7.2 7.2 0 0 1-1.343 1.017c-1.852 1.082-2.74 3.044-2.722 5.448v6.851h-2.184V30.38c0-2.393-1.5-4.662-3.389-5.469c-.901-.35-1.841-.936-2.526-1.702a6.05 6.05 0 0 1-3.408 1.054a6.05 6.05 0 0 1-3.406-1.043a6.14 6.14 0 0 1-3.378 1.006a6.197 6.197 0 0 1-6.198-6.2a6.19 6.19 0 0 1 3.762-5.699a6.07 6.07 0 0 1 5.928-4.773c.155 0 .297.013.44.022a6.08 6.08 0 0 1 7.885-1.938a6.054 6.054 0 0 1 7.475.615a6 6 0 0 1 1.416-.174c2.457 0 4.582 1.479 5.531 3.597a6.07 6.07 0 0 1 4.102 7.392a6.07 6.07 0 0 1 1.244 3.681z" />
                                    </svg>
                                @break

                                @case('Orthopedics')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24">
                                        <path d="M0 0h24v24H0z" fill="none" />
                                        <path fill="currentColor"
                                            d="M12 21.2q-1.016 0-2.137-.215q-1.122-.216-2.013-.533q-.275-.106-.466-.362q-.192-.256-.192-.563v-.635H6.5q-.343 0-.575-.232q-.233-.232-.233-.575v-1.26q0-.358.233-.588t.575-.23h.692v-2.615H6.5q-.343 0-.575-.232q-.233-.232-.233-.575v-1.26q0-.358.233-.587t.575-.23h.692V8.085H6.5q-.343 0-.575-.232q-.233-.233-.233-.576v-1.26q0-.357.233-.587T6.5 5.2h.692V4.104q0-.348.248-.522t.575-.051q.718.23 1.76.45T12 4.2t2.225-.229q1.042-.229 1.76-.46q.326-.123.574.06q.249.181.249.513V5.2h.692q.343 0 .576.232t.232.576v1.26q0 .357-.232.587t-.576.23h-.692v2.423h.692q.343 0 .576.232t.232.576v1.26q0 .357-.232.587t-.576.23h-.692v2.615h.692q.343 0 .576.232t.232.576v1.26q0 .357-.232.587t-.576.23h-.692v.634q0 .307-.192.563q-.191.256-.466.362q-.89.317-2.012.533q-1.122.215-2.138.215m0-11.85q.95 0 1.926-.175t1.882-.5V4.6q-.931.275-1.892.438T11.99 5.2q-.94 0-1.9-.162q-.961-.163-1.898-.438v4.075q.9.325 1.88.5q.978.175 1.928.175m1.92 5.516q.976-.185 1.888-.516v-4.6q-.966.262-1.915.43T12 10.35q-.956 0-1.914-.16q-.957-.16-1.894-.44v4.6q.912.33 1.888.516q.976.184 1.92.184t1.92-.184M12 20.2q.95 0 1.926-.175t1.882-.5V15.45q-.931.275-1.892.438t-1.926.162q-.94 0-1.9-.162q-.961-.163-1.898-.438v4.075q.9.325 1.88.5q.978.175 1.928.175" />
                                    </svg>
                                @break

                                @case('Psychiatry')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24">
                                        <path d="M0 0h24v24H0z" fill="none" />
                                        <path fill="currentColor"
                                            d="M11.5 20.5v-7.898q-1.408 0-2.69-.526T6.536 10.56T5.034 8.275T4.52 5.577v-1h1q1.377 0 2.666.537q1.29.538 2.273 1.525q.794.794 1.287 1.805t.653 2.139q.221-.425.496-.8q.275-.373.623-.721q.988-.987 2.283-1.525T18.5 7h1v1q0 1.406-.538 2.701t-1.526 2.284t-2.258 1.502T12.5 15v5.5zm.02-8.923q0-1.2-.463-2.287T9.744 7.352T7.807 6.04t-2.288-.463q0 1.2.45 2.3t1.3 1.95t1.95 1.3t2.3.45M12.5 14q1.2 0 2.288-.45t1.937-1.3t1.313-1.95T18.5 8q-1.2 0-2.3.463t-1.95 1.312t-1.3 1.938T12.5 14m-.98-2.423" />
                                    </svg>
                                @break

                                @default
                            @endswitch
                        </div>
                        <div class="font-medium">{{ $clinic->name }}</div>
                        <div class="text-xs text-base-content/50">
                            {{ $clinic->doctors_count }} {{ Str::plural('doctor', $clinic->doctors_count) }}
                            @if ($clinic->floor)
                                &middot; {{ $clinic->floor }}
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-4 sm:px-8 pb-16">
        <div class="grid sm:grid-cols-3 gap-6">
            <div class="flex gap-3">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 64 64">
                        <path d="M0 0h64v64H0z" fill="none" />
                        <path fill="#405866"
                            d="M50.23 3.872L31.995 22.107q-9.12-9.115-18.235-18.235C.586 1.601 1.595.592 3.866 13.768C9.943 19.842 16.025 25.923 22.099 32L3.866 50.233C1.595 63.407.586 62.398 13.76 60.128q9.116-9.12 18.235-18.236c6.08 6.079 12.154 12.157 18.235 18.236c13.173 2.27 12.164 3.279 9.895-9.892Q51.005 41.116 41.886 32q9.12-9.12 18.239-18.235C62.394.592 63.403 1.601 50.23 3.872" />
                    </svg>

                </div>
                <div>
                    <div class="font-medium">Free cancellation</div>
                    <div class="text-sm text-base-content/50">Change your mind? Cancel any time before your visit.
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 640 640">
                        <path d="M0 0h640v640H0z" fill="none" />
                        <path fill="currentColor"
                            d="M64 160c0-35.3 28.7-64 64-64h288c35.3 0 64 28.7 64 64v32h50.7c17 0 33.3 6.7 45.3 18.7l45.3 45.3c12 12 18.7 28.3 18.7 45.3V448c0 35.3-28.7 64-64 64h-3.3c-10.4 36.9-44.4 64-84.7 64s-74.2-27.1-84.7-64H300.7c-10.4 36.9-44.4 64-84.7 64s-74.2-27.1-84.7-64H128c-35.3 0-64-28.7-64-64v-48H24c-13.3 0-24-10.7-24-24s10.7-24 24-24h112c13.3 0 24-10.7 24-24s-10.7-24-24-24H24c-13.3 0-24-10.7-24-24s10.7-24 24-24h176c13.3 0 24-10.7 24-24s-10.7-24-24-24H24c-13.3 0-24-10.7-24-24s10.7-24 24-24zm512 192v-50.7L530.7 256H480v96zM256 488c0-22.1-17.9-40-40-40s-40 17.9-40 40s17.9 40 40 40s40-17.9 40-40m232 40c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40s17.9 40 40 40" />
                    </svg>

                </div>
                <div>
                    <div class="font-medium">Instant confirmation</div>
                    <div class="text-sm text-base-content/50">See your slot booked immediately, no waiting on a call
                        back.</div>
                </div>
            </div>
            <div class="flex gap-3">
                <div >
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0z" fill="none" />
                        <path fill="currentColor"
                            d="M6.5 10h-2v7h2zm6 0h-2v7h2zm8.5 9H2v2h19zm-2.5-9h-2v7h2zm-7-6.74L16.71 6H6.29zm0-2.26L2 6v2h19V6z" />
                    </svg>

                </div>
                <div>
                    <div class="font-medium">No account needed</div>
                    <div class="text-sm text-base-content/50">Book as a guest, or create an account to manage visits.
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
