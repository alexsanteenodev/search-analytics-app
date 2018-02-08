@extends('layouts.app')

@section('template_title')
  Create New User
@endsection

@section('template_fastload_css')
@endsection

@section('content')
  <section class="personal_cabinet">
    <div class="container">
      <div class="row">
        <h2 class="col-lg-12">Create New User</h2>
        <a href="/users" class="btn btn-info btn-xs pull-right">
          <i class="fa fa-fw fa-mail-reply" aria-hidden="true"></i>
          <span class="hidden-sm hidden-xs">Back to </span><span class="hidden-xs">Users</span>
        </a>
        @include('users.partials.form-status')
        {!! Form::open(array('action' => 'Auth\UsersController@store', 'method' => 'POST', 'role' => 'form','files' => true)) !!}

        {!! csrf_field() !!}

        <div class="personal_information col-lg-12">
          <div class="site_info_add">
            <div class="personal_block_img col-lg-3 ">
              <div class="personal_block_img_body">
                <img id="holder" style="width: 198px;
    height: 198px;
    display: block;
    margin: 0 auto;
    margin-bottom: 20px;" src="/img/bg_user_icon.png">

                <p class="user_name"></p>
                <div class="file-upload">

                       <span class="input_file">
                         <a id="main_image" data-input="main_image_thumb" data-preview="holder" class="">
                          Загрузить
                         </a>
                       </span>
                  <input id="main_image_thumb" class="form-control" type="text" name="image" style="display: none" value="">

                </div>
                <!--<button type="file">Загрузить</button>-->

              </div>
            </div>
            <div class="personal_block_info col-lg-8 col-lg-offset-1">

              <h3>Основные данные</h3>
              <div class="row">
                <div class="name col-lg-4">
                  Имя
                </div>
                <div class="input_block col-lg-8">
                  {!! Form::text('name', old('name'), array('id' => 'user-name', 'class' => 'form-control', 'placeholder' =>'Введите имя и фамилию')) !!}

                </div>
              </div>
              <div class="row">
                <div class="name col-lg-4">
                  Электронная почта
                </div>
                <div class="input_block col-lg-8">
                  {!! Form::text('email', old('email'), array('id' => 'email', 'class' => 'form-control', 'placeholder' => "example@olshansky.ua")) !!}

                </div>
              </div>
              <h3 class="change_pass">Изменение пароля</h3>
              <div class="row">
                <div class="name col-lg-4">
                  Придумайте новый пароль
                </div>
                <div class="input_block col-lg-8">
                  {!! Form::password('password', array('id' => 'user-new-pass', 'class' => 'form-control ', 'placeholder' => '')) !!}

                </div>
              </div>
              <div class="row">
                <div class="name col-lg-4">
                  Введите новый пароль ещё раз
                </div>
                <div class="input_block col-lg-8">
                  {!! Form::password('password_confirmation', array('id' => 'user-new-pass-repeat', 'class' => 'form-control', 'placeholder' =>'')) !!}
                  <br>
                  <span>Пароль должен быть не менее 6 символов, содержать цифры и заглавные буквы и не должен совпадать с именем и эл. почтой</span>
                </div>
              </div>


            </div>
          </div>
        </div>
        {!! Form::button( Lang::get('forms.create_user_button_text'), array('class' => 'btn btn-success btn-flat margin-bottom-1 pull-right','type' => 'submit', )) !!}

        {!! Form::close() !!}

      </div>
    </div>
  </section>
  <script src="/vendor/laravel-filemanager/js/lfm.js"></script>
  <script>
    var options = {
      filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
      filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{ csrf_token() }}',
      filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
      filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token={{ csrf_token() }}'
    };

    $('#main_image').filemanager('image');
  </script>
@endsection

@section('template_scripts')
@endsection