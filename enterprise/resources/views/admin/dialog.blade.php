<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 2016/9/5
 * Time: 17:16
 */
?>
@extends('layouts.admin')

{{--@include('common.errors')--}}
@section('content')
    <!-- BEGIN PAGE -->
    <div id="main-content">
        <button id="test2">1. content(elem).show()</button>
        <button id="test3">2. close()</button>
        <button id="test4">3. show()</button>
        <button id="test5">4. close().remove()</button>
        <div id="test-content" style="display:none">
            <p>我是隐藏的DOM节点
                <button id="test">alert</button>
            </p>
        </div>
    </div>
    <!-- END PAGE -->
@endsection

@section('javascript')
    <script type="text/javascript">
        $(function() {
            window.dialog = dialog;
            $('#test').on('click', function() {
                alert('click')
            });

            $('#test2').on('click', function() {
                var d = dialog({
                    title: '消息',
                    content: document.getElementById('test-content'),
                    okValue: '确 定'
                });
                d.showModal();
            });

        });
    </script>
@endsection

