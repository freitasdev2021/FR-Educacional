@extends('layouts.app')

@section('content')
<body className='snippet-body'>
    <body id="body-pd">
       <header class="header" id="header">
          <div class="header_toggle"> <i class='bx bx-menu' id="header-toggle"></i> </div>
          <div class="header_img"> <img src="https://i.imgur.com/hczKIze.jpg" alt=""> </div>
       </header>
       <div class="l-navbar" id="nav-bar">
          <nav class="nav" >
             <div>
                <a href="#" class="nav_logo"><i class='bx bx-book-reader text-white'></i><span class="nav_logo-name">FR Educacional</span> </a>
                <div class="nav_list"> 
                  <x-modulo nome="Secretarías" icon="bx bx-buildings" rota="Secretarias/index" endereco="Secretarias"/>
                  <x-modulo nome="Usuários" icon="bx bx-user" rota="Usuarios/indexFornecedor" endereco="Usuarios"/> 
                </div>
             </div>
             <form action="{{route('logout')}}" method="POST">
               @csrf
               <button class="nav_link sair" type="submit"><i class='bx bx-log-out nav_icon'></i> <span class="nav_name">Sair</span> </button>
             </form>
          </nav>
       </div>
       <!--Container Main start-->
       <div class="height-100" style="margin-top:100px; margin-right:15px;">
          {{$slot}}
       </div>
       <!--Container Main end-->
 </body>
 </body>
@endsection