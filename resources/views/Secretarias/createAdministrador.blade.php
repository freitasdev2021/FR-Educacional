<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--LISTAS-->
            <div class="col-sm-12 p-2 center-form">
                <form action="{{route('Secretarias/Administradores/Save')}}" method="POST">
                    @csrf
                    @method("POST")
                    @if(session('success'))
                    <div class="col-sm-12 shadow p-2 bg-success text-white">
                        <strong>{{session('success')}}</strong>
                    </div>
                    @elseif(session('error'))
                    <div class="col-sm-12 shadow p-2 bg-danger text-white">
                        <strong>{{session('error')}}</strong>
                    </div>
                    <br>
                    @endif
                    @if(isset($Registro->id))
                    <input type="hidden" name="id" value="{{$Registro->id}}">
                    @endif
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Organização</label>
                            <select name="id_org" class="form-control @error('Organizacao') is-invalid @enderror" maxlength="100" {{(Route::currentRouteName() == 'Secretarias/Administradores/Novo') ? 'required' : 'disabled'}}>
                                <option value="">Selecione a Organização</option>
                                @foreach($Organizacoes as $o)
                                    <option value="{{$o->id}}" {{(isset($Registro->id) && $Registro->id == $o->id) ? 'selected' : ''}}>{{$o->Organizacao}}</option>
                                @endforeach
                            </select>
                            @error('Organizacao')
                                <div class="text-danger"><strong>{{$message}}</strong></div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>E-mail</label>
                            <input type="email" name="email" class="form-control @error('Email') is-invalid @enderror" maxlength="50" required value="{{isset($Registro->email) ? $Registro->email : ''}}">
                            @error('email')
                            <div class="text-danger"><strong>{{$message}}</strong></div>
                            @enderror
                           
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Nome</label>
                            <input type="text" name="name" class="form-control" maxlength="50" minlength="5" required value="{{isset($Registro->name) ? $Registro->name : ''}}">
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left">
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        <a class="btn btn-light" href="{{route('Secretarias/Administradores')}}">Voltar</a>
                    </div>
                </form>    
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>