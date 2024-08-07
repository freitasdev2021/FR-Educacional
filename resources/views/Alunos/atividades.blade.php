<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'],$id)}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--CABECALHO-->
            <form class="col-sm-12 p-2 row" action="{{$_SERVER['PHP_SELF']}}" method="GET">
                <label>Utilize os Filtros para Iniciar a Pesquisa</label>
                <div class="col-auto">
                    <select name="Disciplina" class="form-control" required>
                        <option value="">Selecione a Disciplina</option>
                        @foreach($Disciplinas as $d)
                        <option value="{{$d['IDDisciplina']}}" {{isset($_GET['Disciplina']) && $_GET['Disciplina'] == $d['IDDisciplina'] ? 'selected' : ''}}>{{$d['Disciplina']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <select name="Estagio" class="form-control" required>
                        <option value="">Selecione o Estagio</option>
                        @foreach($Estagios as $e)
                        <option value="{{$e}}"  {{isset($_GET['Estagio']) && $_GET['Estagio'] == $e ? 'selected' : ''}}>{{$e}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-success">Filtrar</button>
                </div>
            </form>
            <!--LISTAS-->
            <div class="col-sm-12 p-2">
                <hr>
                <table class="table table-sm tabela" id="escolas" data-rota="{{ route('Alunos/Desempenho/list',$id) . (isset($_GET['Disciplina']) ? '?Disciplina=' . $_GET['Disciplina'] : '') . (isset($_GET['Estagio']) ? '&Estagio=' . $_GET['Estagio'] : '') }}">
                    <thead>
                      <tr>
                        <th style="text-align:center;" scope="col">Disciplina</th>
                        <th style="text-align:center;" scope="col">Atividade</th>
                        <th style="text-align:center;" scope="col">Pontuação</th>
                        <th style="text-align:center;" scope="col">Estágio</th>
                        <th style="text-align:center;" scope="col">Data</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                </table>
            </div>
            <!--//-->
        </div>
    </div>
    <script>
        function renovarMatricula(id){
            $("#aluno_"+id).html()
        }
    </script>
</x-educacional-layout>