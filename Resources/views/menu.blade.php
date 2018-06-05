@if(\Auth::check())
    @include('openid::components.systems')
    @include('openid::components.options')

    @else
    <a href="{{ $login }}" class="btn btn-focus">{{ __('openid::options.login') }}</a>
@endif