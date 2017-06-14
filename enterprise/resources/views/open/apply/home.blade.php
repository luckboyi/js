@extends('layouts.third')

@section('content')
    <section class='sec tac'>
        <p class="sec_which_peo f26">根据您的情况，请选择注册成为怎样的开发者，选择后无法进行修改</p>
        <p class="sec_which_con">
            <a href="{{ url('userApply/user') }}"></a>
            <a href="{{ url('userApply/company') }}" class="company"></a>
        </p>
    </section>
@endsection