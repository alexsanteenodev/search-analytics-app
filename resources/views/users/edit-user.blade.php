@extends('layouts.app')

@section('template_title')
  Editing User {{ $user->name }}
@endsection

@section('template_linked_css')
  <style type="text/css">
    .btn-save,
    .pw-change-container {
      display: none;
    }
  </style>
@endsection

@section('content')
  @if (\Session::has('success'))
    <div class="alert alert-success">
      <p>{{ \Session::get('success') }}</p>
    </div>
  @endif
  @if (\Session::has('error'))
    <div class="alert alert-success">
      <p>{{ \Session::get('error') }}</p>
    </div>
  @endif
  <section class="personal_cabinet">
    <div class="container">
      <div class="row">
        <h2 class="col-lg-12">Личный кабинет</h2>
        {!! Form::model($user, array('action' => array('Auth\UsersController@update', $user->id), 'method' => 'PUT', 'files' => true)) !!}

        <div class="personal_information col-lg-12">
            <div class="site_info_add">
              <div class="personal_block_img col-lg-3 ">
                <div class="personal_block_img_body">
                  <img id="holder" style="width: 198px;
    height: 198px;
    display: block;
    margin: 0 auto;
    margin-bottom: 20px;" src="<?= $user->image ?? '/img/bg_user_icon.png' ;?>">

                  <p class="user_name">{{ $user->name }}</p>
                  <div class="file-upload">

                       <span class="input_file">
                         <a id="main_image" data-input="main_image_thumb" data-preview="holder" class="">
                          Загрузить
                         </a>
                       </span>
                        <input id="main_image_thumb" class="form-control" type="text" name="image" style="display: none" value="{{$user->image}}">

                  </div>
                  <!--<button type="file">Загрузить</button>-->

                </div>
              </div>
              <div class="personal_block_info col-lg-8 col-lg-offset-1">

                {!! csrf_field() !!}
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
                    Введите старый пароль
                  </div>
                  <div class="input_block col-lg-8">
                    {!! Form::password('old_password', array('id' => 'user-old-pass', 'class' => 'form-control ', 'placeholder' => '')) !!}

                  </div>
                </div>
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
        {!! Form::button('Сохранить', array('class' => 'save','type' => 'button', 'data-toggle' => 'modal', 'data-target' => '#confirmSave', 'data-title' => 'Сохранить', 'data-message' =>  'Вы действительно хотите сохранить текущие изменения? ')) !!}

        {!! Form::close() !!}

      </div>
    </div>
  </section>

  @include('users.modals.modal-save')
  @include('users.modals.modal-delete')

@endsection

@section('template_scripts')

  @include('users.scripts.delete-modal-script')
  @include('users.scripts.save-modal-script')

  <script type="text/javascript">
    $('.btn-change-pw').click(function(event) {
      event.preventDefault();
      $('.pw-change-container').slideToggle(100);
      $(this).find('.fa').toggleClass('fa-times');
      $(this).find('.fa').toggleClass('fa-lock');
      $(this).find('span').toggleText('', 'Cancel');
    });
    $("input").keyup(function() {
      if(!$('input').val()){
          $(".btn-save").hide();
      }
      else {
          $(".btn-save").show();
      }
    });
  </script>
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