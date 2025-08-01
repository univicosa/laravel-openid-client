<div class="shrink-0" data-kt-dropdown="true" data-kt-dropdown-offset="10px, 10px"
    data-kt-dropdown-offset-rtl="-20px, 10px" data-kt-dropdown-placement="bottom-end"
    data-kt-dropdown-placement-rtl="bottom-start" data-kt-dropdown-trigger="click" data-kt-dropdown-initialized="true">
    <button
        class="kt-btn kt-btn-ghost kt-btn-icon size-11 rounded-full hover:bg-primary/10 hover:[&amp;_i]:text-primary kt-dropdown-open:bg-primary/10 kt-dropdown-open:[&amp;_i]:text-primary"
        data-kt-dropdown-toggle="true">
        <i class="ki-filled ki-element-11 text-2xl ">
        </i>
    </button>
    <div class="kt-dropdown-menu gap-0 sm:max-w-[80vw] max-h-[70vh] lg:max-w-[700px]" data-kt-dropdown-menu="true">
        <div class="kt-dropdown-header">{{ __('openid::menu.fast_access') }}</div>

        <div class="kt-dropdown-body kt-scrollable overflow-y-auto pe-2 sm:max-w-[80vw] max-h-[60vh] lg:max-w-[700px]">

            <ul class="">


                <div class="grid lg:grid-cols-2 lg:gap-5 kt-scrollable overflow-y-auto">
                    @foreach($systems as $key => $system)

                            <li>
                                <a class="kt-dropdown-menu-link" href="{{ $system['domain'] }}">
                                    <i class="{{ $system['icon'] }}">
                                    </i>
                                    {{ $system['name'] }}
                                </a>
                            </li>



                    @endforeach
                    
                </div>

            </ul>
        </div>

    </div>
</div>