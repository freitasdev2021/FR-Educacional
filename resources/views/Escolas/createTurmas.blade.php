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
                <form action="{{route('Escolas/Turmas/Save')}}" method="POST">
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
                    @if(isset($id))
                    <input type="hidden" name="id" value="{{$id}}">
                    @endif
                    <input type="hidden" name="IDOrg" value="{{Auth::user()->id_org}}">
                    @if(Auth::user()->tipo == 2)
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Escola</label>
                            <select name="IDEscola" class="form-control" required>
                                <option value="">Selecione</option>
                                @foreach($escolas as $e)
                                <option value="{{$e->id}}" {{(isset($Registro->IDEscola) && $Registro->IDEscola == $e->id) ? 'selected' : ''}}>{{$e->Nome}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Série</label>
                            <select name="Serie" class="form-control">
                                <option value="">Selecione</option>
                                <optgroup label="Supletivo">
                                    <option value="Educação de Jovens e Adultos (EJA)" {{(isset($Registro->Serie) && $Registro->Serie == "Educação de Jovens e Adultos (EJA)" ) ? 'selected' : ''}}>Educação de Jovens e Adultos (EJA)</option>
                                </optgroup>
                                <optgroup label="Ensino Infantil: 1º ao 4º Periodo">
                                    <option value="1º Periodo E.INFANTIL" {{(isset($Registro->Serie) && $Registro->Serie == "1º Periodo E.INFANTIL" ) ? 'selected' : ''}}>1º Periodo E.INFANTIL</option>
                                    <option value="2º Periodo E.INFANTIL" {{(isset($Registro->Serie) && $Registro->Serie =="2º Periodo E.INFANTIL") ? 'selected' : ''}}>2º Periodo E.INFANTIL</option>
                                    <option value="3º Periodo E.INFANTIL" {{(isset($Registro->Serie) && $Registro->Serie =="3º Periodo E.INFANTIL") ? 'selected' : ''}}>3º Periodo E.INFANTIL</option>
                                    <option value="4º Periodo E.INFANTIL" {{(isset($Registro->Serie) && $Registro->Serie =="4º Periodo E.INFANTIL") ? 'selected' : ''}}>4º Periodo E.INFANTIL</option>
                                </optgroup>
                                <optgroup label="Ensino Fundamental: 1º ao 9º Ano">
                                    <option value="1º Ano E.FUNDAMENTAL" {{(isset($Registro->Serie) && $Registro->Serie == "1º Ano E.FUNDAMENTAL" ) ? 'selected' : ''}}>1º Ano E.FUNDAMENTAL</option>
                                    <option value="2º Ano E.FUNDAMENTAL" {{(isset($Registro->Serie) && $Registro->Serie == "2º Ano E.FUNDAMENTAL" ) ? 'selected' : ''}}>2º Ano E.FUNDAMENTAL</option>
                                    <option value="3º Ano E.FUNDAMENTAL" {{(isset($Registro->Serie) && $Registro->Serie == "3º Ano E.FUNDAMENTAL" ) ? 'selected' : ''}}>3º Ano E.FUNDAMENTAL</option>
                                    <option value="4º Ano E.FUNDAMENTAL" {{(isset($Registro->Serie) && $Registro->Serie == "4º Ano E.FUNDAMENTAL" ) ? 'selected' : ''}}>4º Ano E.FUNDAMENTAL</option>
                                    <option value="5º Ano E.FUNDAMENTAL" {{(isset($Registro->Serie) && $Registro->Serie == "5º Ano E.FUNDAMENTAL" ) ? 'selected' : ''}}>5º Ano E.FUNDAMENTAL</option>
                                    <option value="6º Ano E.FUNDAMENTAL" {{(isset($Registro->Serie) && $Registro->Serie == "6º Ano E.FUNDAMENTAL" ) ? 'selected' : ''}}>6º Ano E.FUNDAMENTAL</option>
                                    <option value="7º Ano E.FUNDAMENTAL" {{(isset($Registro->Serie) && $Registro->Serie == "7º Ano E.FUNDAMENTAL" ) ? 'selected' : ''}}>7º Ano E.FUNDAMENTAL</option>
                                    <option value="8º Ano E.FUNDAMENTAL" {{(isset($Registro->Serie) && $Registro->Serie == "8º Ano E.FUNDAMENTAL" ) ? 'selected' : ''}}>8º Ano E.FUNDAMENTAL</option>
                                    <option value="9º Ano E.FUNDAMENTAL" {{(isset($Registro->Serie) && $Registro->Serie == "9º Ano E.FUNDAMENTAL" ) ? 'selected' : ''}}>9º Ano E.FUNDAMENTAL</option>
                                </optgroup>
                                <optgroup label="Ensino Médio: 1º ao 3º Ano">
                                    <option value="1º Ano E.MÉDIO" {{(isset($Registro->Serie) && $Registro->Serie == "1º Ano E.MÉDIO" ) ? 'selected' : ''}}>1º Ano E.MÉDIO</option>
                                    <option value="2º Ano E.MÉDIO" {{(isset($Registro->Serie) && $Registro->Serie == "2º Ano E.MÉDIO" ) ? 'selected' : ''}}>2º Ano E.MÉDIO</option>
                                    <option value="3º Ano E.MÉDIO" {{(isset($Registro->Serie) && $Registro->Serie == "3º Ano E.MÉDIO" ) ? 'selected' : ''}}>3º Ano E.MÉDIO</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Salas</label>
                            <select name="IDSala" class="form-control">
                                <option value="">Selecione</option>
                                @foreach($Salas as $s)
                                <option value="{{$s->id}}">{{$s->NMSala}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Turma</label>
                            <input type="text" name="Nome" class="form-control" value="{{isset($Registro->Turma) ? $Registro->Turma : ''}}">
                        </div>
                        <div class="col-sm-3">
                            <label>Matérias p/Repetência</label>
                            <input type="text" name="QTRepetencia" class="form-control" value="{{isset($Registro->QTRepetencia) ? $Registro->QTRepetencia : ''}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <label>Início</label>
                            <input type="time" name="INITurma" class="form-control" value="{{isset($Registro->INITurma) ? $Registro->INITurma : ''}}">
                        </div>
                        <div class="col-sm-2">
                            <label>Termino</label>
                            <input type="time" name="TERTurma" class="form-control" value="{{isset($Registro->TERTurma) ? $Registro->TERTurma : ''}}">
                        </div>
                        <div class="col-sm-2">
                            <label>Período</label>
                            <select name="Periodo" class="form-control">
                                <option value="">Selecione</option>
                                <option value="Bimestral" {{(isset($Registro->Periodo) && $Registro->Periodo == "Bimestral" ) ? 'selected' : ''}} >Bimestral</option>
                                <option value="Trimestral" {{(isset($Registro->Periodo) && $Registro->Periodo == "Trimestral" ) ? 'selected' : ''}}>Trimestral</option>
                                <option value="Semestral" {{(isset($Registro->Periodo) && $Registro->Periodo == "Semestral" ) ? 'selected' : ''}}>Semestral</option>
                                <option value="Anual" {{(isset($Registro->Periodo) && $Registro->Periodo == "Anual" ) ? 'selected' : ''}}>Anual</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label>Total</label>
                            <input type="number" name="NotaPeriodo" class="form-control" value="{{isset($Registro->NotaPeriodo) ? $Registro->NotaPeriodo : ''}}"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Média</label>
                            <input type="number" name="MediaPeriodo" class="form-control" value="{{isset($Registro->MediaPeriodo) ? $Registro->MediaPeriodo : ''}}"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Total Ano</label>
                            <input type="number" name="TotalAno" class="form-control" value="{{isset($Registro->TotalAno) ? $Registro->TotalAno : ''}}"> 
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Escolas/Turmas')}}">Voltar</a>
                    </div>
                </form>    
            </div>
            <!--//-->
        </div>
    </div>>
</x-educacional-layout>