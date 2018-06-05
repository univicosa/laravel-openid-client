<li class="m-nav__item m-topbar__quick-actions m-topbar__quick-actions--img m-dropdown m-dropdown--large m-dropdown--header-bg-fill m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push m-dropdown--mobile-full-width m-dropdown--skin-light" m-dropdown-toggle="hover">

    <a href="javascript:;" class="m-nav__link m-dropdown__toggle">
        <span class="m-nav__link-badge m-badge m-badge--dot m-badge--info m--hide"></span>

        <span class="m-nav__link-icon">
            <span class="m-nav__link-icon-wrapper">
                <i class="flaticon-grid-menu"></i>
            </span>
        </span>
    </a>

    <div class="m-dropdown__wrapper">
        <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
        <div class="m-dropdown__inner">
            <div class="m-dropdown__header m--align-center" style="background: url({{ asset('assets/app/media/img/misc/quick_actions_bg.jpg') }}); background-size: cover;">
                <span class="m-dropdown__header-title">
                    {{ __('openid::menu.fast_access') }}
                </span>

                <span class="m-dropdown__header-subtitle">
                    {{ __('openid::menu.shortcuts') }}
                </span>
            </div>

            <div class="m-dropdown__body m-dropdown__body--paddingless">
                <div class="m-dropdown__content">
                    <div class="m-scrollable" data-scrollable="true" data-max-height="380" data-mobile-max-height="200">
                        <div class="m-nav-grid m-nav-grid--skin-light">
                            @foreach($systems as $key => $system)
                                @if(!($key % 2))
                                    <div class="m-nav-grid__row">
                                @endif
                                        <a href="{{ $system['domain'] }}" class="m-nav-grid__item">
                                            <i class="m-nav-grid__icon {{ $system['icon'] }}"></i>
                                            <span class="m-nav-grid__text">
                                                {{ $system['name'] }}
                                            </span>
                                        </a>
                                @if(($key % 2))
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</li>