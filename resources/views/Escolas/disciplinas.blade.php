<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--CABECALHO-->
            <div class="col-sm-12 p-2 row">
                @if(in_array(Auth::user()->tipo,[2,2.5]))
                <div class="col-auto">
                    <a href="{{route('Escolas/Disciplinas/Novo')}}" class="btn btn-fr">Adicionar</a>
                </div>
                <hr>
                @elseif(in_array(Auth::user()->tipo,[6,5,5.5]))
                <form class="row" method="GET">
                    <div class="col-sm-4">
                        <label>Turmas</label>
                        <select name="IDTurma" class="form-control">
                            <option value="">Selecione a turma</option>
                            @foreach($Turmas as $t)
                            <option value="{{$t->id}}" {{isset($_GET['IDTurma']) && $_GET['IDTurma'] == $t->id ? 'selected' : ''}}>{{$t->Turma." (".$t->Serie.") - ". $t->Escola}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label style="visibility:hidden;">Turmas</label>
                        <input type="submit" class="form-control btn btn-default" value="Filtrar">
                    </div>
                </form>
                @endif
            </div>
            <!--LISTAS-->
            <div class="col-sm-12 p-2">
                <table class="table table-sm tabela" id="escolas" data-rota="{{route('Escolas/Disciplinas/list',$IDTurma)}}">
                    <thead>
                      <tr>
                        <th style="text-align:center;" scope="col">Disciplina</th>
                        @if(in_array(Auth::user()->tipo,[2,2.5]))<th style="text-align:center;" scope="col">Escolas</th>@endif
                        <th style="text-align:center;" scope="col">Opções</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                  </table>
            </div>
            <!--//-->
        </div>
    </div>
</x-educacional-layout>