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
                <img src="{{asset('img/logo.png')}}"
                class="img-fluid" alt="Sample image">
            </div>
            <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                <h3 align="center">Bem Vindo!</h3>
                <hr>
                <form id="form_acesso" action="{{route('login')}}" method="POST">
                @csrf
                @method("POST")
                <!-- Email input -->
                <div class="form-outline mb-4">
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg @error('email') is-invalid @enderror" required placeholder="Email" />
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Password input -->
                <div class="form-outline mb-3">
                    <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" required placeholder="Senha" />
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <!-- Checkbox -->
                    <div class="form-check mb-0">
                        <input class="form-check-input me-2" id="remember-me" name="remember"  {{ old('remember') ? 'checked' : '' }} type="checkbox" />
                        <label class="form-check-label" for="form2Example3">Lembrar acesso</label>
                    </div>
                    {{-- <a href="https://api.whatsapp.com/send/?phone=31983086235&text&type=phone_number" target="_blank" class="text-body">Esqueceu a senha? (Chamar suporte)</a> --}}
                </div>

                <div class="text-center text-lg-start mt-4 pt-2">
                    <button type="submit" class="btn btn-lg col-sm-12 bt-login">Acessar</button>
                </div>
                <br>
                <span class="error"></span>
                <!-- <strong class="btcliente"><a href='#'>Quero ser cliente(31 Dias Grátis sem compromisso)</a></strong> -->
                </form>
            </div>
            </div>
        </div>
    </section>
</body>
</html>