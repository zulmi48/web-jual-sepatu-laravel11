<div id="bottom-nav" class="relative flex h-[100px] w-full shrink-0">
    <nav class="fixed bottom-5 w-full max-w-[640px] px-4 z-30">
        <div
            class="grid grid-flow-col auto-cols-auto items-center justify-between rounded-full bg-[#2A2A2A] p-2 px-[30px]">
            <a href="{{ route('front.index') }}" class="mx-auto w-full">
                <div
                    class="flex items-center rounded-full gap-[10px] p-[12px_16px] {{ request()->routeIs('front.index') ? 'bg-[#C5F277]' : '' }}">
                    @if (request()->routeIs('front.index'))
                        <img src="{{ asset('assets/images/icons/3dcube.svg') }}" class="w-6 h-6" alt="icon">
                    @else
                        <img src="{{ asset('assets/images/icons/3dcube-white.svg') }}" class="w-6 h-6" alt="icon">
                    @endif
                    <span
                        class="font-bold text-sm leading-[21px]">{{ request()->routeIs('front.index') ? 'Browse' : '' }}</span>
                </div>
            </a>
            <a href="{{ route('front.check_booking') }}" class="mx-auto w-full">
                <div
                    class="flex items-center rounded-full gap-[10px] p-[12px_16px] {{ request()->routeIs('front.check_booking') ? 'bg-[#C5F277]' : '' }}">
                    @if (request()->routeIs('front.check_booking'))
                        <img src="{{ asset('assets/images/icons/bag-2.svg') }}" class="w-6 h-6" alt="icon">
                    @else
                        <img src="{{ asset('assets/images/icons/bag-2-white.svg') }}" class="w-6 h-6" alt="icon">
                    @endif
                    <span
                        class="font-bold text-sm leading-[21px]">{{ request()->routeIs('front.check_booking') ? 'My Order' : '' }}</span>
                </div>
            </a>
            <a href="#" class="mx-auto w-full">
                <img src="{{ asset('assets/images/icons/star-white.svg') }}" class="w-6 h-6" alt="icon">
            </a>
            <a href="#" class="mx-auto w-full">
                <img src="{{ asset('assets/images/icons/24-support-white.svg') }}" class="w-6 h-6" alt="icon">
            </a>
        </div>
    </nav>
</div>
