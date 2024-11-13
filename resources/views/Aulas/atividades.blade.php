<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <div class="fr-card-body">
            <!--CABECALHO-->
            @if(Auth::user()->tipo == 6)
            <div class="col-sm-12 p-2 row">
                <div class="col-auto">
                    <a href="{{route('Aulas/Atividades/Novo')}}" class="btn btn-fr">Adicionar</a>
                </div>
                <form class="row col-auto" method="GET">
                    <div class="col-auto">
                        <select name="IDTurma" class="form-control">
                            <option value="">Filtre pela Turma</option>
                            @foreach($Turmas as $t)
                            <option value="{{$t->id}}" {{isset($_GET['IDTurma']) && $_GET['IDTurma'] == $t->id ? 'selected' : ''}}>{{$t->Serie}} - {{$t->Nome}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <select name="Estagio" class="form-control">
                            <option value="">Selecione</option>
                            <optgroup label="Bimestre">
                                <option value="1º BIM" {{ isset($_GET['Estagio']) && $_GET['Estagio'] == "1º BIM" ? 'selected' : '' }}>1º Bimestre</option>
                                <option value="2º BIM" {{ isset($_GET['Estagio']) && $_GET['Estagio'] == "2º BIM" ? 'selected' : '' }}>2º Bimestre</option>
                                <option value="3º BIM" {{ isset($_GET['Estagio']) && $_GET['Estagio'] == "3º BIM" ? 'selected' : '' }}>3º Bimestre</option>
                                <option value="4º BIM" {{ isset($_GET['Estagio']) && $_GET['Estagio'] == "4º BIM" ? 'selected' : '' }}>4º Bimestre</option>
                            </optgroup>
                            
                            <optgroup label="Trimestre">
                                <option value="1º TRI" {{ isset($_GET['Estagio']) && $_GET['Estagio'] == "1º TRI" ? 'selected' : '' }}>1º Trimestre</option>
                                <option value="2º TRI" {{ isset($_GET['Estagio']) && $_GET['Estagio'] == "2º TRI" ? 'selected' : '' }}>2º Trimestre</option>
                                <option value="3º TRI" {{ isset($_GET['Estagio']) && $_GET['Estagio'] == "3º TRI" ? 'selected' : '' }}>3º Trimestre</option>
                            </optgroup>
                            
                            <optgroup label="Semestre">
                                <option value="1º SEM" {{ isset($_GET['Estagio']) && $_GET['Estagio'] == "1º SEM" ? 'selected' : '' }}>1º Semestre</option>
                                <option value="2º SEM" {{ isset($_GET['Estagio']) && $_GET['Estagio'] == "2º SEM" ? 'selected' : '' }}>2º Semestre</option>
                            </optgroup>
                            
                            <optgroup label="Periodo">
                                <option value="1º PER" {{ isset($_GET['Estagio']) && $_GET['Estagio'] == "1º PER" ? 'selected' : '' }}>1º Período</option>
                            </optgroup>
                        </select>
                        
                    </div>
                    <div class="col-auto">
                        <input type="submit" class="btn btn-light form-control" value="Filtrar">
                    </div>
                </form>
            </div>
            <hr>
            @endif
            <!--LISTAS-->
            <div class="col-sm-12 p-2">
                <table class="table table-sm tabela" id="escolas" data-rota="{{route('Aulas/Atividades/list')}}{{ isset($_GET['IDTurma']) && !empty($_GET['IDTurma']) ? '?IDTurma=' . $_GET['IDTurma'] : '' }}{{ isset($_GET['Estagio']) && !empty($_GET['Estagio']) ? (isset($_GET['IDTurma']) && !empty($_GET['IDTurma']) ? '&' : '?') . 'Estagio=' . $_GET['Estagio'] : '' }}">
                    <thead>
                      <tr>
                        <th style="text-align:center;" scope="col">Atividade</th>
                        <th style="text-align:center;" scope="col">Professor</th>
                        <th style="text-align:center;" scope="col">Turma</th>
                        <th style="text-align:center;" scope="col">Aula</th>
                        <th style="text-align:center;" scope="col">Aplicação</th>
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