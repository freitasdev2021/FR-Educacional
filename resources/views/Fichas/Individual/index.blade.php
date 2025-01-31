<x-educacional-layout>
    <div class="fr-card p-0 shadow col-sm-12">
        <div class="fr-card-header">
           @foreach($submodulos as $s)
            <x-Submodulo nome="{{$s['nome']}}" endereco="{{$s['endereco']}}" rota="{{route($s['rota'])}}" icon="bx bx-list-ul"/>
           @endforeach
        </div>
        <style>
        table, th, td {
            border: 1px solid black;
        }

        .tableFicha{
            overflow:scroll;
        }
        </style>
        <div class="fr-card-body">
            <!--CABECALHO-->
            <div class="col-sm-12 p-2 row">
                <form class="row col-sm-12" method="GET">
                    <div class="col-sm-6">
                        <select name="IDAluno" class="form-control" required>
                            <option value="">Aluno</option>
                            @foreach($Alunos as $al)
                            <option value="{{$al->id}}" {{isset($_GET['IDAluno']) == $al->id ? 'selected' : ''}}>{{$al->Escola}} - {{$al->Aluno}} - {{$al->Serie}} - {{$al->Turma}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <select name="IDDisciplina" class="form-control">
                            <option value="">Disciplina</option>
                            @foreach($Disciplinas as $d)
                            <option value="{{$d->IDDisciplina}}" {{isset($_GET['IDDisciplina']) && $_GET['IDDisciplina'] == $d->IDDisciplina ? 'selected' : ''}}>{{$d->Disciplina}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <select name="Estagio" class="form-control">
                            <option value="">Etapa</option>
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
                        <input type="submit" class="btn btn-default form-control" value="Filtrar">
                    </div>
                </form>
            </div>
            <!--LISTAS-->
            <div class="col-sm-12">
                <!--LISTA DE ALUNOS-->
                <form action="{{route('Fichas/Individual/Save')}}" class="col-sm-12 row" method="POST" id="formAvaliacao">
                    @csrf
                    <table class="tableFicha">
                        <thead>
                            
                        </thead>
                        <tbody id="sinteses">
                           
                        </tbody>
                    </table>
                    <div class="col-sm-12">
                        <hr>
                    </div>
                    <input type="hidden" name="IDAluno" value="{{isset($_GET['IDAluno']) ? $_GET['IDAluno'] : ''}}">
                    <button class="btn btn-success col-auto" type="submit">Salvar</button>
                </form>
                <!--FIM DA LISTA DE ALUNOS-->
            </div>
            <!--//-->
        </div>                    
    </div>
</x-educacional-layout>