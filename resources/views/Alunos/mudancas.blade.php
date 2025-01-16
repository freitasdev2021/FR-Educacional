<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--LISTAS-->
            <div class="col-sm-12 p-2">
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
                <form class="row" action="{{route('Alunos/Mudancas')}}" method="GET">
                    <div class="col-sm-4">
                        <select name="IDTurma" class="form-control">
                            <option value="">Selecione a turma</option>
                            @foreach($Turmas as $t)
                            <option value="{{$t->IDTurma}}" {{isset($_GET['IDTurma']) && $_GET['IDTurma'] == $t->IDTurma ? 'selected' : ''}}>{{$t->Turma." (".$t->Serie.") - ". $t->Escola}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <input type="submit" class="btn btn-success form-control" value="Filtrar">
                    </div>
                </form>
                <br>
                @if(isset($_GET['IDTurma']))
                <form action="{{route('Alunos/RemanejarMassa',['IDTurmaOrigem'=>$_GET['IDTurma'],'IDEscola'=>$IDEscola])}}" method="POST">
                    @csrf
                    @method("PATCH")
                    <table class="table table-sm tabela">
                        <thead>
                          <tr>
                            <th style="text-align:center;" scope="col">Aluno</th>
                            <th style="text-align:center;" scope="col">Turma de Destino</th>
                            <th style="text-align:center;" scope="col">Última Reclassificação</th>
                            <th style="text-align:center;" scope="col">Matrícula</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($Alunos as $a)
                          <tr>
                            <td>
                                <input type="hidden" name="IDAluno[]" value="{{$a->IDAluno}}">
                                {{$a->Aluno}}
                            </td>
                            <td>
                                <div class="col-auto">
                                    <select name="IDTurmaDestino[]" class="form-control">
                                        <option value="">Selecione a turma</option>
                                        @foreach($Turmas as $t)
                                        <option value="{{$t->IDTurma}}" {{isset($_GET['IDTurma']) && $_GET['IDTurma'] == $t->IDTurma ? 'selected' : ''}}>{{$t->Turma." (".$t->Serie.") - ". $t->Escola}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            <td>{{($a->UReclassificacao) ? date('Y',strtotime($a->UReclassificacao)) : 'Não Houve'}}</td>
                            <td>{{date('Y',strtotime($a->DTEntrada))}}</td>
                          </tr>
                          @endforeach
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-success">Salvar</button>
                </form>
                @endif
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>