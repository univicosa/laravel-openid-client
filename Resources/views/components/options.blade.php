<li class="m-nav__item m-topbar__user-profile m-topbar__user-profile--img  m-dropdown m-dropdown--medium m-dropdown--arrow m-dropdown--header-bg-fill m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light" m-dropdown-toggle="click">
    <a href="javascript:;"  class="m-nav__link m-dropdown__toggle">
        <span class="m-topbar__welcome">{{ $greeting }}&nbsp;</span>

        <span class="m-topbar__username">{{ $firstName }}&nbsp;</span>

        <span class="m-topbar__userpic">
            <img src="{{ $avatar }}" class="m--img-rounded m--marginless m--img-centered" alt="{{ $fullName }}" title="{{ $fullName }}">
        </span>
    </a>

    <div class="m-dropdown__wrapper">
        <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
        <div class="m-dropdown__inner">
            <div class="m-dropdown__header m--align-center" style="background: url({{ asset('assets/app/media/img/misc/user_profile_bg.jpg') }}); background-size: cover;">
                <div class="m-card-user m-card-user--skin-dark">
                    <div class="m-card-user__pic">
                        <img src="{{ $avatar }}" class="m--img-rounded m--marginless" alt="{{ $fullName }}" title="{{ $fullName }}">
                    </div>

                    <div class="m-card-user__details">
                        <span class="m-card-user__name m--font-weight-500">{{ $fullName }}&nbsp;</span>

                        <a href="#" class="m-card-user__email m--font-weight-300 m-link">{{ $email }}</a>
                    </div>
                </div>
            </div>

            <div class="m-dropdown__body">
                <div class="m-dropdown__content">
                    <ul class="m-nav m-nav--skin-light">
                        <li class="m-nav__section m--hide">
                            <span class="m-nav__section-text">
                                {{ __('openid::options.section') }}
                            </span>
                        </li>

                        <li class="m-nav__item">
                            <a href="{{ config('openid.server') }}" class="m-nav__link">
                                <i class="m-nav__link-icon flaticon-profile-1 m--font-warning"></i>

                                <span class="m-nav__link-title">
                                    <span class="m-nav__link-wrap">
                                        <span class="m-nav__link-text">
                                            {{ __('openid::options.my_profile') }}
                                        </span>
                                    </span>
                                </span>
                            </a>
                        </li>

                        <li class="m-nav__item">
                            <a href="#" class="m-nav__link">
                                <i class="m-nav__link-icon flaticon-share m--font-info"></i>
                                <span class="m-nav__link-text">
                                    {{ __('openid::options.activity') }}
                                </span>
                            </a>
                        </li>

                        <li class="m-nav__separator m-nav__separator--fit"></li>

                        <li class="m-nav__item">
                            <a href="{{ config('openid.server') }}/edit?continue={{ config('app.url') }}" class="m-nav__link">
                                <i class="m-nav__link-icon flaticon-user-settings m--font-warning"></i>
                                <span class="m-nav__link-text">{{ __('openid::options.update_profile') }}</span>
                            </a>
                        </li>

                        <li class="m-nav__item">
                            <a href="{{ config('openid.server') }}/edit/email?continue={{ config('app.url') }}" class="m-nav__link">
                                <i class="m-nav__link-icon flaticon-multimedia m--font-success"></i>
                                <span class="m-nav__link-text">
                                    {{ __('openid::options.update_email') }}
                                </span>
                            </a>
                        </li>

                        <li class="m-nav__item">
                            <a href="{{ config('openid.server') }}/edit/address?continue={{ config('app.url') }}" class="m-nav__link">
                                <i class="m-nav__link-icon flaticon-map-location m--font-brand"></i>
                                <span class="m-nav__link-text">
                                    {{ __('openid::options.update_address') }}
                                </span>
                            </a>
                        </li>

                        <li class="m-nav__item">
                            <a href="{{ config('openid.server') }}/edit/password?continue={{ config('app.url') }}" class="m-nav__link">
                                <i class="m-nav__link-icon flaticon-lock m--font-danger"></i>
                                <span class="m-nav__link-text">
                                    {{ __('openid::options.change_password') }}
                                </span>
                            </a>
                        </li>

                        <li class="m-nav__separator m-nav__separator--fit"></li>

                        <li class="m-nav__item">
                            <form action="{{ $logout }}" method="POST">
                                <button class="btn btn-outline-danger m-btn m-btn--pill m-btn--wide">
                                    {{ __('openid::options.logout') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</li>