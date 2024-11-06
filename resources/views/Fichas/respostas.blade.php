<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-Submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
        <!-- Cabeçalho da escola e nome do aluno -->

        <div style="text-align: center; margin-bottom: 20px;">
            <form class="row" method="GET" action="{{$_SERVER['PHP_SELF']}}">
                <div class="col-sm-11">
                    <select name="IDTurma" class="form-control">
                        <option value="">Selecione</option>
                        @foreach($Turmas as $t)
                        <option value="{{$t->IDTurma}}" {{isset($_GET['IDTurma']) && $_GET['IDTurma'] == $t->IDTurma ? 'selected' : ''}}>{{$t->Turma}} - {{$t->Serie}} - ({{$t->Escola}})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-1">
                    <input type="submit" value="Filtrar" class="btn btn-success">
                </div>
            </form>
        </div>

        @foreach($registros as $registro)
            <div>
                <p><strong>Aluno:</strong> {{ $registro->nome }}</p>

                <!-- Tabela de Respostas -->
                <table class="table">
                    <thead>
                        <tr>
                            <th class="content-cell">Conteúdo</th>
                            <th class="response-cell">Resposta</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(json_decode($registro->respostas, true) as $resposta)
                            <tr>
                                <td class="content-cell">{{$resposta['Conteudo']}}</td>
                                <td class="response-cell">{{ $resposta['Resposta'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <br><hr><br> <!-- Espaço entre boletins -->
            </div>
        @endforeach
            <div class="col-auto">
                <a class="btn btn-success" href="{{route('Fichas/Respostas/Export/PDF',["id"=>$id,"IDTurma"=>isset($_GET['IDTurma']) && !empty($_GET['IDTurma']) ? $_GET['IDTurma'] : 0 ])}}">Imprimir</a>
            </div>
        </div>
    </div>
</x-educacional-layout>