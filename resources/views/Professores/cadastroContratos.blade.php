<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--LISTAS-->
            <form action="{{route('Professores/Contratos/Save')}}" method="POST">
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
                @if(!empty($id))
                <input type="hidden" name="id" value="{{$id}}">
                @endif
                <div class="row">
                    <div class="col-sm-12">
                        <label>Selecione o Professor</label>
                        <select name="IDProfessor" class="form-control" required>
                            <option value="">Selecione</option>
                            @foreach($Professores as $p)
                            <option value="{{$p->IDProfessor}}" {{isset($Registro) && $Registro->IDProfessor == $p->IDProfessor ? 'selected' : ''}}>{{$p->Professor}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <label>Nome</label>
                        <input type="text" name="Nome" class="form-control" maxlength="50" required value="{{isset($Registro) ? $Registro->Nome : ''}}">
                    </div>
                    <div class="col-sm-3">
                        <label>Salário</label>
                        <input type="text" name="Salario" class="form-control" value="{{isset($Registro) ? $Registro->Salario : ''}}" required>
                    </div>
                    <div class="col-sm-3">
                        <label>Início</label>
                        <input type="date" name="Inicio" class="form-control" required value="{{isset($Registro) ? $Registro->Inicio : ''}}">
                    </div>
                    <div class="col-sm-3">
                        <label>Término</label>
                        <input type="date" name="Termino" class="form-control" required value="{{isset($Registro) ? $Registro->Termino : ''}}">
                    </div>
                </div>
                <br>
                <div class="col-sm-12 text-left row">
                    <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                    &nbsp;
                    <a class="btn btn-light col-auto" href="{{route('Professores/Contratos')}}">Voltar</a>
                </div>
            </form> 
            <br>
            @if(!empty($id))
            <hr>
            <form class="row" method="POST" action="{{route('Professores/Aditivos/Save',$id)}}">
                @csrf
                @method("PATCH")
                <div class="col-sm-6">
                    <label>Aditivo</label>
                    <input type="text" name="Nome" class="form-control">
                </div>
                <div class="col-sm-4">
                    <label>Data</label>
                    <input type="date" name="Data" class="form-control">
                </div>
                <div class="col-sm-2">
                    <label style="visibility:hidden;">Faz o F</label>
                    <input type="submit" class="form-control" value="Enviar">
                </div>
            </form>
            <br>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Aditivo</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    @if($Aditivos)
                        @foreach($Aditivos as $ad)
                        <tr>
                            <td>{{$ad->Nome}}</td>
                            <td>{{$ad->Data}}</td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            @endif
            <!--//-->
        </div>
    </div>
</x-educacional-layout>