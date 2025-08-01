@if(\Auth::check())
    @include('openid::components.systems_list')
    @include('openid::components.user')

    @else
    <a href="{{ $login }}" class="btn btn-focus">{{ __('openid::options.login') }}</a>
@endif