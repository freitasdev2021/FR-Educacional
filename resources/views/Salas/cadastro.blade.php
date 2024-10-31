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
                <form action="{{route('Escolas/Salas/Save')}}" method="POST">
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
                        <div class="col-sm-12">
                            <label>Escola</label>
                            <select name="IDEscola" class="form-control" required>
                                <option value="">Selecione</option>
                                @foreach($escolas as $e)
                                <option value="{{$e->id}}" {{(isset($Registros->IDEscola) && $Registros->IDEscola == $e->id) ? 'selected' : ''}}>{{$e->Nome}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Nome</label>
                            <input type="text" name="NMSala" value="{{isset($Registros) ? $Registros->NMSala : ''}}" class="form-control">
                        </div>
                        <div class="col-sm-4">
                            <label>Tamanho (M2)</label>
                            <input type="number" name="TMSala" value="{{isset($Registros) ? $Registros->TMSala : ''}}" class="form-control">
                        </div>
                        <div class="col-sm-4">
                            <label>Tipo da Dependência</label>
                            <select name="TPSala" class="form-control" required>
                                <option value="Sala de Aula" {{isset($Registros) && $Registros->TPSala == 'Sala de Aula' ? 'selected' : ''}}>Sala de Aula</option>
                                <option value="Refeitório" {{isset($Registros) && $Registros->TPSala == 'Refeitório' ? 'selected' : ''}}>Refeitório</option>
                                <option value="Auditório" {{isset($Registros) && $Registros->TPSala == 'Auditório' ? 'selected' : ''}}>Auditório</option>
                                <option value="Biblioteca" {{isset($Registros) && $Registros->TPSala == 'Biblioteca' ? 'selected' : ''}}>Biblioteca</option>
                                <option value="Diretoria" {{isset($Registros) && $Registros->TPSala == 'Diretoria' ? 'selected' : ''}}>Diretoria</option>
                                <option value="Sala dos Professores" {{isset($Registros) && $Registros->TPSala == 'Sala dos Professores' ? 'selected' : ''}}>Sala dos Professores</option>
                            </select>
                        </div>
                    </div>
                    <br>
                    @if(isset($Turmas))
                    <div class="col-sm-12">
                        <label>Turmas ocupantes da Sala</label>
                        <table class="table table-sm tabela">
                            <thead>
                            <tr>
                                <th style="text-align:center;" scope="col">Nome</th>
                                <th style="text-align:center;" scope="col">Serie</th>
                                <th style="text-align:center;" scope="col">Início</th>
                                <th style="text-align:center;" scope="col">Término</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($Turmas as $t)
                                <tr>
                                    <td>{{$t->Nome}}</td>
                                    <td>{{$t->Serie}}</td>
                                    <td>{{$t->INITurma}}</td>
                                    <td>{{$t->TERTurma}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                    <br>
                    <div class="col-sm-12 text-left row">
                        <button type="submit" class="btn btn-fr col-auto">Salvar</button>
                        &nbsp;
                        <a class="btn btn-light col-auto" href="{{route('Escolas/Salas')}}">Voltar</a>
                    </div>
                </form>    
            </div>
            <!--//-->
        </div>
    </div>>
</x-educacional-layout>