@section('title', ''.$user->name.'\'s Profile | SISKA &mdash; Sistem Informasi Karier')
@push('styles')
    <style>
        .card-title a {
            color: #7f7f7f;
        }
    </style>
@endpush
@extends('layouts.auth.mst_agency')
@section('inner-content')
    <div class="row" style="font-family: 'PT Sans', Arial, serif">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12 to-animate">
                            <div class="card">
                                <form action="{{route('agency.vacancy.create')}}" method="post" id="form-vacancy">
                                    {{csrf_field()}}
                                    <input type="hidden" name="_method">
                                    <div class="card-content">
                                        <div class="card-title">
                                            <small id="show_vacancy_settings">
                                                Vacancy Setup
                                                <span class="pull-right" style="cursor: pointer; color: #00ADB5">
                                                <i class="fa fa-briefcase"></i>&ensp;Add
                                                </span>
                                            </small>
                                            <hr class="hr-divider">
                                            <div id="stats_vacancy">
                                                @if(count($vacancies) != 0)
                                                    @foreach($vacancies as $row)
                                                        @php
                                                            $city = \App\Cities::find($row->cities_id)->name;
                                                            $salary = \App\Salaries::find($row->salary_id);
                                                            $jobfunc = \App\FungsiKerja::find($row->fungsikerja_id);
                                                            $joblevel = \App\JobLevel::find($row->joblevel_id);
                                                            $industry = \App\Industri::find($row->industry_id);
                                                            $degrees = \App\Tingkatpend::find($row->tingkatpend_id);
                                                            $majors = \App\Jurusanpend::find($row->jurusanpend_id);
                                                        @endphp
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="media">
                                                                    <div class="media-body">
                                                                        <small class="media-heading">
                                                                            <a href="{{route('detail.vacancy',
                                                                        ['id'=>$row->id])}}" style="color: #00ADB5">
                                                                                {{$row->judul}}</a>
                                                                            <span class="pull-right">
                                                                            <a style="color: #00ADB5;cursor: pointer;"
                                                                               onclick="editVacancy('{{encrypt
                                                                               ($row->id)}}')">Edit&ensp;<i
                                                                                        class="fa fa-edit"></i></a>
                                                                            <small style="color: #7f7f7f">&nbsp;&#124;&nbsp;</small>
                                                                            <a href="{{route('agency.vacancy.delete',
                                                                            ['id' => encrypt($row->id),
                                                                            'judul' => $row->judul])}}"
                                                                               class="delete-vacancy"
                                                                               style="color: #FA5555;"><i
                                                                                        class="fa fa-eraser"></i>&ensp;Delete</a>
                                                                        </span>
                                                                        </small>
                                                                        <blockquote
                                                                                style="font-size: 12px;color: #7f7f7f"
                                                                                class="ulTinyMCE">
                                                                            <ul class="list-inline">
                                                                                <li>
                                                                                    <a class="tag" target="_blank"
                                                                                       href="{{route('search.vacancy',
                                                                                   ['loc' => substr($city, 0, 2)=="Ko"
                                                                                   ? substr($city,5)
                                                                                   : substr($city,10)])}}">
                                                                                        <i class="fa fa-map-marked"></i>&ensp;
                                                                                        {{substr($city, 0, 2)=="Ko" ?
                                                                                        substr($city,5) :
                                                                                        substr($city,10)}}
                                                                                    </a>
                                                                                </li>
                                                                                <li>
                                                                                    <a class="tag" target="_blank"
                                                                                       href="{{route('search.vacancy',
                                                                                   ['jobfunc_ids' =>
                                                                                   $row->fungsikerja_id])}}">
                                                                                        <i class="fa fa-warehouse"></i>&ensp;
                                                                                        {{$jobfunc->nama}}
                                                                                    </a>
                                                                                </li>
                                                                                <li>
                                                                                    <a class="tag" target="_blank"
                                                                                       href="{{route('search.vacancy',
                                                                                   ['industry_ids' =>
                                                                                   $row->industry_id])}}">
                                                                                        <i class="fa fa-industry"></i>&ensp;
                                                                                        {{$industry->nama}}
                                                                                    </a>
                                                                                </li>
                                                                                <li>
                                                                                    <a class="tag" target="_blank"
                                                                                       href="{{route('search.vacancy',
                                                                                   ['salary_ids' => $salary->id])}}">
                                                                                        <i class="fa fa-money-bill-wave"></i>
                                                                                        &ensp;IDR {{$salary->name}}</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a class="tag" target="_blank"
                                                                                       href="{{route('search.vacancy',
                                                                                   ['degrees_ids' =>
                                                                                   $row->tingkatpend_id])}}">
                                                                                        <i class="fa fa-graduation-cap"></i>
                                                                                        &ensp;{{$degrees->name}}</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a class="tag" target="_blank"
                                                                                       href="{{route('search.vacancy',
                                                                                   ['majors_ids' =>
                                                                                   $row->jurusanpend_id])}}">
                                                                                        <i class="fa fa-user-graduate"></i>
                                                                                        &ensp;{{$majors->name}}</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a class="tag">
                                                                                        <i class="fa fa-briefcase"></i>
                                                                                        &ensp;{{$row->pengalaman}}
                                                                                    </a>
                                                                                </li>
                                                                            </ul>
                                                                            <table class="stats"
                                                                                   style="font-size: 12px;margin-top: -.5em">
                                                                                <tr>
                                                                                    <td><i class="fa fa-calendar"></i>
                                                                                    </td>
                                                                                    <td>&nbsp;Posted On</td>
                                                                                    <td>
                                                                                        : {{$row->created_at
                                                                                    ->format('j F Y')}}
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td><i class="fa fa-clock"></i></td>
                                                                                    <td>&nbsp;Last Update</td>
                                                                                    <td>
                                                                                        : {{$row->updated_at
                                                                                    ->diffForHumans()}}
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                            <hr class="hr-divider">
                                                                            <small>Requirements</small>
                                                                            {!! $row->syarat !!}
                                                                            <small>Responsibilities</small>
                                                                            {!! $row->tanggungjawab !!}
                                                                        </blockquote>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr class="hr-divider">
                                                    @endforeach
                                                @else
                                                    <p align="justify">
                                                        There seems to be none of the vacancy was found&hellip;</p>
                                                @endif
                                            </div>

                                            <div id="vacancy_settings" style="display: none">
                                                <div class="row form-group">
                                                    <div class="col-lg-12">
                                                        <small>Vacancy Name</small>
                                                        <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-building"></i>
                                                            </span>
                                                            <input class="form-control" type="text"
                                                                   placeholder="ex: Programmer"
                                                                   name="judul" id="judul"
                                                                   maxlength="200" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col-lg-6">
                                                        <small>Job Function</small>
                                                        <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-warehouse"></i>
                                                            </span>
                                                            <select class="form-control selectpicker"
                                                                    title="-- Choose --" id="job_funct"
                                                                    data-live-search="true" data-container="body"
                                                                    name="fungsikerja_id" required>
                                                                @foreach($job_functions as $row)
                                                                    <option value="{{$row->id}}">
                                                                        {{$row->nama}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <small>Industry</small>
                                                        <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-industry"></i>
                                                            </span>
                                                            <select class="form-control selectpicker"
                                                                    title="-- Choose --" id="industry"
                                                                    data-live-search="true" data-container="body"
                                                                    name="industri_id" required>
                                                                @foreach($industries as $row)
                                                                    <option value="{{$row->id}}">
                                                                        {{$row->nama}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col-lg-6">
                                                        <small>Job Level</small>
                                                        <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-level-up-alt"></i>
                                                            </span>
                                                            <select class="form-control selectpicker"
                                                                    title="-- Choose --" id="job_level"
                                                                    data-live-search="true" data-container="body"
                                                                    name="joblevel_id" required>
                                                                @foreach($job_levels as $row)
                                                                    <option value="{{$row->id}}">
                                                                        {{$row->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <small>Job Type</small>
                                                        <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-user-clock"></i>
                                                            </span>
                                                            <select class="form-control selectpicker"
                                                                    title="-- Choose --" data-container="body"
                                                                    id="job_type" name="jobtype_id" required>
                                                                @foreach($job_types as $row)
                                                                    <option value="{{$row->id}}">
                                                                        {{$row->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col-lg-6">
                                                        <small>Location</small>
                                                        <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-map-marked"></i>
                                                            </span>
                                                            <select class="form-control selectpicker"
                                                                    title="-- Choose --" id="city_id"
                                                                    data-live-search="true" data-container="body"
                                                                    name="cities_id" required>
                                                                @foreach($provinces as $province)
                                                                    <optgroup label="{{$province->name}}">
                                                                        @foreach($province->cities as $city)
                                                                            @if(substr($city->name, 0, 2)=="Ko")
                                                                                <option value="{{$city->id}}">
                                                                                    {{substr($city->name,4)}}
                                                                                </option>
                                                                            @else
                                                                                <option value="{{$city->id}}">
                                                                                    {{substr($city->name,9)}}
                                                                                </option>
                                                                            @endif
                                                                        @endforeach
                                                                    </optgroup>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <small>Salary</small>
                                                        <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <strong>Rp</strong></span>
                                                            <select class="form-control selectpicker"
                                                                    id="salary_id" data-live-search="true"
                                                                    name="salary_id" data-container="body" required>
                                                                <option value="" selected>-- Choose --</option>
                                                                @foreach($salaries as $row)
                                                                    <option value="{{$row->id}}">
                                                                        {{$row->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col-lg-6">
                                                        <small>Work Experience</small>
                                                        <div class="input-group" style="text-transform: none">
                                                            <span class="input-group-addon">
                                                                At least</span>
                                                            <input class="form-control" type="text"
                                                                   onkeypress="return numberOnly(event, false)"
                                                                   maxlength="3" placeholder="0"
                                                                   id="pengalaman" name="pengalaman" required>
                                                            <span class="input-group-addon">
                                                                year(s)</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <small>Interview Schedule</small>
                                                        <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-calendar-alt"></i>
                                                            </span>
                                                            <input class="form-control datetimepicker"
                                                                   name="interview" type="text"
                                                                   placeholder="yyyy-mm-dd hh:mm:ss"
                                                                   id="interview" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col-lg-6">
                                                        <small>Education Degree</small>
                                                        <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-graduation-cap"></i>
                                                            </span>
                                                            <select class="form-control selectpicker"
                                                                    title="-- Choose --" id="tingkatpend"
                                                                    data-live-search="true" data-container="body"
                                                                    name="tingkatpend_id" required>
                                                                @foreach(\App\Tingkatpend::all() as $row)
                                                                    <option value="{{$row->id}}">
                                                                        {{$row->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <small>Education Major</small>
                                                        <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-user-graduate"></i>
                                                            </span>
                                                            <select class="form-control selectpicker"
                                                                    title="-- Choose --" id="jurusanpend"
                                                                    data-live-search="true" data-container="body"
                                                                    name="jurusanpend_id" required>
                                                                @foreach(\App\Jurusanpend::all() as $row)
                                                                    <option value="{{$row->id}}">
                                                                        {{$row->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <small>Requirements</small>
                                                <div class="row form-group">
                                                    <div class="col-lg-12">
                                                        <textarea class="form-control" name="syarat"
                                                                  placeholder="Job's requirements"
                                                                  id="syarat"></textarea>
                                                    </div>
                                                </div>

                                                <small>Responsibilities</small>
                                                <div class="row form-group">
                                                    <div class="col-lg-12">
                                                        <textarea class="form-control" name="tanggungjawab"
                                                                  placeholder="Job's responsibilities"
                                                                  id="tanggungjawab"></textarea>
                                                    </div>
                                                </div>
                                                <div class="row form-group" id="btn_cancel_vacancy">
                                                    <div class="col-lg-12">
                                                        <input type="reset" value="CANCEL"
                                                               class="btn btn-default">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-read-more">
                                        <button class="btn btn-link btn-block" data-placement="top" type="button"
                                                data-toggle="tooltip" title="Click here to submit your changes!"
                                                id="btn_save_vacancy" disabled>
                                            <i class="fa fa-briefcase"></i>&nbsp;SAVE CHANGES
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push("scripts")
    <script>
        $(document).ready(function () {
            tinymce.init({
                branding: false,
                path_absolute: '{{url('/')}}',
                selector: '#syarat',
                height: 300,
                themes: 'modern',
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor textcolor',
                    'searchreplace visualblocks code',
                    'insertdatetime media table contextmenu paste code help wordcount'
                ],
                toolbar: 'insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                relative_urls: false,
                file_browser_callback: function (field_name, url, type, win) {
                    var x = window.innerWidth || document.documentElement.clientWidth ||
                        document.getElementsByTagName('body')[0].clientWidth,
                        y = window.innerHeight || document.documentElement.clientHeight ||
                            document.getElementsByTagName('body')[0].clientHeight,
                        cmsURL = editor_config.path_absolute + 'filemanager?field_name=' + field_name;
                    if (type == 'image') {
                        cmsURL = cmsURL + '&type=Images';
                    } else {
                        cmsURL = cmsURL + '&type=Files';
                    }

                    tinyMCE.activeEditor.windowManager.open({
                        file: cmsURL,
                        title: 'File Manager',
                        width: x * 0.8,
                        height: y * 0.8,
                        resizable: 'yes',
                        close_previous: 'no'
                    });
                }
            });
            tinymce.init({
                branding: false,
                path_absolute: '{{url('/')}}',
                selector: '#tanggungjawab',
                height: 300,
                themes: 'modern',
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor textcolor',
                    'searchreplace visualblocks code',
                    'insertdatetime media table contextmenu paste code help wordcount'
                ],
                toolbar: 'insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                relative_urls: false,
                file_browser_callback: function (field_name, url, type, win) {
                    var x = window.innerWidth || document.documentElement.clientWidth ||
                        document.getElementsByTagName('body')[0].clientWidth,
                        y = window.innerHeight || document.documentElement.clientHeight ||
                            document.getElementsByTagName('body')[0].clientHeight,
                        cmsURL = editor_config.path_absolute + 'filemanager?field_name=' + field_name;
                    if (type == 'image') {
                        cmsURL = cmsURL + '&type=Images';
                    } else {
                        cmsURL = cmsURL + '&type=Files';
                    }

                    tinyMCE.activeEditor.windowManager.open({
                        file: cmsURL,
                        title: 'File Manager',
                        width: x * 0.8,
                        height: y * 0.8,
                        resizable: 'yes',
                        close_previous: 'no'
                    });
                }
            });
        });
    </script>
    @include('layouts.partials.auth.Agencies._scripts_auth-agency')
    @include('layouts.partials.auth.Agencies._scripts_ajax-agency')
@endpush
