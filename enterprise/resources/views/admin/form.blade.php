<?php
/**
 * Created by PhpStorm.
 * User: libb
 * Date: 2016/9/5
 * Time: 17:07
 */
?>
@extends('layouts.admin')

{{--@include('common.errors')--}}
@section('content')
    <!-- BEGIN PAGE -->
    <div id="main-content">
        <!-- BEGIN PAGE CONTAINER-->
        <div class="container-fluid">
            <!-- BEGIN PAGE HEADER-->
            <div class="row-fluid">
                <div class="span12">
                    <!-- BEGIN THEME CUSTOMIZER-->
                    <div id="theme-change" class="hidden-phone">
                        <i class="icon-cogs"></i>
                            <span class="settings">
                            <span class="text">Theme Color:</span>
                            <span class="colors">
                                <span class="color-default" data-style="default"></span>
                            <span class="color-green" data-style="green"></span>
                            <span class="color-gray" data-style="gray"></span>
                            <span class="color-purple" data-style="purple"></span>
                            <span class="color-red" data-style="red"></span>
                            </span>
                            </span>
                    </div>
                    <!-- END THEME CUSTOMIZER-->
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title">
                        Form Layout
                    </h3>
                    <ul class="breadcrumb">
                        <li>
                            <a href="#">Home</a>
                            <span class="divider">/</span>
                        </li>
                        <li>
                            <a href="#">Form Stuff</a>
                            <span class="divider">/</span>
                        </li>
                        <li class="active">
                            Form Layout
                        </li>
                        <li class="pull-right search-wrap">
                            <form action="search_result.html" class="hidden-phone">
                                <div class="input-append search-input-area">
                                    <input class="" id="appendedInputButton" type="text">
                                    <button class="btn" type="button"><i class="icon-search"></i> </button>
                                </div>
                            </form>
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>
            <!-- END PAGE HEADER-->
            <!-- BEGIN PAGE CONTENT-->
            <div class="row-fluid">
                <div class="span12">
                    <!-- BEGIN SAMPLE FORMPORTLET-->
                    <div class="widget green">
                        <div class="widget-title">
                            <h4><i class="icon-reorder"></i> Form Layouts</h4>
                                <span class="tools">
                            <a href="javascript:;" class="icon-chevron-down"></a>
                            <a href="javascript:;" class="icon-remove"></a>
                            </span>
                        </div>
                        <div class="widget-body">
                            <!-- BEGIN FORM-->
                            <form action="#" class="form-horizontal">
                                <div class="control-group">
                                    <label class="control-label">Mini Input</label>
                                    <div class="controls">
                                        <input type="text" placeholder=".input-mini" class="input-mini" />
                                        <span class="help-inline">Some hint here</span>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Small Input</label>
                                    <div class="controls">
                                        <input type="text" placeholder=".input-small" class="input-small" />
                                        <span class="help-inline">Some hint here</span>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Meduam Input</label>
                                    <div class="controls">
                                        <input type="text" placeholder=".input-medium" class="input-medium" />
                                        <span class="help-inline">Some hint here</span>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Large Input</label>
                                    <div class="controls">
                                        <input type="text" placeholder=".input-large" class="input-large" />
                                        <span class="help-inline">Some hint here</span>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">xLarge Input</label>
                                    <div class="controls">
                                        <input type="text" placeholder=".input-xlarge" class="input-xlarge" />
                                        <span class="help-inline">Some hint here</span>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">xxLarge Input</label>
                                    <div class="controls">
                                        <input type="text" placeholder=".input-xxlarge" class="input-xxlarge" />
                                        <span class="help-inline">Some hint here</span>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Disabled Input</label>
                                    <div class="controls">
                                        <input class="medium" type="text" placeholder="Disabled input here..." disabled />
                                        <span class="help-inline">Some hint here</span>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Readonly Input</label>
                                    <div class="controls">
                                        <input class="medium" readonly type="text" placeholder="Readonly input here..." disabled />
                                        <span class="help-inline">Some hint here</span>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Small Dropdown</label>
                                    <div class="controls">
                                        <select class="input-small m-wrap" tabindex="1">
                                            <option value="Category 1">Category 1</option>
                                            <option value="Category 2">Category 2</option>
                                            <option value="Category 3">Category 5</option>
                                            <option value="Category 4">Category 4</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Medium Dropdown</label>
                                    <div class="controls">
                                        <select class="input-medium m-wrap" tabindex="1">
                                            <option value="Category 1">Category 1</option>
                                            <option value="Category 2">Category 2</option>
                                            <option value="Category 3">Category 5</option>
                                            <option value="Category 4">Category 4</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Large Dropdown</label>
                                    <div class="controls">
                                        <select class="input-large m-wrap" tabindex="1">
                                            <option value="Category 1">Category 1</option>
                                            <option value="Category 2">Category 2</option>
                                            <option value="Category 3">Category 5</option>
                                            <option value="Category 4">Category 4</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Radio Buttons</label>
                                    <div class="controls">
                                        <label class="radio">
                                            <input type="radio" name="optionsRadios1" value="option1" /> Option 1
                                        </label>
                                        <label class="radio">
                                            <input type="radio" name="optionsRadios1" value="option2" checked /> Option 2
                                        </label>
                                        <label class="radio">
                                            <input type="radio" name="optionsRadios1" value="option2" /> Option 3
                                        </label>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Radio Buttons</label>
                                    <div class="controls">
                                        <label class="radio line">
                                            <input type="radio" name="optionsRadios2" value="option1" /> Option 1
                                        </label>
                                        <label class="radio line">
                                            <input type="radio" name="optionsRadios2" value="option2" checked /> Option 2
                                        </label>
                                        <label class="radio line">
                                            <input type="radio" name="optionsRadios2" value="option2" /> Option 3
                                        </label>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Checkbox</label>
                                    <div class="controls">
                                        <label class="checkbox">
                                            <input type="checkbox" value="" /> Checkbox 1
                                        </label>
                                        <label class="checkbox">
                                            <input type="checkbox" value="" /> Checkbox 2
                                        </label>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Checkbox</label>
                                    <div class="controls">
                                        <label class="checkbox line">
                                            <input type="checkbox" value="" /> Checkbox 1
                                        </label>
                                        <label class="checkbox line">
                                            <input type="checkbox" value="" /> Checkbox 2
                                        </label>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Textarea</label>
                                    <div class="controls">
                                        <textarea class="input-medium" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">Large Textarea</label>
                                    <div class="controls">
                                        <textarea class="input-large" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">xLarge Textarea</label>
                                    <div class="controls">
                                        <textarea class="input-xlarge" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label">xxLarge Textarea</label>
                                    <div class="controls">
                                        <textarea class="input-xxlarge" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="btn blue"><i class="icon-ok"></i> Save</button>
                                    <button type="button" class="btn"><i class=" icon-remove"></i> Cancel</button>
                                </div>
                            </form>
                            <!-- END FORM-->
                        </div>
                    </div>
                    <!-- END SAMPLE FORM PORTLET-->
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <!-- BEGIN SAMPLE FORMPORTLET-->
                    <div class="widget red">
                        <div class="widget-title">
                            <h4>
                                <i class="icon-reorder"></i> Label above input (grid)
                            </h4>
                                <span class="tools">
                            <a href="javascript:;" class="icon-chevron-down"></a>
                            <a href="javascript:;" class="icon-remove"></a>
                            </span>
                        </div>
                        <div class="widget-body">
                            <!-- BEGIN FORM-->
                            <form class="form-vertical" method="get" action="#">
                                <div class="row-fluid">
                                    <div class="span3">
                                        <div class="control-group">
                                            <label class="control-label">Label 3</label>
                                            <div class="controls controls-row">
                                                <input type="text" class="input-block-level" placeholder="Text input" name="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="control-group">
                                            <label class="control-label">label 3</label>
                                            <div class="controls controls-row">
                                                <input type="text" class="input-block-level" placeholder="Text input" name="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="control-group">
                                            <label class="control-label">Label 3</label>
                                            <div class="controls controls-row">
                                                <input type="text" class="input-block-level" placeholder="Text input" name="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span3">
                                        <div class="control-group">
                                            <label class="control-label">label 3</label>
                                            <div class="controls controls-row">
                                                <input type="text" class="input-block-level" placeholder="Text input" name="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row-fluid">
                                    <div class="span4">
                                        <div class="control-group">
                                            <label class="control-label">label 4</label>
                                            <div class="controls controls-row">
                                                <input type="text" class="input-block-level" placeholder="Text input" name="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span4">
                                        <div class="control-group">
                                            <label class="control-label">Label 4</label>
                                            <div class="controls controls-row">
                                                <input type="text" class="input-block-level" placeholder="Text input" name="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span4">
                                        <div class="control-group">
                                            <label class="control-label">label 4</label>
                                            <div class="controls controls-row">
                                                <input type="text" class="input-block-level" placeholder="Text input" name="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row-fluid">
                                    <div class="span1">
                                        <div class="control-group">
                                            <label class="control-label">Label 1</label>
                                            <div class="controls controls-row">
                                                <input type="text" class="input-block-level" placeholder="Text input" name="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span11">
                                        <div class="control-group">
                                            <label class="control-label">label 11</label>
                                            <div class="controls controls-row">
                                                <input type="text" class="input-block-level" placeholder="Text input" name="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row-fluid">
                                    <div class="span2">
                                        <div class="control-group">
                                            <label class="control-label">Label 2</label>
                                            <div class="controls controls-row">
                                                <input type="text" class="input-block-level" placeholder="Text input" name="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span10">
                                        <div class="control-group">
                                            <label class="control-label">label 10</label>
                                            <div class="controls controls-row">
                                                <input type="text" class="input-block-level" placeholder="Text input" name="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row-fluid">
                                    <div class="span3">
                                        <div class="control-group">
                                            <label class="control-label">Label 3</label>
                                            <div class="controls controls-row">
                                                <input type="text" class="input-block-level" placeholder="Text input" name="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span9">
                                        <div class="control-group">
                                            <label class="control-label">label 9</label>
                                            <div class="controls controls-row">
                                                <input type="text" class="input-block-level" placeholder="Text input" name="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row-fluid">
                                    <div class="span4">
                                        <div class="control-group">
                                            <label class="control-label">Label 4</label>
                                            <div class="controls controls-row">
                                                <input type="text" class="input-block-level" placeholder="Text input" name="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span8">
                                        <div class="control-group">
                                            <label class="control-label">label 8</label>
                                            <div class="controls controls-row">
                                                <input type="text" class="input-block-level" placeholder="Text input" name="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row-fluid">
                                    <div class="span5">
                                        <div class="control-group">
                                            <label class="control-label">Label 5</label>
                                            <div class="controls controls-row">
                                                <input type="text" class="input-block-level" placeholder="Text input" name="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span7">
                                        <div class="control-group">
                                            <label class="control-label">label 7</label>
                                            <div class="controls controls-row">
                                                <input type="text" class="input-block-level" placeholder="Text input" name="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row-fluid">
                                    <div class="span6">
                                        <div class="control-group">
                                            <label class="control-label">Label 6</label>
                                            <div class="controls controls-row">
                                                <input type="text" class="input-block-level" placeholder="Text input" name="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span6">
                                        <div class="control-group">
                                            <label class="control-label">label 6</label>
                                            <div class="controls controls-row">
                                                <input type="text" class="input-block-level" placeholder="Text input" name="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- END FORM-->
                        </div>
                    </div>
                    <!-- END SAMPLE FORM PORTLET-->
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
        <!-- END PAGE CONTAINER-->
    </div>
    <!-- END PAGE -->
@endsection
