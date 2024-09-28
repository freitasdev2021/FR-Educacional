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
                <form action="{{route('Escolas/Vagas/Save')}}" method="POST">
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
                            <label>Escola</label>
                            <select name="IDEscola" class="form-control" required>
                                <option value="">Selecione a Escola</option>
                                @foreach($escolas as $e)
                                <option value="{{$e->id}}" {{isset($Registro->IDEscola) && $Registro->IDEscola == $e->id ? 'selected' : ''}}>{{$e->Nome}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Faixa</label>
                            <select name="Faixa" class="form-control">
                                <option value="">Selecione</option>
                                <option value="Ensino Infantil: 1º ao 4º Periodo" {{(isset($Registro->Faixa) && $Registro->Faixa == "Ensino Infantil: 1º ao 4º Periodo" ) ? 'selected' : ''}}>Ensino Infantil: 1º ao 4º Periodo</option>
                                <option value="Ensino Fundamental: 1º ao 9º Ano" {{(isset($Registro->Faixa) && $Registro->Faixa == "Ensino Fundamental: 1º ao 9º Ano" ) ? 'selected' : ''}}>Ensino Fundamental: 1º ao 9º Ano</option>
                                <option value="Ensino Médio: 1º ao 3º Ano" {{(isset($Registro->Faixa) && $Registro->Faixa == "Ensino Médio: 1º ao 3º Ano" ) ? 'selected' : ''}}>Ensino Médio: 1º ao 3º Ano</option> 
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Quantidade de Vagas</label>
                            <input type="text" name="QTVagas" class="form-control" value="{{isset($Registro->QTVagas) ? $Registro->QTVagas : ''}}">
                        </div>
                        <div class="col-sm-3">
                            <label>I.Matrícula</label>
                            <input type="date" name="INIMatricula" class="form-control" value="{{isset($Registro->INIMatricula) ? $Registro->INIMatricula : ''}}">
                        </div>
                        <div class="col-sm-3">
                            <label>TER.Matrícula</label>
                            <input type="date" name="TERMatricula" class="form-control" value="{{isset($Registro->TERMatricula) ? $Registro->TERMatricula : ''}}">
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Escolas/Vagas')}}">Voltar</a>
                    </div>
                </form>    
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>