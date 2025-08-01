<div class="shrink-0" data-kt-dropdown="true" data-kt-dropdown-offset="10px, 10px"
    data-kt-dropdown-offset-rtl="-20px, 10px" data-kt-dropdown-placement="bottom-end"
    data-kt-dropdown-placement-rtl="bottom-start" data-kt-dropdown-trigger="click" data-kt-dropdown-initialized="true">
    <div class="cursor-pointer" data-kt-dropdown-toggle="true">
        <div class="flex items-center flex-wrap gap-3">
            <div class="flex gap-2.5 hidden md:block items-center -ms-1">


                <span class="text-secondary-foreground text-base">{{ $greeting }}&nbsp;</span>

                <span class="text-secondary-foreground text-base">{{ $firstName }}&nbsp;</span>
            </div>
            <img alt="{{ $fullName }}" title="{{ $fullName }}"
                class="size-11 rounded-full border-2 border-green-500 shrink-0" src="{{ $avatar }}">
        </div>

    </div>
    <div class="kt-dropdown-menu w-[250px]" data-kt-dropdown-menu="true">
        <div class="flex items-center justify-between px-2.5 py-1.5 gap-1.5">
            <div class="flex items-center gap-2">
                <img src="{{ $avatar }}" class="size-11 shrink-0 rounded-full border-2 border-green-500"
                    alt="{{ $fullName }}" title="{{ $fullName }}">

                <div class="flex flex-col gap-1.5">
                    <span class="text-sm text-foreground font-semibold leading-none">
                        {{ $fullName }}
                    </span>
                    <a class="text-xs text-secondary-foreground hover:text-primary font-medium leading-none" href="">
                        {{ $email }}
                    </a>
                </div>
            </div>

        </div>
        <ul class="kt-dropdown-menu-sub">
            <li>
                <div class="kt-dropdown-menu-separator">
                </div>
            </li>
            <li>
                <a class="kt-dropdown-menu-link" href="{{ config('openid.server') }}">
                    <i class="fa-solid fa-clipboard-user"></i>
                    </i>
                    {{ __('openid::options.my_profile') }}
                </a>
            </li>
            <li>
                <a class="kt-dropdown-menu-link"
                    href="{{ config('openid.server') }}/edit?continue={{ config('app.url') }}">
                    <i class="fa-solid fa-user-pen"></i>
                    </i>
                    {{ __('openid::options.update_profile') }}
                </a>
            </li>

            <li>
                <a class="kt-dropdown-menu-link"
                    href="{{ config('openid.server') }}/edit/email?continue={{ config('app.url') }}">
                    <i class="fa-solid fa-envelope-circle-check"></i>
                    </i>
                    {{ __('openid::options.update_email') }}
                </a>
            </li>

            <li>
                <a class="kt-dropdown-menu-link"
                    href="{{ config('openid.server') }}/edit/address?continue={{ config('app.url') }}">
                    <i class="fa-solid fa-address-card"></i>
                    {{ __('openid::options.update_address') }}
                </a>
            </li>

            <li>
                <a class="kt-dropdown-menu-link"
                    href="{{ config('openid.server') }}/edit/password?continue={{ config('app.url') }}">
                    <i class="fa-solid fa-lock"></i>
                    </i>
                    {{ __('openid::options.change_password') }}
                </a>
            </li>


            <li>
                <div class="kt-dropdown-menu-separator">
                </div>
            </li>
        </ul>
        <div class="px-2.5 pt-1.5 mb-2.5 flex flex-col gap-3.5">
            <div class="flex items-center gap-2 justify-between">
                <span class="flex items-center gap-2">
                    <i class="ki-filled ki-moon text-base text-muted-foreground">
                    </i>
                    <span class="font-medium text-2sm">
                        {{ __('openid::options.dark_mode') }}
                    </span>
                </span>
                <input class="kt-switch" data-kt-theme-switch-state="dark" data-kt-theme-switch-toggle="true"
                    name="check" type="checkbox" value="1">
            </div>

            <form action="{{ $logout }}" method="POST">
                <button class="kt-btn kt-btn-outline justify-center w-full">
                    {{ __('openid::options.logout') }}
                </button>
            </form>


        </div>
    </div>
</div>