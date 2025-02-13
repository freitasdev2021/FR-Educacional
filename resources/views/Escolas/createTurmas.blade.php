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
                    @if(in_array(Auth::user()->tipo,[2,2.5]))
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Escola</label>
                            <select name="IDEscola" class="form-control" required>
                                <option value="">Selecione</option>
                                @foreach($escolas as $e)
                                <option value="{{$e->id}}" {{(isset($Registro->IDEscola) && $Registro->IDEscola == $e->id) ? 'selected' : ''}}>{{$e->Nome}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label>Série</label>
                            <select name="Serie" class="form-control">
                                <option value="">Selecione</option>
                                <option value="Multiserie" {{(isset($Registro->Serie) && $Registro->Serie == "Multiserie" ) ? 'selected' : ''}}>Multiserie</option>
                                <optgroup label="Supletivo">
                                    <option value="Educação de Jovens e Adultos (EJA)" {{(isset($Registro->Serie) && $Registro->Serie == "Educação de Jovens e Adultos (EJA)" ) ? 'selected' : ''}}>Educação de Jovens e Adultos (EJA)</option>
                                </optgroup>
                                <optgroup label="Ensino Infantil">
                                    <option value="Creche" {{(isset($Registro->Serie) && $Registro->Serie == "Creche" ) ? 'selected' : ''}}>Creche</option>
                                    <option value="1º Periodo E.INFANTIL" {{(isset($Registro->Serie) && $Registro->Serie == "1º Periodo E.INFANTIL" ) ? 'selected' : ''}}>1º Periodo E.INFANTIL</option>
                                    <option value="2º Periodo E.INFANTIL" {{(isset($Registro->Serie) && $Registro->Serie =="2º Periodo E.INFANTIL") ? 'selected' : ''}}>2º Periodo E.INFANTIL</option>
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
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-sm-2">
                            <label>Salas</label>
                            <select name="IDSala" class="form-control">
                                <option value="">Selecione</option>
                                @foreach($Salas as $s)
                                <option value="{{$s->id}}">{{$s->NMSala}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label>Turma</label>
                            <input type="text" name="Nome" class="form-control" value="{{isset($Registro->Turma) ? $Registro->Turma : ''}}">
                        </div>
                        <div class="col-sm-2">
                            <label>Matérias p/Repetência</label>
                            <input type="number" name="QTRepetencia" class="form-control" value="{{isset($Registro->QTRepetencia) ? $Registro->QTRepetencia : ''}}">
                        </div>
                        <div class="col-sm-3">
                            <label>Frequência MIN (%)</label>
                            <input type="number" name="MINFrequencia" class="form-control" value="{{isset($Registro->MINFrequencia) ? $Registro->MINFrequencia : ''}}">
                        </div>
                        <div class="col-sm-3">
                            <label>Tipo de Avaliação</label>
                            <select name="TPAvaliacao" class="form-control">
                                <option value="Nota" {{(isset($Registro->TPAvaliacao) && $Registro->TPAvaliacao == "Nota" ) ? 'selected' : ''}}>Nota</option>
                                <option value="Conceito" {{(isset($Registro->TPAvaliacao) && $Registro->TPAvaliacao == "Conceito" ) ? 'selected' : ''}}>Conceito</option>
                            </select>
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
                            <label>Total Etapa</label>
                            <input type="number" placeholder="Ex: 25" name="NotaPeriodo" class="form-control" value="{{isset($Registro->NotaPeriodo) ? $Registro->NotaPeriodo : ''}}"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Média Etapa</label>
                            <input type="number" placeholder="Ex: 15" name="MediaPeriodo" class="form-control" value="{{isset($Registro->MediaPeriodo) ? $Registro->MediaPeriodo : ''}}"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Total Ano</label>
                            <input type="number" placeholder="Ex: 100" name="TotalAno" class="form-control" value="{{isset($Registro->TotalAno) ? $Registro->TotalAno : ''}}"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Capacidade da turma</label>
                            <input type="number" name="Capacidade" class="form-control" value="{{isset($Registro->Capacidade) ? $Registro->Capacidade : ''}}"> 
                        </div>
                        <div class="col-sm-2">
                            <label>Turno</label>
                            <select name="Turno" class="form-control">
                                <option value="Matutino" {{isset($Registro) && $Registro->Turno == "Matutino" ? 'selected' : ''}}>Matutino</option>
                                <option value="Vespertino" {{isset($Registro) && $Registro->Turno == "Vespertino" ? 'selected' : ''}}>Vespertino</option>
                                <option value="Noturno" {{isset($Registro) && $Registro->Turno == "Noturno" ? 'selected' : ''}}>Noturno</option>
                            </select> 
                        </div>
                    </div>
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Escolas/Turmas')}}">Voltar</a>
                    </div>
                </form> 
                <hr>
                @if(isset($id) && !empty($id))
                <h5>Alunos</h5>
                <div style="width:90%">
                    <div style="margin-bottom:20px;">
                        <a href="{{route('Turmas/Alunos/Exportar',$id)}}" class="btn btn-default">Imprimir</a>
                    </div>
                    <table class="table table-striped text-center">
                        <thead>
                        <tr>
                            <th>Número</th>
                            <th>Nome</th>
                            @if(in_array(Auth::user()->tipo,[2,2.5]))<th>Escola</th>@endif
                            <th>Data de Nascimento</th>
                            <th>CPF</th>
                            <th>Responsável</th>
                            <th>Telefone Responsavel</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($Alunos as $k => $a)
                                <tr>
                                    <td>{{$k+1}}</td>
                                    <td>{{$a->Nome}}</td>
                                    @if(in_array(Auth::user()->tipo,[2,2.5]))<td>{{$a->Escola}}</td>@endif
                                    <td>{{date('d/m/Y', strtotime($a->Nascimento))}}</td>
                                    <td>{{$a->CPF}}</td>
                                    <th>{{$a->NMResponsavel}}</th>
                                    <th>{{$a->CLResponsavel}}</th>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if(Auth::user()->email == "sugey9086@uorak.com")
                <h5>Importar Alunos</h5>
                <form action="{{route('Alunos/Importar',$id)}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method("PATCH")
                    <div class="row">
                        <div class="col-sm-11">
                            <label>Arquivo</label>
                            <input type="file" class="form-control" name="Alunos" accept=".xlsx, .xls, .ods">
                        </div>
                        <div class="col-sm-1">
                            <label style="visibility:hidden;">a</label>
                            <input type="submit" name="Enviar" value="Upload" class="btn btn-success form-control">
                        </div>
                    </div>
                </form>  
                @endif
                @endif 
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>