<!DOCTYPE html>
<html lang="pt-br">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>FR Controller: Login</title>
      <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">
      <link rel="stylesheet" href="{{asset('css/login.css')}}">
      <link rel="icon" type="image/x-icon" href="img/fricon.ico" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
   </head>
   <body>
      <section class="vh-100">
         <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100">
               <div class="col-md-9 col-lg-6 col-xl-5">
                  <img src="{{asset('img/logo.png')}}" class="img-fluid" alt="Sample image">
               </div>
               <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                  <h3 align="center">Recrutamento {{$Orgao}}</h3>
                  <hr>
                  <form id="form_acesso" action="{{ route('Recrutamento/Registrar',$IDOrg) }}" method="POST">
                     @csrf
                     @method("PATCH")
                     @if ($errors->any())
                        @foreach ($errors->all() as $error)
                        <div class="alert alert-danger" role="alert">
                            {{$error}}
                        </div>
                        @endforeach
                     @endif
                     <div class="form-outline mb-4">
                        <input type="name" name="name" class="form-control form-control-lg" required placeholder="Nome" />
                     </div>
                     <div class="form-outline mb-4">
                        <input type="email" name="email" class="form-control form-control-lg" required placeholder="Email" />
                     </div>
                     <div class="form-outline mb-4">
                        <label>Data de Nascimento</label>
                        <input type="date" name="Nascimento" class="form-control form-control-lg" required placeholder="Nascimento" />
                     </div>
                     <div class="form-outline mb-4">
                        <input type="text" name="Telefone" class="form-control form-control-lg" required placeholder="Telefone" />
                     </div>
                     <div class="form-outline mb-4">
                        <select name="Escolaridade" class="form-control form-control-lg">
                            <option value="">Escolaridade</option>
                            <option value="Ensino Fundamental I">Ensino Fundamental I</option>
                            <option value="Ensino Fundamental II">Ensino Fundamental II</option>
                            <option value="Ensino Médio">Ensino Médio</option>
                            <option value="Ensino Técnico">Ensino Técnico</option>
                            <option value="Ensino Superior">Ensino Superior</option>
                        </select>
                     </div>
                     <div class="form-outline mb-3">
                        <input type="password" name="password" class="form-control form-control-lg" required placeholder="Senha" />
                     </div>
                     <div class="form-outline mb-3">
                        <input type="password" name="password_confirmation" class="form-control form-control-lg" required placeholder="Confirme sua senha" />
                     </div>
                     <div class="d-flex justify-content-between align-items-center">
                        <strong>
                        <a class="text-primary" href="{{route("login")}}" class="text-body">Já Está Cadastrado?</a>
                        </strong>
                     </div>
                     <div class="text-center text-lg-start mt-4 pt-2">
                        <button type="submit" class="btn btn-lg col-sm-12 bt-login">Registrar</button>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </section>
   </body>
</html>